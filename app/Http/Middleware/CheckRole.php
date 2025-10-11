<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Middleware untuk validasi role user
     *
     * Cara pakai di route:
     * Route::get('/dashboard', ...)->middleware('role:super_admin,hrd');
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles - Role yang diizinkan (bisa multiple)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Cek apakah akun aktif
        if (!$user->isActive()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }

        // Cek apakah sudah di-approve
        if (!$user->isApproved()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda belum disetujui. Hubungi HRD atau administrator.');
        }

        // Cek role user apakah sesuai dengan yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Redirect ke dashboard sesuai role mereka
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}
