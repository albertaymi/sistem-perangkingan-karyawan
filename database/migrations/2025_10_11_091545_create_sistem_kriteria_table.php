<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel sistem_kriteria dengan tree structure untuk kriteria, sub-kriteria, dropdown options
     */
    public function up(): void
    {
        Schema::create('sistem_kriteria', function (Blueprint $table) {
            $table->id();

            // Tree structure (self-referencing)
            $table->foreignId('id_parent')->nullable()->constrained('sistem_kriteria')->cascadeOnDelete();

            // Data kriteria
            $table->string('nama_kriteria');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_kriteria', ['benefit', 'cost'])->nullable(); // Untuk kriteria utama
            $table->decimal('bobot', 5, 2)->nullable(); // Bobot dalam persen (0-100)

            // Tipe input untuk sub-kriteria
            $table->enum('tipe_input', ['angka', 'rating', 'dropdown'])->nullable();

            // Range nilai untuk validasi (khusus tipe angka)
            $table->decimal('nilai_min', 10, 2)->nullable();
            $table->decimal('nilai_max', 10, 2)->nullable();

            // Nilai tetap untuk dropdown options
            $table->decimal('nilai_tetap', 10, 2)->nullable();

            // Level dalam tree (1=kriteria utama, 2=sub-kriteria, 3=dropdown option)
            $table->integer('level')->default(1);

            // Urutan tampilan
            $table->integer('urutan')->default(0);

            // Assignment ke supervisor (untuk kriteria utama)
            $table->foreignId('assigned_to_supervisor_id')->nullable()->constrained('users')->nullOnDelete();

            // Relasi creator
            $table->foreignId('created_by_super_admin_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_hrd_id')->nullable()->constrained('users')->cascadeOnDelete();

            // Status aktif/tidak aktif
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistem_kriteria');
    }
};
