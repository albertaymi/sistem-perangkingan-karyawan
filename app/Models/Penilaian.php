<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel di database
     */
    protected $table = 'penilaian';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'id_karyawan',
        'id_kriteria',
        'id_sub_kriteria',
        'nilai',
        'bulan',
        'tahun',
        'periode_label',
        'catatan',
        'tanggal_penilaian',
        'dinilai_oleh_supervisor_id',
        'created_by_super_admin_id',
        'created_by_hrd_id',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'nilai' => 'decimal:2',
        'bulan' => 'integer',
        'tahun' => 'integer',
        'tanggal_penilaian' => 'datetime',
    ];

    /**
     * Relasi: Karyawan yang dinilai
     */
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'id_karyawan');
    }

    /**
     * Relasi: Kriteria utama
     */
    public function kriteria()
    {
        return $this->belongsTo(SistemKriteria::class, 'id_kriteria');
    }

    /**
     * Relasi: Sub-kriteria (nullable)
     */
    public function subKriteria()
    {
        return $this->belongsTo(SistemKriteria::class, 'id_sub_kriteria');
    }

    /**
     * Relasi: Supervisor yang menilai
     */
    public function dinilaiOlehSupervisor()
    {
        return $this->belongsTo(User::class, 'dinilai_oleh_supervisor_id');
    }

    /**
     * Relasi: Dibuat oleh Super Admin
     */
    public function createdBySuperAdmin()
    {
        return $this->belongsTo(User::class, 'created_by_super_admin_id');
    }

    /**
     * Relasi: Dibuat oleh HRD
     */
    public function createdByHRD()
    {
        return $this->belongsTo(User::class, 'created_by_hrd_id');
    }

    /**
     * Helper: Get nama karyawan
     */
    public function getNamaKaryawanAttribute()
    {
        return $this->karyawan->nama ?? '-';
    }

    /**
     * Helper: Get nama kriteria
     */
    public function getNamaKriteriaAttribute()
    {
        return $this->kriteria->nama_kriteria ?? '-';
    }

    /**
     * Helper: Get nama sub-kriteria
     */
    public function getNamaSubKriteriaAttribute()
    {
        return $this->subKriteria->nama_kriteria ?? '-';
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
     * Scope: Filter by karyawan
     */
    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('id_karyawan', $karyawanId);
    }

    /**
     * Scope: Filter by kriteria
     */
    public function scopeByKriteria($query, $kriteriaId)
    {
        return $query->where('id_kriteria', $kriteriaId);
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
     * Scope: Filter by supervisor
     */
    public function scopeBySupervisor($query, $supervisorId)
    {
        return $query->where('dinilai_oleh_supervisor_id', $supervisorId);
    }

    /**
     * Scope: Urutkan terbaru
     */
    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal_penilaian', 'desc');
    }

    /**
     * Scope: Urutkan terlama
     */
    public function scopeTerlama($query)
    {
        return $query->orderBy('tanggal_penilaian', 'asc');
    }
}
