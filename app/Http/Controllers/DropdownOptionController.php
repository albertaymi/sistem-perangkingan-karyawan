<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SistemKriteria;

class DropdownOptionController extends Controller
{
    /**
     * Store a newly created dropdown option.
     * Validasi urutan dan nilai tetap
     */
    public function store(Request $request, $kriteriaId, $subKriteriaId)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($kriteriaId);
        $subKriteria = SistemKriteria::where('level', 2)
            ->where('id_parent', $kriteriaId)
            ->findOrFail($subKriteriaId);

        // Validasi tipe_input harus dropdown
        if ($subKriteria->tipe_input !== 'dropdown') {
            return redirect()->route('kriteria.detail', $kriteriaId)
                ->with('error', 'Dropdown options hanya bisa ditambahkan untuk sub-kriteria dengan tipe input dropdown');
        }

        $request->validate([
            'nama_kriteria' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'nilai_tetap' => 'required|numeric|min:0',
        ], [
            'nama_kriteria.required' => 'Nama option wajib diisi',
            'nama_kriteria.max' => 'Nama option maksimal 100 karakter',
            'nilai_tetap.required' => 'Nilai option wajib diisi',
            'nilai_tetap.numeric' => 'Nilai harus berupa angka',
            'nilai_tetap.min' => 'Nilai minimal 0',
        ]);

        // Get urutan terakhir
        $urutanTerakhir = SistemKriteria::where('id_parent', $subKriteriaId)
            ->where('level', 3)
            ->max('urutan') ?? 0;

        // Create dropdown option
        SistemKriteria::create([
            'id_parent' => $subKriteriaId,
            'nama_kriteria' => $request->nama_kriteria,
            'deskripsi' => $request->deskripsi,
            'tipe_kriteria' => null,
            'bobot' => null, // Level 3 tidak punya bobot
            'tipe_input' => null, // Level 3 tidak punya tipe_input
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => $request->nilai_tetap,
            'level' => 3,
            'urutan' => $urutanTerakhir + 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
            'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
            'is_active' => true,
        ]);

        return redirect()->route('kriteria.detail', $kriteriaId)
            ->with('success', 'Dropdown option berhasil ditambahkan');
    }

    /**
     * Get all dropdown options for a sub-kriteria (JSON response for modal).
     */
    public function index($kriteriaId, $subKriteriaId)
    {
        $subKriteria = SistemKriteria::where('level', 2)
            ->where('id_parent', $kriteriaId)
            ->findOrFail($subKriteriaId);

        $options = SistemKriteria::where('id_parent', $subKriteriaId)
            ->where('level', 3)
            ->orderBy('urutan', 'asc')
            ->get(['id', 'nama_kriteria', 'deskripsi', 'nilai_tetap', 'is_active', 'urutan']);

        return response()->json([
            'success' => true,
            'options' => $options,
        ]);
    }

    /**
     * Get dropdown option data for editing (JSON response for AJAX).
     */
    public function edit($kriteriaId, $subKriteriaId, $id)
    {
        $option = SistemKriteria::where('level', 3)
            ->where('id_parent', $subKriteriaId)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $option->id,
                'nama_kriteria' => $option->nama_kriteria,
                'deskripsi' => $option->deskripsi,
                'nilai_tetap' => $option->nilai_tetap,
            ]
        ]);
    }

    /**
     * Update the specified dropdown option.
     */
    public function update(Request $request, $kriteriaId, $subKriteriaId, $id)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($kriteriaId);
        $subKriteria = SistemKriteria::where('level', 2)
            ->where('id_parent', $kriteriaId)
            ->findOrFail($subKriteriaId);
        $option = SistemKriteria::where('level', 3)
            ->where('id_parent', $subKriteriaId)
            ->findOrFail($id);

        $request->validate([
            'nama_kriteria' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'nilai_tetap' => 'required|numeric|min:0',
        ], [
            'nama_kriteria.required' => 'Nama option wajib diisi',
            'nama_kriteria.max' => 'Nama option maksimal 100 karakter',
            'nilai_tetap.required' => 'Nilai option wajib diisi',
            'nilai_tetap.numeric' => 'Nilai harus berupa angka',
            'nilai_tetap.min' => 'Nilai minimal 0',
        ]);

        // Update dropdown option
        $option->update([
            'nama_kriteria' => $request->nama_kriteria,
            'deskripsi' => $request->deskripsi,
            'nilai_tetap' => $request->nilai_tetap,
        ]);

        return redirect()->route('kriteria.detail', $kriteriaId)
            ->with('success', 'Dropdown option berhasil diupdate');
    }

    /**
     * Remove the specified dropdown option (soft delete).
     * Cek dulu apakah option sudah digunakan di penilaian
     */
    public function destroy($kriteriaId, $subKriteriaId, $id)
    {
        $option = SistemKriteria::where('level', 3)
            ->where('id_parent', $subKriteriaId)
            ->findOrFail($id);

        // Cek apakah option sudah digunakan di penilaian
        $countPenilaian = $option->penilaianAsOption()->count();
        if ($countPenilaian > 0) {
            return redirect()->route('kriteria.detail', $kriteriaId)
                ->with('error', 'Tidak dapat menghapus dropdown option karena sudah digunakan dalam ' . $countPenilaian . ' penilaian.');
        }

        // Soft delete
        $option->delete();

        return redirect()->route('kriteria.detail', $kriteriaId)
            ->with('success', 'Dropdown option berhasil dihapus');
    }

    /**
     * Toggle status aktif/nonaktif dropdown option.
     */
    public function toggleStatus($kriteriaId, $subKriteriaId, $id)
    {
        $option = SistemKriteria::where('level', 3)
            ->where('id_parent', $subKriteriaId)
            ->findOrFail($id);

        $option->update([
            'is_active' => !$option->is_active
        ]);

        $status = $option->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('kriteria.detail', $kriteriaId)
            ->with('success', 'Dropdown option berhasil ' . $status);
    }

    /**
     * Update urutan dropdown options (drag and drop).
     */
    public function updateUrutan(Request $request, $kriteriaId, $subKriteriaId)
    {
        $request->validate([
            'urutan' => 'required|array',
            'urutan.*' => 'required|integer|exists:sistem_kriteria,id',
        ]);

        foreach ($request->urutan as $index => $optionId) {
            SistemKriteria::where('id', $optionId)
                ->where('level', 3)
                ->where('id_parent', $subKriteriaId)
                ->update(['urutan' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan dropdown options berhasil diupdate',
        ]);
    }
}
