<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SistemKriteria;
use Illuminate\Support\Facades\DB;

class KriteriaController extends Controller
{
    /**
     * Display a listing of kriteria utama (level 1).
     * Menampilkan daftar kriteria utama dengan total bobot
     */
    public function index()
    {
        // Get hanya kriteria utama (level 1) dengan urutan terbaru di atas
        $kriteria = SistemKriteria::where('level', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung total bobot kriteria utama
        $totalBobot = $kriteria->sum('bobot');

        return view('kriteria.index', compact('kriteria', 'totalBobot'));
    }

    /**
     * Store a newly created kriteria utama.
     * Validasi total bobot tidak boleh lebih dari 100%
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tipe_kriteria' => 'required|in:benefit,cost',
            'bobot' => 'required|numeric|min:0|max:100',
        ], [
            'nama_kriteria.required' => 'Nama kriteria wajib diisi',
            'nama_kriteria.max' => 'Nama kriteria maksimal 100 karakter',
            'tipe_kriteria.required' => 'Tipe kriteria wajib dipilih',
            'tipe_kriteria.in' => 'Tipe kriteria harus benefit atau cost',
            'bobot.required' => 'Bobot kriteria wajib diisi',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0',
            'bobot.max' => 'Bobot maksimal 100',
        ]);

        // Validasi total bobot tidak boleh lebih dari 100%
        $currentTotalBobot = SistemKriteria::where('level', 1)->sum('bobot');
        $newTotalBobot = $currentTotalBobot + $request->bobot;

        if ($newTotalBobot > 100) {
            $sisaBobot = 100 - $currentTotalBobot;
            return redirect()->route('kriteria.index')
                ->with('error', 'Gagal menambahkan kriteria. Total bobot akan melebihi 100%. Sisa bobot yang tersedia: ' . number_format($sisaBobot, 2) . '%');
        }

        // Get urutan terakhir
        $urutanTerakhir = SistemKriteria::where('level', 1)->max('urutan') ?? 0;

        // Create kriteria utama
        SistemKriteria::create([
            'id_parent' => null,
            'nama_kriteria' => $request->nama_kriteria,
            'deskripsi' => $request->deskripsi,
            'tipe_kriteria' => $request->tipe_kriteria,
            'bobot' => $request->bobot,
            'tipe_input' => null,
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => null,
            'level' => 1,
            'urutan' => $urutanTerakhir + 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
            'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
            'is_active' => true,
        ]);

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan');
    }

    /**
     * Show detail kriteria with sub-kriteria list.
     */
    public function detail($id)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($id);

        // Get all sub-kriteria for this kriteria
        $subKriteria = SistemKriteria::where('id_parent', $kriteria->id)
            ->where('level', 2)
            ->orderBy('urutan', 'asc')
            ->get();

        // Hitung total bobot sub-kriteria
        $totalBobot = $subKriteria->sum('bobot');

        return view('kriteria.detail', compact('kriteria', 'subKriteria', 'totalBobot'));
    }

    /**
     * Get kriteria data for editing (JSON response for AJAX).
     */
    public function edit($id)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $kriteria->id,
                'nama_kriteria' => $kriteria->nama_kriteria,
                'deskripsi' => $kriteria->deskripsi,
                'tipe_kriteria' => $kriteria->tipe_kriteria,
                'bobot' => $kriteria->bobot,
            ]
        ]);
    }

    /**
     * Update the specified kriteria utama.
     * Validasi total bobot (exclude kriteria yang sedang diedit)
     */
    public function update(Request $request, $id)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($id);

        $request->validate([
            'nama_kriteria' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tipe_kriteria' => 'required|in:benefit,cost',
            'bobot' => 'required|numeric|min:0|max:100',
        ], [
            'nama_kriteria.required' => 'Nama kriteria wajib diisi',
            'nama_kriteria.max' => 'Nama kriteria maksimal 100 karakter',
            'tipe_kriteria.required' => 'Tipe kriteria wajib dipilih',
            'tipe_kriteria.in' => 'Tipe kriteria harus benefit atau cost',
            'bobot.required' => 'Bobot kriteria wajib diisi',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0',
            'bobot.max' => 'Bobot maksimal 100',
        ]);

        // Validasi total bobot (exclude kriteria yang sedang diedit)
        $currentTotalBobot = SistemKriteria::where('level', 1)
            ->where('id', '!=', $id)
            ->sum('bobot');
        $newTotalBobot = $currentTotalBobot + $request->bobot;

        if ($newTotalBobot > 100) {
            $sisaBobot = 100 - $currentTotalBobot;
            return redirect()->route('kriteria.index')
                ->with('error', 'Gagal mengupdate kriteria. Total bobot akan melebihi 100%. Sisa bobot yang tersedia: ' . number_format($sisaBobot, 2) . '%');
        }

        // Update kriteria
        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'deskripsi' => $request->deskripsi,
            'tipe_kriteria' => $request->tipe_kriteria,
            'bobot' => $request->bobot,
        ]);

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil diupdate');
    }

    /**
     * Remove the specified kriteria utama (soft delete).
     * Cek dulu apakah kriteria memiliki sub-kriteria atau penilaian
     */
    public function destroy($id)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($id);

        // Cek apakah kriteria memiliki sub-kriteria
        $countSubKriteria = SistemKriteria::where('id_parent', $kriteria->id)->count();
        if ($countSubKriteria > 0) {
            return redirect()->route('kriteria.index')
                ->with('error', 'Tidak dapat menghapus kriteria karena masih memiliki ' . $countSubKriteria . ' sub-kriteria. Hapus sub-kriteria terlebih dahulu.');
        }

        // Cek apakah kriteria sudah digunakan di penilaian
        $countPenilaian = $kriteria->penilaian()->count();
        if ($countPenilaian > 0) {
            return redirect()->route('kriteria.index')
                ->with('error', 'Tidak dapat menghapus kriteria karena sudah digunakan dalam ' . $countPenilaian . ' penilaian.');
        }

        // Soft delete
        $kriteria->delete();

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus');
    }

    /**
     * Get total bobot saat ini (untuk real-time validation via AJAX).
     */
    public function getTotalBobot(Request $request)
    {
        // Jika ada exclude_id, kurangi bobot kriteria tersebut dari total
        $excludeId = $request->query('exclude_id');

        $query = SistemKriteria::where('level', 1);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $totalBobot = $query->sum('bobot');
        $sisaBobot = 100 - $totalBobot;

        return response()->json([
            'success' => true,
            'total_bobot' => $totalBobot,
            'sisa_bobot' => $sisaBobot,
            'max_bobot' => $sisaBobot,
        ]);
    }

    /**
     * Toggle status aktif/nonaktif kriteria.
     */
    public function toggleStatus($id)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($id);

        $kriteria->update([
            'is_active' => !$kriteria->is_active
        ]);

        $status = $kriteria->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil ' . $status);
    }
}
