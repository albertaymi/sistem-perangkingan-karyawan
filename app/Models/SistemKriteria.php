<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SistemKriteria extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel di database
     */
    protected $table = 'sistem_kriteria';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'id_parent',
        'nama_kriteria',
        'deskripsi',
        'tipe_kriteria',
        'bobot',
        'tipe_input',
        'nilai_min',
        'nilai_max',
        'nilai_tetap',
        'level',
        'urutan',
        'assigned_to_supervisor_id',
        'created_by_super_admin_id',
        'created_by_hrd_id',
        'is_active',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'bobot' => 'integer',
        'nilai_min' => 'integer',
        'nilai_max' => 'integer',
        'nilai_tetap' => 'integer',
        'level' => 'integer',
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: Parent kriteria (tree structure)
     * Untuk sub-kriteria, ini akan menunjuk ke kriteria utama
     * Untuk dropdown option, ini akan menunjuk ke sub-kriteria
     */
    public function parent()
    {
        return $this->belongsTo(SistemKriteria::class, 'id_parent');
    }

    /**
     * Relasi: Children kriteria (tree structure)
     * Untuk kriteria utama, ini akan return sub-kriteria
     * Untuk sub-kriteria dengan dropdown, ini akan return dropdown options
     */
    public function children()
    {
        return $this->hasMany(SistemKriteria::class, 'id_parent')->orderBy('urutan');
    }

    /**
     * Relasi: Sub-kriteria (level 2) dari kriteria utama
     * Khusus untuk kriteria level 1
     */
    public function subKriteria()
    {
        return $this->hasMany(SistemKriteria::class, 'id_parent')
            ->where('level', 2)
            ->orderBy('urutan');
    }

    /**
     * Relasi: Dropdown options (level 3) dari sub-kriteria
     * Khusus untuk sub-kriteria level 2 dengan tipe_input = 'dropdown'
     */
    public function dropdownOptions()
    {
        return $this->hasMany(SistemKriteria::class, 'id_parent')
            ->where('level', 3)
            ->orderBy('urutan');
    }

    /**
     * Relasi: Get all descendants (recursive)
     * Untuk mendapatkan semua anak, cucu, dst
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Relasi: Supervisor yang di-assign untuk kriteria ini
     */
    public function assignedSupervisor()
    {
        return $this->belongsTo(User::class, 'assigned_to_supervisor_id');
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
     * Relasi: Penilaian yang menggunakan kriteria ini
     */
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_kriteria');
    }

    /**
     * Relasi: Penilaian yang menggunakan ini sebagai sub-kriteria
     */
    public function penilaianAsSub()
    {
        return $this->hasMany(Penilaian::class, 'id_sub_kriteria');
    }

    /**
     * Helper: Cek apakah ini kriteria utama (level 1)
     */
    public function isKriteriaUtama(): bool
    {
        return $this->level === 1;
    }

    /**
     * Helper: Cek apakah ini sub-kriteria (level 2)
     */
    public function isSubKriteria(): bool
    {
        return $this->level === 2;
    }

    /**
     * Helper: Cek apakah ini dropdown option (level 3)
     */
    public function isDropdownOption(): bool
    {
        return $this->level === 3;
    }

    /**
     * Helper: Cek apakah kriteria aktif
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Helper: Cek apakah tipe kriteria benefit
     */
    public function isBenefit(): bool
    {
        return $this->tipe_kriteria === 'benefit';
    }

    /**
     * Helper: Cek apakah tipe kriteria cost
     */
    public function isCost(): bool
    {
        return $this->tipe_kriteria === 'cost';
    }

    /**
     * Scope: Filter hanya kriteria utama
     */
    public function scopeKriteriaUtama($query)
    {
        return $query->where('level', 1);
    }

    /**
     * Scope: Filter hanya sub-kriteria
     */
    public function scopeSubKriteria($query)
    {
        return $query->where('level', 2);
    }

    /**
     * Scope: Filter hanya yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by parent
     */
    public function scopeByParent($query, $parentId)
    {
        return $query->where('id_parent', $parentId);
    }

    /**
     * Scope: Urutkan berdasarkan urutan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
