<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat data dummy user untuk testing (HRD, Supervisor, Karyawan)
     * Password semua user: password123
     */
    public function run(): void
    {
        // Get Super Admin untuk relasi
        $superAdmin = User::where('role', 'super_admin')->first();

        if (!$superAdmin) {
            $this->command->error('❌ Super Admin tidak ditemukan! Jalankan SuperAdminSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('🔄 Membuat data user dummy untuk testing...');

        // ========================================
        // HRD (2 user)
        // ========================================
        $hrd1 = User::create([
            'nama' => 'Budi Santoso',
            'username' => 'hrd_budi',
            'password' => 'password123',
            'role' => 'hrd',
            'nik' => 'HRD001',
            'divisi' => 'Human Resource',
            'jabatan' => 'HRD Manager',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $superAdmin->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        $hrd2 = User::create([
            'nama' => 'Siti Nurhaliza',
            'username' => 'hrd_siti',
            'password' => 'password123',
            'role' => 'hrd',
            'nik' => 'HRD002',
            'divisi' => 'Human Resource',
            'jabatan' => 'HRD Staff',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => null,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $superAdmin->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        $this->command->info('  ✓ 2 HRD berhasil dibuat');

        // ========================================
        // SUPERVISOR (3 user)
        // ========================================
        $supervisor1 = User::create([
            'nama' => 'Ahmad Hidayat',
            'username' => 'spv_ahmad',
            'password' => 'password123',
            'role' => 'supervisor',
            'nik' => 'SPV001',
            'divisi' => 'Produksi',
            'jabatan' => 'Supervisor Produksi',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd1->id,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $hrd1->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        $supervisor2 = User::create([
            'nama' => 'Dewi Lestari',
            'username' => 'spv_dewi',
            'password' => 'password123',
            'role' => 'supervisor',
            'nik' => 'SPV002',
            'divisi' => 'Quality Control',
            'jabatan' => 'Supervisor QC',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd1->id,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $hrd1->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        $supervisor3 = User::create([
            'nama' => 'Rudi Hermawan',
            'username' => 'spv_rudi',
            'password' => 'password123',
            'role' => 'supervisor',
            'nik' => 'SPV003',
            'divisi' => 'Warehouse',
            'jabatan' => 'Supervisor Gudang',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd2->id,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $hrd2->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        $this->command->info('  ✓ 3 Supervisor berhasil dibuat');

        // ========================================
        // KARYAWAN (10 user - 5 aktif, 3 pending, 2 rejected)
        // ========================================

        // 5 Karyawan Aktif (Approved)
        User::create([
            'nama' => 'Andi Wijaya',
            'username' => 'karyawan_andi',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY001',
            'divisi' => 'Produksi',
            'jabatan' => 'Operator Produksi',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd1->id,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $hrd1->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        User::create([
            'nama' => 'Sari Indah',
            'username' => 'karyawan_sari',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY002',
            'divisi' => 'Quality Control',
            'jabatan' => 'QC Inspector',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd1->id,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $hrd1->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        User::create([
            'nama' => 'Tono Sumarno',
            'username' => 'karyawan_tono',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY003',
            'divisi' => 'Produksi',
            'jabatan' => 'Operator Mesin',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd2->id,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $hrd2->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        User::create([
            'nama' => 'Linda Kusuma',
            'username' => 'karyawan_linda',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY004',
            'divisi' => 'Warehouse',
            'jabatan' => 'Staff Gudang',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd2->id,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $hrd2->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        User::create([
            'nama' => 'Joko Prasetyo',
            'username' => 'karyawan_joko',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY005',
            'divisi' => 'Produksi',
            'jabatan' => 'Operator Packing',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd1->id,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $hrd1->id,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        // 3 Karyawan Pending (Belum di-approve)
        User::create([
            'nama' => 'Budi Setiawan',
            'username' => 'karyawan_budis',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY006',
            'divisi' => 'Produksi',
            'jabatan' => 'Operator Produksi',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd1->id,
            'status_approval' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
            'status_akun' => 'tidak_aktif',
        ]);

        User::create([
            'nama' => 'Rita Melati',
            'username' => 'karyawan_rita',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY007',
            'divisi' => 'Quality Control',
            'jabatan' => 'QC Staff',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd2->id,
            'status_approval' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
            'status_akun' => 'tidak_aktif',
        ]);

        User::create([
            'nama' => 'Agus Salim',
            'username' => 'karyawan_agus',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY008',
            'divisi' => 'Warehouse',
            'jabatan' => 'Staff Gudang',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd1->id,
            'status_approval' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
            'status_akun' => 'tidak_aktif',
        ]);

        // 2 Karyawan Rejected (Ditolak)
        User::create([
            'nama' => 'Bambang Pamungkas',
            'username' => 'karyawan_bambang',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY009',
            'divisi' => 'Produksi',
            'jabatan' => 'Operator Produksi',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd1->id,
            'status_approval' => 'rejected',
            'approved_at' => null,
            'approved_by' => $hrd1->id,
            'rejection_reason' => 'Dokumen tidak lengkap',
            'status_akun' => 'tidak_aktif',
        ]);

        User::create([
            'nama' => 'Rina Wijayanti',
            'username' => 'karyawan_rina',
            'password' => 'password123',
            'role' => 'karyawan',
            'nik' => 'KRY010',
            'divisi' => 'Quality Control',
            'jabatan' => 'QC Inspector',
            'created_by_super_admin_id' => $superAdmin->id,
            'created_by_hrd_id' => $hrd2->id,
            'status_approval' => 'rejected',
            'approved_at' => null,
            'approved_by' => $hrd2->id,
            'rejection_reason' => 'Tidak memenuhi kualifikasi',
            'status_akun' => 'tidak_aktif',
        ]);

        $this->command->info('  ✓ 10 Karyawan berhasil dibuat (5 aktif, 3 pending, 2 rejected)');

        $this->command->info('');
        $this->command->info('✅ Seeding data user dummy berhasil!');
        $this->command->info('');
        $this->command->info('📋 Ringkasan User:');
        $this->command->info('   • 1 Super Admin (admin / admin123)');
        $this->command->info('   • 2 HRD (hrd_budi / password123, hrd_siti / password123)');
        $this->command->info('   • 3 Supervisor (spv_ahmad / password123, spv_dewi / password123, spv_rudi / password123)');
        $this->command->info('   • 10 Karyawan (karyawan_* / password123)');
        $this->command->info('');
        $this->command->warn('⚠️  Password semua user: password123');
    }
}
