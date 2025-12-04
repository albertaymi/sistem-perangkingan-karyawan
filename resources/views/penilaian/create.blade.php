@extends('layouts.dashboard')

@section('title', 'Tambah Penilaian - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('penilaian.index') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                        </path>
                    </svg>
                    Penilaian Karyawan
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah Penilaian</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">
                    @if ($singleKriteria)
                        Penilaian: {{ $singleKriteria->nama_kriteria }}
                    @else
                        Tambah Penilaian Karyawan
                    @endif
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    @if ($singleKriteria)
                        Input penilaian untuk kriteria {{ $singleKriteria->nama_kriteria }} - {{ $karyawan->nama ?? '' }}
                    @else
                        Input penilaian kinerja karyawan berdasarkan kriteria yang telah ditentukan
                    @endif
                </p>
            </div>
            @if ($singleKriteria && $karyawan)
                <a href="{{ route('penilaian.overview', ['karyawanId' => $karyawan->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                    <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke Overview
                </a>
            @endif
        </div>
    </div>

    {{-- Selection Form: Pilih Karyawan & Periode (Hide if single kriteria mode) --}}
    @if (!$singleKriteria)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Karyawan & Periode Penilaian</h3>

                <form method="GET" action="{{ route('penilaian.create') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Pilih Karyawan --}}
                        <div>
                            <label for="karyawan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Karyawan <span class="text-red-500">*</span>
                            </label>
                            <select name="karyawan_id" id="karyawan_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Pilih Karyawan</option>
                                @foreach ($karyawanList as $karyawanItem)
                                    <option value="{{ $karyawanItem->id }}"
                                        {{ request('karyawan_id') == $karyawanItem->id ? 'selected' : '' }}>
                                        {{ $karyawanItem->nama }} ({{ $karyawanItem->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih Bulan --}}
                        <div>
                            <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">
                                Bulan <span class="text-red-500">*</span>
                            </label>
                            <select name="bulan" id="bulan" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $namaBulan)
                                    <option value="{{ $index + 1 }}" {{ $bulan == $index + 1 ? 'selected' : '' }}>
                                        {{ $namaBulan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih Tahun --}}
                        <div>
                            <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun <span class="text-red-500">*</span>
                            </label>
                            <select name="tahun" id="tahun" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 cursor-pointer">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Load Form Penilaian
                        </button>
                        @if ($karyawan)
                            <a href="{{ route('penilaian.create') }}"
                                class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($karyawan && $kriteria->isNotEmpty())
        {{-- Info Karyawan & Periode --}}
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                        <span class="text-white font-bold text-xl">
                            {{ strtoupper(substr($karyawan->nama, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $karyawan->nama }}</h3>
                        <div class="flex items-center gap-4 mt-1">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">NIK:</span> {{ $karyawan->nik }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Divisi:</span> {{ $karyawan->divisi }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Jabatan:</span> {{ $karyawan->jabatan }}
                            </p>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            <strong>Periode:</strong> {{ $periodeLabel }}
                        </p>
                    </div>
                </div>
                @if ($existingPenilaian->isNotEmpty())
                    <div class="text-right">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Sudah Ada Penilaian
                        </span>
                        <p class="text-xs text-gray-600 mt-1">Data existing akan di-replace</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Form Penilaian --}}
        <form action="{{ route('penilaian.store') }}" method="POST" id="form-penilaian">
            @csrf

            <input type="hidden" name="karyawan_id" value="{{ $karyawan->id }}">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            @if ($singleKriteria)
                <input type="hidden" name="from_overview" value="1">
            @endif

            {{-- Loop Through Kriteria --}}
            @foreach ($kriteria as $kriteriaIndex => $kriteriaItem)
                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
                    {{-- Kriteria Header --}}
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ $kriteriaIndex + 1 }}</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $kriteriaItem->nama_kriteria }}</h3>
                                    @if ($kriteriaItem->deskripsi)
                                        <p class="text-sm text-gray-600">{{ $kriteriaItem->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $kriteriaItem->tipe_kriteria === 'benefit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($kriteriaItem->tipe_kriteria) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Bobot: {{ number_format($kriteriaItem->bobot, 0) }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Kriteria List OR Direct Input (if kriteria has tipe_input without sub-kriteria) --}}
                    <div class="p-6 space-y-6">
                        @if ($kriteriaItem->tipe_input && $kriteriaItem->subKriteria->isEmpty())
                            {{-- Single Kriteria Mode: Direct Input without sub-kriteria --}}
                            <div class="border-l-4 border-blue-500 pl-4">
                                <div class="mb-3">
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">
                                        Nilai {{ $kriteriaItem->nama_kriteria }}
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-600 mb-2">Input nilai langsung untuk kriteria ini (tidak
                                        memiliki sub-kriteria)</p>
                                </div>

                                {{-- Hidden Fields --}}
                                <input type="hidden" name="penilaian[{{ $kriteriaIndex }}_0][id_kriteria]"
                                    value="{{ $kriteriaItem->id }}">
                                <input type="hidden" name="penilaian[{{ $kriteriaIndex }}_0][id_sub_kriteria]"
                                    value="{{ $kriteriaItem->id }}">

                                {{-- Dynamic Input Based on Tipe Input --}}
                                @if ($kriteriaItem->tipe_input === 'angka')
                                    {{-- Input Angka --}}
                                    <div>
                                        <input type="number" name="penilaian[{{ $kriteriaIndex }}_0][nilai]"
                                            id="nilai_{{ $kriteriaItem->id }}_{{ $kriteriaItem->id }}"
                                            min="{{ $kriteriaItem->nilai_min }}" max="{{ $kriteriaItem->nilai_max }}"
                                            step="1"
                                            value="{{ $existingPenilaian->has($kriteriaItem->id) ? (int) $existingPenilaian->get($kriteriaItem->id)->nilai : '' }}"
                                            required
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Masukkan nilai ({{ $kriteriaItem->nilai_min }} - {{ $kriteriaItem->nilai_max }})">
                                        <p class="mt-1 text-xs text-gray-500">
                                            Range: {{ $kriteriaItem->nilai_min }} - {{ $kriteriaItem->nilai_max }}
                                        </p>
                                    </div>
                                @elseif($kriteriaItem->tipe_input === 'rating')
                                    {{-- Input Rating (Stars) --}}
                                    <div>
                                        <div class="flex items-center gap-2">
                                            @for ($star = $kriteriaItem->nilai_min; $star <= $kriteriaItem->nilai_max; $star++)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="penilaian[{{ $kriteriaIndex }}_0][nilai]"
                                                        value="{{ $star }}"
                                                        {{ ($existingPenilaian->get($kriteriaItem->id)->nilai ?? '') == $star ? 'checked' : '' }}
                                                        required class="sr-only peer" onchange="updateStarDisplay(this)">
                                                    <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-300 transition-colors"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                        </path>
                                                    </svg>
                                                </label>
                                            @endfor
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">
                                            Pilih rating dari {{ $kriteriaItem->nilai_min }} sampai
                                            {{ $kriteriaItem->nilai_max }}
                                            bintang
                                        </p>
                                    </div>
                                @elseif($kriteriaItem->tipe_input === 'dropdown')
                                    {{-- Input Dropdown --}}
                                    <div>
                                        <select name="penilaian[{{ $kriteriaIndex }}_0][nilai]"
                                            id="nilai_{{ $kriteriaItem->id }}_{{ $kriteriaItem->id }}" required
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="">Pilih opsi...</option>
                                            @foreach ($kriteriaItem->dropdownOptions as $option)
                                                <option value="{{ $option->nilai_tetap }}"
                                                    {{ ($existingPenilaian->get($kriteriaItem->id)->nilai ?? '') == $option->nilai_tetap ? 'selected' : '' }}>
                                                    {{ $option->nama_kriteria }} (Nilai: {{ $option->nilai_tetap }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($kriteriaItem->dropdownOptions->isEmpty())
                                            <p class="mt-1 text-xs text-red-600">
                                                <svg class="inline-block w-3 h-3 mr-1" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                Dropdown options belum tersedia untuk kriteria ini
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                {{-- Catatan (Optional) --}}
                                <div class="mt-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Catatan (Optional)
                                    </label>
                                    <textarea name="penilaian[{{ $kriteriaIndex }}_0][catatan]" rows="2"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Tambahkan catatan jika diperlukan...">{{ $existingPenilaian->get($kriteriaItem->id)->catatan ?? '' }}</textarea>
                                </div>
                            </div>
                        @else
                            {{-- Normal Mode: Loop through sub-kriteria --}}
                            @forelse($kriteriaItem->subKriteria as $subIndex => $subItem)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <div class="mb-3">
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                                            {{ $subItem->nama_kriteria }}
                                            <span class="text-red-500">*</span>
                                            <span class="ml-2 text-xs font-normal text-gray-500">(Bobot:
                                                {{ number_format($subItem->bobot, 0) }}%)</span>
                                            @if ($subItem->tipe_kriteria === 'benefit')
                                                <span
                                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Benefit
                                                </span>
                                            @else
                                                <span
                                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Cost
                                                </span>
                                            @endif
                                        </label>
                                        @if ($subItem->deskripsi)
                                            <p class="text-xs text-gray-600 mb-2">{{ $subItem->deskripsi }}</p>
                                        @endif
                                    </div>

                                    {{-- Hidden Fields --}}
                                    <input type="hidden"
                                        name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][id_kriteria]"
                                        value="{{ $kriteriaItem->id }}">
                                    <input type="hidden"
                                        name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][id_sub_kriteria]"
                                        value="{{ $subItem->id }}">

                                    {{-- Dynamic Input Based on Tipe Input --}}
                                    @if ($subItem->tipe_input === 'angka')
                                        {{-- Input Angka --}}
                                        <div>
                                            <input type="number"
                                                name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                                id="nilai_{{ $kriteriaItem->id }}_{{ $subItem->id }}"
                                                min="{{ $subItem->nilai_min }}" max="{{ $subItem->nilai_max }}"
                                                step="1"
                                                value="{{ $existingPenilaian->has($subItem->id) ? (int) $existingPenilaian->get($subItem->id)->nilai : '' }}"
                                                required
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Masukkan nilai ({{ $subItem->nilai_min }} - {{ $subItem->nilai_max }})">
                                            <p class="mt-1 text-xs text-gray-500">
                                                Range: {{ $subItem->nilai_min }} - {{ $subItem->nilai_max }}
                                            </p>
                                        </div>
                                    @elseif($subItem->tipe_input === 'rating')
                                        {{-- Input Rating (Stars) --}}
                                        <div>
                                            <div class="flex items-center gap-2">
                                                @for ($star = $subItem->nilai_min; $star <= $subItem->nilai_max; $star++)
                                                    <label class="cursor-pointer">
                                                        <input type="radio"
                                                            name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                                            value="{{ $star }}"
                                                            {{ ($existingPenilaian->get($subItem->id)->nilai ?? '') == $star ? 'checked' : '' }}
                                                            required class="sr-only peer"
                                                            onchange="updateStarDisplay(this)">
                                                        <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-300 transition-colors"
                                                            fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                            </path>
                                                        </svg>
                                                    </label>
                                                @endfor
                                            </div>
                                            <p class="mt-2 text-xs text-gray-500">
                                                Pilih rating dari {{ $subItem->nilai_min }} sampai
                                                {{ $subItem->nilai_max }}
                                                bintang
                                            </p>
                                        </div>
                                    @elseif($subItem->tipe_input === 'dropdown')
                                        {{-- Input Dropdown --}}
                                        <div>
                                            <select name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                                id="nilai_{{ $kriteriaItem->id }}_{{ $subItem->id }}" required
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                                <option value="">Pilih opsi...</option>
                                                @foreach ($subItem->dropdownOptions as $option)
                                                    <option value="{{ $option->nilai_tetap }}"
                                                        {{ ($existingPenilaian->get($subItem->id)->nilai ?? '') == $option->nilai_tetap ? 'selected' : '' }}>
                                                        {{ $option->nama_kriteria }} (Nilai: {{ $option->nilai_tetap }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($subItem->dropdownOptions->isEmpty())
                                                <p class="mt-1 text-xs text-red-600">
                                                    <svg class="inline-block w-3 h-3 mr-1" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Dropdown options belum tersedia untuk sub-kriteria ini
                                                </p>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Catatan (Optional) --}}
                                    <div class="mt-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Catatan (Optional)
                                        </label>
                                        <textarea name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][catatan]" rows="2"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Tambahkan catatan jika diperlukan...">{{ $existingPenilaian->get($subItem->id)->catatan ?? '' }}</textarea>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <p>Belum ada sub-kriteria untuk kriteria ini</p>
                                </div>
                            @endforelse
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Action Buttons --}}
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
                <div class="flex items-center justify-between gap-4">
                    @if ($singleKriteria)
                        <a href="{{ route('penilaian.overview', ['karyawanId' => $karyawan->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Overview
                        </a>
                    @else
                        <a href="{{ route('penilaian.index') }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali
                        </a>
                    @endif

                    <button type="submit"
                        class="px-8 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-sm cursor-pointer">
                        <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Simpan Penilaian
                    </button>
                </div>
            </div>
        </form>
    @elseif($karyawan && $kriteria->isEmpty())
        {{-- No Kriteria Available --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Kriteria Aktif</h3>
            <p class="mt-2 text-sm text-gray-600">
                Tidak ada kriteria penilaian yang aktif saat ini.<br>
                Silakan tambahkan kriteria terlebih dahulu di menu Kriteria.
            </p>
            <div class="mt-6">
                <a href="{{ route('kriteria.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Kelola Kriteria
                </a>
            </div>
        </div>
    @endif

    {{-- JavaScript --}}
    <script>
        // Show toast for session messages
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session('success') }}', 'success');
            });
        @endif

        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session('error') }}', 'error');
            });
        @endif

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                @foreach ($errors->all() as $error)
                    showToast('{{ $error }}', 'error');
                @endforeach
            });
        @endif

        // Update star display when rating is selected
        function updateStarDisplay(radio) {
            const container = radio.closest('.flex');
            const allStars = container.querySelectorAll('svg');
            const selectedValue = parseInt(radio.value);

            allStars.forEach((star, index) => {
                if (index < selectedValue) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        // Initialize star displays on page load
        document.addEventListener('DOMContentLoaded', function() {
            const checkedRadios = document.querySelectorAll('input[type="radio"]:checked');
            checkedRadios.forEach(radio => updateStarDisplay(radio));
        });

        // Form validation before submit
        document.getElementById('form-penilaian')?.addEventListener('submit', function(e) {
            const requiredInputs = this.querySelectorAll('[required]');
            let allFilled = true;

            requiredInputs.forEach(input => {
                if (!input.value) {
                    allFilled = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            if (!allFilled) {
                e.preventDefault();
                showToast('Mohon lengkapi semua field yang wajib diisi', 'error');

                // Scroll to first empty field
                const firstEmpty = this.querySelector('[required]:not([value])');
                if (firstEmpty) {
                    firstEmpty.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstEmpty.focus();
                }
            }
        });

        // Show Toast Function
        function showToast(message, type = 'success') {
            const existingToast = document.getElementById('custom-toast');
            if (existingToast) {
                existingToast.remove();
            }

            const toast = document.createElement('div');
            toast.id = 'custom-toast';
            toast.className =
                'fixed top-20 right-5 z-50 flex items-center w-full max-w-md p-4 text-gray-500 bg-white rounded-lg shadow-lg border border-gray-200 transition-opacity duration-500';

            const iconColor = type === 'success' ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100';
            const iconPath = type === 'success' ?
                '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>' :
                '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>';

            toast.innerHTML = `
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ${iconColor}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">${iconPath}</svg>
                </div>
                <div class="ml-3 text-sm font-normal">${message}</div>
                <button type="button" onclick="this.parentElement.remove()" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 cursor-pointer">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        // Add validation for number inputs with min/max
        document.addEventListener('DOMContentLoaded', function() {
            const numberInputs = document.querySelectorAll('input[type="number"][min][max]');

            numberInputs.forEach(input => {
                // Add inline error element if not exists
                if (!input.nextElementSibling || !input.nextElementSibling.classList.contains(
                        'inline-error')) {
                    const errorSpan = document.createElement('span');
                    errorSpan.className = 'inline-error text-xs text-red-600 mt-1 hidden';
                    input.parentNode.insertBefore(errorSpan, input.nextSibling);
                }

                input.addEventListener('input', function() {
                    const min = parseFloat(this.min);
                    const max = parseFloat(this.max);
                    const value = parseFloat(this.value);
                    const errorSpan = this.nextElementSibling;

                    if (this.value && (value < min || value > max)) {
                        this.classList.add('border-red-500', 'focus:ring-red-500',
                            'focus:border-red-500');
                        this.classList.remove('border-gray-300', 'focus:ring-blue-500',
                            'focus:border-blue-500');
                        if (errorSpan && errorSpan.classList.contains('inline-error')) {
                            errorSpan.textContent = `Nilai harus antara ${min} dan ${max}`;
                            errorSpan.classList.remove('hidden');
                        }
                        this.setCustomValidity(`Nilai harus antara ${min} dan ${max}`);
                    } else {
                        this.classList.remove('border-red-500', 'focus:ring-red-500',
                            'focus:border-red-500');
                        this.classList.add('border-gray-300', 'focus:ring-blue-500',
                            'focus:border-blue-500');
                        if (errorSpan && errorSpan.classList.contains('inline-error')) {
                            errorSpan.classList.add('hidden');
                        }
                        this.setCustomValidity('');
                    }
                });

                // Validate on blur
                input.addEventListener('blur', function() {
                    this.dispatchEvent(new Event('input'));
                });
            });
        });
    </script>
@endsection
