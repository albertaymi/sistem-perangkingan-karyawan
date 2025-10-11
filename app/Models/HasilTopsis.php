<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HasilTopsis extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel di database
     */
    protected $table = 'hasil_topsis';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'id_karyawan',
        'bulan',
        'tahun',
        'periode_label',
        'skor_topsis',
        'ranking',
        'jarak_ideal_positif',
        'jarak_ideal_negatif',
        'nilai_per_kriteria',
        'detail_perhitungan',
        'tanggal_generate',
        'generated_by_super_admin_id',
        'generated_by_hrd_id',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'skor_topsis' => 'decimal:6',
        'ranking' => 'integer',
        'jarak_ideal_positif' => 'decimal:6',
        'jarak_ideal_negatif' => 'decimal:6',
        'nilai_per_kriteria' => 'array', // JSON -> Array
        'detail_perhitungan' => 'array', // JSON -> Array
        'tanggal_generate' => 'datetime',
    ];

    /**
     * Relasi: Karyawan yang diranking
     */
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'id_karyawan');
    }

    /**
     * Relasi: Di-generate oleh Super Admin
     */
    public function generatedBySuperAdmin()
    {
        return $this->belongsTo(User::class, 'generated_by_super_admin_id');
    }

    /**
     * Relasi: Di-generate oleh HRD
     */
    public function generatedByHRD()
    {
        return $this->belongsTo(User::class, 'generated_by_hrd_id');
    }

    /**
     * Helper: Get nama karyawan
     */
    public function getNamaKaryawanAttribute()
    {
        return $this->karyawan->nama ?? '-';
    }

    /**
     * Helper: Get NIK karyawan
     */
    public function getNikKaryawanAttribute()
    {
        return $this->karyawan->nik ?? '-';
    }

    /**
     * Helper: Get divisi karyawan
     */
    public function getDivisiKaryawanAttribute()
    {
        return $this->karyawan->divisi ?? '-';
    }

    /**
     * Helper: Get periode lengkap (contoh: "Januari 2025")
     */
    public function getPeriodeLengkapAttribute()
    {
        if ($this->periode_label) {
            return $this->periode_label;
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return ($namaBulan[$this->bulan] ?? '') . ' ' . $this->tahun;
    }

    /**
     * Helper: Format skor TOPSIS sebagai persentase
     */
    public function getSkorPersenAttribute()
    {
        return number_format($this->skor_topsis * 100, 2) . '%';
    }

    /**
     * Helper: Get badge color berdasarkan ranking
     */
    public function getRankingBadgeColorAttribute()
    {
        if ($this->ranking <= 3) {
            return 'success'; // Hijau untuk top 3
        } elseif ($this->ranking <= 10) {
            return 'primary'; // Biru untuk top 10
        } else {
            return 'secondary'; // Abu untuk lainnya
        }
    }

    /**
     * Helper: Cek apakah masuk top 3
     */
    public function isTop3(): bool
    {
        return $this->ranking <= 3;
    }

    /**
     * Helper: Cek apakah masuk top 10
     */
    public function isTop10(): bool
    {
        return $this->ranking <= 10;
    }

    /**
     * Scope: Filter by karyawan
     */
    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('id_karyawan', $karyawanId);
    }

    /**
     * Scope: Filter by periode (bulan & tahun)
     */
    public function scopeByPeriode($query, $bulan, $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }

    /**
     * Scope: Filter by bulan
     */
    public function scopeByBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    /**
     * Scope: Filter by tahun
     */
    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope: Urutkan berdasarkan ranking (terbaik dulu)
     */
    public function scopeOrderedByRanking($query)
    {
        return $query->orderBy('ranking', 'asc');
    }

    /**
     * Scope: Urutkan berdasarkan skor (tertinggi dulu)
     */
    public function scopeOrderedBySkor($query)
    {
        return $query->orderBy('skor_topsis', 'desc');
    }

    /**
     * Scope: Filter top N ranking
     */
    public function scopeTopRanking($query, $limit = 10)
    {
        return $query->orderBy('ranking', 'asc')->limit($limit);
    }

    /**
     * Scope: Filter hanya top 3
     */
    public function scopeTop3($query)
    {
        return $query->where('ranking', '<=', 3)->orderBy('ranking', 'asc');
    }
}
