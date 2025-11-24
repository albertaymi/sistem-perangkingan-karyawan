<?php

namespace App\Services;

use App\Models\Penilaian;
use App\Models\SistemKriteria;
use App\Models\User;

/**
 * Service Perhitungan TOPSIS
 *
 * Service class untuk menghitung ranking karyawan menggunakan metode TOPSIS
 * (Technique for Order Preference by Similarity to Ideal Solution).
 *
 * Implementasi sesuai dengan skripsi PT. Karya Buana Cilacap dengan sistem
 * standardisasi untuk 3 tipe input: Angka, Rating Scale, dan Dropdown Selection.
 */
class PerhitunganTopsisService
{
    /**
     * Standardisasi Nilai Berdasarkan Tipe Input
     *
     * Mengimplementasikan Formula 3.1, 3.2, 3.3, 3.4, dan 3.5 dari skripsi:
     * - Tipe Angka: Menggunakan Min-Max Normalization
     * - Tipe Rating: Menggunakan Linear Conversion
     * - Tipe Dropdown: Menggunakan Nilai Tetap yang sudah ditentukan
     *
     * @param array $nilaiMentah Array nilai mentah dari semua karyawan untuk 1 sub-kriteria [id_karyawan => nilai]
     * @param string $tipeInput 'angka', 'rating', atau 'dropdown'
     * @param string $tipeKriteria 'benefit' atau 'cost'
     * @param float|null $nilaiMin Nilai minimum range (untuk angka/rating)
     * @param float|null $nilaiMax Nilai maksimum range (untuk angka/rating)
     * @return array Array nilai yang sudah distandardisasi dalam skala 0-100 [id_karyawan => nilai_standar]
     */
    public function standardisasiNilai(
        array $nilaiMentah,
        string $tipeInput,
        string $tipeKriteria,
        ?float $nilaiMin = null,
        ?float $nilaiMax = null
    ): array {
        $nilaiStandar = [];

        // Ambil nilai min dan max dari data aktual
        $dataMin = !empty($nilaiMentah) ? min($nilaiMentah) : 0;
        $dataMax = !empty($nilaiMentah) ? max($nilaiMentah) : 0;

        foreach ($nilaiMentah as $idKaryawan => $nilai) {
            switch ($tipeInput) {
                case 'angka':
                    // Formula 3.1 (Benefit) dan 3.2 (Cost) dari skripsi
                    if ($tipeKriteria === 'benefit') {
                        // Benefit: Skor_benefit = ((nilai - min) / (max - min)) × 100
                        if ($dataMax - $dataMin > 0) {
                            $nilaiStandar[$idKaryawan] = (($nilai - $dataMin) / ($dataMax - $dataMin)) * 100;
                        } else {
                            $nilaiStandar[$idKaryawan] = 100; // Semua nilai sama
                        }
                    } else {
                        // Cost: Skor_cost = ((max - nilai) / (max - min)) × 100
                        if ($dataMax - $dataMin > 0) {
                            $nilaiStandar[$idKaryawan] = (($dataMax - $nilai) / ($dataMax - $dataMin)) * 100;
                        } else {
                            $nilaiStandar[$idKaryawan] = 100; // Semua nilai sama
                        }
                    }
                    break;

                case 'rating':
                    // Formula 3.3 (Benefit) dan 3.4 (Cost) dari skripsi
                    $ratingMaks = $nilaiMax ?? 5; // Default rating scale 1-5
                    if ($tipeKriteria === 'benefit') {
                        // Benefit: Skor_benefit = (rating / rating_max) × 100
                        $nilaiStandar[$idKaryawan] = ($nilai / $ratingMaks) * 100;
                    } else {
                        // Cost: Skor_cost = ((rating_max - rating + 1) / rating_max) × 100
                        $nilaiStandar[$idKaryawan] = (($ratingMaks - $nilai + 1) / $ratingMaks) * 100;
                    }
                    break;

                case 'dropdown':
                    // Formula 3.5 dari skripsi
                    // Skor = Nilai_tetap (nilai fixed dari dropdown option)
                    $nilaiStandar[$idKaryawan] = $nilai;
                    break;

                default:
                    $nilaiStandar[$idKaryawan] = $nilai;
            }
        }

        return $nilaiStandar;
    }

