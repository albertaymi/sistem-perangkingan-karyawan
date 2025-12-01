<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat data user sesuai dengan contoh di skripsi (Tabel 3.2)
     * - A1: Budi Santoso
     * - A2: Sari Wulandari  
     * - A3: Andi Pratama
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

        $this->command->info('🔄 Membuat data user sesuai skripsi (Tabel 3.2 Data Alternatif)...');

        // ========================================
        // HRD Manager
        // ========================================
        $hrd = User::updateOrCreate(
            ['username' => 'hrd_manager'],
            [
                'nama' => 'Manager HRD',
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
            ]
        );

        $this->command->info('  ✓ HRD Manager berhasil dibuat');

        // ========================================
        // SUPERVISOR
        // ========================================
        $supervisor = User::updateOrCreate(
            ['username' => 'supervisor'],
            [
                'nama' => 'Supervisor Produksi',
                'password' => 'password123',
                'role' => 'supervisor',
                'nik' => 'SPV001',
                'divisi' => 'Produksi',
                'jabatan' => 'Supervisor',
                'created_by_super_admin_id' => $superAdmin->id,
                'created_by_hrd_id' => $hrd->id,
                'status_approval' => 'approved',
                'approved_at' => now(),
                'approved_by' => $hrd->id,
                'rejection_reason' => null,
                'status_akun' => 'aktif',
            ]
        );

        $this->command->info('  ✓ Supervisor berhasil dibuat');

        // ========================================
        // KARYAWAN (3 Alternatif sesuai Tabel 3.2)
        // ========================================

        // A1: Budi Santoso
        User::updateOrCreate(
            ['username' => 'budi_santoso'],
            [
                'nama' => 'Budi Santoso',
                'password' => 'password123',
                'role' => 'karyawan',
                'nik' => 'A1',
                'divisi' => 'Produksi',
                'jabatan' => 'Operator Produksi',
                'created_by_super_admin_id' => $superAdmin->id,
                'created_by_hrd_id' => $hrd->id,
                'status_approval' => 'approved',
                'approved_at' => now(),
                'approved_by' => $hrd->id,
                'rejection_reason' => null,
                'status_akun' => 'aktif',
            ]
        );

        // A2: Sari Wulandari
        User::updateOrCreate(
            ['username' => 'sari_wulandari'],
            [
                'nama' => 'Sari Wulandari',
                'password' => 'password123',
                'role' => 'karyawan',
                'nik' => 'A2',
                'divisi' => 'Produksi',
                'jabatan' => 'Operator Produksi',
                'created_by_super_admin_id' => $superAdmin->id,
                'created_by_hrd_id' => $hrd->id,
                'status_approval' => 'approved',
                'approved_at' => now(),
                'approved_by' => $hrd->id,
                'rejection_reason' => null,
                'status_akun' => 'aktif',
            ]
        );

        // A3: Andi Pratama
        User::updateOrCreate(
            ['username' => 'andi_pratama'],
            [
                'nama' => 'Andi Pratama',
                'password' => 'password123',
                'role' => 'karyawan',
                'nik' => 'A3',
                'divisi' => 'Produksi',
                'jabatan' => 'Operator Produksi',
                'created_by_super_admin_id' => $superAdmin->id,
                'created_by_hrd_id' => $hrd->id,
                'status_approval' => 'approved',
                'approved_at' => now(),
                'approved_by' => $hrd->id,
                'rejection_reason' => null,
                'status_akun' => 'aktif',
            ]
        );

        $this->command->info('  ✓ 3 Karyawan berhasil dibuat (A1, A2, A3)');

        $this->command->info('');
        $this->command->info('✅ Seeding data user sesuai skripsi berhasil!');
        $this->command->info('');
        $this->command->info('📋 User yang dibuat (Tabel 3.2 Data Alternatif):');
        $this->command->info('   • HRD Manager (hrd_manager / password123)');
        $this->command->info('   • Supervisor (supervisor / password123)');
        $this->command->info('   • A1 - Budi Santoso (budi_santoso / password123)');
        $this->command->info('   • A2 - Sari Wulandari (sari_wulandari / password123)');
        $this->command->info('   • A3 - Andi Pratama (andi_pratama / password123)');
        $this->command->info('');
        $this->command->warn('⚠️  Password semua user: password123');
    }
}