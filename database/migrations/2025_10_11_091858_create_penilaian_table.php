<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel penilaian untuk menyimpan input penilaian karyawan dari supervisor
     */
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();

            // Relasi ke karyawan yang dinilai
            $table->foreignId('id_karyawan')->constrained('users')->cascadeOnDelete();

            // Relasi ke kriteria (kriteria utama)
            $table->foreignId('id_kriteria')->constrained('sistem_kriteria')->cascadeOnDelete();

            // Relasi ke sub-kriteria (nullable karena ada kasus kriteria tanpa sub)
            $table->foreignId('id_sub_kriteria')->nullable()->constrained('sistem_kriteria')->cascadeOnDelete();

            // Nilai penilaian (fleksibel untuk angka, rating, atau ID dropdown)
            $table->decimal('nilai', 10, 2);

            // Periode penilaian
            $table->integer('bulan'); // 1-12
            $table->integer('tahun'); // 2024, 2025, dst
            $table->string('periode_label')->nullable(); // "Januari 2024"

            // Catatan/komentar (opsional)
            $table->text('catatan')->nullable();

            // Tanggal saat penilaian dilakukan (timezone: Asia/Jakarta - WIB)
            $table->dateTime('tanggal_penilaian');

            // Tracking siapa yang input penilaian
            $table->foreignId('dinilai_oleh_supervisor_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_super_admin_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_hrd_id')->nullable()->constrained('users')->cascadeOnDelete();

            // Prevent duplicate (satu karyawan, satu sub-kriteria, satu periode = satu penilaian)
            $table->unique(['id_karyawan', 'id_sub_kriteria', 'bulan', 'tahun'], 'unique_penilaian');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
