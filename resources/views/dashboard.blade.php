@extends('layouts.dashboard')

@section('title', 'Dashboard - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Simple Welcome Section --}}
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Selamat Datang di Dashboard, {{ auth()->user()->nama }}!
            </h1>
            <p class="text-2xl text-gray-700">
                Sistem Penilaian Karyawan dengan Algoritma TOPSIS
            </p>
        </div>
    </div>
@endsection
