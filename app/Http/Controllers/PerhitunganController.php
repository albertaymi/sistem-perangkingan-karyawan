<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HasilTopsis;
use App\Models\Penilaian;
use App\Models\User;
use App\Models\SistemKriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\RankingExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PerhitunganTopsisService;

class PerhitunganController extends Controller
{
    /**
     * Display a listing of periode perhitungan.
     * Menampilkan daftar periode yang bisa/sudah dihitung ranking-nya
     *
     * Authorization: Hanya Super Admin & HRD
     */
    public function index(Request $request)
    {
        // Get selected periode (default to current month/year)
        $bulan = $request->query('bulan', date('n'));
        $tahun = $request->query('tahun', date('Y'));

        // Get selected divisi filter (default to empty = semua divisi)
        $divisiFilter = $request->query('divisi', '');

        // Get available years (5 years back + existing penilaian years)
        $tahunList = collect();
        $currentYear = (int) date('Y');
        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
            $tahunList->push($y);
        }

        $existingYears = Penilaian::select('tahun')->distinct()->pluck('tahun');
        if ($existingYears->isNotEmpty()) {
            $tahunList = $tahunList->merge($existingYears)
                ->unique()
                ->sort()
                ->values()
                ->reverse();
        }

        // Get list of divisi untuk dropdown filter
        $divisiList = User::where('role', 'karyawan')
            ->where('status_akun', 'aktif')
            ->orderBy('divisi')
            ->pluck('divisi')
            ->unique()
            ->values();

        // Build periode label
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Get all active karyawan (dengan filter divisi jika dipilih)
        $karyawanAktifQuery = User::where('role', 'karyawan')
            ->where('status_akun', 'aktif');

        if (!empty($divisiFilter)) {
            $karyawanAktifQuery->where('divisi', $divisiFilter);
        }

        $karyawanAktif = $karyawanAktifQuery->count();

        // Get IDs karyawan yang akan divalidasi (sesuai filter divisi)
        $karyawanIdsToValidate = User::where('role', 'karyawan')
            ->where('status_akun', 'aktif');

        if (!empty($divisiFilter)) {
            $karyawanIdsToValidate->where('divisi', $divisiFilter);
        }

        $karyawanIdsFiltered = $karyawanIdsToValidate->pluck('id');

        // Get karyawan with penilaian in this periode (sesuai filter divisi)
        $karyawanDenganPenilaian = Penilaian::byPeriode($bulan, $tahun)
            ->whereIn('id_karyawan', $karyawanIdsFiltered)
            ->distinct('id_karyawan')
            ->pluck('id_karyawan');

        $dataPenilaianLengkap = $karyawanDenganPenilaian->count();
        $dataTidakLengkap = $karyawanAktif - $dataPenilaianLengkap;

        // Get all active kriteria
        $kriteriaAktif = SistemKriteria::where('level', 1)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        // Calculate validation status per kriteria (dengan filter divisi)
        $validasiKriteria = [];
        foreach ($kriteriaAktif as $kriteria) {
            $validation = $this->validateKriteriaCompletion($kriteria->id, $bulan, $tahun, $divisiFilter);
            $validasiKriteria[] = [
                'kriteria' => $kriteria,
                'karyawan_lengkap' => $validation['complete_count'],
                'total_karyawan' => $validation['total_karyawan'],
                'is_complete' => $validation['is_complete'],
                'karyawan_tidak_lengkap' => $validation['incomplete_employees'],
            ];
        }

        // Check if all data is complete for TOPSIS generation
        $allDataComplete = collect($validasiKriteria)->every(function ($item) {
            return $item['is_complete'];
        });

        // Calculate bobot kriteria validation
        $totalBobot = $kriteriaAktif->sum('bobot');
        $bobotValid = abs($totalBobot - 100) < 0.01; // Allow small floating point variance

