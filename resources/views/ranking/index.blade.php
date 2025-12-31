@extends('layouts.dashboard')

@section('title', 'Hasil Ranking Karyawan - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        @if (auth()->user()->isKaryawan())
            <h2 class="text-3xl font-bold text-gray-900">Ranking Saya</h2>
            <p class="mt-2 text-sm text-gray-600">Lihat hasil ranking dan penilaian kinerja Anda berdasarkan periode yang
                dipilih</p>
        @else
            <h2 class="text-3xl font-bold text-gray-900">Hasil Ranking Karyawan</h2>
            <p class="mt-2 text-sm text-gray-600">Lihat dan analisis hasil ranking karyawan berdasarkan periode dan filter
                yang
                dipilih</p>
        @endif
    </div>

    {{-- Filter & Export Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('ranking.index') }}" id="filterForm" class="space-y-4">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 {{ auth()->user()->isKaryawan() ? 'lg:grid-cols-3' : 'lg:grid-cols-5' }} gap-4">
                    {{-- Filter Periode (Bulan) --}}
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                            Bulan
                        </label>
                        <select name="bulan" id="bulan" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $namaBulan)
                                <option value="{{ $index + 1 }}" {{ $bulan == $index + 1 ? 'selected' : '' }}>
                                    {{ $namaBulan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Periode (Tahun) --}}
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun
                        </label>
                        <select name="tahun" id="tahun" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @if ($availablePeriods->isEmpty())
                                {{-- Jika tidak ada data, tampilkan tahun saat ini --}}
                                @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                    <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            @else
                                @foreach ($availablePeriods->unique('tahun')->sortByDesc('tahun') as $period)
                                    <option value="{{ $period->tahun }}" {{ $tahun == $period->tahun ? 'selected' : '' }}>
                                        {{ $period->tahun }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Filter Divisi --}}
                    <div>
                        <label for="divisi" class="block text-sm font-medium text-gray-700 mb-2">
                            Divisi
                        </label>
                        <select name="divisi" id="divisi" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Semua Divisi</option>
                            @foreach ($divisiList as $divisiItem)
                                <option value="{{ $divisiItem }}" {{ $divisiFilter == $divisiItem ? 'selected' : '' }}>
                                    {{ $divisiItem }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Nama/NIK - HANYA untuk Admin/HRD/Supervisor --}}
                    @if (!auth()->user()->isKaryawan())
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                Cari Karyawan
                            </label>
                            <input type="text" name="search" id="search" value="{{ $search }}"
                                placeholder="Nama atau NIK..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    @endif

                    {{-- Button Filter --}}
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 shadow-sm cursor-pointer">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>

                {{-- Export & Reset Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    {{-- Export PDF --}}
                    <a href="{{ route('ranking.export.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'divisi' => $divisiFilter, 'search' => $search]) }}"
                        class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-medium rounded-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export PDF
                    </a>

                    {{-- Export Excel --}}
                    <a href="{{ route('ranking.export.excel', ['bulan' => $bulan, 'tahun' => $tahun, 'divisi' => $divisiFilter, 'search' => $search]) }}"
                        class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 cursor-pointer shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export Excel
                    </a>

                    {{-- Reset Filter --}}
                    @if (!empty($divisiFilter) || !empty($search))
                        <a href="{{ route('ranking.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Info Periode & Generate --}}
    <div class="mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <span class="font-medium">Periode:</span> {{ $periodeLabel }}
                        @if (!empty($divisiFilter))
                            <span class="mx-2">•</span>
                            <span class="font-medium">Divisi:</span> {{ $divisiFilter }}
                        @endif
                        @if ($tanggalGenerate && $generatedBy)
                            <span class="mx-2">•</span>
                            <span class="font-medium">Di-generate:</span> {{ $tanggalGenerate->format('d F Y, H:i') }} WIB
                            oleh {{ $generatedBy->nama }}
                        @elseif ($hasilRanking->isEmpty())
                            <span class="mx-2">•</span>
                            <span class="text-gray-600 italic">Belum ada data ranking untuk periode ini</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Total Karyawan Dinilai --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate mb-1">Total Karyawan Dinilai</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $totalKaryawan }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Skor Tertinggi --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate mb-1">Skor Tertinggi</dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                {{ number_format($skorTertinggi, 4) }}
                            </dd>
                            @if ($karyawanTertinggi)
                                <dd class="text-xs text-gray-600 mt-1 truncate">{{ $karyawanTertinggi->karyawan->nama }}
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Skor Terendah --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate mb-1">Skor Terendah</dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                {{ number_format($skorTerendah, 4) }}
                            </dd>
                            @if ($karyawanTerendah)
                                <dd class="text-xs text-gray-600 mt-1 truncate">{{ $karyawanTerendah->karyawan->nama }}
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rata-rata Skor --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate mb-1">Rata-rata Skor</dt>
                            <dd class="text-3xl font-bold text-gray-900">
                                {{ number_format($rataRataSkor, 4) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Ranking Karyawan --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Tabel Ranking Karyawan</h3>
                <span class="text-sm text-gray-600">
                    Menampilkan {{ $hasilRanking->count() }} karyawan
                    @if (!empty($search))
                        dari hasil pencarian "{{ $search }}"
                    @endif
                </span>
            </div>
        </div>

        @if ($hasilRanking->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ditemukan hasil ranking untuk filter yang dipilih.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rank
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Karyawan
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Divisi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jabatan
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Skor TOPSIS
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($hasilRanking as $hasil)
                            {{-- Highlight row jika karyawan yang login --}}
                            <tr
                                class="hover:bg-gray-50 transition-colors duration-150 {{ auth()->user()->isKaryawan() && $hasil->id_karyawan === auth()->id() ? 'bg-blue-50 ring-2 ring-blue-400' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($hasil->ranking == 1)
                                            <span
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 text-white font-bold text-lg shadow-md">
                                                1
                                            </span>
                                        @elseif($hasil->ranking == 2)
                                            <span
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 text-white font-bold text-lg shadow-md">
                                                2
                                            </span>
                                        @elseif($hasil->ranking == 3)
                                            <span
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-orange-300 to-orange-500 text-white font-bold text-lg shadow-md">
                                                3
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-700 font-semibold text-base">
                                                {{ $hasil->ranking }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">
                                                {{ strtoupper(substr($hasil->karyawan->nama, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ $hasil->karyawan->nama }}</span>
                                                @if (auth()->user()->isKaryawan() && $hasil->id_karyawan === auth()->id())
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-600 text-white">
                                                        Anda
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">NIK: {{ $hasil->karyawan->nik }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $hasil->karyawan->divisi }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $hasil->karyawan->jabatan }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-lg font-bold text-gray-900">
                                            {{ number_format($hasil->skor_topsis, 4) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            ({{ number_format($hasil->skor_topsis * 100, 2) }}%)
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    @if (auth()->user()->isSuperAdmin() ||
                                            auth()->user()->isHRD() ||
                                            auth()->user()->isSupervisor() ||
                                            $hasil->id_karyawan === auth()->id())
                                        <a href="{{ route('ranking.detail', $hasil->id) }}"
                                            class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-xs font-medium rounded-lg hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 cursor-pointer">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            Detail
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
