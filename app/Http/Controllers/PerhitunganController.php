<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HasilTopsis;
use App\Models\Penilaian;
use App\Models\User;
use App\Models\SistemKriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        // Get unique periode from penilaian
        $periodeList = Penilaian::select('bulan', 'tahun', 'periode_label')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get()
            ->map(function ($item) {
                // Check if ranking sudah di-generate untuk periode ini
                $hasilExists = HasilTopsis::byPeriode($item->bulan, $item->tahun)->exists();
                $jumlahKaryawan = Penilaian::byPeriode($item->bulan, $item->tahun)
                    ->distinct('id_karyawan')
                    ->count('id_karyawan');

                // Get tanggal generate terakhir jika ada
                $lastGenerated = null;
                $generatedBy = null;
                if ($hasilExists) {
                    $latestHasil = HasilTopsis::byPeriode($item->bulan, $item->tahun)
                        ->orderBy('tanggal_generate', 'desc')
                        ->first();

                    if ($latestHasil) {
                        $lastGenerated = $latestHasil->tanggal_generate;
                        $generatedBy = $latestHasil->generatedBySuperAdmin
                            ?? $latestHasil->generatedByHRD;
                    }
                }

                return [
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'periode_label' => $item->periode_label,
                    'has_ranking' => $hasilExists,
                    'jumlah_karyawan' => $jumlahKaryawan,
                    'last_generated' => $lastGenerated,
                    'generated_by' => $generatedBy,
                ];
            });

        return view('perhitungan.index', compact('periodeList'));
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
                return redirect()->route('perhitungan.index')
                    ->with('error', 'Tidak ada data penilaian untuk periode ini.');
            }

            // STEP 2: Get all active kriteria (level 1)
            $kriteriaList = SistemKriteria::where('level', 1)
                ->where('is_active', true)
                ->orderBy('urutan', 'asc')
                ->get();

            if ($kriteriaList->isEmpty()) {
                return redirect()->route('perhitungan.index')
                    ->with('error', 'Tidak ada kriteria aktif.');
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
}
