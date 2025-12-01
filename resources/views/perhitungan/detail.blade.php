@extends('layouts.dashboard')

@section('title', 'Detail Perhitungan - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('ranking.index') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                    </path>
                                </svg>
                                Ranking
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail Penilaian</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="text-3xl font-bold text-gray-900">Detail Penilaian Karyawan</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $hasil->karyawan->nama }} - Periode {{ $periodeLabel }}</p>
            </div>
            <a href="{{ route('ranking.index') }}"
                class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Karyawan Info Card --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg rounded-xl overflow-hidden mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-white">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div
                            class="h-16 w-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white/30">
                            <span class="text-white font-bold text-xl">
                                {{ strtoupper(substr($hasil->karyawan->nama, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-blue-100">Nama Karyawan</p>
                        <h3 class="text-lg font-bold">{{ $hasil->karyawan->nama }}</h3>
                        <p class="text-xs text-blue-100">NIK: {{ $hasil->karyawan->nik }}</p>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-sm text-blue-100 mb-1">Ranking</p>
                    @if ($hasil->ranking == 1)
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 text-white font-bold text-2xl shadow-lg border-2 border-white/30">
                            #1
                        </div>
                    @elseif($hasil->ranking == 2)
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 text-white font-bold text-2xl shadow-lg border-2 border-white/30">
                            #2
                        </div>
                    @elseif($hasil->ranking == 3)
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 text-white font-bold text-2xl shadow-lg border-2 border-white/30">
                            #3
                        </div>
                    @else
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20 text-white font-bold text-2xl border-2 border-white/30">
                            #{{ $hasil->ranking }}
                        </div>
                    @endif
                </div>

                <div class="text-center">
                    <p class="text-sm text-blue-100 mb-1">Divisi</p>
                    <p class="text-2xl font-bold">{{ $hasil->karyawan->divisi }}</p>
                    <p class="text-xs text-blue-100 mt-1">{{ $hasil->karyawan->jabatan }}</p>
                </div>

                <div class="text-center">
                    <p class="text-sm text-blue-100 mb-1">Skor TOPSIS</p>
                    <p class="text-3xl font-bold">{{ number_format($hasil->skor_topsis, 4) }}</p>
                    <p class="text-xs text-blue-100 mt-1">Periode: {{ $periodeLabel }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kriteria Sections --}}
    @php
        $kriteriaColors = [
            'benefit' => [
                0 => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'icon' => 'bg-green-500', 'text' => 'text-green-700', 'badge' => 'bg-green-100 text-green-800'],
                1 => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'icon' => 'bg-blue-500', 'text' => 'text-blue-700', 'badge' => 'bg-blue-100 text-blue-800'],
                2 => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'icon' => 'bg-purple-500', 'text' => 'text-purple-700', 'badge' => 'bg-purple-100 text-purple-800'],
                3 => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'icon' => 'bg-indigo-500', 'text' => 'text-indigo-700', 'badge' => 'bg-indigo-100 text-indigo-800'],
            ],
            'cost' => [
                0 => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'icon' => 'bg-yellow-500', 'text' => 'text-yellow-700', 'badge' => 'bg-yellow-100 text-yellow-800'],
                1 => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'icon' => 'bg-orange-500', 'text' => 'text-orange-700', 'badge' => 'bg-orange-100 text-orange-800'],
            ]
        ];
        $benefitIndex = 0;
        $costIndex = 0;
    @endphp

    @foreach ($kriteriaList as $kriteria)
        @php
            $isSingleKriteria = !empty($kriteria->tipe_input);
            $tipe = $kriteria->tipe_kriteria ?? 'benefit';

            if ($tipe === 'benefit') {
                $colors = $kriteriaColors['benefit'][$benefitIndex % 4];
                $benefitIndex++;
            } else {
                $colors = $kriteriaColors['cost'][$costIndex % 2];
                $costIndex++;
            }

            $penilaianKriteria = $penilaianByKriteria[$kriteria->id] ?? null;
            $totalSkor = $penilaianKriteria['total'] ?? 0;
        @endphp

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border {{ $colors['border'] }} mb-6">
            <div class="px-6 py-4 {{ $colors['bg'] }} border-b {{ $colors['border'] }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="{{ $colors['icon'] }} p-3 rounded-lg text-white">
                            @if ($tipe === 'benefit')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-bold {{ $colors['text'] }}">{{ $kriteria->nama_kriteria }}</h3>
                            <p class="text-sm text-gray-600">{{ $kriteria->deskripsi }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Bobot</p>
                            <p class="text-2xl font-bold {{ $colors['text'] }}">{{ $kriteria->bobot }}%</p>
                        </div>
                        <span class="px-3 py-1 {{ $colors['badge'] }} rounded-full text-xs font-semibold">
                            {{ ucfirst($tipe) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($isSingleKriteria)
                    {{-- Single Kriteria (No Sub-Kriteria) --}}
                    @php
                        $penilaianItem = $penilaianKriteria['items']['single'] ?? null;
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                        Kriteria
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Bobot (%)
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Nilai Input
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                        Tipe
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Skor
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $kriteria->nama_kriteria }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $kriteria->bobot }}%
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $colors['badge'] }}">
                                            {{ $penilaianItem ? number_format($penilaianItem->nilai, 0) : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        @if ($kriteria->tipe_input === 'angka')
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                                </svg>
                                                Input Angka ({{ $kriteria->nilai_min ?? 0 }}-{{ $kriteria->nilai_max ?? 100 }})
                                            </span>
                                        @elseif($kriteria->tipe_input === 'rating')
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                Rating Scale (1-5)
                                            </span>
                                        @elseif($kriteria->tipe_input === 'dropdown')
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                                Dropdown
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="text-lg font-bold {{ $colors['text'] }}">
                                            {{ $penilaianItem ? number_format($penilaianItem->nilai, 0) : '-' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Multi Sub-Kriteria --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                        Sub-Kriteria
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Bobot (%)
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Nilai Input
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                        Tipe
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Skor
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($kriteria->subKriteria as $subKriteria)
                                    @php
                                        $penilaianItem = $penilaianKriteria['items'][$subKriteria->id] ?? null;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $subKriteria->nama_kriteria }}
                                            @if ($subKriteria->deskripsi)
                                                <p class="text-xs text-gray-500 mt-1">{{ $subKriteria->deskripsi }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                            {{ $subKriteria->bobot }}%
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $colors['badge'] }}">
                                                {{ $penilaianItem ? number_format($penilaianItem->nilai, 0) : '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                            @if ($subKriteria->tipe_input === 'angka')
                                                <span class="inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                                    </svg>
                                                    Input Angka ({{ $subKriteria->nilai_min ?? 0 }}-{{ $subKriteria->nilai_max ?? 100 }})
                                                </span>
                                            @elseif($subKriteria->tipe_input === 'rating')
                                                <span class="inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                        </path>
                                                    </svg>
                                                    Rating Scale (1-5)
                                                </span>
                                            @elseif($subKriteria->tipe_input === 'dropdown')
                                                <span class="inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                    Dropdown
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="text-lg font-bold {{ $colors['text'] }}">
                                                {{ $penilaianItem ? number_format($penilaianItem->nilai, 0) : '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Total Row --}}
                                <tr class="{{ $colors['bg'] }} font-semibold">
                                    <td class="px-4 py-3 text-sm text-gray-900" colspan="4">
                                        Total Skor {{ $kriteria->nama_kriteria }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="text-xl font-bold {{ $colors['text'] }}">
                                            {{ number_format($totalSkor, 0) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Catatan Penilaian --}}
                @if ($penilaianKriteria && !empty($penilaianKriteria['catatan']))
                    <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Catatan Penilaian:</h4>
                        <p class="text-sm text-gray-600">{{ $penilaianKriteria['catatan'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Ringkasan Keseluruhan --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-semibold text-gray-900">📊 Ringkasan Keseluruhan</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-blue-600 mb-2">Skor TOPSIS Final</div>
                    <div class="text-4xl font-bold text-blue-700">{{ number_format($hasil->skor_topsis, 4) }}</div>
                    <div class="text-sm text-blue-600 mt-1">{{ number_format($hasil->skor_topsis * 100, 2) }}%</div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-green-600 mb-2">Jarak Ideal Positif (D+)</div>
                    <div class="text-4xl font-bold text-green-700">{{ number_format($hasil->jarak_ideal_positif, 4) }}
                    </div>
                    <div class="text-xs text-green-600 mt-1">Semakin kecil semakin baik</div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                    <div class="text-sm font-medium text-red-600 mb-2">Jarak Ideal Negatif (D-)</div>
                    <div class="text-4xl font-bold text-red-700">{{ number_format($hasil->jarak_ideal_negatif, 4) }}
                    </div>
                    <div class="text-xs text-red-600 mt-1">Semakin besar semakin baik</div>
                </div>
            </div>

            {{-- Ranking Info --}}
            <div class="mt-6 text-center p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-gray-700">
                    Dengan skor <span class="font-bold text-blue-700">{{ number_format($hasil->skor_topsis, 4) }}</span>,
                    <span class="font-bold">{{ $hasil->karyawan->nama }}</span>
                    berada di peringkat
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-lg font-bold
                        @if ($hasil->ranking == 1) bg-yellow-200 text-yellow-800
                        @elseif($hasil->ranking == 2) bg-gray-200 text-gray-800
                        @elseif($hasil->ranking == 3) bg-orange-200 text-orange-800
                        @else bg-blue-100 text-blue-800 @endif
                    ">
                        #{{ $hasil->ranking }}
                    </span>
                    dari total {{ $allHasil->count() }} karyawan untuk periode <span
                        class="font-semibold">{{ $periodeLabel }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection
