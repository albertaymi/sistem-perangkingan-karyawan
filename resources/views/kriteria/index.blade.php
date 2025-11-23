@extends('layouts.dashboard')

@section('title', 'Kelola Kriteria - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Kelola Kriteria Penilaian</h2>
        <p class="mt-2 text-sm text-gray-600">Manajemen kriteria utama untuk penilaian karyawan menggunakan metode TOPSIS</p>
    </div>

    {{-- Info Total Bobot --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Total Bobot Kriteria</h3>
                    <p class="text-sm text-gray-600">Total bobot harus sama dengan 100%</p>
                </div>
                <div class="text-right">
                    <div
                        class="text-4xl font-bold {{ $totalBobot == 100 ? 'text-green-600' : ($totalBobot > 100 ? 'text-red-600' : 'text-yellow-600') }}">
                        {{ number_format($totalBobot, 0) }}%
                    </div>
                    <div class="text-sm mt-1">
                        @if ($totalBobot == 100)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Seimbang
                            </span>
                        @elseif($totalBobot > 100)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Melebihi Batas
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Sisa: {{ number_format(100 - $totalBobot, 0) }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Button --}}
    <div class="mb-6 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Total: <span class="font-semibold text-gray-900">{{ $kriteria->count() }}</span> kriteria utama
        </div>
        <button type="button" data-modal-target="modal-tambah-kriteria" data-modal-toggle="modal-tambah-kriteria"
            class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-sm cursor-pointer">
            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Kriteria
        </button>
    </div>

    {{-- Table Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Nama Kriteria
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Bobot
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Tipe Input
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Assigned To
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Sub-Kriteria
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kriteria as $index => $item)
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
                                @if ($item->tipe_kriteria === 'benefit')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Benefit
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Cost
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format($item->bobot, 0) }}%
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->tipe_input)
                                    @if ($item->tipe_input === 'angka')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                <path fill-rule="evenodd"
                                                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Input Angka
                                        </span>
                                    @elseif($item->tipe_input === 'rating')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                </path>
                                            </svg>
                                            Rating
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Dropdown
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">Sub-Kriteria</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->assigned_to_supervisor_id)
                                    <div class="text-sm text-gray-900">
                                        {{ $item->assignedSupervisor->nama ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $item->assignedSupervisor->nik ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Tidak Ada Assignment</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $countSubKriteria = $item->children()->count();
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $countSubKriteria }} sub-kriteria
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->is_active)
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 mr-1.5 bg-green-600 rounded-full"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-2 h-2 mr-1.5 bg-gray-600 rounded-full"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    {{-- Button Kelola Sub-Kriteria --}}
                                    <a href="{{ route('kriteria.detail', $item->id) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-150 cursor-pointer">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        Sub-Kriteria
                                    </a>

                                    {{-- Button Edit --}}
                                    <button type="button" onclick="editKriteria({{ $item->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 cursor-pointer">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Edit
                                    </button>

                                    {{-- Button Toggle Status --}}
                                    <form action="{{ route('kriteria.toggle-status', $item->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 {{ $item->is_active ? 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} text-white text-xs font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-150 cursor-pointer">
                                            @if ($item->is_active)
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Nonaktifkan
                                            @else
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Aktifkan
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Button Hapus --}}
                                    <button type="button"
                                        onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_kriteria }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kriteria</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan kriteria penilaian pertama.
                                </p>
                                <div class="mt-6">
                                    <button type="button" data-modal-target="modal-tambah-kriteria"
                                        data-modal-toggle="modal-tambah-kriteria"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 cursor-pointer">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Kriteria
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah Kriteria --}}
    <div id="modal-tambah-kriteria" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Tambah Kriteria Penilaian
                    </h3>
                    <button type="button" data-modal-hide="modal-tambah-kriteria"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors cursor-pointer">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form action="{{ route('kriteria.store') }}" method="POST" class="p-6 space-y-4"
                    id="form-tambah-kriteria" onsubmit="return validateKriteriaForm('tambah')">
                    @csrf

                    {{-- Nama Kriteria --}}
                    <div>
                        <label for="nama_kriteria_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Kriteria <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kriteria" id="nama_kriteria_tambah" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            placeholder="Contoh: Presensi, Kinerja, dll">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi" id="deskripsi_tambah" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            placeholder="Deskripsi kriteria (opsional)"></textarea>
                    </div>

                    {{-- Tipe Kriteria --}}
                    <div>
                        <label for="tipe_kriteria_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Kriteria <span class="text-red-500">*</span>
                        </label>
                        <select name="tipe_kriteria" id="tipe_kriteria_tambah" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="">Pilih Tipe</option>
                            <option value="benefit">Benefit (Semakin tinggi semakin baik)</option>
                            <option value="cost">Cost (Semakin rendah semakin baik)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <strong>Benefit:</strong> Digunakan untuk kriteria positif (Kehadiran, Kinerja, dll)<br>
                            <strong>Cost:</strong> Digunakan untuk kriteria negatif (Catatan Buruk, Pelanggaran, dll)
                        </p>
                    </div>

                    {{-- Bobot --}}
                    <div>
                        <label for="bobot_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Bobot (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="bobot" id="bobot_tambah" required step="0.01"
                                min="0" max="100"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Contoh: 25">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Total bobot saat ini: <strong>{{ number_format($totalBobot, 0) }}%</strong> |
                            Sisa: <strong
                                class="{{ 100 - $totalBobot > 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format(100 - $totalBobot, 0) }}%</strong>
                        </p>
                    </div>

                    {{-- Assign ke Supervisor --}}
                    <div>
                        <label for="assigned_to_supervisor_id_tambah"
                            class="block text-sm font-medium text-gray-700 mb-2">
                            Assign ke Supervisor <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <select name="assigned_to_supervisor_id" id="assigned_to_supervisor_id_tambah"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="">-- Tidak di-assign (Akses Semua) --</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">
                                    {{ $supervisor->nama }} ({{ $supervisor->nik ?? '-' }}) -
                                    {{ $supervisor->divisi ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Jika di-assign, hanya supervisor terpilih yang dapat melakukan penilaian pada kriteria ini
                        </p>
                    </div>

                    {{-- Tipe Input Penilaian --}}
                    <div>
                        <label for="tipe_input_tambah" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Input Penilaian <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <select name="tipe_input" id="tipe_input_tambah" onchange="toggleKriteriaRangeFields('tambah')"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="">-- Tidak ada (Gunakan Sub-Kriteria) --</option>
                            <option value="angka">Input Angka (Rentang Nilai)</option>
                            <option value="rating">Rating Scale (1-5)</option>
                            <option value="dropdown">Dropdown Selection (Pilihan Tetap)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <strong>Kosongkan</strong> jika kriteria ini akan memiliki sub-kriteria.<br>
                            <strong>Pilih tipe</strong> jika kriteria ini langsung digunakan untuk penilaian tanpa
                            sub-kriteria.
                        </p>

                        {{-- Conditional Range Fields --}}
                        <div id="range_fields_tambah"
                            class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Range Nilai</h4>
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Nilai Minimum --}}
                                <div>
                                    <label for="nilai_min_tambah" class="block text-xs font-medium text-gray-600 mb-1">
                                        Nilai Minimum
                                    </label>
                                    <input type="number" name="nilai_min" id="nilai_min_tambah"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors text-sm"
                                        placeholder="Contoh: 0" oninput="clearFieldError(this)">
                                </div>

                                {{-- Nilai Maximum --}}
                                <div>
                                    <label for="nilai_max_tambah" class="block text-xs font-medium text-gray-600 mb-1">
                                        Nilai Maximum
                                    </label>
                                    <input type="number" name="nilai_max" id="nilai_max_tambah"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors text-sm"
                                        placeholder="Contoh: 100" oninput="clearFieldError(this)">
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Tentukan rentang nilai yang valid untuk input penilaian
                            </p>
                        </div>

                        {{-- Conditional Dropdown Options Fields --}}
                        <div id="dropdown_options_tambah"
                            class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-700">Dropdown Options</h4>
                                <button type="button" onclick="addDropdownOption('tambah')"
                                    class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Option
                                </button>
                            </div>
                            <div id="options_container_tambah" class="space-y-2">
                                {{-- Options will be added here dynamically --}}
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Tambahkan minimal 2 pilihan untuk dropdown. Contoh: "Lolos", "Tidak Lolos"
                            </p>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit"
                            class="flex-1 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 cursor-pointer">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Simpan Kriteria
                        </button>
                        <button type="button" data-modal-hide="modal-tambah-kriteria"
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Kriteria --}}
    <div id="modal-edit-kriteria" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Edit Kriteria Penilaian
                    </h3>
                    <button type="button" data-modal-hide="modal-edit-kriteria"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors cursor-pointer">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form id="form-edit-kriteria" method="POST" class="p-6 space-y-4"
                    onsubmit="return validateKriteriaForm('edit')">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="kriteria_id" id="kriteria_id_edit">

                    {{-- Nama Kriteria --}}
                    <div>
                        <label for="nama_kriteria_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Kriteria <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kriteria" id="nama_kriteria_edit" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi" id="deskripsi_edit" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></textarea>
                    </div>

                    {{-- Tipe Kriteria --}}
                    <div>
                        <label for="tipe_kriteria_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Kriteria <span class="text-red-500">*</span>
                        </label>
                        <select name="tipe_kriteria" id="tipe_kriteria_edit" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Pilih Tipe</option>
                            <option value="benefit">Benefit (Semakin tinggi semakin baik)</option>
                            <option value="cost">Cost (Semakin rendah semakin baik)</option>
                        </select>
                    </div>

                    {{-- Bobot --}}
                    <div>
                        <label for="bobot_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Bobot (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="bobot" id="bobot_edit" required step="0.01" min="0"
                                max="100"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="info-bobot-edit">
                            Loading...
                        </p>
                    </div>

                    {{-- Assign ke Supervisor --}}
                    <div>
                        <label for="assigned_to_supervisor_id_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Assign ke Supervisor <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <select name="assigned_to_supervisor_id" id="assigned_to_supervisor_id_edit"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">-- Tidak di-assign (Akses Semua) --</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">
                                    {{ $supervisor->nama }} ({{ $supervisor->nik ?? '-' }}) -
                                    {{ $supervisor->divisi ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Jika di-assign, hanya supervisor terpilih yang dapat melakukan penilaian pada kriteria ini
                        </p>
                    </div>

                    {{-- Tipe Input Penilaian --}}
                    <div>
                        <label for="tipe_input_edit" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Input Penilaian <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <select name="tipe_input" id="tipe_input_edit" onchange="toggleKriteriaRangeFields('edit')"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">-- Tidak ada (Gunakan Sub-Kriteria) --</option>
                            <option value="angka">Input Angka (Rentang Nilai)</option>
                            <option value="rating">Rating Scale (1-5)</option>
                            <option value="dropdown">Dropdown Selection (Pilihan Tetap)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <strong>Kosongkan</strong> jika kriteria ini akan memiliki sub-kriteria.<br>
                            <strong>Pilih tipe</strong> jika kriteria ini langsung digunakan untuk penilaian tanpa
                            sub-kriteria.
                        </p>

                        {{-- Conditional Range Fields --}}
                        <div id="range_fields_edit" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Range Nilai</h4>
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Nilai Minimum --}}
                                <div>
                                    <label for="nilai_min_edit" class="block text-xs font-medium text-gray-600 mb-1">
                                        Nilai Minimum
                                    </label>
                                    <input type="number" name="nilai_min" id="nilai_min_edit"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm"
                                        placeholder="Contoh: 0" oninput="clearFieldError(this)">
                                </div>

                                {{-- Nilai Maximum --}}
                                <div>
                                    <label for="nilai_max_edit" class="block text-xs font-medium text-gray-600 mb-1">
                                        Nilai Maximum
                                    </label>
                                    <input type="number" name="nilai_max" id="nilai_max_edit"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm"
                                        placeholder="Contoh: 100" oninput="clearFieldError(this)">
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Tentukan rentang nilai yang valid untuk input penilaian
                            </p>
                        </div>

                        {{-- Conditional Dropdown Options Fields --}}
                        <div id="dropdown_options_edit"
                            class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-700">Dropdown Options</h4>
                                <button type="button" onclick="addDropdownOption('edit')"
                                    class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Option
                                </button>
                            </div>
                            <div id="options_container_edit" class="space-y-2">
                                {{-- Options will be added here dynamically --}}
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Tambahkan minimal 2 pilihan untuk dropdown. Contoh: "Lolos", "Tidak Lolos"
                            </p>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit"
                            class="flex-1 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 cursor-pointer">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Update Kriteria
                        </button>
                        <button type="button" data-modal-hide="modal-edit-kriteria"
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div id="modal-hapus-kriteria" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl">
                <div class="p-6 text-center">
                    <svg class="mx-auto mb-4 w-12 h-12 text-red-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                    <p class="mb-5 text-sm text-gray-600">
                        Apakah Anda yakin ingin menghapus kriteria<br>
                        <strong id="nama-kriteria-hapus" class="text-gray-900"></strong>?
                    </p>

                    <form id="form-hapus-kriteria" method="POST" class="inline-block w-full">
                        @csrf
                        @method('DELETE')
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="flex-1 px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer">
                                Ya, Hapus
                            </button>
                            <button type="button" data-modal-hide="modal-hapus-kriteria"
                                class="flex-1 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
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
    </script>

    <script>
        // Clear all validation errors
        function clearValidationErrors(mode) {
            // Remove all error messages
            const form = mode === 'tambah' ? document.getElementById('form-tambah-kriteria') : document.getElementById(
                'form-edit-kriteria');
            if (form) {
                const errorMessages = form.querySelectorAll('.validation-error-message');
                errorMessages.forEach(msg => msg.remove());

                // Remove error styling from inputs
                const errorInputs = form.querySelectorAll('.border-red-500, .border-red-300');
                errorInputs.forEach(input => {
                    input.classList.remove('border-red-500', 'border-red-300', 'focus:ring-red-500',
                        'focus:border-red-500');
                    input.classList.add('border-gray-300', 'focus:ring-green-500', 'focus:border-green-500');
                });
            }
        }

        // Show validation error on field
        function showFieldError(fieldElement, errorMessage) {
            // Add error styling to input
            fieldElement.classList.remove('border-gray-300', 'focus:ring-green-500', 'focus:border-green-500',
                'focus:ring-blue-500', 'focus:border-blue-500');
            fieldElement.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');

            // Check if error message already exists
            let errorDiv = fieldElement.parentElement.querySelector('.validation-error-message');
            if (!errorDiv) {
                // Create error message element
                errorDiv = document.createElement('div');
                errorDiv.className = 'validation-error-message flex items-center mt-1 text-sm text-red-600';
                errorDiv.innerHTML = `
                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span>${errorMessage}</span>
                `;
                fieldElement.parentElement.appendChild(errorDiv);
            } else {
                // Update existing error message
                errorDiv.querySelector('span').textContent = errorMessage;
            }
        }

        // Show validation error on dropdown options container
        function showDropdownContainerError(container, errorMessage) {
            // Check if error message already exists
            let errorDiv = container.parentElement.querySelector('.validation-error-message');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className =
                    'validation-error-message flex items-center mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600';
                errorDiv.innerHTML = `
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">${errorMessage}</span>
                `;
                container.parentElement.appendChild(errorDiv);
            } else {
                errorDiv.querySelector('span').textContent = errorMessage;
            }
        }

        // Validate Kriteria Form before submission
        function validateKriteriaForm(mode) {
            // Clear previous errors
            clearValidationErrors(mode);

            const tipeInput = document.getElementById(`tipe_input_${mode}`).value;
            const nilaiMin = document.getElementById(`nilai_min_${mode}`);
            const nilaiMax = document.getElementById(`nilai_max_${mode}`);
            const container = document.getElementById(`options_container_${mode}`);
            let hasError = false;

            // Validate dropdown options
            if (tipeInput === 'dropdown') {
                const options = container.querySelectorAll('[id^="option_item_"]');

                if (options.length < 2) {
                    showDropdownContainerError(container,
                        'Tipe input dropdown memerlukan minimal 2 pilihan dropdown options.');
                    hasError = true;
                }

                // Validate each option has nama and nilai_tetap
                options.forEach((option) => {
                    const namaInput = option.querySelector('input[name*="[nama]"]');
                    const nilaiInput = option.querySelector('input[name*="[nilai_tetap]"]');

                    if (!namaInput || !namaInput.value.trim()) {
                        showFieldError(namaInput, 'Nama option wajib diisi');
                        hasError = true;
                    }
                    if (!nilaiInput || !nilaiInput.value || isNaN(nilaiInput.value)) {
                        showFieldError(nilaiInput, 'Nilai tetap harus berupa angka');
                        hasError = true;
                    }
                });
            }

            // Validate nilai_min and nilai_max for angka and rating
            if (tipeInput === 'angka' || tipeInput === 'rating') {
                if (!nilaiMin.value || nilaiMin.value === '') {
                    showFieldError(nilaiMin, 'Nilai minimum wajib diisi untuk tipe input angka atau rating');
                    hasError = true;
                }
                if (!nilaiMax.value || nilaiMax.value === '') {
                    showFieldError(nilaiMax, 'Nilai maximum wajib diisi untuk tipe input angka atau rating');
                    hasError = true;
                } else if (nilaiMin.value && parseFloat(nilaiMax.value) <= parseFloat(nilaiMin.value)) {
                    showFieldError(nilaiMax, 'Nilai maximum harus lebih besar dari nilai minimum');
                    hasError = true;
                }
            }

            if (hasError) {
                // Scroll to first error
                const firstError = document.querySelector('.validation-error-message');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return false;
            }

            return true;
        }

        // Toggle Range Fields for Kriteria Modal (sama seperti sub-kriteria)
        function toggleKriteriaRangeFields(mode) {
            // Clear validation errors when changing tipe input
            clearValidationErrors(mode);

            const tipeInput = document.getElementById(`tipe_input_${mode}`).value;
            const rangeFields = document.getElementById(`range_fields_${mode}`);
            const dropdownOptions = document.getElementById(`dropdown_options_${mode}`);
            const nilaiMin = document.getElementById(`nilai_min_${mode}`);
            const nilaiMax = document.getElementById(`nilai_max_${mode}`);

            // Hide both sections first
            rangeFields.classList.add('hidden');
            dropdownOptions.classList.add('hidden');

            if (tipeInput === 'angka' || tipeInput === 'rating') {
                rangeFields.classList.remove('hidden');
                nilaiMin.required = true;
                nilaiMax.required = true;

                // Set default values for rating
                if (tipeInput === 'rating') {
                    nilaiMin.value = 1;
                    nilaiMax.value = 5;
                } else {
                    nilaiMin.value = '';
                    nilaiMax.value = '';
                }
            } else if (tipeInput === 'dropdown') {
                dropdownOptions.classList.remove('hidden');
                nilaiMin.required = false;
                nilaiMax.required = false;
                nilaiMin.value = '';
                nilaiMax.value = '';

                // Initialize with 2 empty options for tambah mode
                const container = document.getElementById(`options_container_${mode}`);
                if (mode === 'tambah' && container.children.length === 0) {
                    addDropdownOption(mode);
                    addDropdownOption(mode);
                }
            } else {
                nilaiMin.required = false;
                nilaiMax.required = false;
                nilaiMin.value = '';
                nilaiMax.value = '';
            }
        }

        // Counter for dropdown options
        let dropdownOptionCounter = {
            tambah: 0,
            edit: 0
        };

        // Add dropdown option field
        function addDropdownOption(mode) {
            const container = document.getElementById(`options_container_${mode}`);
            const index = dropdownOptionCounter[mode]++;

            // Clear dropdown container error when adding new option
            const containerError = container.parentElement.querySelector('.validation-error-message');
            if (containerError) {
                containerError.remove();
            }

            const optionDiv = document.createElement('div');
            optionDiv.className = 'flex items-start gap-2 p-3 bg-white border border-gray-200 rounded-lg';
            optionDiv.id = `option_item_${mode}_${index}`;

            optionDiv.innerHTML = `
                <div class="flex-1 grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            Nama Option <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            name="dropdown_options[${index}][nama]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            placeholder="Contoh: Lolos"
                            oninput="clearFieldError(this)"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            Nilai Tetap <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                            name="dropdown_options[${index}][nilai_tetap]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            placeholder="Contoh: 100"
                            step="0.01"
                            oninput="clearFieldError(this)"
                            required>
                    </div>
                </div>
                <button type="button"
                    onclick="removeDropdownOption(${index}, '${mode}')"
                    class="mt-6 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Hapus option">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;

            container.appendChild(optionDiv);
        }

        // Clear field error when user starts typing
        function clearFieldError(fieldElement) {
            // Remove error styling
            fieldElement.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            fieldElement.classList.add('border-gray-300', 'focus:ring-blue-500', 'focus:border-blue-500');

            // Remove error message
            const errorDiv = fieldElement.parentElement.querySelector('.validation-error-message');
            if (errorDiv) {
                errorDiv.remove();
            }
        }

        // Remove dropdown option field
        function removeDropdownOption(index, mode) {
            const optionDiv = document.getElementById(`option_item_${mode}_${index}`);
            if (optionDiv) {
                optionDiv.remove();
            }
        }

        // Clear dropdown options container
        function clearDropdownOptions(mode) {
            const container = document.getElementById(`options_container_${mode}`);
            container.innerHTML = '';
            dropdownOptionCounter[mode] = 0;
        }

        // Edit Kriteria
        function editKriteria(id) {
            // Fetch data kriteria
            fetch(`/kriteria/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const kriteria = data.data;

                        // Populate form
                        document.getElementById('kriteria_id_edit').value = kriteria.id;
                        document.getElementById('nama_kriteria_edit').value = kriteria.nama_kriteria;
                        document.getElementById('deskripsi_edit').value = kriteria.deskripsi || '';
                        document.getElementById('tipe_kriteria_edit').value = kriteria.tipe_kriteria;
                        document.getElementById('bobot_edit').value = Math.round(kriteria.bobot);

                        // Populate new fields
                        document.getElementById('assigned_to_supervisor_id_edit').value = kriteria
                            .assigned_to_supervisor_id || '';
                        document.getElementById('tipe_input_edit').value = kriteria.tipe_input || '';
                        document.getElementById('nilai_min_edit').value = kriteria.nilai_min !== null ? kriteria
                            .nilai_min : '';
                        document.getElementById('nilai_max_edit').value = kriteria.nilai_max !== null ? kriteria
                            .nilai_max : '';

                        // Clear dropdown options container first
                        clearDropdownOptions('edit');

                        // Toggle range fields visibility based on tipe_input
                        toggleKriteriaRangeFields('edit');

                        // Load existing dropdown options if tipe_input is dropdown
                        if (kriteria.tipe_input === 'dropdown' && kriteria.dropdown_options && kriteria.dropdown_options
                            .length > 0) {
                            kriteria.dropdown_options.forEach((option, index) => {
                                const container = document.getElementById('options_container_edit');
                                const optionIndex = dropdownOptionCounter.edit++;

                                const optionDiv = document.createElement('div');
                                optionDiv.className =
                                    'flex items-start gap-2 p-3 bg-white border border-gray-200 rounded-lg';
                                optionDiv.id = `option_item_edit_${optionIndex}`;

                                optionDiv.innerHTML = `
                                    <div class="flex-1 grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                Nama Option <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                name="dropdown_options[${optionIndex}][nama]"
                                                value="${option.nama_kriteria}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                placeholder="Contoh: Lolos"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                Nilai Tetap <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number"
                                                name="dropdown_options[${optionIndex}][nilai_tetap]"
                                                value="${option.nilai_tetap}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                placeholder="Contoh: 100"
                                                step="0.01"
                                                required>
                                        </div>
                                    </div>
                                    <button type="button"
                                        onclick="removeDropdownOption(${optionIndex}, 'edit')"
                                        class="mt-6 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus option">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                `;

                                container.appendChild(optionDiv);
                            });
                        }

                        // Update form action
                        document.getElementById('form-edit-kriteria').action = `/kriteria/${kriteria.id}`;

                        // Get sisa bobot (exclude kriteria yang sedang diedit)
                        fetch(`/kriteria/total-bobot?exclude_id=${kriteria.id}`)
                            .then(response => response.json())
                            .then(bobotData => {
                                const sisaBobot = Math.round(bobotData.sisa_bobot);
                                const totalBobot = Math.round(bobotData.total_bobot);
                                const colorClass = sisaBobot > 0 ? 'text-green-600' : 'text-red-600';
                                document.getElementById('info-bobot-edit').innerHTML = `
                                    Total bobot kriteria lain: <strong>${totalBobot}%</strong> |
                                    Sisa: <strong class="${colorClass}">${sisaBobot}%</strong>
                                `;
                            });

                        // Show backdrop terlebih dahulu
                        showBackdrop('modal-edit-kriteria');

                        // Show modal
                        const modalElement = document.getElementById('modal-edit-kriteria');
                        modalElement.classList.remove('hidden');
                        modalElement.classList.add('flex');
                        modalElement.setAttribute('aria-modal', 'true');
                        modalElement.setAttribute('role', 'dialog');
                        modalElement.removeAttribute('aria-hidden');

                        // Setup event listener untuk menutup modal (hanya sekali)
                        if (!modalElement.hasAttribute('data-listeners-attached')) {
                            setupModalCloseListeners('modal-edit-kriteria');
                            modalElement.setAttribute('data-listeners-attached', 'true');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengambil data kriteria');
                });
        }

        // Confirm Delete
        function confirmDelete(id, nama) {
            document.getElementById('nama-kriteria-hapus').textContent = nama;
            document.getElementById('form-hapus-kriteria').action = `/kriteria/${id}`;

            // Show backdrop
            showBackdrop('modal-hapus-kriteria');

            // Show modal
            const modalElement = document.getElementById('modal-hapus-kriteria');
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
            modalElement.setAttribute('aria-modal', 'true');
            modalElement.setAttribute('role', 'dialog');
            modalElement.removeAttribute('aria-hidden');

            // Setup event listener
            if (!modalElement.hasAttribute('data-listeners-attached')) {
                setupModalCloseListeners('modal-hapus-kriteria');
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

        // Show Toast Function
        function showToast(message, type = 'success') {
            // Remove existing toast if any
            const existingToast = document.getElementById('custom-toast');
            if (existingToast) {
                existingToast.remove();
            }

            // Create toast element
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

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }
    </script>
@endsection
