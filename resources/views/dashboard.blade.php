@extends('layouts.dashboard')

@section('title', 'Dashboard - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Welcome Section --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">
            Selamat Datang, {{ auth()->user()->nama }}!
        </h2>
        <p class="mt-3 text-base text-gray-600">
            @if(auth()->user()->isSuperAdmin())
                Anda login sebagai Super Administrator. Anda memiliki akses penuh ke seluruh sistem.
            @elseif(auth()->user()->isHRD())
                Anda login sebagai HRD. Anda dapat mengelola user, melakukan approval, dan generate ranking karyawan.
            @elseif(auth()->user()->isSupervisor())
                Anda login sebagai Supervisor. Anda dapat melakukan penilaian terhadap karyawan di divisi Anda.
            @else
                Anda login sebagai Karyawan. Anda dapat melihat penilaian dan ranking Anda.
            @endif
        </p>
    </div>

    {{-- Info Cards --}}
    @include('partials.dashboard-cards')

    {{-- Quick Info / Next Steps --}}
    <div class="mt-8">
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-800 leading-relaxed">
                                @if(auth()->user()->isSuperAdmin())
                                    Dashboard ini adalah pusat kontrol sistem. Fitur manajemen user dan kriteria akan segera tersedia.
                                @elseif(auth()->user()->isHRD())
                                    Gunakan menu navigasi untuk mengelola approval user, input penilaian, dan generate ranking karyawan.
                                @elseif(auth()->user()->isSupervisor())
                                    Anda dapat melakukan penilaian terhadap karyawan yang berada di bawah supervisi Anda.
                                @else
                                    Anda dapat melihat detail penilaian dan ranking Anda melalui menu yang tersedia.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
