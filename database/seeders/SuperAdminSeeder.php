<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat akun Super Admin default untuk akses awal sistem
     */
    public function run(): void
    {
        // Cek apakah Super Admin sudah ada
        $existingSuperAdmin = User::where('role', 'super_admin')->first();

        if ($existingSuperAdmin) {
            $this->command->info('✅ Super Admin sudah ada, skip seeding.');
            return;
        }

        // Buat Super Admin default
        User::create([
            'nama' => 'Super Administrator',
            'username' => 'admin',
            'password' => 'admin123', // Auto-hashed oleh model
            'role' => 'super_admin',
            'nik' => null,
            'divisi' => null,
            'jabatan' => 'Super Administrator',
            'created_by_super_admin_id' => null,
            'created_by_hrd_id' => null,
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => null,
            'rejection_reason' => null,
            'status_akun' => 'aktif',
        ]);

        $this->command->info('✅ Super Admin berhasil dibuat!');
        $this->command->info('   Username: admin');
        $this->command->info('   Password: admin123');
        $this->command->warn('⚠️  PENTING: Ganti password setelah login pertama kali!');
    }
}
