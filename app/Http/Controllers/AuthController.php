<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Attempt login
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Cek apakah akun aktif
            if (!$user->isActive()) {
                Auth::logout();
                return back()->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            }

            // Cek apakah sudah di-approve
            if (!$user->isApproved()) {
                Auth::logout();
                return back()->with('error', 'Akun Anda belum disetujui. Hubungi HRD atau administrator.');
            }

            // Login berhasil
            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang, {$user->nama}!");
        }

        // Login gagal
        return back()->with('error', 'Username atau password salah')->withInput($request->only('username'));
    }

    /**
     * Tampilkan halaman register
     */
    public function showRegister()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'nik' => 'required|string|max:50|unique:users,nik',
            'divisi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'role' => 'required|in:supervisor,karyawan',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'divisi.required' => 'Divisi wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role tidak valid',
        ]);

        try {
            // Buat user baru dengan status pending
            User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => $request->password, // Auto-hashed by model
                'role' => $request->role,
                'nik' => $request->nik,
                'divisi' => $request->divisi,
                'jabatan' => $request->jabatan,
                'created_by_super_admin_id' => null,
                'created_by_hrd_id' => null,
                'status_approval' => 'pending',
                'approved_at' => null,
                'approved_by' => null,
                'rejection_reason' => null,
                'status_akun' => 'tidak_aktif',
            ]);

            return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda menunggu persetujuan dari HRD.');
        } catch (\Exception $e) {
            return back()->with('error', 'Registrasi gagal. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }
}
