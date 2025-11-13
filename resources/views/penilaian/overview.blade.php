@extends('layouts.dashboard')

@section('title', 'Overview Penilaian Karyawan - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Overview Penilaian Karyawan</h2>
                <p class="mt-2 text-sm text-gray-600">Status penilaian per kriteria untuk {{ $karyawan->nama }}</p>
            </div>
            <a href="{{ route('penilaian.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- Karyawan Info Card --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    <div
                        class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                        <span class="text-white font-bold text-xl">
                            {{ strtoupper(substr($karyawan->nama, 0, 2)) }}
                        </span>
                    </div>
                </div>
                <div class="ml-6">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $karyawan->nama }}</h3>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">NIK:</span> {{ $karyawan->nik }}
                        </span>
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">Divisi:</span> {{ $karyawan->divisi }}
                        </span>
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">Jabatan:</span> {{ $karyawan->jabatan }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Periode Info --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Periode Penilaian: {{ $periodeLabel }}</h3>
        <p class="text-sm text-gray-600 mt-1">Pilih kriteria di bawah untuk mulai menilai</p>
    </div>

    {{-- Kriteria List --}}
    <div class="space-y-4">
        @forelse($kriteriaWithStatus as $index => $data)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <h4 class="text-lg font-bold text-gray-900">{{ $data['kriteria']->nama_kriteria }}</h4>
                                @if ($data['status'] === 'selesai')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Selesai Dinilai
                                    </span>
                                @elseif($data['status'] === 'sebagian')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Sebagian Dinilai
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Belum Dinilai
                                    </span>
                                @endif
                            </div>

                            @if ($data['kriteria']->deskripsi)
                                <p class="text-sm text-gray-600 mb-3">{{ $data['kriteria']->deskripsi }}</p>
                            @endif

                            <div class="flex items-center gap-6 mb-4">
                                <div class="text-sm">
                                    <span class="text-gray-600">Bobot:</span>
                                    <span class="font-semibold text-gray-900">{{ $data['kriteria']->bobot }}%</span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-600">Tipe:</span>
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded {{ $data['kriteria']->tipe_kriteria === 'benefit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($data['kriteria']->tipe_kriteria) }}
                                    </span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-600">Sub-Kriteria:</span>
                                    <span class="font-semibold text-gray-900">{{ $data['total_sub'] }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between text-sm mb-2">
                                        <span class="text-gray-700">
                                            Progress: <span class="font-medium">{{ $data['dinilai'] }} /
                                                {{ $data['total_sub'] }} dinilai</span>
                                        </span>
                                        <span class="text-gray-600 font-semibold">{{ $data['persentase'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full transition-all duration-300 {{ $data['status'] === 'selesai' ? 'bg-gradient-to-r from-green-500 to-green-600' : ($data['status'] === 'sebagian' ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gray-300') }}"
                                            style="width: {{ $data['persentase'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ml-6">
                            <a href="{{ route('penilaian.create', ['karyawan_id' => $karyawan->id, 'bulan' => $bulan, 'tahun' => $tahun, 'kriteria_id' => $data['kriteria']->id]) }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 shadow-sm cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Nilai
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-12">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada kriteria aktif</h3>
                    <p class="text-sm text-gray-500">Belum ada kriteria yang tersedia untuk dinilai</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection
