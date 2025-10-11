<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Nama tabel di database
     */
    protected $table = 'users';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'nik',
        'divisi',
        'jabatan',
        'created_by_super_admin_id',
        'created_by_hrd_id',
        'status_approval',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'status_akun',
    ];

    /**
     * Field yang disembunyikan saat serialize ke array/JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Auto hash password
        'approved_at' => 'datetime',
    ];

    /**
     * Relasi: User dibuat oleh Super Admin
     */
    public function createdBySuperAdmin()
    {
        return $this->belongsTo(User::class, 'created_by_super_admin_id');
    }

    /**
     * Relasi: User dibuat oleh HRD
     */
    public function createdByHRD()
    {
        return $this->belongsTo(User::class, 'created_by_hrd_id');
    }

    /**
     * Relasi: User di-approve oleh siapa
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi: Users yang dibuat oleh Super Admin ini
     */
    public function usersCreatedAsSuperAdmin()
    {
        return $this->hasMany(User::class, 'created_by_super_admin_id');
    }

    /**
     * Relasi: Users yang dibuat oleh HRD ini
     */
    public function usersCreatedAsHRD()
    {
        return $this->hasMany(User::class, 'created_by_hrd_id');
    }

    /**
     * Relasi: Penilaian yang diterima karyawan ini
     */
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_karyawan');
    }

    /**
     * Relasi: Hasil TOPSIS karyawan ini
     */
    public function hasilTopsis()
    {
        return $this->hasMany(HasilTopsis::class, 'id_karyawan');
    }

    /**
     * Helper: Cek apakah user adalah Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Helper: Cek apakah user adalah HRD
     */
    public function isHRD(): bool
    {
        return $this->role === 'hrd';
    }

    /**
     * Helper: Cek apakah user adalah Supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Helper: Cek apakah user adalah Karyawan
     */
    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    /**
     * Helper: Cek apakah akun aktif
     */
    public function isActive(): bool
    {
        return $this->status_akun === 'aktif';
    }

    /**
     * Helper: Cek apakah sudah di-approve
     */
    public function isApproved(): bool
    {
        return $this->status_approval === 'approved';
    }

    /**
     * Scope: Filter by role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope: Filter hanya yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status_akun', 'aktif');
    }

    /**
     * Scope: Filter hanya yang sudah approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }
}
