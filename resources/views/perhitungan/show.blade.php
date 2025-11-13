@extends('layouts.dashboard')

@section('title', 'Hasil Ranking - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Hasil Ranking Karyawan</h2>
                <p class="mt-2 text-sm text-gray-600">Periode: {{ $periodeLabel }}</p>
            </div>
            @if (auth()->user()->isSuperAdmin() || auth()->user()->isHRD())
                <a href="{{ route('perhitungan.index') }}"
                    class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                    <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            @endif
        </div>
    </div>

    {{-- Info Generate --}}
    @if ($tanggalGenerate && $generatedBy)
        <div class="mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <span class="font-medium">Ranking di-generate pada:</span>
                        {{ $tanggalGenerate->format('d F Y, H:i') }} WIB
                        <span class="mx-2">•</span>
                        <span class="font-medium">Oleh:</span> {{ $generatedBy->nama }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Podium Top 3 --}}
    @if ($hasilRanking->count() >= 3)
        <div class="mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-8 border border-blue-100">
                <h3 class="text-2xl font-bold text-center text-gray-900 mb-8">🏆 Top 3 Karyawan Terbaik 🏆</h3>

                <div class="flex items-end justify-center gap-8 max-w-4xl mx-auto">
                    {{-- Rank 2 (Silver) --}}
                    @php
                        $rank2 = $hasilRanking->where('ranking', 2)->first();
                    @endphp
                    @if ($rank2)
                        <div class="flex flex-col items-center flex-1">
                            <div class="relative mb-4">
                                <div
                                    class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-white font-bold text-3xl shadow-lg ring-4 ring-gray-200">
                                    {{ strtoupper(substr($rank2->karyawan->nama, 0, 2)) }}
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 w-10 h-10 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-white">
                                    2
                                </div>
                            </div>
                            <div class="text-center mb-4">
                                <h4 class="font-bold text-lg text-gray-900">{{ $rank2->karyawan->nama }}</h4>
                                <p class="text-sm text-gray-600">{{ $rank2->karyawan->divisi }}</p>
                                <p class="text-sm text-gray-600">{{ $rank2->karyawan->jabatan }}</p>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-800">
                                        Skor: {{ number_format($rank2->skor_topsis * 100, 2) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-gradient-to-br from-gray-300 to-gray-400 rounded-t-xl h-32 flex items-center justify-center shadow-xl">
                                <span class="text-white font-bold text-6xl">2</span>
                            </div>
                        </div>
                    @endif

                    {{-- Rank 1 (Gold) - Highest --}}
                    @php
                        $rank1 = $hasilRanking->where('ranking', 1)->first();
                    @endphp
                    @if ($rank1)
                        <div class="flex flex-col items-center flex-1">
                            <div class="relative mb-4">
                                <div
                                    class="w-28 h-28 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold text-4xl shadow-xl ring-4 ring-yellow-200">
                                    {{ strtoupper(substr($rank1->karyawan->nama, 0, 2)) }}
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 w-12 h-12 rounded-full bg-gradient-to-br from-yellow-500 to-yellow-700 flex items-center justify-center text-white font-bold text-xl shadow-xl ring-2 ring-white">
                                    👑
                                </div>
                            </div>
                            <div class="text-center mb-4">
                                <h4 class="font-bold text-xl text-gray-900">{{ $rank1->karyawan->nama }}</h4>
                                <p class="text-sm text-gray-600">{{ $rank1->karyawan->divisi }}</p>
                                <p class="text-sm text-gray-600">{{ $rank1->karyawan->jabatan }}</p>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-200 text-yellow-900">
                                        Skor: {{ number_format($rank1->skor_topsis * 100, 2) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-t-xl h-48 flex items-center justify-center shadow-2xl">
                                <span class="text-white font-bold text-7xl">1</span>
                            </div>
                        </div>
                    @endif

                    {{-- Rank 3 (Bronze) --}}
                    @php
                        $rank3 = $hasilRanking->where('ranking', 3)->first();
                    @endphp
                    @if ($rank3)
                        <div class="flex flex-col items-center flex-1">
                            <div class="relative mb-4">
                                <div
                                    class="w-24 h-24 rounded-full bg-gradient-to-br from-orange-300 to-orange-500 flex items-center justify-center text-white font-bold text-3xl shadow-lg ring-4 ring-orange-200">
                                    {{ strtoupper(substr($rank3->karyawan->nama, 0, 2)) }}
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-white">
                                    3
                                </div>
                            </div>
                            <div class="text-center mb-4">
                                <h4 class="font-bold text-lg text-gray-900">{{ $rank3->karyawan->nama }}</h4>
                                <p class="text-sm text-gray-600">{{ $rank3->karyawan->divisi }}</p>
                                <p class="text-sm text-gray-600">{{ $rank3->karyawan->jabatan }}</p>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-200 text-orange-900">
                                        Skor: {{ number_format($rank3->skor_topsis * 100, 2) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-gradient-to-br from-orange-300 to-orange-500 rounded-t-xl h-24 flex items-center justify-center shadow-xl">
                                <span class="text-white font-bold text-6xl">3</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Full Ranking Table --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Tabel Ranking Lengkap</h3>
                <span class="text-sm text-gray-600">Total: {{ $hasilRanking->count() }} Karyawan</span>
            </div>
        </div>

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
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ auth()->user()->isKaryawan() && $hasil->id_karyawan === auth()->id() ? 'bg-blue-50 ring-2 ring-blue-400' : '' }}">
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
                                            <span class="text-sm font-medium text-gray-900">{{ $hasil->karyawan->nama }}</span>
                                            @if (auth()->user()->isKaryawan() && $hasil->id_karyawan === auth()->id())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-600 text-white">
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
                                @if (auth()->user()->isSuperAdmin() || auth()->user()->isHRD() || auth()->user()->isSupervisor() || $hasil->id_karyawan === auth()->id())
                                    <a href="{{ route('ranking.detail', $hasil->id) }}"
                                        class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-xs font-medium rounded-lg hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 cursor-pointer">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    </div>

    {{-- Statistics --}}
    <div class="mt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                <dt class="text-sm font-medium text-gray-500 truncate mb-1">Total Karyawan</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $hasilRanking->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                <dd class="text-3xl font-bold text-gray-900">
                                    {{ number_format($hasilRanking->max('skor_topsis') * 100, 2) }}%
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                    {{ number_format($hasilRanking->avg('skor_topsis') * 100, 2) }}%
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
