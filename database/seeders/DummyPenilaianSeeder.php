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
     * Membuat data penilaian sesuai dengan contoh perhitungan di skripsi (Tabel 3.3 - 3.6)
     * Periode: Desember 2025
     */
    public function run(): void
    {
        $this->command->info('🔄 Membuat data penilaian sesuai skripsi untuk Desember 2025...');

        $bulan = 12;
        $tahun = 2025;

        // Get karyawan by NIK
        $budiSantoso = User::where('nik', 'A1')->first();
        $sariWulandari = User::where('nik', 'A2')->first();
        $andiPratama = User::where('nik', 'A3')->first();

        if (!$budiSantoso || !$sariWulandari || !$andiPratama) {
            $this->command->error('❌ Karyawan tidak ditemukan! Jalankan DummyUserSeeder terlebih dahulu.');
            return;
        }

        // Get supervisor
        $supervisor = User::where('role', 'supervisor')->first();

        if (!$supervisor) {
            $this->command->error('❌ Supervisor tidak ditemukan!');
            return;
        }

        // Get kriteria
        $presensi = SistemKriteria::where('nama_kriteria', 'Presensi')->where('level', 1)->first();
        $catatanBuruk = SistemKriteria::where('nama_kriteria', 'Catatan Buruk')->where('level', 1)->first();
        $kinerja = SistemKriteria::where('nama_kriteria', 'Kinerja')->where('level', 1)->first();
        $mcu = SistemKriteria::where('nama_kriteria', 'MCU (Medical Check Up)')->where('level', 1)->first();

        if (!$presensi || !$catatanBuruk || !$kinerja || !$mcu) {
            $this->command->error('❌ Kriteria tidak ditemukan! Jalankan KriteriaSeeder terlebih dahulu.');
            return;
        }

        // Hapus penilaian lama untuk periode yang sama
        Penilaian::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereIn('id_karyawan', [$budiSantoso->id, $sariWulandari->id, $andiPratama->id])
            ->delete();

        // ========================================
        // PENILAIAN BUDI SANTOSO (A1)
        // ========================================
        $this->createPenilaianPresensi($presensi, $budiSantoso->id, $bulan, $tahun, $supervisor->id, [
            'Kehadiran' => 20,
            'Alpha/Tanpa Keterangan' => 1,
            'Keterlambatan' => 3,
        ]);

        $this->createPenilaianCatatanBuruk($catatanBuruk, $budiSantoso->id, $bulan, $tahun, $supervisor->id, [
            'SP1 (Surat Peringatan 1)' => 1,
            'SP2 (Surat Peringatan 2)' => 0,
            'SP3 (Surat Peringatan 3)' => 0,
            'Pelanggaran Ringan' => 1,
        ]);

        $this->createPenilaianKinerja($kinerja, $budiSantoso->id, $bulan, $tahun, $supervisor->id, [
            'Kualitas Kerja' => 4,
            'Produktivitas' => 3,
            'Ketepatan Waktu' => 5,
            'Inisiatif' => 3,
            'Teamwork' => 4,
        ]);

        $this->createPenilaianMCU($mcu, $budiSantoso->id, $bulan, $tahun, $supervisor->id, 'Lolos');

        $this->command->info('  ✓ Penilaian A1 - Budi Santoso selesai');

        // ========================================
        // PENILAIAN SARI WULANDARI (A2)
        // ========================================
        $this->createPenilaianPresensi($presensi, $sariWulandari->id, $bulan, $tahun, $supervisor->id, [
            'Kehadiran' => 22,
            'Alpha/Tanpa Keterangan' => 0,
            'Keterlambatan' => 1,
        ]);

        $this->createPenilaianCatatanBuruk($catatanBuruk, $sariWulandari->id, $bulan, $tahun, $supervisor->id, [
            'SP1 (Surat Peringatan 1)' => 0,
            'SP2 (Surat Peringatan 2)' => 0,
            'SP3 (Surat Peringatan 3)' => 0,
            'Pelanggaran Ringan' => 1,
        ]);

        $this->createPenilaianKinerja($kinerja, $sariWulandari->id, $bulan, $tahun, $supervisor->id, [
            'Kualitas Kerja' => 5,
            'Produktivitas' => 4,
            'Ketepatan Waktu' => 4,
            'Inisiatif' => 4,
            'Teamwork' => 5,
        ]);

        $this->createPenilaianMCU($mcu, $sariWulandari->id, $bulan, $tahun, $supervisor->id, 'Lolos');

        $this->command->info('  ✓ Penilaian A2 - Sari Wulandari selesai');

        // ========================================
        // PENILAIAN ANDI PRATAMA (A3)
        // ========================================
        $this->createPenilaianPresensi($presensi, $andiPratama->id, $bulan, $tahun, $supervisor->id, [
            'Kehadiran' => 18,
            'Alpha/Tanpa Keterangan' => 2,
            'Keterlambatan' => 5,
        ]);

        $this->createPenilaianCatatanBuruk($catatanBuruk, $andiPratama->id, $bulan, $tahun, $supervisor->id, [
            'SP1 (Surat Peringatan 1)' => 2,
            'SP2 (Surat Peringatan 2)' => 1,
            'SP3 (Surat Peringatan 3)' => 0,
            'Pelanggaran Ringan' => 2,
        ]);

        $this->createPenilaianKinerja($kinerja, $andiPratama->id, $bulan, $tahun, $supervisor->id, [
            'Kualitas Kerja' => 3,
            'Produktivitas' => 5,
            'Ketepatan Waktu' => 3,
            'Inisiatif' => 5,
            'Teamwork' => 3,
        ]);

        $this->createPenilaianMCU($mcu, $andiPratama->id, $bulan, $tahun, $supervisor->id, 'Lolos dengan Catatan');

        $this->command->info('  ✓ Penilaian A3 - Andi Pratama selesai');

        $this->command->info('');
        $this->command->info('✅ Seeding penilaian sesuai skripsi berhasil!');
        $this->command->info('   Periode: Desember 2025');
        $this->command->info('   Total: 3 karyawan (A1, A2, A3)');
        $this->command->info('');
        $this->command->info('📊 Data Penilaian (sesuai Tabel 3.3 - 3.6):');
        $this->command->info('');
        $this->command->info('   C1 (Presensi) - Tabel 3.3:');
        $this->command->info('     • A1: Kehadiran=20, Keterlambatan=3, Alpha=1');
        $this->command->info('     • A2: Kehadiran=22, Keterlambatan=1, Alpha=0');
        $this->command->info('     • A3: Kehadiran=18, Keterlambatan=5, Alpha=2');
        $this->command->info('');
        $this->command->info('   C2 (Catatan Buruk) - Tabel 3.4:');
        $this->command->info('     • A1: SP1=1, SP2=0, SP3=0, Pelanggaran=1');
        $this->command->info('     • A2: SP1=0, SP2=0, SP3=0, Pelanggaran=1');
        $this->command->info('     • A3: SP1=2, SP2=1, SP3=0, Pelanggaran=2');
        $this->command->info('');
        $this->command->info('   C3 (Kinerja) - Tabel 3.5:');
        $this->command->info('     • A1: Kualitas=4, Produktivitas=3, Ketepatan=5, Inisiatif=3, Teamwork=4');
        $this->command->info('     • A2: Kualitas=5, Produktivitas=4, Ketepatan=4, Inisiatif=4, Teamwork=5');
        $this->command->info('     • A3: Kualitas=3, Produktivitas=5, Ketepatan=3, Inisiatif=5, Teamwork=3');
        $this->command->info('');
        $this->command->info('   C4 (MCU) - Tabel 3.6:');
        $this->command->info('     • A1: Lolos (100)');
        $this->command->info('     • A2: Lolos (100)');
        $this->command->info('     • A3: Lolos dengan Catatan (75)');
    }

    /**
     * Create penilaian untuk kriteria Presensi
     */
    private function createPenilaianPresensi($kriteria, $idKaryawan, $bulan, $tahun, $supervisorId, $data)
    {
        $subKriteriaMap = [
            'Kehadiran' => 'Kehadiran',
            'Alpha/Tanpa Keterangan' => 'Alpha/Tanpa Keterangan',
            'Keterlambatan' => 'Keterlambatan',
        ];

        foreach ($subKriteriaMap as $key => $namaSubKriteria) {
            $subKriteria = SistemKriteria::where('id_parent', $kriteria->id)
                ->where('nama_kriteria', $namaSubKriteria)
                ->first();

            if ($subKriteria) {
                Penilaian::create([
                    'id_karyawan' => $idKaryawan,
                    'id_kriteria' => $kriteria->id,
                    'id_sub_kriteria' => $subKriteria->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nilai' => $data[$key],
                    'catatan' => 'Data sesuai Tabel 3.3 skripsi',
                    'tanggal_penilaian' => now(),
                    'created_by_super_admin_id' => null,
                    'created_by_hrd_id' => null,
                    'dinilai_oleh_supervisor_id' => $supervisorId,
                ]);
            }
        }
    }

    /**
     * Create penilaian untuk kriteria Catatan Buruk
     */
    private function createPenilaianCatatanBuruk($kriteria, $idKaryawan, $bulan, $tahun, $supervisorId, $data)
    {
        $subKriteriaMap = [
            'SP1 (Surat Peringatan 1)' => 'SP1 (Surat Peringatan 1)',
            'SP2 (Surat Peringatan 2)' => 'SP2 (Surat Peringatan 2)',
            'SP3 (Surat Peringatan 3)' => 'SP3 (Surat Peringatan 3)',
            'Pelanggaran Ringan' => 'Pelanggaran Ringan',
        ];

        foreach ($subKriteriaMap as $key => $namaSubKriteria) {
            $subKriteria = SistemKriteria::where('id_parent', $kriteria->id)
                ->where('nama_kriteria', $namaSubKriteria)
                ->first();

            if ($subKriteria) {
                Penilaian::create([
                    'id_karyawan' => $idKaryawan,
                    'id_kriteria' => $kriteria->id,
                    'id_sub_kriteria' => $subKriteria->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nilai' => $data[$key],
                    'catatan' => 'Data sesuai Tabel 3.4 skripsi',
                    'tanggal_penilaian' => now(),
                    'created_by_super_admin_id' => null,
                    'created_by_hrd_id' => null,
                    'dinilai_oleh_supervisor_id' => $supervisorId,
                ]);
            }
        }
    }

    /**
     * Create penilaian untuk kriteria Kinerja (Rating 1-5)
     */
    private function createPenilaianKinerja($kriteria, $idKaryawan, $bulan, $tahun, $supervisorId, $data)
    {
        $subKriteriaMap = [
            'Kualitas Kerja' => 'Kualitas Kerja',
            'Produktivitas' => 'Produktivitas',
            'Ketepatan Waktu' => 'Ketepatan Waktu',
            'Inisiatif' => 'Inisiatif',
            'Teamwork' => 'Teamwork',
        ];

        foreach ($subKriteriaMap as $key => $namaSubKriteria) {
            $subKriteria = SistemKriteria::where('id_parent', $kriteria->id)
                ->where('nama_kriteria', $namaSubKriteria)
                ->first();

            if ($subKriteria) {
                Penilaian::create([
                    'id_karyawan' => $idKaryawan,
                    'id_kriteria' => $kriteria->id,
                    'id_sub_kriteria' => $subKriteria->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nilai' => $data[$key],
                    'catatan' => 'Data sesuai Tabel 3.5 skripsi (Rating Scale 1-5)',
                    'tanggal_penilaian' => now(),
                    'created_by_super_admin_id' => null,
                    'created_by_hrd_id' => null,
                    'dinilai_oleh_supervisor_id' => $supervisorId,
                ]);
            }
        }
    }

    /**
     * Create penilaian untuk kriteria MCU (Dropdown)
     */
    private function createPenilaianMCU($kriteria, $idKaryawan, $bulan, $tahun, $supervisorId, $statusMCU)
    {
        // Get sub-kriteria Status MCU
        $subKriteria = SistemKriteria::where('id_parent', $kriteria->id)
            ->where('nama_kriteria', 'Status MCU')
            ->first();

        if (!$subKriteria) {
            return;
        }

        // Get dropdown option based on status
        $dropdownOption = SistemKriteria::where('id_parent', $subKriteria->id)
            ->where('nama_kriteria', $statusMCU)
            ->first();

        if ($dropdownOption) {
            // Nilai untuk dropdown adalah nilai_tetap dari option yang dipilih
            Penilaian::create([
                'id_karyawan' => $idKaryawan,
                'id_kriteria' => $kriteria->id,
                'id_sub_kriteria' => $subKriteria->id, // ID dari Status MCU (level 2), bukan dropdown option (level 3)
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nilai' => $dropdownOption->nilai_tetap,
                'catatan' => "Data sesuai Tabel 3.6 skripsi (Status: {$statusMCU})",
                'tanggal_penilaian' => now(),
                'created_by_super_admin_id' => null,
                'created_by_hrd_id' => null,
                'dinilai_oleh_supervisor_id' => $supervisorId,
            ]);
        }
    }
}