    /**
     * Agregasi Sub-Kriteria dengan Weighted Average
     *
     * Mengimplementasikan Formula 3.6 dari skripsi:
     * Skor_kriteria = Σ(Skor_sub-kriteria_i × Bobot_sub-kriteria_i) / Σ(Bobot_sub-kriteria_i)
     *
     * @param array $nilaiSubKriteria Array [id_sub_kriteria => [id_karyawan => nilai_standar]]
     * @param array $bobotSubKriteria Array [id_sub_kriteria => bobot]
     * @return array Array [id_karyawan => skor_akhir_kriteria]
     */
    public function agregasiSubKriteria(array $nilaiSubKriteria, array $bobotSubKriteria): array
    {
        // Dapatkan semua ID karyawan
        $daftarIdKaryawan = [];
        foreach ($nilaiSubKriteria as $nilaiArray) {
            $daftarIdKaryawan = array_merge($daftarIdKaryawan, array_keys($nilaiArray));
        }
        $daftarIdKaryawan = array_unique($daftarIdKaryawan);

        $skorAgregat = [];
        foreach ($daftarIdKaryawan as $idKaryawan) {
            $totalSkorTerbobot = 0;
            $totalBobot = 0;

            foreach ($nilaiSubKriteria as $idSub => $nilaiArray) {
                if (isset($nilaiArray[$idKaryawan]) && isset($bobotSubKriteria[$idSub])) {
                    $totalSkorTerbobot += $nilaiArray[$idKaryawan] * $bobotSubKriteria[$idSub];
                    $totalBobot += $bobotSubKriteria[$idSub];
                }
            }

            // Weighted average: Σ(skor_i × bobot_i) / Σ(bobot_i)
            $skorAgregat[$idKaryawan] = $totalBobot > 0 ? $totalSkorTerbobot / $totalBobot : 0;
        }

        return $skorAgregat;
    }

