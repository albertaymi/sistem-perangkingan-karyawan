@extends('layouts.dashboard')

@section('title', 'Detail Perhitungan - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Detail Perhitungan TOPSIS</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $hasil->karyawan->nama }} - {{ $periodeLabel }}</p>
            </div>
            <a href="{{ route('ranking.show', [$hasil->bulan, $hasil->tahun]) }}"
                class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Ranking
            </a>
        </div>
    </div>

    {{-- Karyawan Info Card --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-20 w-20">
                        <div
                            class="h-20 w-20 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">
                                {{ strtoupper(substr($hasil->karyawan->nama, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $hasil->karyawan->nama }}</h3>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-sm text-gray-600">
                                <span class="font-medium">NIK:</span> {{ $hasil->karyawan->nik }}
                            </span>
                            <span class="text-sm text-gray-600">
                                <span class="font-medium">Divisi:</span> {{ $hasil->karyawan->divisi }}
                            </span>
                            <span class="text-sm text-gray-600">
                                <span class="font-medium">Jabatan:</span> {{ $hasil->karyawan->jabatan }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    @if ($hasil->ranking == 1)
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 text-white font-bold text-3xl shadow-lg">
                            1
                        </div>
                    @elseif($hasil->ranking == 2)
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 text-white font-bold text-3xl shadow-lg">
                            2
                        </div>
                    @elseif($hasil->ranking == 3)
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-orange-300 to-orange-500 text-white font-bold text-3xl shadow-lg">
                            3
                        </div>
                    @else
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 text-gray-700 font-bold text-3xl">
                            {{ $hasil->ranking }}
                        </div>
                    @endif
                    <p class="text-sm text-gray-600 mt-2">Ranking #{{ $hasil->ranking }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Skor TOPSIS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500 mb-2">Skor TOPSIS</div>
                    <div class="text-4xl font-bold text-blue-600">{{ number_format($hasil->skor_topsis, 4) }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ number_format($hasil->skor_topsis * 100, 2) }}%</div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500 mb-2">Jarak Ideal Positif (D+)</div>
                    <div class="text-4xl font-bold text-green-600">{{ number_format($hasil->jarak_ideal_positif, 4) }}
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Semakin kecil semakin baik</div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500 mb-2">Jarak Ideal Negatif (D-)</div>
                    <div class="text-4xl font-bold text-red-600">{{ number_format($hasil->jarak_ideal_negatif, 4) }}</div>
                    <div class="text-sm text-gray-500 mt-1">Semakin besar semakin baik</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nilai Per Kriteria --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-semibold text-gray-900">Nilai Per Kriteria</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($hasil->nilai_per_kriteria as $namaKriteria => $nilai)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">{{ $namaKriteria }}</h4>
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($nilai, 2) }}</div>
                            </div>
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Detail Perhitungan Matriks --}}
    @if (isset($hasil->detail_perhitungan['decision_matrix']))
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-lg font-semibold text-gray-900">Matriks Keputusan (Decision Matrix)</h3>
                <p class="text-sm text-gray-600 mt-1">Nilai asli sebelum normalisasi</p>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kriteria ID
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nilai
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($hasil->detail_perhitungan['decision_matrix'] as $kriteriaId => $nilai)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Kriteria #{{ $kriteriaId }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                    {{ number_format($nilai, 4) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Normalized Matrix --}}
    @if (isset($hasil->detail_perhitungan['normalized_matrix']))
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-lg font-semibold text-gray-900">Matriks Ternormalisasi (Normalized Matrix)</h3>
                <p class="text-sm text-gray-600 mt-1">Hasil normalisasi menggunakan vector normalization</p>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kriteria ID
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nilai Normalized
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($hasil->detail_perhitungan['normalized_matrix'] as $kriteriaId => $nilai)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Kriteria #{{ $kriteriaId }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                    {{ number_format($nilai, 6) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Weighted Matrix --}}
    @if (isset($hasil->detail_perhitungan['weighted_matrix']))
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-lg font-semibold text-gray-900">Matriks Terbobot (Weighted Matrix)</h3>
                <p class="text-sm text-gray-600 mt-1">Hasil perkalian nilai normalized dengan bobot kriteria</p>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kriteria ID
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nilai Weighted
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($hasil->detail_perhitungan['weighted_matrix'] as $kriteriaId => $nilai)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Kriteria #{{ $kriteriaId }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                    {{ number_format($nilai, 6) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Rumus TOPSIS --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-semibold text-gray-900">Rumus Perhitungan TOPSIS</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4 text-sm text-gray-700">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">Nilai Preferensi (V):</h4>
                    <div class="font-mono text-center py-3 bg-white rounded border border-blue-100">
                        V = D- / (D+ + D-)
                    </div>
                    <p class="text-xs text-blue-700 mt-2">
                        V = {{ number_format($hasil->jarak_ideal_negatif, 4) }} / ({{
                            number_format($hasil->jarak_ideal_positif, 4) }} +
                        {{ number_format($hasil->jarak_ideal_negatif, 4) }})
                        = <span class="font-bold">{{ number_format($hasil->skor_topsis, 4) }}</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-semibold text-green-900 mb-2">Jarak Ideal Positif (D+):</h4>
                        <div class="font-mono text-center py-2 text-sm bg-white rounded border border-green-100">
                            D+ = √Σ(Vi - A+)²
                        </div>
                        <p class="text-xs text-green-700 mt-2">
                            D+ = <span class="font-bold">{{ number_format($hasil->jarak_ideal_positif, 6) }}</span>
                        </p>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-semibold text-red-900 mb-2">Jarak Ideal Negatif (D-):</h4>
                        <div class="font-mono text-center py-2 text-sm bg-white rounded border border-red-100">
                            D- = √Σ(Vi - A-)²
                        </div>
                        <p class="text-xs text-red-700 mt-2">
                            D- = <span class="font-bold">{{ number_format($hasil->jarak_ideal_negatif, 6) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
