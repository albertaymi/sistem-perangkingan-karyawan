@extends('layouts.dashboard')

@section('title', 'Pengaturan Akun - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Pengaturan Akun</h2>
                <p class="mt-2 text-sm text-gray-600">Kelola informasi akun dan keamanan Anda</p>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-green-700 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Profile Information Card --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Profil</h3>
                <a href="{{ route('profile.edit') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit Profil
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="flex items-start gap-6">
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    <div
                        class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-3xl shadow-lg">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>
                    <div class="mt-3 text-center">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $user->isSuperAdmin() ? 'bg-purple-100 text-purple-800' : ($user->isHRD() ? 'bg-blue-100 text-blue-800' : ($user->isSupervisor() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>

                {{-- Info Grid --}}
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">NIK</label>
                        <p class="text-base font-semibold text-gray-900">{{ $user->nik }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                        <p class="text-base font-semibold text-gray-900">{{ $user->nama }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Username</label>
                        <p class="text-base text-gray-900">{{ $user->username }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                        <p class="text-base text-gray-900">{{ ucfirst($user->role) }}</p>
                    </div>

                    @if ($user->isKaryawan() || $user->isSupervisor())
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Divisi</label>
                            <p class="text-base text-gray-900">{{ $user->divisi }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jabatan</label>
                            <p class="text-base text-gray-900">{{ $user->jabatan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Security Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Keamanan</h3>
                <a href="{{ route('profile.edit-password') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-medium rounded-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    Ubah Password
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="flex items-start gap-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <svg class="h-6 w-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-yellow-800 mb-1">Keamanan Password</h4>
                    <p class="text-sm text-yellow-700">
                        Pastikan password Anda kuat dan unik. Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.
                        Ubah password secara berkala untuk menjaga keamanan akun Anda.
                    </p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Akun Dibuat</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $user->created_at->format('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Terakhir Diupdate</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $user->updated_at->format('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
