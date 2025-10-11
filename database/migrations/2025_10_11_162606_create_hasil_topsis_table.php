<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel hasil_topsis untuk menyimpan hasil perhitungan algoritma TOPSIS
     */
    public function up(): void
    {
        Schema::create('hasil_topsis', function (Blueprint $table) {
            $table->id();

            // Relasi ke karyawan
            $table->foreignId('id_karyawan')->constrained('users')->cascadeOnDelete();

            // Periode perhitungan
            $table->integer('bulan'); // 1-12
            $table->integer('tahun'); // 2024, 2025, dst
            $table->string('periode_label')->nullable(); // "Januari 2024"

            // Hasil perhitungan TOPSIS
            $table->decimal('skor_topsis', 10, 6); // Skor preferensi (V)
            $table->integer('ranking'); // Peringkat (1, 2, 3, dst)

            // Detail perhitungan
            $table->decimal('jarak_ideal_positif', 10, 6)->nullable(); // D+ (jarak ke solusi ideal positif)
            $table->decimal('jarak_ideal_negatif', 10, 6)->nullable(); // D- (jarak ke solusi ideal negatif)

            // Breakdown nilai per kriteria (JSON untuk fleksibilitas)
            $table->json('nilai_per_kriteria')->nullable(); // {"presensi": 85.5, "catatan_buruk": 90.2, ...}
            $table->json('detail_perhitungan')->nullable(); // Detail lengkap perhitungan TOPSIS

            // Tanggal generate ranking (WIB)
            $table->dateTime('tanggal_generate');

            // Tracking siapa yang generate (hanya Super Admin atau HRD)
            // Salah satu dari kolom ini HARUS terisi
            $table->foreignId('generated_by_super_admin_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('generated_by_hrd_id')->nullable()->constrained('users')->cascadeOnDelete();

            // Prevent duplicate (satu karyawan per periode hanya punya satu hasil)
            $table->unique(['id_karyawan', 'bulan', 'tahun'], 'unique_hasil_topsis');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_topsis');
    }
};