        // Check if ranking already exists (untuk semua karyawan sesuai filter divisi)
        // Jika filter divisi, cek apakah SEMUA karyawan di divisi tersebut sudah punya hasil ranking dengan divisi_filter yang sama
        // Jika semua divisi, cek apakah SEMUA karyawan aktif sudah punya hasil ranking dengan divisi_filter = NULL
        $hasilExists = false;
        if ($karyawanIdsFiltered->isNotEmpty()) {
            $hasilQuery = HasilTopsis::byPeriode($bulan, $tahun)
                ->whereIn('id_karyawan', $karyawanIdsFiltered);

            // Filter berdasarkan divisi_filter yang sama
            if (empty($divisiFilter)) {
                $hasilQuery->whereNull('divisi_filter');
            } else {
                $hasilQuery->where('divisi_filter', $divisiFilter);
            }

            $karyawanWithHasil = $hasilQuery->distinct('id_karyawan')
                ->pluck('id_karyawan');

            // Cek apakah semua karyawan sesuai filter sudah punya hasil
            $hasilExists = $karyawanWithHasil->count() === $karyawanIdsFiltered->count();
        }

        // Get history of TOPSIS generations
        $riwayatGenerate = HasilTopsis::select(
            'bulan',
            'tahun',
            'periode_label',
            'divisi_filter',
            'tanggal_generate',
            'generated_by_super_admin_id',
            'generated_by_hrd_id'
        )
            ->distinct()
            ->with(['generatedBySuperAdmin', 'generatedByHRD'])
            ->orderByRaw('tahun DESC, bulan DESC')
            ->orderBy('divisi_filter', 'asc')
            ->orderBy('tanggal_generate', 'desc')
            ->get()
            ->unique(function ($item) {
                return $item->bulan . '-' . $item->tahun . '-' . ($item->divisi_filter ?? 'semua');
            })
            ->take(10);