    /**
     * Membangun Decision Matrix dengan Standardisasi yang Benar
     *
     * Langkah ini menggabungkan:
     * 1. Standardisasi nilai per tipe input (Formula 3.1 - 3.5)
     * 2. Agregasi sub-kriteria jika ada (Formula 3.6)
     *
     * Menghasilkan matriks keputusan yang siap untuk proses TOPSIS.
     *
     * @param array $daftarIdKaryawan Array ID karyawan yang akan di-ranking
     * @param \Illuminate\Support\Collection $daftarKriteria Collection kriteria aktif
     * @param int $bulan Bulan periode penilaian
     * @param int $tahun Tahun periode penilaian
     * @return array [matriksKeputusan, nilaiPerKriteria]
     */
    public function buatMatriksKeputusan(array $daftarIdKaryawan, $daftarKriteria, int $bulan, int $tahun): array
    {
        $matriksKeputusan = [];
        $nilaiPerKriteria = [];

        foreach ($daftarKriteria as $kriteria) {
            // Cek apakah kriteria tunggal atau memiliki sub-kriteria
            $adalahKriteriaTunggal = !empty($kriteria->tipe_input);

            if ($adalahKriteriaTunggal) {
                // KRITERIA TUNGGAL: Standardisasi langsung
                $nilaiMentah = [];
                foreach ($daftarIdKaryawan as $idKaryawan) {
                    $penilaian = Penilaian::where('id_karyawan', $idKaryawan)
                        ->where('id_kriteria', $kriteria->id)
                        ->whereNull('id_sub_kriteria')
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->first();

                    $nilaiMentah[$idKaryawan] = $penilaian ? $penilaian->nilai : 0;
                }

                // Standardisasi nilai
                $nilaiStandar = $this->standardisasiNilai(
                    $nilaiMentah,
                    $kriteria->tipe_input,
                    $kriteria->tipe_kriteria,
                    $kriteria->nilai_min,
                    $kriteria->nilai_max
                );

                foreach ($daftarIdKaryawan as $idKaryawan) {
                    $matriksKeputusan[$idKaryawan][$kriteria->id] = $nilaiStandar[$idKaryawan] ?? 0;
                    $nilaiPerKriteria[$idKaryawan][$kriteria->nama_kriteria] = round($nilaiStandar[$idKaryawan] ?? 0, 2);
                }
            } else {
                // MULTI SUB-KRITERIA: Standardisasi tiap sub, lalu agregasi
                $subKriteria = $kriteria->subKriteria()->where('is_active', true)->get();
                $nilaiSubKriteria = [];
                $bobotSubKriteria = [];

                foreach ($subKriteria as $sub) {
                    $bobotSubKriteria[$sub->id] = $sub->bobot;

                    // Ambil nilai mentah untuk sub-kriteria ini
                    $nilaiMentah = [];
                    foreach ($daftarIdKaryawan as $idKaryawan) {
                        $penilaian = Penilaian::where('id_karyawan', $idKaryawan)
                            ->where('id_sub_kriteria', $sub->id)
                            ->where('bulan', $bulan)
                            ->where('tahun', $tahun)
                            ->first();

                        $nilaiMentah[$idKaryawan] = $penilaian ? $penilaian->nilai : 0;
                    }

                    // Standardisasi untuk sub-kriteria ini
                    $nilaiSubKriteria[$sub->id] = $this->standardisasiNilai(
                        $nilaiMentah,
                        $sub->tipe_input,
                        $kriteria->tipe_kriteria, // Inherit tipe kriteria dari parent
                        $sub->nilai_min,
                        $sub->nilai_max
                    );
                }

                // Agregasi dengan weighted average (Formula 3.6)
                $skorAgregat = $this->agregasiSubKriteria($nilaiSubKriteria, $bobotSubKriteria);

                foreach ($daftarIdKaryawan as $idKaryawan) {
                    $matriksKeputusan[$idKaryawan][$kriteria->id] = $skorAgregat[$idKaryawan] ?? 0;
                    $nilaiPerKriteria[$idKaryawan][$kriteria->nama_kriteria] = round($skorAgregat[$idKaryawan] ?? 0, 2);
                }
            }
        }

        return [$matriksKeputusan, $nilaiPerKriteria];
    }

