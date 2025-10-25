<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan halaman kelola akun dengan list user
     */
    public function index(Request $request)
    {
        // Query dasar untuk ambil user, kecuali diri sendiri
        $query = User::where('id', '!=', auth()->id());

        // Filter berdasarkan role (jika Super Admin bisa lihat semua, HRD hanya lihat kecuali Super Admin)
        if (auth()->user()->isHRD()) {
            $query->where('role', '!=', 'super_admin');
        }

        // Search berdasarkan nama, username, atau NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan status approval
        if ($request->filled('status_approval')) {
            $query->where('status_approval', $request->status_approval);
        }

        // Filter berdasarkan status akun
        if ($request->filled('status_akun')) {
            $query->where('status_akun', $request->status_akun);
        }

        // Urutkan berdasarkan created_at terbaru
        $query->orderBy('created_at', 'desc');

        // Pagination
        $users = $query->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Menyimpan user baru yang dibuat oleh Super Admin / HRD
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['hrd', 'supervisor', 'karyawan'])],
            'nik' => 'required|string|max:255|unique:users,nik',
            'divisi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'status_akun' => ['required', Rule::in(['aktif', 'tidak_aktif'])],
        ], [
            'nama.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'divisi.required' => 'Divisi wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
            'status_akun.required' => 'Status akun wajib dipilih',
        ]);

        // Buat user baru
        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'password' => $validated['password'], // Auto-hash di model
            'role' => $validated['role'],
            'nik' => $validated['nik'],
            'divisi' => $validated['divisi'],
            'jabatan' => $validated['jabatan'],
            'status_akun' => $validated['status_akun'],
            'status_approval' => 'approved', // Langsung approved karena dibuat oleh admin
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            // Set foreign key sesuai role pembuat
            'created_by_super_admin_id' => auth()->user()->isSuperAdmin() ? auth()->id() : null,
            'created_by_hrd_id' => auth()->user()->isHRD() ? auth()->id() : null,
        ]);

        return redirect()->route('users.index')
            ->with('success', "User {$user->nama} berhasil ditambahkan");
    }

    /**
     * Menampilkan detail user untuk edit
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Cek permission: HRD tidak boleh edit Super Admin
        if (auth()->user()->isHRD() && $user->isSuperAdmin()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit Super Admin');
        }

        return response()->json($user);
    }

    /**
     * Update data user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cek permission: HRD tidak boleh edit Super Admin
        if (auth()->user()->isHRD() && $user->isSuperAdmin()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit Super Admin');
        }

        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['hrd', 'supervisor', 'karyawan'])],
            'nik' => ['required', 'string', 'max:255', Rule::unique('users', 'nik')->ignore($user->id)],
            'divisi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'status_akun' => ['required', Rule::in(['aktif', 'tidak_aktif'])],
        ], [
            'nama.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'divisi.required' => 'Divisi wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
            'status_akun.required' => 'Status akun wajib dipilih',
        ]);

        // Update user
        $user->update([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'nik' => $validated['nik'],
            'divisi' => $validated['divisi'],
            'jabatan' => $validated['jabatan'],
            'status_akun' => $validated['status_akun'],
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->update(['password' => $validated['password']]);
        }

        return redirect()->route('users.index')
            ->with('success', "Data user {$user->nama} berhasil diperbarui");
    }

    /**
     * Approve user yang register (atau re-approve user yang rejected)
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);

        // Cek apakah user sudah approved sebelumnya
        if ($user->status_approval === 'approved') {
            return redirect()->route('users.index')
                ->with('error', 'User ini sudah disetujui sebelumnya');
        }

        // Approve user dan set status akun menjadi aktif
        $user->update([
            'status_approval' => 'approved',
            'status_akun' => 'aktif',  // Set status akun menjadi aktif
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        $message = $user->wasRecentlyCreated ?
            "User {$user->nama} berhasil disetujui dan diaktifkan" :
            "User {$user->nama} berhasil disetujui kembali dan diaktifkan";

        return redirect()->route('users.index')
            ->with('success', $message);
    }

    /**
     * Reject user yang register
     */
    public function reject(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cek apakah user masih pending
        if ($user->status_approval !== 'pending') {
            return redirect()->route('users.index')
                ->with('error', 'User ini sudah diproses sebelumnya');
        }

        // Validasi alasan penolakan
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter',
        ]);

        // Reject user
        $user->update([
            'status_approval' => 'rejected',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('users.index')
            ->with('success', "User {$user->nama} berhasil ditolak");
    }

    /**
     * Soft delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cek permission: HRD tidak boleh hapus Super Admin
        if (auth()->user()->isHRD() && $user->isSuperAdmin()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus Super Admin');
        }

        // Tidak boleh hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        // Soft delete
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User {$user->nama} berhasil dihapus");
    }
}
