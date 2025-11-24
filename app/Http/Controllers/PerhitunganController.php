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

        // Build periode label
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Get all active karyawan
        $karyawanAktif = User::where('role', 'karyawan')
            ->where('status_akun', 'aktif')
            ->count();

        // Get karyawan with penilaian in this periode
        $karyawanDenganPenilaian = Penilaian::byPeriode($bulan, $tahun)
            ->distinct('id_karyawan')
            ->pluck('id_karyawan');

        $dataPenilaianLengkap = $karyawanDenganPenilaian->count();
        $dataTidakLengkap = $karyawanAktif - $dataPenilaianLengkap;

        // Get all active kriteria
        $kriteriaAktif = SistemKriteria::where('level', 1)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        // Calculate validation status per kriteria
        $validasiKriteria = [];
        foreach ($kriteriaAktif as $kriteria) {
            $validation = $this->validateKriteriaCompletion($kriteria->id, $bulan, $tahun);
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

        // Check if ranking already exists
        $hasilExists = HasilTopsis::byPeriode($bulan, $tahun)->exists();

        // Get history of TOPSIS generations
        $riwayatGenerate = HasilTopsis::select('bulan', 'tahun', 'periode_label', 'tanggal_generate',
                                               'generated_by_super_admin_id', 'generated_by_hrd_id')
            ->distinct()
            ->with(['generatedBySuperAdmin', 'generatedByHRD'])
            ->orderByRaw('tahun DESC, bulan DESC')
            ->orderBy('tanggal_generate', 'desc')
            ->get()
            ->unique(function ($item) {
                return $item->bulan . '-' . $item->tahun;
            })
            ->take(10);

        return view('perhitungan.index', compact(
            'bulan',
            'tahun',
            'tahunList',
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
     * @return array
     */
    private function validateKriteriaCompletion($kriteriaId, $bulan, $tahun)
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

        // Get all active karyawan
        $allKaryawan = User::where('role', 'karyawan')
            ->where('status_akun', 'aktif')
            ->get();

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
     * Redirect ke periode terbaru yang sudah di-generate
     *
     * Authorization: Semua role yang login
     */
    public function rankingIndex()
    {
        // Get periode terbaru yang sudah ada ranking-nya
        $latestHasil = HasilTopsis::select('bulan', 'tahun')
            ->distinct()
            ->orderByRaw('tahun DESC, bulan DESC')
            ->first();

        if (!$latestHasil) {
            return redirect()->route('dashboard')
                ->with('error', 'Belum ada data ranking yang tersedia.');
        }

        // Redirect ke halaman show periode terbaru
        return redirect()->route('ranking.show', [$latestHasil->bulan, $latestHasil->tahun]);
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
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
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

        // Get all hasil for this periode for comparison
        $allHasil = HasilTopsis::byPeriode($hasil->bulan, $hasil->tahun)
            ->orderedByRanking()
            ->get();

        // Get periode label
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$hasil->bulan] . ' ' . $hasil->tahun;

        return view('perhitungan.detail', compact(
            'hasil',
            'allHasil',
            'periodeLabel'
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
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        try {
            DB::beginTransaction();

            // STEP 1: Get all karyawan yang punya penilaian di periode ini
            $karyawanIds = Penilaian::byPeriode($bulan, $tahun)
                ->distinct()
                ->pluck('id_karyawan');

            if ($karyawanIds->isEmpty()) {
                DB::rollBack();
                return redirect()->route('perhitungan.index', ['bulan' => $bulan, 'tahun' => $tahun])
                    ->with('error', 'Tidak ada data penilaian untuk periode ini.');
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
                return redirect()->route('perhitungan.index', ['bulan' => $bulan, 'tahun' => $tahun])
                    ->with('error', 'Total bobot kriteria harus 100%. Saat ini: ' . number_format($totalBobot, 2) . '%');
            }

            // STEP 2.2: Validate all karyawan have complete penilaian for all kriteria
            $allKaryawan = User::where('role', 'karyawan')
                ->where('status_akun', 'aktif')
                ->get();

            $incompleteKaryawan = [];
            $incompleteKriteria = [];

            foreach ($kriteriaList as $kriteria) {
                $validation = $this->validateKriteriaCompletion($kriteria->id, $bulan, $tahun);

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

                $errorMsg = 'TOPSIS tidak dapat dijalankan. Ada ' . count($incompleteKaryawan) . ' karyawan dengan data penilaian tidak lengkap';
                if (count($incompleteKaryawan) <= 5) {
                    $errorMsg .= ': ' . $karyawanList;
                } else {
                    $errorMsg .= ' (menampilkan 5 dari ' . count($incompleteKaryawan) . '): ' . $karyawanList . ', dll.';
                }

                return redirect()->route('perhitungan.index', ['bulan' => $bulan, 'tahun' => $tahun])
                    ->with('error', $errorMsg);
            }

            // STEP 3: Build decision matrix (nilai per karyawan per kriteria)
            $decisionMatrix = [];
            $nilaiPerKriteria = []; // For storing in hasil_topsis

            foreach ($karyawanIds as $karyawanId) {
                $decisionMatrix[$karyawanId] = [];
                $nilaiPerKriteria[$karyawanId] = [];

                foreach ($kriteriaList as $kriteria) {
                    // Get average nilai for this kriteria (aggregate dari sub-kriteria)
                    // Menggunakan weighted average berdasarkan bobot sub-kriteria
                    $subKriteria = $kriteria->subKriteria()->where('is_active', true)->get();

                    $totalNilai = 0;
                    $totalBobot = 0;

                    foreach ($subKriteria as $sub) {
                        $penilaian = Penilaian::byKaryawan($karyawanId)
                            ->byPeriode($bulan, $tahun)
                            ->where('id_sub_kriteria', $sub->id)
                            ->first();

                        if ($penilaian) {
                            $totalNilai += $penilaian->nilai * $sub->bobot;
                            $totalBobot += $sub->bobot;
                        }
                    }

                    // Weighted average
                    $avgNilai = $totalBobot > 0 ? $totalNilai / $totalBobot : 0;

                    $decisionMatrix[$karyawanId][$kriteria->id] = $avgNilai;
                    $nilaiPerKriteria[$karyawanId][$kriteria->nama_kriteria] = round($avgNilai, 2);
                }
            }

            // STEP 4: Normalisasi matriks (menggunakan vector normalization)
            $normalizedMatrix = [];

            foreach ($kriteriaList as $kriteria) {
                // Calculate sum of squares for this kriteria
                $sumOfSquares = 0;
                foreach ($karyawanIds as $karyawanId) {
                    $sumOfSquares += pow($decisionMatrix[$karyawanId][$kriteria->id], 2);
                }

                $sqrtSum = sqrt($sumOfSquares);

                // Normalize each value
                foreach ($karyawanIds as $karyawanId) {
                    $normalizedMatrix[$karyawanId][$kriteria->id] =
                        $sqrtSum > 0
                            ? $decisionMatrix[$karyawanId][$kriteria->id] / $sqrtSum
                            : 0;
                }
            }

            // STEP 5: Weighted normalized matrix (kalikan dengan bobot)
            $weightedMatrix = [];

            foreach ($karyawanIds as $karyawanId) {
                $weightedMatrix[$karyawanId] = [];
                foreach ($kriteriaList as $kriteria) {
                    $weightedMatrix[$karyawanId][$kriteria->id] =
                        $normalizedMatrix[$karyawanId][$kriteria->id] * ($kriteria->bobot / 100);
                }
            }

            // STEP 6: Determine ideal positive (A+) and ideal negative (A-) solutions
            $idealPositive = [];
            $idealNegative = [];

            foreach ($kriteriaList as $kriteria) {
                $values = [];
                foreach ($karyawanIds as $karyawanId) {
                    $values[] = $weightedMatrix[$karyawanId][$kriteria->id];
                }

                if ($kriteria->tipe_kriteria === 'benefit') {
                    // Benefit: max is ideal positive, min is ideal negative
                    $idealPositive[$kriteria->id] = max($values);
                    $idealNegative[$kriteria->id] = min($values);
                } else {
                    // Cost: min is ideal positive, max is ideal negative
                    $idealPositive[$kriteria->id] = min($values);
                    $idealNegative[$kriteria->id] = max($values);
                }
            }

            // STEP 7: Calculate distance to ideal positive (D+) and ideal negative (D-)
            $distances = [];

            foreach ($karyawanIds as $karyawanId) {
                $dPlus = 0; // Distance to ideal positive
                $dMinus = 0; // Distance to ideal negative

                foreach ($kriteriaList as $kriteria) {
                    $value = $weightedMatrix[$karyawanId][$kriteria->id];

                    $dPlus += pow($value - $idealPositive[$kriteria->id], 2);
                    $dMinus += pow($value - $idealNegative[$kriteria->id], 2);
                }

                $distances[$karyawanId] = [
                    'dPlus' => sqrt($dPlus),
                    'dMinus' => sqrt($dMinus),
                ];
            }

            // STEP 8: Calculate preference value (V = D- / (D+ + D-))
            $preferenceValues = [];

            foreach ($karyawanIds as $karyawanId) {
                $dPlus = $distances[$karyawanId]['dPlus'];
                $dMinus = $distances[$karyawanId]['dMinus'];

                $preference = ($dPlus + $dMinus) > 0
                    ? $dMinus / ($dPlus + $dMinus)
                    : 0;

                $preferenceValues[$karyawanId] = $preference;
            }

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

            // STEP 10: Save to database (delete existing and insert new)
            HasilTopsis::byPeriode($bulan, $tahun)->forceDelete();

            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
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

            return redirect()->route('perhitungan.show', [$bulan, $tahun])
                ->with('success', "Berhasil menghitung ranking untuk {$insertedCount} karyawan pada periode {$periodeLabel}");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('perhitungan.index')
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
    public function exportExcel($bulan, $tahun)
    {
        // Get periode label
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Cek apakah ranking sudah di-generate
        if (!HasilTopsis::byPeriode($bulan, $tahun)->exists()) {
            return redirect()->back()
                ->with('error', 'Ranking untuk periode ini belum di-generate.');
        }

        // Tentukan apakah export pribadi atau semua
        $idKaryawan = null;
        if (auth()->user()->isKaryawan()) {
            $idKaryawan = auth()->id();
        }

        $fileName = 'Ranking_' . $periodeLabel . ($idKaryawan ? '_' . auth()->user()->nama : '') . '.xlsx';

        return Excel::download(
            new RankingExport($bulan, $tahun, $periodeLabel, $idKaryawan),
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
    public function exportPDF($bulan, $tahun)
    {
        // Get periode label
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Cek apakah ranking sudah di-generate
        if (!HasilTopsis::byPeriode($bulan, $tahun)->exists()) {
            return redirect()->back()
                ->with('error', 'Ranking untuk periode ini belum di-generate.');
        }

        // Jika karyawan, export laporan pribadi
        if (auth()->user()->isKaryawan()) {
            return $this->exportPersonalPDF($bulan, $tahun, $periodeLabel);
        }

        // Jika admin/HRD/supervisor, export laporan lengkap
        return $this->exportFullPDF($bulan, $tahun, $periodeLabel);
    }

    /**
     * Export personal ranking report to PDF (untuk karyawan)
     */
    private function exportPersonalPDF($bulan, $tahun, $periodeLabel)
    {
        // Get hasil untuk karyawan yang login
        $hasil = HasilTopsis::with('karyawan')
            ->byPeriode($bulan, $tahun)
            ->where('id_karyawan', auth()->id())
            ->first();

        if (!$hasil) {
            return redirect()->back()
                ->with('error', 'Data ranking Anda tidak ditemukan untuk periode ini.');
        }

        // Get total karyawan untuk konteks
        $totalKaryawan = HasilTopsis::byPeriode($bulan, $tahun)->count();

        // Get detail perhitungan
        $detailPerhitungan = $hasil->detail_perhitungan;

        // Get kriteria dengan nilai
        $kriteria = SistemKriteria::where('level', 1)->orderBy('urutan')->get();
        $kriteriaScores = [];

        foreach ($kriteria as $k) {
            $kriteriaScores[] = [
                'nama' => $k->nama_kriteria,
                'bobot' => number_format($k->bobot, 0),
                'tipe' => $k->tipe === 'benefit' ? 'Benefit' : 'Cost',
                'nilai' => number_format($detailPerhitungan['decision_matrix'][$k->nama_kriteria] ?? 0, 0),
            ];
        }

        // Generate PDF
        $pdf = Pdf::loadView('exports.ranking-personal-pdf', compact(
            'hasil',
            'periodeLabel',
            'totalKaryawan',
            'detailPerhitungan',
            'kriteriaScores'
        ));

        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Laporan_Ranking_' . auth()->user()->nama . '_' . $periodeLabel . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export full ranking report to PDF (untuk admin/HRD/supervisor)
     */
    private function exportFullPDF($bulan, $tahun, $periodeLabel)
    {
        // Get all hasil ranking untuk periode ini
        $hasilRanking = HasilTopsis::with('karyawan')
            ->byPeriode($bulan, $tahun)
            ->orderedByRanking()
            ->get();

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