    /**
     * Hitung TOPSIS - Implementasi Lengkap Algoritma TOPSIS
     *
     * Mengimplementasikan langkah-langkah TOPSIS dari skripsi:
     * 1. Normalisasi matriks (Formula 3.8 - Vector Normalization)
     * 2. Matriks ternormalisasi terbobot (Formula 3.9)
     * 3. Solusi ideal positif dan negatif (Formula 3.10 dan 3.11)
     * 4. Jarak ke solusi ideal (Formula 3.12 dan 3.13)
     * 5. Nilai preferensi (Formula 3.14)
     *
     * @param array $matriksKeputusan Matriks keputusan yang sudah distandardisasi
     * @param \Illuminate\Support\Collection $daftarKriteria Collection kriteria aktif
     * @return array [matriksTernormalisasi, matriksTerbobot, idealPositif, idealNegatif, jarak, nilaiPreferensi]
     */
    public function hitungTOPSIS(array $matriksKeputusan, $daftarKriteria): array
    {
        $daftarIdKaryawan = array_keys($matriksKeputusan);

        // LANGKAH 1: Normalisasi matriks (Formula 3.8 dari skripsi)
        // r_ij = x_ij / √(Σ x_ij²)
        $matriksTernormalisasi = [];
        foreach ($daftarKriteria as $kriteria) {
            $jumlahKuadrat = 0;
            foreach ($daftarIdKaryawan as $idKaryawan) {
                $jumlahKuadrat += pow($matriksKeputusan[$idKaryawan][$kriteria->id], 2);
            }

            $akarJumlahKuadrat = sqrt($jumlahKuadrat);

            foreach ($daftarIdKaryawan as $idKaryawan) {
                $matriksTernormalisasi[$idKaryawan][$kriteria->id] = $akarJumlahKuadrat > 0
                    ? $matriksKeputusan[$idKaryawan][$kriteria->id] / $akarJumlahKuadrat
                    : 0;
            }
        }

        // LANGKAH 2: Matriks ternormalisasi terbobot (Formula 3.9 dari skripsi)
        // y_ij = w_j × r_ij
        $matriksTerbobot = [];
        foreach ($daftarIdKaryawan as $idKaryawan) {
            foreach ($daftarKriteria as $kriteria) {
                $matriksTerbobot[$idKaryawan][$kriteria->id] =
                    $matriksTernormalisasi[$idKaryawan][$kriteria->id] * ($kriteria->bobot / 100);
            }
        }

        // LANGKAH 3: Solusi Ideal Positif (A+) dan Negatif (A-)
        // Formula 3.10 dan 3.11 dari skripsi
        $idealPositif = [];
        $idealNegatif = [];

        foreach ($daftarKriteria as $kriteria) {
            $nilaiArray = [];
            foreach ($daftarIdKaryawan as $idKaryawan) {
                $nilaiArray[] = $matriksTerbobot[$idKaryawan][$kriteria->id];
            }

            if ($kriteria->tipe_kriteria === 'benefit') {
                // Benefit: A+ = max, A- = min
                $idealPositif[$kriteria->id] = max($nilaiArray);
                $idealNegatif[$kriteria->id] = min($nilaiArray);
            } else {
                // Cost: A+ = min, A- = max
                $idealPositif[$kriteria->id] = min($nilaiArray);
                $idealNegatif[$kriteria->id] = max($nilaiArray);
            }
        }

        // LANGKAH 4: Hitung jarak ke solusi ideal (Formula 3.12 dan 3.13 dari skripsi)
        // D+ = √(Σ(y_ij - A+_j)²)
        // D- = √(Σ(y_ij - A-_j)²)
        $jarak = [];
        foreach ($daftarIdKaryawan as $idKaryawan) {
            $dPlus = 0;  // Jarak ke solusi ideal positif
            $dMinus = 0; // Jarak ke solusi ideal negatif

            foreach ($daftarKriteria as $kriteria) {
                $nilai = $matriksTerbobot[$idKaryawan][$kriteria->id];
                $dPlus += pow($nilai - $idealPositif[$kriteria->id], 2);
                $dMinus += pow($nilai - $idealNegatif[$kriteria->id], 2);
            }

            $jarak[$idKaryawan] = [
                'dPlus' => sqrt($dPlus),
                'dMinus' => sqrt($dMinus),
            ];
        }

        // LANGKAH 5: Hitung nilai preferensi (Formula 3.14 dari skripsi)
        // V = D- / (D+ + D-)
        $nilaiPreferensi = [];
        foreach ($daftarIdKaryawan as $idKaryawan) {
            $dPlus = $jarak[$idKaryawan]['dPlus'];
            $dMinus = $jarak[$idKaryawan]['dMinus'];

            $nilaiPreferensi[$idKaryawan] = ($dPlus + $dMinus) > 0
                ? $dMinus / ($dPlus + $dMinus)
                : 0;
        }

        return [
            'matriksTernormalisasi' => $matriksTernormalisasi,
            'matriksTerbobot' => $matriksTerbobot,
            'idealPositif' => $idealPositif,
            'idealNegatif' => $idealNegatif,
            'jarak' => $jarak,
            'nilaiPreferensi' => $nilaiPreferensi,
        ];
    }
}