        return view('perhitungan.index', compact(
            'bulan',
            'tahun',
            'tahunList',
            'divisiFilter',
            'divisiList',
            'periodeLabel',
            'karyawanAktif',
            'dataPenilaianLengkap',
            'dataTidakLengkap',
            'kriteriaAktif',
            'validasiKriteria',
            'allDataComplete',
            'bobotValid',
            'hasilExists',
            'riwayatGenerate'
        ));
    }

    /**
     * Validate if all employees have complete assessment for a specific kriteria
     *
     * @param int $kriteriaId
     * @param int $bulan
     * @param int $tahun
     * @param string $divisiFilter Filter divisi (empty string = semua divisi)
     * @return array
     */
    private function validateKriteriaCompletion($kriteriaId, $bulan, $tahun, $divisiFilter = '')
    {
        // Get the kriteria
        $kriteria = SistemKriteria::find($kriteriaId);

        if (!$kriteria || $kriteria->level !== 1) {
            return [
                'is_complete' => false,
                'complete_count' => 0,
                'total_karyawan' => 0,
                'incomplete_employees' => [],
            ];
        }

        // Get all active karyawan (dengan filter divisi jika dipilih)
        $karyawanQuery = User::where('role', 'karyawan')
            ->where('status_akun', 'aktif');

        if (!empty($divisiFilter)) {
            $karyawanQuery->where('divisi', $divisiFilter);
        }

        $allKaryawan = $karyawanQuery->get();

        $totalKaryawan = $allKaryawan->count();
        $completeCount = 0;
        $incompleteEmployees = [];

        // Check if this is a SINGLE KRITERIA (kriteria tunggal with tipe_input)
        $isSingleKriteria = !empty($kriteria->tipe_input);
        $subKriteriaIds = $kriteria->subKriteria()
            ->where('is_active', true)
            ->pluck('id');

        // If kriteria has no sub-kriteria and no tipe_input, it's invalid/incomplete setup
        if ($subKriteriaIds->isEmpty() && !$isSingleKriteria) {
            return [
                'is_complete' => true, // Don't block TOPSIS for setup issues
                'complete_count' => 0,
                'total_karyawan' => 0,
                'incomplete_employees' => [],
            ];
        }

        foreach ($allKaryawan as $karyawan) {
            $isComplete = false;

            if ($isSingleKriteria) {
                // SINGLE KRITERIA: Check penilaian with id_kriteria and id_sub_kriteria = NULL
                $penilaianExists = Penilaian::where('id_karyawan', $karyawan->id)
                    ->where('id_kriteria', $kriteria->id)
                    ->whereNull('id_sub_kriteria')
                    ->byPeriode($bulan, $tahun)
                    ->exists();

                $isComplete = $penilaianExists;
            } else {
                // MULTI SUB-KRITERIA: Check if karyawan has penilaian for ALL sub-kriteria
                $penilaianCount = Penilaian::where('id_karyawan', $karyawan->id)
                    ->whereIn('id_sub_kriteria', $subKriteriaIds)
                    ->byPeriode($bulan, $tahun)
                    ->count();

                $isComplete = ($penilaianCount === $subKriteriaIds->count());
            }

            if ($isComplete) {
                $completeCount++;
            } else {
                $incompleteEmployees[] = [
                    'id' => $karyawan->id,
                    'nama' => $karyawan->nama,
                    'nik' => $karyawan->nik,
                    'penilaian_count' => $isSingleKriteria ? 0 : ($penilaianCount ?? 0),
                    'total_required' => $isSingleKriteria ? 1 : $subKriteriaIds->count(),
                    'is_single_kriteria' => $isSingleKriteria,
                ];
            }
        }

        return [
            'is_complete' => $completeCount === $totalKaryawan && $totalKaryawan > 0,
            'complete_count' => $completeCount,
            'total_karyawan' => $totalKaryawan,
            'incomplete_employees' => $incompleteEmployees,
            'is_single_kriteria' => $isSingleKriteria,
        ];
    }

    /**
     * Display hasil ranking index untuk semua role.
     * Menampilkan ranking dengan fitur filter periode, divisi, dan search
     *
     * Authorization: Semua role yang login
     */
    public function rankingIndex(Request $request)
    {
        // Get available periods dari hasil TOPSIS (unique bulan+tahun)
        $availablePeriods = HasilTopsis::select('bulan', 'tahun', 'periode_label')
            ->distinct()
            ->orderByRaw('tahun DESC, bulan DESC')
            ->get()
            ->unique(function ($item) {
                return $item->bulan . '-' . $item->tahun;
            })
            ->values();

        // Get filter parameters
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $divisiFilter = $request->query('divisi', '');
        $search = $request->query('search', '');

        // If no periode selected and data exists, use latest
        if ((!$bulan || !$tahun) && $availablePeriods->isNotEmpty()) {
            $latest = $availablePeriods->first();
            $bulan = $latest->bulan;
            $tahun = $latest->tahun;
        }

        // Set default to current month/year if still empty
        if (!$bulan || !$tahun) {
            $bulan = (int) date('n'); // Current month
            $tahun = (int) date('Y'); // Current year
        }

        // Get list of available divisi_filter untuk periode ini
        $divisiList = HasilTopsis::byPeriode($bulan, $tahun)
            ->select('divisi_filter')
            ->distinct()
            ->pluck('divisi_filter')
            ->filter(function ($value) {
                return $value !== null;
            })
            ->sort()
            ->values();

        // Build query untuk hasil ranking dengan filter
        $hasilQuery = HasilTopsis::byPeriode($bulan, $tahun)
            ->with('karyawan');

        // Filter by divisi_filter field
        // Jika divisiFilter kosong (Semua Divisi) = hanya tampilkan yang divisi_filter = NULL
        // Jika divisiFilter ada (divisi tertentu) = tampilkan yang divisi_filter = divisi tersebut
        if (!empty($divisiFilter)) {
            $hasilQuery->where('divisi_filter', $divisiFilter);
        } else {
            $hasilQuery->whereNull('divisi_filter');
        }

        // Filter by search (nama atau NIK)
        if (!empty($search)) {
            $hasilQuery->whereHas('karyawan', function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $hasilRanking = $hasilQuery->orderedByRanking()->get();

        // Calculate statistics dari hasil yang sudah difilter
        $totalKaryawan = $hasilRanking->count();

        if ($totalKaryawan > 0) {
            $skorTertinggi = $hasilRanking->max('skor_topsis');
            $skorTerendah = $hasilRanking->min('skor_topsis');
            $rataRataSkor = $hasilRanking->avg('skor_topsis');

            $karyawanTertinggi = $hasilRanking->where('skor_topsis', $skorTertinggi)->first();
            $karyawanTerendah = $hasilRanking->where('skor_topsis', $skorTerendah)->first();
        } else {
            $skorTertinggi = 0;
            $skorTerendah = 0;
            $rataRataSkor = 0;
            $karyawanTertinggi = null;
            $karyawanTerendah = null;
        }

        // Get periode label
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Get tanggal generate info (dari hasil yang sudah difilter)
        $latestHasil = $hasilRanking->first();
        $tanggalGenerate = $latestHasil ? $latestHasil->tanggal_generate : null;
        $generatedBy = $latestHasil
            ? ($latestHasil->generatedBySuperAdmin ?? $latestHasil->generatedByHRD)
            : null;

        return view('ranking.index', compact(
            'hasilRanking',
            'bulan',
            'tahun',
            'periodeLabel',
            'tanggalGenerate',
            'generatedBy',
            'availablePeriods',
            'divisiList',
            'divisiFilter',
            'search',
            'totalKaryawan',
            'skorTertinggi',
            'skorTerendah',
            'rataRataSkor',
            'karyawanTertinggi',
            'karyawanTerendah'
        ));
    }

    /**
     * Show hasil ranking for specific periode.
     * Menampilkan tabel ranking karyawan untuk periode tertentu
     */
    public function show($bulan, $tahun)
    {
        // Check if hasil exists
        $hasilExists = HasilTopsis::byPeriode($bulan, $tahun)->exists();

        if (!$hasilExists) {
            return redirect()->route('perhitungan.index')
                ->with('error', 'Ranking untuk periode ini belum di-generate. Silakan generate terlebih dahulu.');
        }

        // Get hasil ranking ordered by ranking
        $hasilRanking = HasilTopsis::byPeriode($bulan, $tahun)
            ->with('karyawan')
            ->orderedByRanking()
            ->get();

        // Get periode label
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Get tanggal generate info
        $latestHasil = $hasilRanking->first();
        $tanggalGenerate = $latestHasil ? $latestHasil->tanggal_generate : null;
        $generatedBy = $latestHasil
            ? ($latestHasil->generatedBySuperAdmin ?? $latestHasil->generatedByHRD)
            : null;

        return view('perhitungan.show', compact(
            'hasilRanking',
            'bulan',
            'tahun',
            'periodeLabel',
            'tanggalGenerate',
            'generatedBy'
        ));
    }

    /**
     * Display detail perhitungan for specific karyawan.
     * Menampilkan breakdown detail perhitungan TOPSIS per karyawan
     *
     * Authorization:
     * - Super Admin, HRD: Bisa lihat detail semua karyawan
     * - Karyawan: Hanya bisa lihat detail diri sendiri
     */
    public function detail($id)
    {
        $hasil = HasilTopsis::with('karyawan')->findOrFail($id);

        // Authorization check: Karyawan hanya bisa lihat detail diri sendiri
        if (auth()->user()->isKaryawan()) {
            if ($hasil->id_karyawan !== auth()->id()) {
                return redirect()->route('ranking.index')
                    ->with('error', 'Anda tidak memiliki akses untuk melihat detail karyawan lain.');
            }
        }

        // Get all hasil for this periode for comparison (with same divisi_filter)
        $allHasilQuery = HasilTopsis::byPeriode($hasil->bulan, $hasil->tahun);

        // Filter by same divisi_filter as current hasil
        if ($hasil->divisi_filter) {
            $allHasilQuery->where('divisi_filter', $hasil->divisi_filter);
        } else {
            $allHasilQuery->whereNull('divisi_filter');
        }

        $allHasil = $allHasilQuery->orderedByRanking()->get();

        // Get periode label
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$hasil->bulan] . ' ' . $hasil->tahun;

        // Get kriteria with sub-kriteria and penilaian data
        $kriteriaList = SistemKriteria::where('level', 1)
            ->where('is_active', true)
            ->with(['subKriteria' => function ($query) {
                $query->where('is_active', true)->orderBy('urutan');
            }])
            ->orderBy('urutan')
            ->get();

        // Get all penilaian for this karyawan in this periode
        $penilaianData = Penilaian::where('id_karyawan', $hasil->id_karyawan)
            ->where('bulan', $hasil->bulan)
            ->where('tahun', $hasil->tahun)
            ->with(['kriteria', 'subKriteria'])
            ->get();

        // Organize penilaian by kriteria
        $penilaianByKriteria = [];
        foreach ($penilaianData as $penilaian) {
            $kriteriaId = $penilaian->id_kriteria;
            $subKriteriaId = $penilaian->id_sub_kriteria;

            if (!isset($penilaianByKriteria[$kriteriaId])) {
                $penilaianByKriteria[$kriteriaId] = [
                    'items' => [],
                    'catatan' => $penilaian->catatan,
                    'total' => 0,
                ];
            }

            if ($subKriteriaId) {
                // Multi sub-kriteria
                $penilaianByKriteria[$kriteriaId]['items'][$subKriteriaId] = $penilaian;
            } else {
                // Single kriteria
                $penilaianByKriteria[$kriteriaId]['items']['single'] = $penilaian;
            }
        }

        // Calculate total per kriteria (sum of all sub-kriteria values)
        foreach ($penilaianByKriteria as $kriteriaId => $data) {
            $total = 0;
            foreach ($data['items'] as $item) {
                if ($item && is_object($item)) {
                    $total += $item->nilai;
                }
            }
            $penilaianByKriteria[$kriteriaId]['total'] = $total;
        }

        return view('perhitungan.detail', compact(
            'hasil',
            'allHasil',
            'periodeLabel',
            'kriteriaList',
            'penilaianByKriteria'
        ));
    }

    /**
     * Calculate/Generate ranking using TOPSIS algorithm.
     * Menghitung ranking karyawan menggunakan algoritma TOPSIS
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'divisi' => 'nullable|string',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $divisiFilter = $request->divisi ?? '';

        try {
            DB::beginTransaction();

            // STEP 1: Get all karyawan yang punya penilaian di periode ini (dengan filter divisi)
            // Ambil karyawan berdasarkan filter divisi
            $karyawanQuery = User::where('role', 'karyawan')
                ->where('status_akun', 'aktif');

            if (!empty($divisiFilter)) {
                $karyawanQuery->where('divisi', $divisiFilter);
            }

            $karyawanIdsFromFilter = $karyawanQuery->pluck('id');

            // Filter penilaian hanya untuk karyawan sesuai filter divisi
            $karyawanIds = Penilaian::byPeriode($bulan, $tahun)
                ->whereIn('id_karyawan', $karyawanIdsFromFilter)
                ->distinct()
                ->pluck('id_karyawan');

            if ($karyawanIds->isEmpty()) {
                DB::rollBack();
                $divisiLabel = !empty($divisiFilter) ? " divisi {$divisiFilter}" : '';
                return redirect()->route('perhitungan.index', ['bulan' => $bulan, 'tahun' => $tahun, 'divisi' => $divisiFilter])
                    ->with('error', "Tidak ada data penilaian untuk periode ini{$divisiLabel}.");
            }

            // STEP 2: Get all active kriteria (level 1)
            $kriteriaList = SistemKriteria::where('level', 1)
                ->where('is_active', true)
                ->orderBy('urutan', 'asc')
                ->get();

            if ($kriteriaList->isEmpty()) {
                DB::rollBack();
                return redirect()->route('perhitungan.index', ['bulan' => $bulan, 'tahun' => $tahun])
                    ->with('error', 'Tidak ada kriteria aktif.');
            }

            // STEP 2.1: Validate bobot kriteria
            $totalBobot = $kriteriaList->sum('bobot');
            if (abs($totalBobot - 100) >= 0.01) {
                DB::rollBack();
                return redirect()->route('perhitungan.index', ['bulan' => $bulan, 'tahun' => $tahun, 'divisi' => $divisiFilter])
                    ->with('error', 'Total bobot kriteria harus 100%. Saat ini: ' . number_format($totalBobot, 2) . '%');
            }

            // STEP 2.2: Validate all karyawan have complete penilaian for all kriteria (dengan filter divisi)
            $incompleteKaryawan = [];
            $incompleteKriteria = [];

            foreach ($kriteriaList as $kriteria) {
                $validation = $this->validateKriteriaCompletion($kriteria->id, $bulan, $tahun, $divisiFilter);

                if (!$validation['is_complete']) {
                    $incompleteKriteria[] = $kriteria->nama_kriteria;

                    foreach ($validation['incomplete_employees'] as $employee) {
                        $key = $employee['id'];
                        if (!isset($incompleteKaryawan[$key])) {
                            $incompleteKaryawan[$key] = [
                                'nama' => $employee['nama'],
                                'nik' => $employee['nik'],
                                'kriteria_tidak_lengkap' => [],
                            ];
                        }
                        $incompleteKaryawan[$key]['kriteria_tidak_lengkap'][] = $kriteria->nama_kriteria;
                    }
                }
            }

            if (!empty($incompleteKaryawan)) {
                DB::rollBack();

                // Build error message
                $karyawanList = collect($incompleteKaryawan)->map(function ($item) {
                    return $item['nama'] . ' (' . $item['nik'] . ')';
                })->take(5)->implode(', ');

                $divisiLabel = !empty($divisiFilter) ? " divisi {$divisiFilter}" : '';
                $errorMsg = "TOPSIS tidak dapat dijalankan{$divisiLabel}. Ada " . count($incompleteKaryawan) . ' karyawan dengan data penilaian tidak lengkap';
                if (count($incompleteKaryawan) <= 5) {
                    $errorMsg .= ': ' . $karyawanList;
                } else {
                    $errorMsg .= ' (menampilkan 5 dari ' . count($incompleteKaryawan) . '): ' . $karyawanList . ', dll.';
                }

                return redirect()->route('perhitungan.index', ['bulan' => $bulan, 'tahun' => $tahun, 'divisi' => $divisiFilter])
                    ->with('error', $errorMsg);
            }

            // STEP 3: Build decision matrix dengan standardisasi yang benar (sesuai skripsi)
            $topsisService = new PerhitunganTopsisService();
            [$decisionMatrix, $nilaiPerKriteria] = $topsisService->buatMatriksKeputusan(
                $karyawanIds->toArray(),
                $kriteriaList,
                $bulan,
                $tahun
            );

            // STEP 4-8: Calculate TOPSIS (normalisasi, weighted, ideal solutions, distances, preference)
            $topsisResult = $topsisService->hitungTOPSIS($decisionMatrix, $kriteriaList);

            $normalizedMatrix = $topsisResult['matriksTernormalisasi'];
            $weightedMatrix = $topsisResult['matriksTerbobot'];
            $idealPositive = $topsisResult['idealPositif'];
            $idealNegative = $topsisResult['idealNegatif'];
            $distances = $topsisResult['jarak'];
            $preferenceValues = $topsisResult['nilaiPreferensi'];

            // STEP 9: Rank based on preference value (highest first)
            arsort($preferenceValues);

            $ranking = 1;
            $rankedResults = [];
            foreach ($preferenceValues as $karyawanId => $preference) {
                $rankedResults[$karyawanId] = [
                    'ranking' => $ranking,
                    'skor_topsis' => $preference,
                    'jarak_ideal_positif' => $distances[$karyawanId]['dPlus'],
                    'jarak_ideal_negatif' => $distances[$karyawanId]['dMinus'],
                ];
                $ranking++;
            }

            // STEP 10: Save to database (delete existing untuk divisi ini saja, insert new)
            // Hapus hanya hasil ranking untuk periode + divisi filter yang sama
            // Jika divisiFilter kosong (semua divisi), hapus yang divisi_filter = NULL
            // Jika divisiFilter ada (divisi tertentu), hapus yang divisi_filter = divisi tersebut
            $deleteQuery = HasilTopsis::byPeriode($bulan, $tahun);
            if (empty($divisiFilter)) {
                $deleteQuery->whereNull('divisi_filter');
            } else {
                $deleteQuery->where('divisi_filter', $divisiFilter);
            }
            $deleteQuery->forceDelete();

            $namaBulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;
            $tanggalGenerate = Carbon::now();

            $insertedCount = 0;
            foreach ($rankedResults as $karyawanId => $result) {
                // Build detail perhitungan
                $detailPerhitungan = [
                    'decision_matrix' => $decisionMatrix[$karyawanId],
                    'normalized_matrix' => $normalizedMatrix[$karyawanId],
                    'weighted_matrix' => $weightedMatrix[$karyawanId],
                    'ideal_positive' => $idealPositive,
                    'ideal_negative' => $idealNegative,
                ];

                HasilTopsis::create([
                    'id_karyawan' => $karyawanId,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'periode_label' => $periodeLabel,
                    'divisi_filter' => !empty($divisiFilter) ? $divisiFilter : null,
                    'skor_topsis' => $result['skor_topsis'],
                    'ranking' => $result['ranking'],
                    'jarak_ideal_positif' => $result['jarak_ideal_positif'],
                    'jarak_ideal_negatif' => $result['jarak_ideal_negatif'],
                    'nilai_per_kriteria' => $nilaiPerKriteria[$karyawanId],
                    'detail_perhitungan' => $detailPerhitungan,
                    'tanggal_generate' => $tanggalGenerate,
                    'generated_by_super_admin_id' => auth()->user()->isSuperAdmin() ? auth()->id() : null,
                    'generated_by_hrd_id' => auth()->user()->isHRD() ? auth()->id() : null,
                ]);

                $insertedCount++;
            }

            DB::commit();

            // Redirect ke ranking.index dengan filter periode dan divisi
            $redirectParams = [
                'bulan' => $bulan,
                'tahun' => $tahun
            ];

            // Tambahkan divisi filter jika ada
            if (!empty($divisiFilter)) {
                $redirectParams['divisi'] = $divisiFilter;
            }

            return redirect()->route('ranking.index', $redirectParams)
                ->with('success', "Berhasil menghitung ranking untuk {$insertedCount} karyawan pada periode {$periodeLabel}");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('perhitungan.index', ['bulan' => $bulan, 'tahun' => $tahun, 'divisi' => $divisiFilter])
                ->with('error', 'Gagal menghitung ranking: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate/Regenerate ranking for specific periode.
     * Menghitung ulang ranking yang sudah ada
     */
    public function recalculate($bulan, $tahun)
    {
        // Redirect to calculate with same parameters
        return $this->calculate(new Request(['bulan' => $bulan, 'tahun' => $tahun]));
    }

    /**
     * Export ranking to Excel
     *
     * Authorization:
     * - Karyawan: hanya bisa export data pribadi
     * - Admin/HRD/Supervisor: bisa export semua data
     */
    public function exportExcel(Request $request, $bulan, $tahun)
    {
        // Get periode label
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Cek apakah ranking sudah di-generate
        if (!HasilTopsis::byPeriode($bulan, $tahun)->exists()) {
            return redirect()->back()
                ->with('error', 'Ranking untuk periode ini belum di-generate.');
        }

        // Get filter parameters
        $divisiFilter = $request->query('divisi', '');
        $search = $request->query('search', '');

        // Tentukan apakah export pribadi atau semua
        $idKaryawan = null;
        if (auth()->user()->isKaryawan()) {
            $idKaryawan = auth()->id();
        }

        // Build filename dengan info filter
        $fileName = 'Ranking_' . $periodeLabel;
        if (!empty($divisiFilter)) {
            $fileName .= '_' . $divisiFilter;
        } else {
            $fileName .= '_Semua_Divisi';
        }
        if ($idKaryawan) {
            $fileName .= '_' . auth()->user()->nama;
        }
        $fileName .= '.xlsx';

        return Excel::download(
            new RankingExport($bulan, $tahun, $periodeLabel, $idKaryawan, $divisiFilter, $search),
            $fileName
        );
    }

    /**
     * Export ranking to PDF
     *
     * Authorization:
     * - Karyawan: hanya bisa export data pribadi (laporan personal)
     * - Admin/HRD/Supervisor: bisa export semua data (laporan lengkap)
     */
    public function exportPDF(Request $request, $bulan, $tahun)
    {
        // Get periode label
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Cek apakah ranking sudah di-generate
        if (!HasilTopsis::byPeriode($bulan, $tahun)->exists()) {
            return redirect()->back()
                ->with('error', 'Ranking untuk periode ini belum di-generate.');
        }

        // Get filter parameters
        $divisiFilter = $request->query('divisi', '');
        $search = $request->query('search', '');

        // Jika karyawan, export laporan pribadi
        if (auth()->user()->isKaryawan()) {
            return $this->exportPersonalPDF($bulan, $tahun, $periodeLabel, $divisiFilter, $search);
        }

        // Jika admin/HRD/supervisor, export laporan lengkap
        return $this->exportFullPDF($bulan, $tahun, $periodeLabel, $divisiFilter, $search);
    }

    /**
     * Export personal ranking report to PDF (untuk karyawan)
     */
    private function exportPersonalPDF($bulan, $tahun, $periodeLabel, $divisiFilter = '', $search = '')
    {
        // Get hasil untuk karyawan yang login
        $hasilQuery = HasilTopsis::with('karyawan')
            ->byPeriode($bulan, $tahun)
            ->where('id_karyawan', auth()->id());

        // Filter by divisi_filter - sama seperti di rankingIndex
        if (!empty($divisiFilter)) {
            $hasilQuery->where('divisi_filter', $divisiFilter);
        } else {
            $hasilQuery->whereNull('divisi_filter');
        }

        $hasil = $hasilQuery->first();

        if (!$hasil) {
            return redirect()->back()
                ->with('error', 'Data ranking Anda tidak ditemukan untuk periode ini.');
        }

        // Get total karyawan untuk konteks (dengan filter yang sama)
        $totalKaryawanQuery = HasilTopsis::byPeriode($bulan, $tahun);
        if (!empty($divisiFilter)) {
            $totalKaryawanQuery->where('divisi_filter', $divisiFilter);
        }
        $totalKaryawan = $totalKaryawanQuery->count();

        // Get detail perhitungan
        $detailPerhitungan = $hasil->detail_perhitungan;

        // Get kriteria dengan nilai dan sub-kriteria
        $kriteriaData = SistemKriteria::where('level', 1)
            ->with('subKriteria')
            ->orderBy('urutan')
            ->get();

        $kriteriaScores = [];

        foreach ($kriteriaData as $k) {
            $kriteriaScores[] = [
                'nama' => $k->nama_kriteria,
                'bobot' => number_format($k->bobot, 0),
                'tipe' => $k->jenis_kriteria === 'benefit' ? 'Benefit' : 'Cost',
                'nilai' => number_format($detailPerhitungan['decision_matrix'][$k->nama_kriteria] ?? 0, 0),
            ];
        }

        // Generate PDF
        $pdf = Pdf::loadView('exports.ranking-personal-pdf', compact(
            'hasil',
            'periodeLabel',
            'totalKaryawan',
            'detailPerhitungan',
            'kriteriaScores',
            'kriteriaData'
        ));

        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Laporan_Ranking_' . auth()->user()->nama . '_' . $periodeLabel . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export full ranking report to PDF (untuk admin/HRD/supervisor)
     */
    private function exportFullPDF($bulan, $tahun, $periodeLabel, $divisiFilter = '', $search = '')
    {
        // Get all hasil ranking untuk periode ini dengan filter
        $hasilQuery = HasilTopsis::with('karyawan')
            ->byPeriode($bulan, $tahun);

        // Filter by divisi_filter - sama seperti di rankingIndex
        if (!empty($divisiFilter)) {
            $hasilQuery->where('divisi_filter', $divisiFilter);
        } else {
            $hasilQuery->whereNull('divisi_filter');
        }

        // Filter by search (nama atau NIK)
        if (!empty($search)) {
            $hasilQuery->whereHas('karyawan', function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $hasilRanking = $hasilQuery->orderedByRanking()->get();

        // Get info generate
        $latestHasil = HasilTopsis::byPeriode($bulan, $tahun)
            ->orderBy('tanggal_generate', 'desc')
            ->first();

        $tanggalGenerate = $latestHasil->tanggal_generate;
        $generatedBy = $latestHasil->generatedBySuperAdmin ?? $latestHasil->generatedByHRD;

        // Generate PDF
        $pdf = Pdf::loadView('exports.ranking-full-pdf', compact(
            'hasilRanking',
            'periodeLabel',
            'tanggalGenerate',
            'generatedBy'
        ));

        $pdf->setPaper('a4', 'landscape');

        $fileName = 'Laporan_Ranking_Lengkap_' . $periodeLabel . '.pdf';

        return $pdf->download($fileName);
    }
}
