<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SistemKriteria;
use App\Models\User;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat data kriteria sesuai project brief:
     * 1. Presensi (Benefit, 25%)
     * 2. Catatan Buruk (Cost, 25%)
     * 3. Kinerja (Benefit, 25%)
     * 4. MCU (Benefit, 25%)
     */
    public function run(): void
    {
        // Get Super Admin untuk creator
        $superAdmin = User::where('role', 'super_admin')->first();

        if (!$superAdmin) {
            $this->command->error('❌ Super Admin tidak ditemukan! Jalankan SuperAdminSeeder terlebih dahulu.');
            return;
        }

        // Cek apakah kriteria sudah ada
        $existingKriteria = SistemKriteria::kriteriaUtama()->count();
        if ($existingKriteria > 0) {
            $this->command->info('✅ Kriteria sudah ada, skip seeding.');
            return;
        }

        $this->command->info('🔄 Membuat kriteria penilaian...');

        // ========================================
        // KRITERIA 1: PRESENSI (Benefit, 25%)
        // ========================================
        $presensi = SistemKriteria::create([
            'id_parent' => null,
            'nama_kriteria' => 'Presensi',
            'deskripsi' => 'Penilaian kehadiran dan kedisiplinan karyawan',
            'tipe_kriteria' => 'benefit',
            'bobot' => 25,
            'tipe_input' => null,
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => null,
            'level' => 1,
            'urutan' => 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        // Sub-kriteria Presensi
        SistemKriteria::create([
            'id_parent' => $presensi->id,
            'nama_kriteria' => 'Kehadiran',
            'deskripsi' => 'Jumlah hari hadir dalam sebulan (0-22 hari)',
            'tipe_kriteria' => 'benefit',
            'bobot' => 50,
            'tipe_input' => 'angka',
            'nilai_min' => 0,
            'nilai_max' => 22,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $presensi->id,
            'nama_kriteria' => 'Alpha/Tanpa Keterangan',
            'deskripsi' => 'Jumlah tidak masuk tanpa keterangan (0-5 kali)',
            'tipe_kriteria' => 'cost',
            'bobot' => 35,
            'tipe_input' => 'angka',
            'nilai_min' => 0,
            'nilai_max' => 5,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 2,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $presensi->id,
            'nama_kriteria' => 'Keterlambatan',
            'deskripsi' => 'Jumlah keterlambatan dalam sebulan (0-10 kali)',
            'tipe_kriteria' => 'cost',
            'bobot' => 15,
            'tipe_input' => 'angka',
            'nilai_min' => 0,
            'nilai_max' => 10,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 3,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        $this->command->info('  ✓ Kriteria Presensi dengan 3 sub-kriteria');

        // ========================================
        // KRITERIA 2: CATATAN BURUK (Cost, 25%)
        // ========================================
        $catatanBuruk = SistemKriteria::create([
            'id_parent' => null,
            'nama_kriteria' => 'Catatan Buruk',
            'deskripsi' => 'Penilaian surat peringatan dan pelanggaran',
            'tipe_kriteria' => 'cost',
            'bobot' => 25,
            'tipe_input' => null,
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => null,
            'level' => 1,
            'urutan' => 2,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        // Sub-kriteria Catatan Buruk
        SistemKriteria::create([
            'id_parent' => $catatanBuruk->id,
            'nama_kriteria' => 'SP1 (Surat Peringatan 1)',
            'deskripsi' => 'Jumlah SP1 yang diterima (0-3 kali)',
            'tipe_kriteria' => 'cost',
            'bobot' => 20,
            'tipe_input' => 'angka',
            'nilai_min' => 0,
            'nilai_max' => 3,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $catatanBuruk->id,
            'nama_kriteria' => 'SP2 (Surat Peringatan 2)',
            'deskripsi' => 'Jumlah SP2 yang diterima (0-2 kali)',
            'tipe_kriteria' => 'cost',
            'bobot' => 30,
            'tipe_input' => 'angka',
            'nilai_min' => 0,
            'nilai_max' => 2,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 2,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $catatanBuruk->id,
            'nama_kriteria' => 'SP3 (Surat Peringatan 3)',
            'deskripsi' => 'Jumlah SP3 yang diterima (0-1 kali)',
            'tipe_kriteria' => 'cost',
            'bobot' => 40,
            'tipe_input' => 'angka',
            'nilai_min' => 0,
            'nilai_max' => 1,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 3,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $catatanBuruk->id,
            'nama_kriteria' => 'Pelanggaran Ringan',
            'deskripsi' => 'Jumlah pelanggaran ringan (0-5 kali)',
            'tipe_kriteria' => 'cost',
            'bobot' => 10,
            'tipe_input' => 'angka',
            'nilai_min' => 0,
            'nilai_max' => 5,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 4,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        $this->command->info('  ✓ Kriteria Catatan Buruk dengan 4 sub-kriteria');

        // ========================================
        // KRITERIA 3: KINERJA (Benefit, 25%)
        // ========================================
        $kinerja = SistemKriteria::create([
            'id_parent' => null,
            'nama_kriteria' => 'Kinerja',
            'deskripsi' => 'Penilaian kinerja dan produktivitas karyawan',
            'tipe_kriteria' => 'benefit',
            'bobot' => 25,
            'tipe_input' => null,
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => null,
            'level' => 1,
            'urutan' => 3,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        // Sub-kriteria Kinerja
        SistemKriteria::create([
            'id_parent' => $kinerja->id,
            'nama_kriteria' => 'Kualitas Kerja',
            'deskripsi' => 'Penilaian kualitas hasil kerja (Rating 1-5)',
            'tipe_kriteria' => 'benefit',
            'bobot' => 30,
            'tipe_input' => 'rating',
            'nilai_min' => 1,
            'nilai_max' => 5,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $kinerja->id,
            'nama_kriteria' => 'Produktivitas',
            'deskripsi' => 'Penilaian produktivitas kerja (Rating 1-5)',
            'tipe_kriteria' => 'benefit',
            'bobot' => 25,
            'tipe_input' => 'rating',
            'nilai_min' => 1,
            'nilai_max' => 5,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 2,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $kinerja->id,
            'nama_kriteria' => 'Ketepatan Waktu',
            'deskripsi' => 'Penilaian ketepatan waktu penyelesaian tugas (Rating 1-5)',
            'tipe_kriteria' => 'benefit',
            'bobot' => 20,
            'tipe_input' => 'rating',
            'nilai_min' => 1,
            'nilai_max' => 5,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 3,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $kinerja->id,
            'nama_kriteria' => 'Inisiatif',
            'deskripsi' => 'Penilaian inisiatif dan proaktif (Rating 1-5)',
            'tipe_kriteria' => 'benefit',
            'bobot' => 15,
            'tipe_input' => 'rating',
            'nilai_min' => 1,
            'nilai_max' => 5,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 4,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $kinerja->id,
            'nama_kriteria' => 'Teamwork',
            'deskripsi' => 'Penilaian kerjasama tim (Rating 1-5)',
            'tipe_kriteria' => 'benefit',
            'bobot' => 10,
            'tipe_input' => 'rating',
            'nilai_min' => 1,
            'nilai_max' => 5,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 5,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        $this->command->info('  ✓ Kriteria Kinerja dengan 5 sub-kriteria');

        // ========================================
        // KRITERIA 4: MCU (Benefit, 25%)
        // ========================================
        $mcu = SistemKriteria::create([
            'id_parent' => null,
            'nama_kriteria' => 'MCU (Medical Check Up)',
            'deskripsi' => 'Hasil pemeriksaan kesehatan karyawan',
            'tipe_kriteria' => 'benefit',
            'bobot' => 25,
            'tipe_input' => null,
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => null,
            'level' => 1,
            'urutan' => 4,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        // Sub-kriteria MCU (hanya 1, dengan dropdown options)
        $statusMcu = SistemKriteria::create([
            'id_parent' => $mcu->id,
            'nama_kriteria' => 'Status MCU',
            'deskripsi' => 'Status hasil Medical Check Up',
            'tipe_kriteria' => 'benefit',
            'bobot' => 100,
            'tipe_input' => 'dropdown',
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => null,
            'level' => 2,
            'urutan' => 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        // Dropdown options untuk Status MCU
        SistemKriteria::create([
            'id_parent' => $statusMcu->id,
            'nama_kriteria' => 'Lolos',
            'deskripsi' => 'Hasil MCU lolos tanpa catatan',
            'tipe_kriteria' => null,
            'bobot' => null,
            'tipe_input' => null,
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => 100,
            'level' => 3,
            'urutan' => 1,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        SistemKriteria::create([
            'id_parent' => $statusMcu->id,
            'nama_kriteria' => 'Lolos dengan Catatan',
            'deskripsi' => 'Hasil MCU lolos namun ada catatan khusus',
            'tipe_kriteria' => null,
            'bobot' => null,
            'tipe_input' => null,
            'nilai_min' => null,
            'nilai_max' => null,
            'nilai_tetap' => 75,
            'level' => 3,
            'urutan' => 2,
            'assigned_to_supervisor_id' => null,
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'is_active' => true,
        ]);

        $this->command->info('  ✓ Kriteria MCU dengan 1 sub-kriteria dan 2 dropdown options');

        $this->command->info('');
        $this->command->info('✅ Seeding kriteria berhasil!');
        $this->command->info('   Total: 4 kriteria utama, 13 sub-kriteria, 2 dropdown options');
    }
}