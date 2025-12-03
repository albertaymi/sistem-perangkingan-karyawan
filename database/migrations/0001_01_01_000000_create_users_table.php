<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel users untuk 4 role: super_admin, hrd, supervisor, karyawan
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Data dasar pengguna
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['super_admin', 'hrd', 'supervisor', 'karyawan'])->default('karyawan');

            // Data tambahan untuk karyawan
            $table->string('nik')->nullable()->unique(); // Nomor Induk Karyawan
            $table->string('divisi')->nullable(); // Divisi/Departemen
            $table->string('jabatan')->nullable(); // Jabatan

            // Relasi ke Super Admin dan HRD (creator/pengelola)
            $table->foreignId('created_by_super_admin_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_hrd_id')->nullable()->constrained('users')->cascadeOnDelete();

            // Relasi ke Supervisor (untuk karyawan yang ditugaskan ke supervisor)
            $table->foreignId('assigned_to_supervisor_id')->nullable()->constrained('users')->nullOnDelete();

            // Status approval untuk registrasi baru
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();

            // Status akun (aktif/tidak aktif)
            $table->enum('status_akun', ['aktif', 'tidak_aktif'])->default('aktif');

            // Standar Laravel
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk keamanan data
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
