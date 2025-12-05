<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SistemKriteria;

class SubKriteriaController extends Controller
{
    /**
     * Store a newly created sub-kriteria.
     * Validasi total bobot sub-kriteria dalam 1 kriteria tidak boleh lebih dari 100%
     */
    public function store(Request $request, $kriteriaId)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($kriteriaId);

        // Validasi: Kriteria tidak boleh sudah memiliki tipe input
        if ($kriteria->tipe_input) {
            return redirect()->route('kriteria.detail', $kriteriaId)
                ->with('error', 'Tidak dapat menambahkan sub-kriteria karena kriteria ini sudah memiliki tipe input (' . $kriteria->tipe_input . '). Silakan ubah tipe input kriteria menjadi kosong terlebih dahulu.');
        }

        $request->validate([
            'nama_kriteria' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|numeric|min:0|max:100',
            'tipe_input' => 'required|in:angka,rating,dropdown',
            'nilai_min' => 'nullable|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0|gt:nilai_min',
        ], [
            'nama_kriteria.required' => 'Nama sub-kriteria wajib diisi',
            'nama_kriteria.max' => 'Nama sub-kriteria maksimal 100 karakter',
            'bobot.required' => 'Bobot sub-kriteria wajib diisi',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0',
            'bobot.max' => 'Bobot maksimal 100',
            'tipe_input.required' => 'Tipe input wajib dipilih',
            'tipe_input.in' => 'Tipe input harus angka, rating, atau dropdown',
            'nilai_min.numeric' => 'Nilai min harus berupa angka',
            'nilai_min.min' => 'Nilai minimum tidak boleh negatif',
            'nilai_max.numeric' => 'Nilai max harus berupa angka',
            'nilai_max.min' => 'Nilai maximum tidak boleh negatif',
            'nilai_max.gt' => 'Nilai max harus lebih besar dari nilai min',
        ]);

        // Custom validation for tipe_input specific requirements
        if ($request->tipe_input === 'dropdown') {
            // Validate dropdown options
            if (!$request->has('dropdown_options') || !is_array($request->dropdown_options) || count($request->dropdown_options) < 2) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tipe input dropdown memerlukan minimal 2 pilihan dropdown options.');
            }

            // Validate each dropdown option
            foreach ($request->dropdown_options as $index => $option) {
                if (empty($option['nama']) || trim($option['nama']) === '') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Semua dropdown options harus memiliki nama yang valid.');
                }
                if (!isset($option['nilai_tetap']) || !is_numeric($option['nilai_tetap'])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Semua dropdown options harus memiliki nilai tetap yang valid.');
                }
            }
        } elseif ($request->tipe_input === 'angka' || $request->tipe_input === 'rating') {
            // Validate nilai_min and nilai_max for angka and rating
            if ($request->nilai_min === null || $request->nilai_min === '') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nilai minimum wajib diisi untuk tipe input angka atau rating.');
            }
            if ($request->nilai_max === null || $request->nilai_max === '') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nilai maximum wajib diisi untuk tipe input angka atau rating.');
            }
            if ($request->nilai_max <= $request->nilai_min) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nilai maximum harus lebih besar dari nilai minimum.');
            }
        }

        // Validasi total bobot sub-kriteria tidak boleh lebih dari 100%
        $currentTotalBobot = SistemKriteria::where('id_parent', $kriteriaId)
            ->where('level', 2)
            ->sum('bobot');
        $newTotalBobot = $currentTotalBobot + $request->bobot;

        if ($newTotalBobot > 100) {
            $sisaBobot = 100 - $currentTotalBobot;
            return redirect()->route('kriteria.detail', $kriteriaId)
                ->with('error', 'Gagal menambahkan sub-kriteria. Total bobot akan melebihi 100%. Sisa bobot yang tersedia: ' . number_format($sisaBobot, 2) . '%');
        }

        // Validasi nilai_min dan nilai_max untuk tipe angka dan rating
        if (in_array($request->tipe_input, ['angka', 'rating'])) {
            if (is_null($request->nilai_min) || is_null($request->nilai_max)) {
                return redirect()->route('kriteria.detail', $kriteriaId)
                    ->with('error', 'Nilai min dan nilai max wajib diisi untuk tipe input angka atau rating');
            }
        }

        // Get urutan terakhir
        $urutanTerakhir = SistemKriteria::where('id_parent', $kriteriaId)
            ->where('level', 2)
            ->max('urutan') ?? 0;

        // Create sub-kriteria
        $subKriteria = SistemKriteria::create([
            'id_parent' => $kriteriaId,
            'nama_kriteria' => $request->nama_kriteria,
            'deskripsi' => $request->deskripsi,
            'tipe_kriteria' => $request->tipe_kriteria, // Sub-kriteria memiliki tipe_kriteria sendiri
            'bobot' => $request->bobot,
            'tipe_input' => $request->tipe_input,
            'nilai_min' => $request->tipe_input !== 'dropdown' ? $request->nilai_min : null,
            'nilai_max' => $request->tipe_input !== 'dropdown' ? $request->nilai_max : null,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => $urutanTerakhir + 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
            'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
            'is_active' => true,
        ]);

        // Create dropdown options if tipe_input is dropdown
        if ($request->tipe_input === 'dropdown' && $request->has('dropdown_options')) {
            foreach ($request->dropdown_options as $index => $option) {
                SistemKriteria::create([
                    'id_parent' => $subKriteria->id,
                    'nama_kriteria' => $option['nama'],
                    'deskripsi' => null,
                    'tipe_kriteria' => null,
                    'bobot' => null,
                    'tipe_input' => null,
                    'nilai_min' => null,
                    'nilai_max' => null,
                    'nilai_tetap' => $option['nilai_tetap'],
                    'level' => 3, // Level 3 for dropdown options
                    'urutan' => $index + 1,
                    'assigned_to_supervisor_id' => null,
                    'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
                    'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('kriteria.detail', $kriteriaId)
            ->with('success', 'Sub-kriteria berhasil ditambahkan');
    }

    /**
     * Get sub-kriteria data for editing (JSON response for AJAX).
     */
    public function edit($kriteriaId, $id)
    {
        $subKriteria = SistemKriteria::where('level', 2)
            ->where('id_parent', $kriteriaId)
            ->with('dropdownOptions') // Eager load dropdown options
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $subKriteria->id,
                'nama_kriteria' => $subKriteria->nama_kriteria,
                'deskripsi' => $subKriteria->deskripsi,
                'tipe_kriteria' => $subKriteria->tipe_kriteria,
                'bobot' => $subKriteria->bobot,
                'tipe_input' => $subKriteria->tipe_input,
                'nilai_min' => $subKriteria->nilai_min,
                'nilai_max' => $subKriteria->nilai_max,
                'dropdown_options' => $subKriteria->dropdownOptions->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'nama_kriteria' => $option->nama_kriteria,
                        'nilai_tetap' => $option->nilai_tetap,
                        'urutan' => $option->urutan,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Update the specified sub-kriteria.
     * Validasi total bobot (exclude sub-kriteria yang sedang diedit)
     */
    public function update(Request $request, $kriteriaId, $id)
    {
        $kriteria = SistemKriteria::where('level', 1)->findOrFail($kriteriaId);
        $subKriteria = SistemKriteria::where('level', 2)
            ->where('id_parent', $kriteriaId)
            ->findOrFail($id);

        $request->validate([
            'nama_kriteria' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|numeric|min:0|max:100',
            'tipe_input' => 'required|in:angka,rating,dropdown',
            'nilai_min' => 'nullable|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0|gt:nilai_min',
        ], [
            'nama_kriteria.required' => 'Nama sub-kriteria wajib diisi',
            'nama_kriteria.max' => 'Nama sub-kriteria maksimal 100 karakter',
            'bobot.required' => 'Bobot sub-kriteria wajib diisi',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0',
            'bobot.max' => 'Bobot maksimal 100',
            'tipe_input.required' => 'Tipe input wajib dipilih',
            'tipe_input.in' => 'Tipe input harus angka, rating, atau dropdown',
            'nilai_min.numeric' => 'Nilai min harus berupa angka',
            'nilai_min.min' => 'Nilai minimum tidak boleh negatif',
            'nilai_max.numeric' => 'Nilai max harus berupa angka',
            'nilai_max.min' => 'Nilai maximum tidak boleh negatif',
            'nilai_max.gt' => 'Nilai max harus lebih besar dari nilai min',
        ]);

        // Custom validation for tipe_input specific requirements
        if ($request->tipe_input === 'dropdown') {
            // Validate dropdown options
            if (!$request->has('dropdown_options') || !is_array($request->dropdown_options) || count($request->dropdown_options) < 2) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tipe input dropdown memerlukan minimal 2 pilihan dropdown options.');
            }

            // Validate each dropdown option
            foreach ($request->dropdown_options as $index => $option) {
                if (empty($option['nama']) || trim($option['nama']) === '') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Semua dropdown options harus memiliki nama yang valid.');
                }
                if (!isset($option['nilai_tetap']) || !is_numeric($option['nilai_tetap'])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Semua dropdown options harus memiliki nilai tetap yang valid.');
                }
            }
        } elseif ($request->tipe_input === 'angka' || $request->tipe_input === 'rating') {
            // Validate nilai_min and nilai_max for angka and rating
            if ($request->nilai_min === null || $request->nilai_min === '') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nilai minimum wajib diisi untuk tipe input angka atau rating.');
            }
            if ($request->nilai_max === null || $request->nilai_max === '') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nilai maximum wajib diisi untuk tipe input angka atau rating.');
            }
            if ($request->nilai_max <= $request->nilai_min) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nilai maximum harus lebih besar dari nilai minimum.');
            }
        }

        // Validasi total bobot (exclude sub-kriteria yang sedang diedit)
        $currentTotalBobot = SistemKriteria::where('id_parent', $kriteriaId)
            ->where('level', 2)
            ->where('id', '!=', $id)
            ->sum('bobot');
        $newTotalBobot = $currentTotalBobot + $request->bobot;

        if ($newTotalBobot > 100) {
            $sisaBobot = 100 - $currentTotalBobot;
            return redirect()->route('kriteria.detail', $kriteriaId)
                ->with('error', 'Gagal mengupdate sub-kriteria. Total bobot akan melebihi 100%. Sisa bobot yang tersedia: ' . number_format($sisaBobot, 2) . '%');
        }

        // Validasi nilai_min dan nilai_max untuk tipe angka dan rating
        if (in_array($request->tipe_input, ['angka', 'rating'])) {
            if (is_null($request->nilai_min) || is_null($request->nilai_max)) {
                return redirect()->route('kriteria.detail', $kriteriaId)
                    ->with('error', 'Nilai min dan nilai max wajib diisi untuk tipe input angka atau rating');
            }
        }

        // Update sub-kriteria
        $subKriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'deskripsi' => $request->deskripsi,
            'tipe_kriteria' => $request->tipe_kriteria,
            'bobot' => $request->bobot,
            'tipe_input' => $request->tipe_input,
            'nilai_min' => $request->tipe_input !== 'dropdown' ? $request->nilai_min : null,
            'nilai_max' => $request->tipe_input !== 'dropdown' ? $request->nilai_max : null,
        ]);

        // Sync dropdown options if tipe_input is dropdown
        if ($request->tipe_input === 'dropdown') {
            // Delete all existing dropdown options for this sub-kriteria
            SistemKriteria::where('id_parent', $subKriteria->id)
                ->where('level', 3)
                ->delete();

            // Create new dropdown options
            if ($request->has('dropdown_options')) {
                foreach ($request->dropdown_options as $index => $option) {
                    SistemKriteria::create([
                        'id_parent' => $subKriteria->id,
                        'nama_kriteria' => $option['nama'],
                        'deskripsi' => null,
                        'tipe_kriteria' => null,
                        'bobot' => null,
                        'tipe_input' => null,
                        'nilai_min' => null,
                        'nilai_max' => null,
                        'nilai_tetap' => $option['nilai_tetap'],
                        'level' => 3, // Level 3 for dropdown options
                        'urutan' => $index + 1,
                        'assigned_to_supervisor_id' => null,
                        'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
                        'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
                        'is_active' => true,
                    ]);
                }
            }
        } else {
            // If tipe_input changed from dropdown to something else, delete dropdown options
            SistemKriteria::where('id_parent', $subKriteria->id)
                ->where('level', 3)
                ->delete();
        }

        return redirect()->route('kriteria.detail', $kriteriaId)
            ->with('success', 'Sub-kriteria berhasil diupdate');
    }

    /**
     * Remove the specified sub-kriteria (soft delete).
     * Cek dulu apakah sub-kriteria memiliki penilaian atau dropdown options
     */
    public function destroy($kriteriaId, $id)
    {
        $subKriteria = SistemKriteria::where('level', 2)
            ->where('id_parent', $kriteriaId)
            ->findOrFail($id);

        // Cek apakah sub-kriteria memiliki dropdown options (Level 3)
        $countOptions = SistemKriteria::where('id_parent', $subKriteria->id)
            ->where('level', 3)
            ->count();
        if ($countOptions > 0) {
            return redirect()->route('kriteria.detail', $kriteriaId)
                ->with('error', 'Tidak dapat menghapus sub-kriteria karena masih memiliki ' . $countOptions . ' dropdown options. Hapus dropdown options terlebih dahulu.');
        }

        // Cek apakah sub-kriteria sudah digunakan di penilaian (yang tidak soft deleted)
        $penilaianQuery = $subKriteria->penilaianAsSub();
        $countPenilaian = $penilaianQuery->count();

        // Debug: Log penilaian yang ditemukan
        \Log::info('Checking penilaian for sub-kriteria', [
            'sub_kriteria_id' => $id,
            'sub_kriteria_nama' => $subKriteria->nama_kriteria,
            'count_penilaian' => $countPenilaian,
            'penilaian_details' => $penilaianQuery->get()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'id_karyawan' => $p->id_karyawan,
                    'id_kriteria' => $p->id_kriteria,
                    'id_sub_kriteria' => $p->id_sub_kriteria,
                    'bulan' => $p->bulan,
                    'tahun' => $p->tahun,
                    'deleted_at' => $p->deleted_at,
                ];
            })->toArray(),
        ]);

        if ($countPenilaian > 0) {
            return redirect()->route('kriteria.detail', $kriteriaId)
                ->with('error', 'Tidak dapat menghapus sub-kriteria karena sudah digunakan dalam ' . $countPenilaian . ' penilaian.');
        }

        // Soft delete
        $subKriteria->delete();

        return redirect()->route('kriteria.detail', $kriteriaId)
            ->with('success', 'Sub-kriteria berhasil dihapus');
    }

    /**
     * Get total bobot sub-kriteria saat ini (untuk real-time validation via AJAX).
     */
    public function getTotalBobot(Request $request, $kriteriaId)
    {
        // Jika ada exclude_id, kurangi bobot sub-kriteria tersebut dari total
        $excludeId = $request->query('exclude_id');

        $query = SistemKriteria::where('id_parent', $kriteriaId)->where('level', 2);

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
     * Toggle status aktif/nonaktif sub-kriteria.
     */
    public function toggleStatus($kriteriaId, $id)
    {
        $subKriteria = SistemKriteria::where('level', 2)
            ->where('id_parent', $kriteriaId)
            ->findOrFail($id);

        $subKriteria->update([
            'is_active' => !$subKriteria->is_active
        ]);

        $status = $subKriteria->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('kriteria.detail', $kriteriaId)
            ->with('success', 'Sub-kriteria berhasil ' . $status);
    }
}
