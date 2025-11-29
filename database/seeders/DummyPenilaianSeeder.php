<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penilaian;
use App\Models\User;
use App\Models\SistemKriteria;

class DummyPenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat data penilaian dummy untuk testing TOPSIS
     */
    public function run(): void
    {
        echo "🔄 Membuat data penilaian dummy untuk November 2025...\n";

        $bulan = 11;
        $tahun = 2025;

        // Get all active karyawan
        $karyawanList = User::where('role', 'karyawan')
            ->where('status_akun', 'aktif')
            ->get();

        // Get all active kriteria level 1
        $kriteriaList = SistemKriteria::where('level', 1)
            ->where('is_active', true)
            ->with('subKriteria')
            ->get();

        $totalPenilaian = 0;

        foreach ($karyawanList as $karyawan) {
            foreach ($kriteriaList as $kriteria) {
                // Check if this is a single kriteria or has sub-kriteria
                $isSingleKriteria = !empty($kriteria->tipe_input);

                if ($isSingleKriteria) {
                    // Single kriteria: Create one penilaian with id_sub_kriteria = NULL
                    $nilai = rand(60, 100); // Random nilai between 60-100

                    Penilaian::create([
                        'id_karyawan' => $karyawan->id,
                        'id_kriteria' => $kriteria->id,
                        'id_sub_kriteria' => null,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'nilai' => $nilai,
                        'catatan' => 'Dummy data for testing',
                        'tanggal_penilaian' => now(),
                        'created_by_super_admin_id' => 1,
                        'created_by_hrd_id' => null,
                        'dinilai_oleh_supervisor_id' => null,
                    ]);

                    $totalPenilaian++;
                } else {
                    // Multi sub-kriteria: Create penilaian for each sub-kriteria
                    $subKriteriaList = $kriteria->subKriteria()->where('is_active', true)->get();

                    foreach ($subKriteriaList as $subKriteria) {
                        $nilai = rand(60, 100); // Random nilai between 60-100

                        Penilaian::create([
                            'id_karyawan' => $karyawan->id,
                            'id_kriteria' => $kriteria->id,
                            'id_sub_kriteria' => $subKriteria->id,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                            'nilai' => $nilai,
                            'catatan' => 'Dummy data for testing',
                            'tanggal_penilaian' => now(),
                            'created_by_super_admin_id' => 1,
                            'created_by_hrd_id' => null,
                            'dinilai_oleh_supervisor_id' => null,
                        ]);

                        $totalPenilaian++;
                    }
                }
            }

            echo "  ✓ Penilaian untuk {$karyawan->nama} berhasil dibuat\n";
        }

        echo "\n✅ Seeding penilaian dummy berhasil!\n";
        echo "   Total: {$totalPenilaian} penilaian untuk {$karyawanList->count()} karyawan\n";
        echo "   Periode: November 2025\n";
    }
}
