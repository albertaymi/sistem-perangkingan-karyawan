@extends('layouts.dashboard')

@section('title', 'Perhitungan Ranking - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Perhitungan Ranking Karyawan</h2>
                <p class="mt-2 text-sm text-gray-600">Generate dan kelola ranking karyawan menggunakan metode TOPSIS</p>
            </div>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Tentang TOPSIS</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution) adalah metode
                            pengambilan keputusan yang menghitung jarak terhadap solusi ideal positif dan negatif untuk
                            menentukan ranking karyawan terbaik.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Periode List --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Periode Perhitungan</h3>
        </div>

        <div class="p-6">
            @if ($periodeList->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Data Penilaian</h3>
                    <p class="mt-2 text-sm text-gray-500">Silakan input penilaian karyawan terlebih dahulu sebelum
                        melakukan perhitungan ranking.</p>
                    <div class="mt-6">
                        <a href="{{ route('penilaian.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 cursor-pointer">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Input Penilaian
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($periodeList as $periode)
                        <div class="bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-4 mb-3">
                                            <h4 class="text-xl font-bold text-gray-900">{{ $periode['periode_label'] }}</h4>
                                            @if ($periode['has_ranking'])
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Sudah Dihitung
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Belum Dihitung
                                                </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                                <span class="font-medium">{{ $periode['jumlah_karyawan'] }}</span>
                                                <span class="ml-1">Karyawan</span>
                                            </div>

                                            @if ($periode['has_ranking'])
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <span>{{ $periode['last_generated']->format('d M Y, H:i') }}</span>
                                                </div>

                                                @if ($periode['generated_by'])
                                                    <div class="flex items-center text-sm text-gray-600">
                                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        </svg>
                                                        <span>{{ $periode['generated_by']->nama }}</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ml-6 flex flex-col gap-2">
                                        @if ($periode['has_ranking'])
                                            {{-- Lihat Hasil Button --}}
                                            <a href="{{ route('perhitungan.show', [$periode['bulan'], $periode['tahun']]) }}"
                                                class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 shadow-sm cursor-pointer">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                    </path>
                                                </svg>
                                                Lihat Hasil
                                            </a>

                                            {{-- Hitung Ulang Button --}}
                                            <button type="button"
                                                data-modal-target="modal-recalculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}"
                                                data-modal-toggle="modal-recalculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}"
                                                class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                                Hitung Ulang
                                            </button>
                                        @else
                                            {{-- Generate Ranking Button --}}
                                            <button type="button"
                                                data-modal-target="modal-calculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}"
                                                data-modal-toggle="modal-calculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}"
                                                class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-sm cursor-pointer">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                                Generate Ranking
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Informasi TOPSIS --}}
    <div class="mt-6">
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cara Kerja Metode TOPSIS</h3>
                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex items-start">
                        <span
                            class="flex-shrink-0 inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold mr-3">1</span>
                        <p><span class="font-semibold">Normalisasi Matriks:</span> Menormalisasi nilai menggunakan vector
                            normalization untuk membuat nilai comparable.</p>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="flex-shrink-0 inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold mr-3">2</span>
                        <p><span class="font-semibold">Weighted Matrix:</span> Mengalikan nilai normalized dengan bobot
                            kriteria.</p>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="flex-shrink-0 inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold mr-3">3</span>
                        <p><span class="font-semibold">Ideal Solutions:</span> Menentukan solusi ideal positif (A+) dan
                            ideal negatif (A-) berdasarkan tipe kriteria (benefit/cost).</p>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="flex-shrink-0 inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold mr-3">4</span>
                        <p><span class="font-semibold">Distance Calculation:</span> Menghitung jarak euclidean ke solusi
                            ideal positif (D+) dan negatif (D-).</p>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="flex-shrink-0 inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold mr-3">5</span>
                        <p><span class="font-semibold">Preference Value:</span> Menghitung nilai preferensi (V = D- / (D+ +
                            D-)) untuk menentukan ranking.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals untuk Generate & Recalculate --}}
    @foreach ($periodeList as $periode)
        @if ($periode['has_ranking'])
            {{-- Modal Recalculate --}}
            <div id="modal-recalculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Konfirmasi Hitung Ulang
                            </h3>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                                data-modal-toggle="modal-recalculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                            </button>
                        </div>
                        <form action="{{ route('perhitungan.calculate') }}" method="POST" class="p-4 md:p-5">
                            @csrf
                            <input type="hidden" name="bulan" value="{{ $periode['bulan'] }}">
                            <input type="hidden" name="tahun" value="{{ $periode['tahun'] }}">
                            <div class="mb-4 text-center">
                                <svg class="mx-auto mb-4 text-yellow-600 w-12 h-12" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <p class="text-sm text-gray-600 mb-2">
                                    Hitung ulang ranking untuk periode:
                                </p>
                                <p class="text-base font-semibold text-gray-900 mb-3">{{ $periode['periode_label'] }}</p>
                                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                                    <p class="text-xs font-semibold text-yellow-800 mb-1">⚠️ Perhatian:</p>
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
                                <button type="button"
                                    data-modal-toggle="modal-recalculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}"
                                    class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else
            {{-- Modal Calculate --}}
            <div id="modal-calculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Konfirmasi Generate Ranking
                            </h3>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                                data-modal-toggle="modal-calculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                            </button>
                        </div>
                        <form action="{{ route('perhitungan.calculate') }}" method="POST" class="p-4 md:p-5">
                            @csrf
                            <input type="hidden" name="bulan" value="{{ $periode['bulan'] }}">
                            <input type="hidden" name="tahun" value="{{ $periode['tahun'] }}">
                            <div class="mb-4 text-center">
                                <svg class="mx-auto mb-4 text-green-600 w-12 h-12" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <p class="text-sm text-gray-600 mb-2">
                                    Generate ranking untuk periode:
                                </p>
                                <p class="text-base font-semibold text-gray-900 mb-3">{{ $periode['periode_label'] }}</p>
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-left">
                                    <p class="text-xs font-semibold text-blue-800 mb-1">ℹ️ Informasi:</p>
                                    <p class="text-xs text-blue-700">
                                        Sistem akan menghitung ranking menggunakan metode TOPSIS berdasarkan data
                                        penilaian yang sudah diinput untuk {{ $periode['jumlah_karyawan'] }} karyawan.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="submit"
                                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 cursor-pointer">
                                    Ya, Generate
                                </button>
                                <button type="button"
                                    data-modal-toggle="modal-calculate-{{ $periode['bulan'] }}-{{ $periode['tahun'] }}"
                                    class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
