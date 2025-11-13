<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilaian;
use App\Models\User;
use App\Models\SistemKriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenilaianController extends Controller
{
    /**
     * Display a listing of penilaian with filters.
     * Menampilkan daftar penilaian yang sudah diinput dengan filter
     */
    public function index(Request $request)
    {
        // Query base dengan eager loading
        $query = Penilaian::with(['karyawan', 'kriteria', 'subKriteria', 'dinilaiOlehSupervisor'])
            ->terbaru();

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            $query->byKaryawan($request->karyawan_id);
        }

        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->byBulan($request->bulan);
        }

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->byTahun($request->tahun);
        }

        // Role-based filtering
        if (auth()->user()->role === 'supervisor') {
            // Supervisor hanya bisa lihat penilaian yang dia input
            $query->bySupervisor(auth()->id());
        }

        // Get paginated results
        $penilaian = $query->paginate(20);

        // Group penilaian by karyawan + periode untuk tampilan yang lebih baik
        $penilaianGrouped = $penilaian->groupBy(function ($item) {
            return $item->id_karyawan . '_' . $item->bulan . '_' . $item->tahun;
        });

        // Get data for filters
        $karyawanList = User::where('role', 'karyawan')
            ->approved()
            ->orderBy('nama', 'asc')
            ->get();

        // Get available years from existing penilaian
        $tahunList = Penilaian::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('penilaian.index', compact('penilaian', 'penilaianGrouped', 'karyawanList', 'tahunList'));
    }

    /**
     * Show the form for creating a new penilaian.
     * Form dynamic yang load kriteria & sub-kriteria berdasarkan karyawan
     */
    public function create(Request $request)
    {
        // Get karyawan list yang bisa dinilai
        $karyawanList = User::where('role', 'karyawan')
            ->approved()
            ->orderBy('nama', 'asc')
            ->get();

        // Default periode = bulan dan tahun sekarang
        $bulan = $request->query('bulan', date('n'));
        $tahun = $request->query('tahun', date('Y'));

        // Jika karyawan sudah dipilih, load kriteria & sub-kriteria
        $karyawanId = $request->query('karyawan_id');
        $karyawan = null;
        $kriteria = collect();
        $existingPenilaian = collect();

        if ($karyawanId) {
            $karyawan = User::findOrFail($karyawanId);

            // Get all active kriteria dengan sub-kriteria & dropdown options
            $kriteria = SistemKriteria::where('level', 1)
                ->where('is_active', true)
                ->with(['subKriteria' => function ($query) {
                    $query->where('is_active', true)
                        ->with(['dropdownOptions' => function ($q) {
                            $q->where('is_active', true)->orderBy('urutan', 'asc');
                        }])
                        ->orderBy('urutan', 'asc');
                }])
                ->orderBy('urutan', 'asc')
                ->get();

            // Check existing penilaian untuk periode ini
            $existingPenilaian = Penilaian::byKaryawan($karyawanId)
                ->byPeriode($bulan, $tahun)
                ->get()
                ->keyBy('id_sub_kriteria');
        }

        return view('penilaian.create', compact(
            'karyawanList',
            'karyawan',
            'kriteria',
            'bulan',
            'tahun',
            'existingPenilaian'
        ));
    }

    /**
     * Store a newly created penilaian in storage.
     * Menyimpan multiple penilaian sekaligus (batch insert untuk satu karyawan satu periode)
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'karyawan_id' => 'required|exists:users,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'penilaian' => 'required|array|min:1',
            'penilaian.*.id_kriteria' => 'required|exists:sistem_kriteria,id',
            'penilaian.*.id_sub_kriteria' => 'required|exists:sistem_kriteria,id',
            'penilaian.*.nilai' => 'required|numeric|min:0',
        ], [
            'karyawan_id.required' => 'Karyawan wajib dipilih',
            'karyawan_id.exists' => 'Karyawan tidak ditemukan',
            'bulan.required' => 'Bulan penilaian wajib dipilih',
            'bulan.integer' => 'Bulan harus berupa angka',
            'bulan.min' => 'Bulan minimal 1 (Januari)',
            'bulan.max' => 'Bulan maksimal 12 (Desember)',
            'tahun.required' => 'Tahun penilaian wajib diisi',
            'tahun.integer' => 'Tahun harus berupa angka',
            'penilaian.required' => 'Data penilaian wajib diisi',
            'penilaian.array' => 'Format data penilaian tidak valid',
            'penilaian.min' => 'Minimal harus ada 1 penilaian',
            'penilaian.*.nilai.required' => 'Nilai wajib diisi untuk semua sub-kriteria',
            'penilaian.*.nilai.numeric' => 'Nilai harus berupa angka',
        ]);

        try {
            DB::beginTransaction();

            $karyawanId = $request->karyawan_id;
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $tanggalPenilaian = Carbon::now();

            // Generate periode label
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

            // Delete existing penilaian untuk periode ini (jika ada, untuk prevent duplicate)
            Penilaian::byKaryawan($karyawanId)
                ->byPeriode($bulan, $tahun)
                ->forceDelete(); // Force delete karena mau re-insert

            // Insert batch penilaian
            $insertedCount = 0;
            foreach ($request->penilaian as $data) {
                Penilaian::create([
                    'id_karyawan' => $karyawanId,
                    'id_kriteria' => $data['id_kriteria'],
                    'id_sub_kriteria' => $data['id_sub_kriteria'],
                    'nilai' => $data['nilai'],
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'periode_label' => $periodeLabel,
                    'catatan' => $data['catatan'] ?? null,
                    'tanggal_penilaian' => $tanggalPenilaian,
                    'dinilai_oleh_supervisor_id' => auth()->user()->role === 'supervisor' ? auth()->id() : null,
                    'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
                    'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
                ]);
                $insertedCount++;
            }

            DB::commit();

            return redirect()->route('penilaian.index')
                ->with('success', "Berhasil menyimpan {$insertedCount} penilaian untuk periode {$periodeLabel}");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified penilaian.
     * Menampilkan detail lengkap penilaian per karyawan per periode
     */
    public function show($karyawanId, $bulan, $tahun)
    {
        $karyawan = User::findOrFail($karyawanId);

        // Get all penilaian untuk karyawan & periode ini
        $penilaianList = Penilaian::byKaryawan($karyawanId)
            ->byPeriode($bulan, $tahun)
            ->with(['kriteria', 'subKriteria', 'dinilaiOlehSupervisor'])
            ->get();

        if ($penilaianList->isEmpty()) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Penilaian tidak ditemukan untuk periode ini');
        }

        // Group by kriteria untuk tampilan terstruktur
        $penilaianGrouped = $penilaianList->groupBy('id_kriteria');

        // Get kriteria info
        $kriteriaList = SistemKriteria::whereIn('id', $penilaianGrouped->keys())
            ->get()
            ->keyBy('id');

        // Get periode info
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        return view('penilaian.show', compact(
            'karyawan',
            'penilaianList',
            'penilaianGrouped',
            'kriteriaList',
            'bulan',
            'tahun',
            'periodeLabel'
        ));
    }

    /**
     * Show the form for editing the specified penilaian.
     * Form edit dengan data pre-filled
     */
    public function edit($karyawanId, $bulan, $tahun)
    {
        $karyawan = User::findOrFail($karyawanId);

        // Get existing penilaian
        $existingPenilaian = Penilaian::byKaryawan($karyawanId)
            ->byPeriode($bulan, $tahun)
            ->with(['kriteria', 'subKriteria'])
            ->get()
            ->keyBy('id_sub_kriteria');

        if ($existingPenilaian->isEmpty()) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Penilaian tidak ditemukan untuk periode ini');
        }

        // Get all active kriteria dengan sub-kriteria & dropdown options
        $kriteria = SistemKriteria::where('level', 1)
            ->where('is_active', true)
            ->with(['subKriteria' => function ($query) {
                $query->where('is_active', true)
                    ->with(['dropdownOptions' => function ($q) {
                        $q->where('is_active', true)->orderBy('urutan', 'asc');
                    }])
                    ->orderBy('urutan', 'asc');
            }])
            ->orderBy('urutan', 'asc')
            ->get();

        return view('penilaian.edit', compact(
            'karyawan',
            'kriteria',
            'bulan',
            'tahun',
            'existingPenilaian'
        ));
    }

    /**
     * Update the specified penilaian in storage.
     * Update batch penilaian untuk satu periode
     */
    public function update(Request $request, $karyawanId, $bulan, $tahun)
    {
        // Validation
        $request->validate([
            'penilaian' => 'required|array|min:1',
            'penilaian.*.id_kriteria' => 'required|exists:sistem_kriteria,id',
            'penilaian.*.id_sub_kriteria' => 'required|exists:sistem_kriteria,id',
            'penilaian.*.nilai' => 'required|numeric|min:0',
        ], [
            'penilaian.required' => 'Data penilaian wajib diisi',
            'penilaian.*.nilai.required' => 'Nilai wajib diisi untuk semua sub-kriteria',
        ]);

        try {
            DB::beginTransaction();

            $tanggalPenilaian = Carbon::now();

            // Generate periode label
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

            // Delete existing penilaian
            Penilaian::byKaryawan($karyawanId)
                ->byPeriode($bulan, $tahun)
                ->forceDelete();

            // Re-insert dengan data baru
            $updatedCount = 0;
            foreach ($request->penilaian as $data) {
                Penilaian::create([
                    'id_karyawan' => $karyawanId,
                    'id_kriteria' => $data['id_kriteria'],
                    'id_sub_kriteria' => $data['id_sub_kriteria'],
                    'nilai' => $data['nilai'],
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'periode_label' => $periodeLabel,
                    'catatan' => $data['catatan'] ?? null,
                    'tanggal_penilaian' => $tanggalPenilaian,
                    'dinilai_oleh_supervisor_id' => auth()->user()->role === 'supervisor' ? auth()->id() : null,
                    'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
                    'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
                ]);
                $updatedCount++;
            }

            DB::commit();

            return redirect()->route('penilaian.show', [$karyawanId, $bulan, $tahun])
                ->with('success', "Berhasil mengupdate {$updatedCount} penilaian untuk periode {$periodeLabel}");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified penilaian from storage.
     * Menghapus semua penilaian untuk satu karyawan di satu periode
     */
    public function destroy($karyawanId, $bulan, $tahun)
    {
        try {
            // Get periode label for message
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

            // Soft delete
            $deleted = Penilaian::byKaryawan($karyawanId)
                ->byPeriode($bulan, $tahun)
                ->delete();

            if ($deleted > 0) {
                return redirect()->route('penilaian.index')
                    ->with('success', "Berhasil menghapus penilaian periode {$periodeLabel}");
            } else {
                return redirect()->route('penilaian.index')
                    ->with('error', 'Penilaian tidak ditemukan');
            }
        } catch (\Exception $e) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Gagal menghapus penilaian: ' . $e->getMessage());
        }
    }
}
