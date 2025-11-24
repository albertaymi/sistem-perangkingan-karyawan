@extends('layouts.dashboard')

@section('title', 'Generate TOPSIS - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Generate TOPSIS</h2>
        <p class="mt-2 text-sm text-gray-600">Generate ranking karyawan menggunakan metode TOPSIS untuk periode yang dipilih</p>
    </div>

    {{-- Pilih Periode Penilaian Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-semibold text-gray-900">Pilih Periode Penilaian</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('perhitungan.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Periode Dropdown --}}
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                            Periode
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

                    {{-- Tahun Dropdown --}}
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun
                        </label>
                        <select name="tahun" id="tahun" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @foreach ($tahunList as $tahunItem)
                                <option value="{{ $tahunItem }}" {{ $tahun == $tahunItem ? 'selected' : '' }}>
                                    {{ $tahunItem }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Periode Terpilih Info --}}
                    <div class="flex items-end">
                        <div class="w-full p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs font-medium text-blue-800 mb-1">Periode Terpilih:</p>
                            <p class="text-sm font-bold text-blue-900">{{ $periodeLabel }}</p>
                            <p class="text-xs text-blue-700 mt-1">Data akan dianalisis untuk menghasilkan ranking karyawan periode ini</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Karyawan Aktif --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Karyawan Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $karyawanAktif }}</p>
                </div>
            </div>
            <div class="mt-2">
                <button type="button" onclick="showTooltip('tooltip-karyawan')" class="text-xs text-blue-600 hover:text-blue-800 cursor-pointer">
                    Siap Dievaluasi
                </button>
            </div>
        </div>

        {{-- Data Penilaian Lengkap --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Data Penilaian Lengkap</p>
                    <p class="text-2xl font-bold text-green-600">{{ $dataPenilaianLengkap }}</p>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs text-gray-600">
                    {{ $dataPenilaianLengkap > 0 ? number_format(($dataPenilaianLengkap / max($karyawanAktif, 1)) * 100, 1) : 0 }}% Complete
                </span>
            </div>
        </div>

        {{-- Data Tidak Lengkap --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 {{ $dataTidakLengkap > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg p-3">
                    <svg class="h-6 w-6 {{ $dataTidakLengkap > 0 ? 'text-red-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Data Tidak Lengkap</p>
                    <p class="text-2xl font-bold {{ $dataTidakLengkap > 0 ? 'text-red-600' : 'text-gray-600' }}">{{ $dataTidakLengkap }}</p>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-xs {{ $dataTidakLengkap > 0 ? 'text-red-600' : 'text-gray-600' }}">
                    {{ $dataTidakLengkap > 0 ? number_format(($dataTidakLengkap / max($karyawanAktif, 1)) * 100, 1) : 0 }}% Incomplete
                </span>
            </div>
        </div>

        {{-- Kriteria Aktif --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Kriteria Aktif</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $kriteriaAktif->count() }}</p>
                </div>
            </div>
            <div class="mt-2">
                @if ($kriteriaAktif->isNotEmpty())
                    <span class="text-xs {{ $bobotValid ? 'text-green-600' : 'text-red-600' }}">
                        Bobot: {{ number_format($kriteriaAktif->sum('bobot'), 1) }}%
                    </span>
                @else
                    <span class="text-xs text-gray-600">Tidak ada kriteria</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Validasi Data Penilaian Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-semibold text-gray-900">Validasi Data Penilaian</h3>
        </div>
        <div class="p-6">
            @if ($kriteriaAktif->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Kriteria Aktif</h3>
                    <p class="mt-2 text-sm text-gray-500">Silakan aktifkan kriteria terlebih dahulu di menu Kelola Kriteria.</p>
                    <div class="mt-6">
                        <a href="{{ route('kriteria.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 cursor-pointer">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Kelola Kriteria
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($validasiKriteria as $item)
                        @php
                            $subKriteriaCount = $item['kriteria']->subKriteria()->where('is_active', true)->count();
                            $isSingleKriteria = !empty($item['kriteria']->tipe_input);
                        @endphp
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-sm transition-shadow duration-200">
                            <div class="flex items-center space-x-4 flex-1">
                                @if ($item['is_complete'])
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $item['kriteria']->nama_kriteria }}</h4>
                                        @if ($isSingleKriteria)
                                            <span class="px-2 py-0.5 text-xs font-medium rounded bg-purple-100 text-purple-700">
                                                Kriteria Tunggal
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">
                                        @if ($isSingleKriteria)
                                            Tipe: {{ ucfirst($item['kriteria']->tipe_input) }} •
                                        @else
                                            {{ $subKriteriaCount }} Sub-Kriteria •
                                        @endif
                                        Bobot: {{ number_format($item['kriteria']->bobot, 0) }}%
                                    </p>
                                </div>

                                <div class="text-right">
                                    @if ($item['is_complete'])
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $item['karyawan_lengkap'] }}/{{ $item['total_karyawan'] }} Complete
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $item['karyawan_lengkap'] }}/{{ $item['total_karyawan'] }} Complete
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Bobot Kriteria Validation --}}
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg {{ $bobotValid ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                        <div class="flex items-center space-x-4">
                            @if ($bobotValid)
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-green-900">Bobot Kriteria - Total bobot kriteria = 100%</h4>
                                    <p class="text-xs text-green-700 mt-1">Total: {{ number_format($kriteriaAktif->sum('bobot'), 2) }}%</p>
                                </div>
                            @else
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-red-900">Bobot Kriteria - Total bobot harus 100%</h4>
                                    <p class="text-xs text-red-700 mt-1">Total saat ini: {{ number_format($kriteriaAktif->sum('bobot'), 2) }}%</p>
                                </div>
                            @endif
                        </div>
                        @if ($bobotValid)
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Valid
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Invalid
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Warning Message if data incomplete --}}
    @if (!$allDataComplete || !$bobotValid)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Perhatian: Data Tidak Lengkap</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Ada {{ $dataTidakLengkap }} karyawan dengan data penilaian yang belum lengkap. TOPSIS tetap dapat dijalankan, namun karyawan dengan data tidak lengkap akan dikecualikan dari perhitungan ranking.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Generate Ranking TOPSIS Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Generate Ranking TOPSIS</h3>
                @if ($hasilExists)
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Sudah Di-generate
                    </span>
                @endif
            </div>
        </div>
        <div class="p-6">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 {{ $allDataComplete && $bobotValid ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Generate Ranking TOPSIS</h3>
                <p class="mt-2 text-sm text-gray-500 max-w-2xl mx-auto">
                    Sistem akan menganalisis semua data penilaian menggunakan algoritma TOPSIS untuk menghasilkan ranking objektif karyawan.
                    Proses ini akan menghitung jarak terhadap solusi ideal positif dan negatif untuk setiap alternatif karyawan.
                </p>

                @if ($allDataComplete && $bobotValid)
                    <div class="mt-6 flex justify-center gap-3">
                        @if ($hasilExists)
                            <a href="{{ route('perhitungan.show', [$bulan, $tahun]) }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 shadow-sm cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                Lihat Hasil
                            </a>
                            <button type="button" data-modal-target="modal-recalculate" data-modal-toggle="modal-recalculate"
                                class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Hitung Ulang
                            </button>
                        @else
                            <button type="button" data-modal-target="modal-generate" data-modal-toggle="modal-generate"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-sm cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Jalankan Algoritma TOPSIS
                            </button>
                        @endif
                    </div>
                @else
                    <div class="mt-6">
                        <button type="button" disabled
                            class="inline-flex items-center px-6 py-3 bg-gray-300 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed opacity-60">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            TOPSIS Tidak Dapat Dijalankan
                        </button>
                        <p class="mt-2 text-xs text-red-600">
                            @if (!$bobotValid)
                                Total bobot kriteria harus 100% untuk menjalankan TOPSIS.
                            @elseif (!$allDataComplete)
                                Semua karyawan harus memiliki penilaian lengkap untuk semua kriteria.
                            @endif
                        </p>
                    </div>
                @endif

                <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg text-left max-w-2xl mx-auto">
                    <p class="text-xs font-semibold text-blue-800 mb-1">Catatan:</p>
                    <p class="text-xs text-blue-700">
                        Proses generate akan menggantikan hasil TOPSIS periode yang sama jika sudah ada. Pastikan semua data penilaian sudah benar sebelum menjalankan algoritma.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Generate TOPSIS --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Generate TOPSIS</h3>
                <a href="{{ route('ranking.index') }}"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium cursor-pointer">
                    Lihat Semua
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if ($riwayatGenerate->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Riwayat</h3>
                    <p class="mt-2 text-sm text-gray-500">Hasil generate TOPSIS akan muncul di sini setelah Anda menjalankan algoritma.</p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Generate
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Generated By
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($riwayatGenerate as $riwayat)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $riwayat->periode_label }}</div>
                                    <div class="text-xs text-gray-500">{{ $riwayat->bulan }}/{{ $riwayat->tahun }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $riwayat->tanggal_generate->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $riwayat->tanggal_generate->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $riwayat->generatedBySuperAdmin ? $riwayat->generatedBySuperAdmin->nama : ($riwayat->generatedByHRD ? $riwayat->generatedByHRD->nama : '-') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $riwayat->generatedBySuperAdmin ? 'Super Admin' : 'HRD Admin' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('perhitungan.show', [$riwayat->bulan, $riwayat->tahun]) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150 cursor-pointer">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        Lihat Hasil
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Modal Generate --}}
    @if ($allDataComplete && $bobotValid && !$hasilExists)
        <div id="modal-generate" tabindex="-1" aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Konfirmasi Generate Ranking
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                            data-modal-toggle="modal-generate">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('perhitungan.calculate') }}" method="POST" class="p-4 md:p-5">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <div class="mb-4 text-center">
                            <svg class="mx-auto mb-4 text-green-600 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <p class="text-sm text-gray-600 mb-2">
                                Generate ranking untuk periode:
                            </p>
                            <p class="text-base font-semibold text-gray-900 mb-3">{{ $periodeLabel }}</p>
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-left">
                                <p class="text-xs font-semibold text-blue-800 mb-1">Informasi:</p>
                                <p class="text-xs text-blue-700">
                                    Sistem akan menghitung ranking menggunakan metode TOPSIS berdasarkan data penilaian yang sudah diinput untuk {{ $karyawanAktif }} karyawan.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 cursor-pointer">
                                Ya, Generate
                            </button>
                            <button type="button" data-modal-toggle="modal-generate"
                                class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Recalculate --}}
    @if ($hasilExists)
        <div id="modal-recalculate" tabindex="-1" aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Konfirmasi Hitung Ulang
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                            data-modal-toggle="modal-recalculate">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('perhitungan.calculate') }}" method="POST" class="p-4 md:p-5">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <div class="mb-4 text-center">
                            <svg class="mx-auto mb-4 text-yellow-600 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <p class="text-sm text-gray-600 mb-2">
                                Hitung ulang ranking untuk periode:
                            </p>
                            <p class="text-base font-semibold text-gray-900 mb-3">{{ $periodeLabel }}</p>
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                                <p class="text-xs font-semibold text-yellow-800 mb-1">Perhatian:</p>
                                <p class="text-xs text-yellow-700">
                                    Data ranking sebelumnya akan dihapus dan diganti dengan hasil perhitungan baru.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white font-medium rounded-lg hover:from-yellow-700 hover:to-yellow-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-150 cursor-pointer">
                                Ya, Hitung Ulang
                            </button>
                            <button type="button" data-modal-toggle="modal-recalculate"
                                class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
