@extends('layouts.dashboard')

@section('title', 'Detail Kriteria - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('kriteria.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Kelola Kriteria
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail Kriteria</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">{{ $kriteria->nama_kriteria }}</h2>
        <p class="mt-2 text-sm text-gray-600">Kelola sub-kriteria untuk kriteria {{ $kriteria->nama_kriteria }}</p>
    </div>

    {{-- Info Kriteria --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Card Tipe Kriteria --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Tipe Kriteria</p>
                    @if($kriteria->tipe_kriteria === 'benefit')
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Benefit
                        </div>
                    @else
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"></path>
                            </svg>
                            Cost
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card Bobot Kriteria --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Bobot Kriteria</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($kriteria->bobot, 2) }}%</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card Total Bobot Sub-Kriteria --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Bobot Sub-Kriteria</p>
                    <p class="text-3xl font-bold {{ $totalBobot == 100 ? 'text-green-600' : ($totalBobot > 100 ? 'text-red-600' : 'text-yellow-600') }}">
                        {{ number_format($totalBobot, 2) }}%
                    </p>
                    @if($totalBobot == 100)
                        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Seimbang
                        </span>
                    @elseif($totalBobot > 100)
                        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                            Melebihi 100%
                        </span>
                    @else
                        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                            Sisa: {{ number_format(100 - $totalBobot, 2) }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Action Button --}}
    <div class="mb-6 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Total: <span class="font-semibold text-gray-900">{{ $subKriteria->count() }}</span> sub-kriteria
        </div>
        <button type="button" data-modal-target="modal-tambah-sub-kriteria" data-modal-toggle="modal-tambah-sub-kriteria"
            class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-sm">
            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Sub-Kriteria
        </button>
    </div>

    {{-- Table Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Nama Sub-Kriteria
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Tipe Input
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Range Nilai
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Bobot
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subKriteria as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->nama_kriteria }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $item->deskripsi }}">
                                    {{ $item->deskripsi ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->tipe_input === 'angka')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Angka
                                    </span>
                                @elseif($item->tipe_input === 'rating')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Rating
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        Dropdown
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($item->tipe_input !== 'dropdown')
                                    {{ $item->nilai_min }} - {{ $item->nilai_max }}
                                @else
                                    @php
                                        $countOptions = \App\Models\SistemKriteria::where('id_parent', $item->id)->where('level', 3)->count();
                                    @endphp
                                    <span class="text-xs text-gray-500">{{ $countOptions }} opsi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($item->bobot, 2) }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 mr-1.5 bg-green-600 rounded-full"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-2 h-2 mr-1.5 bg-gray-600 rounded-full"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    {{-- Button Kelola Options (only for dropdown type) --}}
                                    @if($item->tipe_input === 'dropdown')
                                        <button type="button" onclick="openDropdownOptionsModal({{ $kriteria->id }}, {{ $item->id }}, '{{ $item->nama_kriteria }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-150">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                            </svg>
                                            Options
                                        </button>
                                    @endif

                                    {{-- Button Edit --}}
                                    <button type="button" onclick="editSubKriteria({{ $kriteria->id }}, {{ $item->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>

                                    {{-- Button Toggle Status --}}
                                    <form action="{{ route('kriteria.sub-kriteria.toggle-status', [$kriteria->id, $item->id]) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 {{ $item->is_active ? 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} text-white text-xs font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-150">
                                            @if($item->is_active)
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Nonaktifkan
                                            @else
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Aktifkan
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Button Hapus --}}
                                    <button type="button" onclick="confirmDelete({{ $kriteria->id }}, {{ $item->id }}, '{{ $item->nama_kriteria }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada sub-kriteria</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan sub-kriteria pertama.</p>
                                <div class="mt-6">
                                    <button type="button" data-modal-target="modal-tambah-sub-kriteria" data-modal-toggle="modal-tambah-sub-kriteria"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Sub-Kriteria
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah Sub-Kriteria --}}
    <div id="modal-tambah-sub-kriteria" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Tambah Sub-Kriteria
                    </h3>
                    <button type="button" data-modal-hide="modal-tambah-sub-kriteria"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form action="{{ route('kriteria.sub-kriteria.store', $kriteria->id) }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    {{-- Nama Sub-Kriteria --}}
                    <div>
                        <label for="nama_kriteria_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Sub-Kriteria <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kriteria" id="nama_kriteria_tambah" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            placeholder="Contoh: Kehadiran, Kualitas Kerja, dll">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi" id="deskripsi_tambah" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            placeholder="Deskripsi sub-kriteria (opsional)"></textarea>
                    </div>

                    {{-- Tipe Input --}}
                    <div>
                        <label for="tipe_input_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Input <span class="text-red-500">*</span>
                        </label>
                        <select name="tipe_input" id="tipe_input_tambah" required onchange="toggleRangeFields('tambah')"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="">Pilih Tipe Input</option>
                            <option value="angka">Angka (Input numerik dengan range)</option>
                            <option value="rating">Rating (Bintang 1-5)</option>
                            <option value="dropdown">Dropdown (Pilihan tetap)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <strong>Angka:</strong> Untuk input angka seperti jumlah hari, jumlah keterlambatan, dll<br>
                            <strong>Rating:</strong> Untuk penilaian dengan bintang 1-5<br>
                            <strong>Dropdown:</strong> Untuk pilihan tetap seperti "Lolos", "Tidak Lolos", dll
                        </p>
                    </div>

                    {{-- Range Nilai (Show/Hide berdasarkan tipe_input) --}}
                    <div id="range_fields_tambah" class="space-y-4 hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="nilai_min_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai Minimum <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai_min" id="nilai_min_tambah" step="0.01"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                    placeholder="Contoh: 0">
                            </div>
                            <div>
                                <label for="nilai_max_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai Maximum <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai_max" id="nilai_max_tambah" step="0.01"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                    placeholder="Contoh: 22">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">
                            Range nilai untuk validasi input. Contoh: 0-22 untuk jumlah hari kehadiran
                        </p>
                    </div>

                    {{-- Bobot --}}
                    <div>
                        <label for="bobot_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Bobot (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="bobot" id="bobot_tambah" required step="0.01" min="0" max="100"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Contoh: 25">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Total bobot saat ini: <strong>{{ number_format($totalBobot, 2) }}%</strong> |
                            Sisa: <strong class="{{ (100 - $totalBobot) > 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format(100 - $totalBobot, 2) }}%</strong>
                        </p>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit"
                            class="flex-1 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Sub-Kriteria
                        </button>
                        <button type="button" data-modal-hide="modal-tambah-sub-kriteria"
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Sub-Kriteria --}}
    <div id="modal-edit-sub-kriteria" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Edit Sub-Kriteria
                    </h3>
                    <button type="button" data-modal-hide="modal-edit-sub-kriteria"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form id="form-edit-sub-kriteria" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="sub_kriteria_id" id="sub_kriteria_id_edit">

                    {{-- Nama Sub-Kriteria --}}
                    <div>
                        <label for="nama_kriteria_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Sub-Kriteria <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kriteria" id="nama_kriteria_edit" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi" id="deskripsi_edit" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></textarea>
                    </div>

                    {{-- Tipe Input --}}
                    <div>
                        <label for="tipe_input_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Input <span class="text-red-500">*</span>
                        </label>
                        <select name="tipe_input" id="tipe_input_edit" required onchange="toggleRangeFields('edit')"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Pilih Tipe Input</option>
                            <option value="angka">Angka (Input numerik dengan range)</option>
                            <option value="rating">Rating (Bintang 1-5)</option>
                            <option value="dropdown">Dropdown (Pilihan tetap)</option>
                        </select>
                    </div>

                    {{-- Range Nilai --}}
                    <div id="range_fields_edit" class="space-y-4 hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="nilai_min_edit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai Minimum <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai_min" id="nilai_min_edit" step="0.01"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            <div>
                                <label for="nilai_max_edit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai Maximum <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai_max" id="nilai_max_edit" step="0.01"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>

                    {{-- Bobot --}}
                    <div>
                        <label for="bobot_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Bobot (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="bobot" id="bobot_edit" required step="0.01" min="0" max="100"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="info-bobot-edit">
                            Loading...
                        </p>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit"
                            class="flex-1 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Sub-Kriteria
                        </button>
                        <button type="button" data-modal-hide="modal-edit-sub-kriteria"
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div id="modal-hapus-sub-kriteria" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl">
                <div class="p-6 text-center">
                    <svg class="mx-auto mb-4 w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                    <p class="mb-5 text-sm text-gray-600">
                        Apakah Anda yakin ingin menghapus sub-kriteria<br>
                        <strong id="nama-sub-kriteria-hapus" class="text-gray-900"></strong>?
                    </p>

                    <form id="form-hapus-sub-kriteria" method="POST" class="inline-block w-full">
                        @csrf
                        @method('DELETE')
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="flex-1 px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150">
                                Ya, Hapus
                            </button>
                            <button type="button" data-modal-hide="modal-hapus-sub-kriteria"
                                class="flex-1 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Dropdown Options Management --}}
    <div id="modal-dropdown-options" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-6xl max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl max-h-[90vh] flex flex-col">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-200 rounded-t flex-shrink-0">
                    <h3 id="modal-options-title" class="text-xl font-semibold text-gray-900">
                        Kelola Dropdown Options
                    </h3>
                    <button type="button" onclick="closeModal('modal-dropdown-options')"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 overflow-y-auto flex-1">
                    {{-- Form Tambah Option (Collapsible) --}}
                    <div class="mb-6">
                        <button id="btn-show-form" type="button" onclick="showTambahOptionForm()"
                            class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-150 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Option Baru
                        </button>

                        <div id="form-tambah-option" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <form id="form-tambah-option-inner" action="{{ route('kriteria.dropdown-options.store', [$kriteria->id, 0]) }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="sub_kriteria_id" id="sub_kriteria_id_tambah">

                                <div>
                                    <label for="nama_option_tambah" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Option <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama_kriteria" id="nama_option_tambah" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Contoh: Sangat Baik, Baik, Cukup">
                                </div>

                                <div>
                                    <label for="deskripsi_option_tambah" class="block text-sm font-medium text-gray-700 mb-1">
                                        Deskripsi
                                    </label>
                                    <textarea name="deskripsi" id="deskripsi_option_tambah" rows="2"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Deskripsi optional untuk option ini"></textarea>
                                </div>

                                <div>
                                    <label for="nilai_option_tambah" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nilai (Skor) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="nilai_tetap" id="nilai_option_tambah" required step="0.01" min="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Contoh: 5, 4, 3">
                                    <p class="mt-1 text-xs text-gray-500">Nilai numerik yang akan digunakan untuk perhitungan TOPSIS</p>
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Simpan Option
                                    </button>
                                    <button type="button" onclick="hideTambahOptionForm()"
                                        class="flex-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Table Options --}}
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Nama Option</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Deskripsi</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Nilai</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="options-table-body" class="bg-white divide-y divide-gray-200">
                                    {{-- Will be populated by JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end p-5 border-t border-gray-200 rounded-b flex-shrink-0">
                    <button type="button" onclick="closeModal('modal-dropdown-options')"
                        class="px-5 py-2.5 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Option --}}
    <div id="modal-edit-option" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Edit Dropdown Option
                    </h3>
                    <button type="button" onclick="closeModal('modal-edit-option')"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form id="form-edit-option-inner" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="nama_option_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Option <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kriteria" id="nama_option_edit" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: Sangat Baik, Baik, Cukup">
                    </div>

                    <div>
                        <label for="deskripsi_option_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi" id="deskripsi_option_edit" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Deskripsi optional untuk option ini"></textarea>
                    </div>

                    <div>
                        <label for="nilai_option_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Nilai (Skor) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="nilai_tetap" id="nilai_option_edit" required step="0.01" min="0"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: 5, 4, 3">
                        <p class="mt-1 text-xs text-gray-500">Nilai numerik yang akan digunakan untuk perhitungan TOPSIS</p>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit"
                            class="flex-1 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeModal('modal-edit-option')"
                            class="flex-1 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Delete Option --}}
    <div id="modal-delete-option" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl">
                <div class="p-6 text-center">
                    <svg class="mx-auto mb-4 w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                    <p class="mb-5 text-sm text-gray-600">
                        Apakah Anda yakin ingin menghapus option<br>
                        <strong id="delete-option-name" class="text-gray-900"></strong>?
                    </p>

                    <form id="form-delete-option" method="POST" class="inline-block w-full">
                        @csrf
                        @method('DELETE')
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="flex-1 px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-all">
                                Ya, Hapus
                            </button>
                            <button type="button" onclick="closeModal('modal-delete-option')"
                                class="flex-1 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // Show toast for session messages
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session('success') }}', 'success');
            });
        @endif

        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session('error') }}', 'error');
            });
        @endif

        // Toggle Range Fields based on Tipe Input
        function toggleRangeFields(mode) {
            const tipeInput = document.getElementById(`tipe_input_${mode}`).value;
            const rangeFields = document.getElementById(`range_fields_${mode}`);
            const nilaiMin = document.getElementById(`nilai_min_${mode}`);
            const nilaiMax = document.getElementById(`nilai_max_${mode}`);

            if (tipeInput === 'angka' || tipeInput === 'rating') {
                rangeFields.classList.remove('hidden');
                nilaiMin.required = true;
                nilaiMax.required = true;

                // Set default values for rating
                if (tipeInput === 'rating') {
                    nilaiMin.value = 1;
                    nilaiMax.value = 5;
                }
            } else {
                rangeFields.classList.add('hidden');
                nilaiMin.required = false;
                nilaiMax.required = false;
                nilaiMin.value = '';
                nilaiMax.value = '';
            }
        }

        // Edit Sub-Kriteria
        function editSubKriteria(kriteriaId, id) {
            // Fetch data sub-kriteria
            fetch(`/kriteria/${kriteriaId}/sub-kriteria/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const subKriteria = data.data;

                        // Populate form
                        document.getElementById('sub_kriteria_id_edit').value = subKriteria.id;
                        document.getElementById('nama_kriteria_edit').value = subKriteria.nama_kriteria;
                        document.getElementById('deskripsi_edit').value = subKriteria.deskripsi || '';
                        document.getElementById('tipe_input_edit').value = subKriteria.tipe_input;
                        document.getElementById('nilai_min_edit').value = subKriteria.nilai_min || '';
                        document.getElementById('nilai_max_edit').value = subKriteria.nilai_max || '';
                        document.getElementById('bobot_edit').value = subKriteria.bobot;

                        // Toggle range fields
                        toggleRangeFields('edit');

                        // Update form action
                        document.getElementById('form-edit-sub-kriteria').action = `/kriteria/${kriteriaId}/sub-kriteria/${subKriteria.id}`;

                        // Get sisa bobot (exclude sub-kriteria yang sedang diedit)
                        fetch(`/kriteria/${kriteriaId}/sub-kriteria/total-bobot?exclude_id=${subKriteria.id}`)
                            .then(response => response.json())
                            .then(bobotData => {
                                const sisaBobot = bobotData.sisa_bobot;
                                const totalBobot = bobotData.total_bobot;
                                const colorClass = sisaBobot > 0 ? 'text-green-600' : 'text-red-600';
                                document.getElementById('info-bobot-edit').innerHTML = `
                                    Total bobot sub-kriteria lain: <strong>${totalBobot.toFixed(2)}%</strong> |
                                    Sisa: <strong class="${colorClass}">${sisaBobot.toFixed(2)}%</strong>
                                `;
                            });

                        // Show backdrop terlebih dahulu
                        showBackdrop('modal-edit-sub-kriteria');

                        // Show modal
                        const modalElement = document.getElementById('modal-edit-sub-kriteria');
                        modalElement.classList.remove('hidden');
                        modalElement.classList.add('flex');
                        modalElement.setAttribute('aria-modal', 'true');
                        modalElement.setAttribute('role', 'dialog');
                        modalElement.removeAttribute('aria-hidden');

                        // Setup event listener untuk menutup modal (hanya sekali)
                        if (!modalElement.hasAttribute('data-listeners-attached')) {
                            setupModalCloseListeners('modal-edit-sub-kriteria');
                            modalElement.setAttribute('data-listeners-attached', 'true');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Gagal mengambil data sub-kriteria', 'error');
                });
        }

        // Confirm Delete
        function confirmDelete(kriteriaId, id, nama) {
            document.getElementById('nama-sub-kriteria-hapus').textContent = nama;
            document.getElementById('form-hapus-sub-kriteria').action = `/kriteria/${kriteriaId}/sub-kriteria/${id}`;

            // Show backdrop
            showBackdrop('modal-hapus-sub-kriteria');

            // Show modal
            const modalElement = document.getElementById('modal-hapus-sub-kriteria');
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
            modalElement.setAttribute('aria-modal', 'true');
            modalElement.setAttribute('role', 'dialog');
            modalElement.removeAttribute('aria-hidden');

            // Setup event listener
            if (!modalElement.hasAttribute('data-listeners-attached')) {
                setupModalCloseListeners('modal-hapus-sub-kriteria');
                modalElement.setAttribute('data-listeners-attached', 'true');
            }
        }

        // Show Backdrop Function
        function showBackdrop(modalId) {
            let backdrop = document.getElementById(`backdrop-${modalId}`);

            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = `backdrop-${modalId}`;
                backdrop.className = 'fixed inset-0 z-40 bg-gray-900/50 transition-opacity';
                backdrop.setAttribute('modal-backdrop', '');

                backdrop.addEventListener('click', function() {
                    closeModal(modalId);
                });

                document.body.appendChild(backdrop);
            }

            backdrop.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Hide Backdrop Function
        function hideBackdrop(modalId) {
            const backdrop = document.getElementById(`backdrop-${modalId}`);
            if (backdrop) {
                backdrop.classList.add('hidden');
            }
            document.body.style.overflow = '';
        }

        // Setup Modal Close Listeners
        function setupModalCloseListeners(modalId) {
            const modalElement = document.getElementById(modalId);

            // Close button
            const closeButtons = modalElement.querySelectorAll('[data-modal-hide]');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    closeModal(modalId);
                });
            });

            // ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !modalElement.classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }

        // Close Modal Function
        function closeModal(modalId) {
            const modalElement = document.getElementById(modalId);
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');

            hideBackdrop(modalId);
        }

        function showToast(message, type = 'success') {
            const existingToast = document.getElementById('custom-toast');
            if (existingToast) {
                existingToast.remove();
            }

            const toast = document.createElement('div');
            toast.id = 'custom-toast';
            toast.className = 'fixed top-20 right-5 z-50 flex items-center w-full max-w-md p-4 text-gray-500 bg-white rounded-lg shadow-lg border border-gray-200 transition-opacity duration-500';

            const iconColor = type === 'success' ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100';
            const iconPath = type === 'success'
                ? '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>'
                : '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>';

            toast.innerHTML = `
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ${iconColor}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">${iconPath}</svg>
                </div>
                <div class="ml-3 text-sm font-normal">${message}</div>
                <button type="button" onclick="this.parentElement.remove()" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8">
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
        // ========================================
        // DROPDOWN OPTIONS MANAGEMENT
        // ========================================

        let currentSubKriteriaId = null;
        let currentSubKriteriaName = '';

        function openDropdownOptionsModal(kriteriaId, subKriteriaId, subKriteriaName) {
            currentSubKriteriaId = subKriteriaId;
            currentSubKriteriaName = subKriteriaName;

            // Update modal title
            document.getElementById('modal-options-title').textContent = `Kelola Options - ${subKriteriaName}`;

            // Update form action for tambah option
            const formTambah = document.getElementById('form-tambah-option-inner');
            formTambah.action = `/kriteria/${kriteriaId}/sub-kriteria/${subKriteriaId}/options`;

            // Load options data
            loadDropdownOptions(kriteriaId, subKriteriaId);

            // Show modal
            showBackdrop('modal-dropdown-options');
            const modal = document.getElementById('modal-dropdown-options');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
        }

        function loadDropdownOptions(kriteriaId, subKriteriaId) {
            const tbody = document.getElementById('options-table-body');
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500"><svg class="animate-spin h-8 w-8 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><p class="mt-2">Memuat data...</p></td></tr>';

            // Fetch dropdown options from backend
            fetch(`/kriteria/${kriteriaId}/sub-kriteria/${subKriteriaId}/options`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.options.length > 0) {
                        let html = '';
                        data.options.forEach((option, index) => {
                            const statusBadge = option.is_active
                                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><span class="w-1.5 h-1.5 mr-1 bg-green-600 rounded-full"></span>Aktif</span>'
                                : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"><span class="w-1.5 h-1.5 mr-1 bg-gray-600 rounded-full"></span>Nonaktif</span>';

                            html += `
                                <tr class="hover:bg-gray-50 transition-colors" data-option-id="${option.id}">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">${index + 1}</td>
                                    <td class="px-6 py-3"><div class="text-sm font-medium text-gray-900">${option.nama_kriteria}</div></td>
                                    <td class="px-6 py-3"><div class="text-sm text-gray-600 max-w-xs truncate" title="${option.deskripsi || '-'}">${option.deskripsi || '-'}</div></td>
                                    <td class="px-6 py-3 whitespace-nowrap"><div class="text-sm font-semibold text-gray-900">${option.nilai_tetap}</div></td>
                                    <td class="px-6 py-3 whitespace-nowrap">${statusBadge}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" onclick="editDropdownOption(${kriteriaId}, ${subKriteriaId}, ${option.id})"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <form action="/kriteria/${kriteriaId}/sub-kriteria/${subKriteriaId}/options/${option.id}/toggle-status" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-2.5 py-1.5 ${option.is_active ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700'} text-white text-xs font-medium rounded transition-colors">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${option.is_active ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7'}"></path>
                                                    </svg>
                                                    ${option.is_active ? 'Nonaktif' : 'Aktif'}
                                                </button>
                                            </form>
                                            <button type="button" onclick="confirmDeleteOption(${kriteriaId}, ${subKriteriaId}, ${option.id}, '${option.nama_kriteria}')"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada dropdown options</h3>
                                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan option pertama.</p>
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading options:', error);
                    tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-red-500">Gagal memuat data. Silakan coba lagi.</td></tr>';
                });
        }

        function showTambahOptionForm() {
            document.getElementById('form-tambah-option').classList.remove('hidden');
            document.getElementById('btn-show-form').classList.add('hidden');
        }

        function hideTambahOptionForm() {
            document.getElementById('form-tambah-option').classList.add('hidden');
            document.getElementById('btn-show-form').classList.remove('hidden');
            document.getElementById('form-tambah-option-inner').reset();
        }

        function editDropdownOption(kriteriaId, subKriteriaId, optionId) {
            fetch(`/kriteria/${kriteriaId}/sub-kriteria/${subKriteriaId}/options/${optionId}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const option = data.data;

                        // Set form action
                        document.getElementById('form-edit-option-inner').action = `/kriteria/${kriteriaId}/sub-kriteria/${subKriteriaId}/options/${optionId}`;

                        // Populate form
                        document.getElementById('nama_option_edit').value = option.nama_kriteria;
                        document.getElementById('deskripsi_option_edit').value = option.deskripsi || '';
                        document.getElementById('nilai_option_edit').value = option.nilai_tetap;

                        // Show modal
                        showBackdrop('modal-edit-option');
                        const modal = document.getElementById('modal-edit-option');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        modal.setAttribute('aria-hidden', 'false');
                        modal.setAttribute('aria-modal', 'true');
                        modal.setAttribute('role', 'dialog');
                    }
                })
                .catch(error => {
                    console.error('Error loading option:', error);
                    showToast('Gagal memuat data option', 'error');
                });
        }

        function confirmDeleteOption(kriteriaId, subKriteriaId, optionId, optionName) {
            document.getElementById('delete-option-name').textContent = optionName;
            document.getElementById('form-delete-option').action = `/kriteria/${kriteriaId}/sub-kriteria/${subKriteriaId}/options/${optionId}`;

            showBackdrop('modal-delete-option');
            const modal = document.getElementById('modal-delete-option');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
        }
    </script>
@endsection
