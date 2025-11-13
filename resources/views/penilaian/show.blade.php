@extends('layouts.dashboard')

@section('title', 'Detail Penilaian Karyawan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('penilaian.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Penilaian Karyawan</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail Penilaian</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header dengan Action Buttons -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Penilaian Karyawan</h1>
            <p class="mt-1 text-sm text-gray-600">Rincian lengkap penilaian per kriteria</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('penilaian.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>

            <a href="{{ route('penilaian.edit', [$karyawan->id, $bulan, $tahun]) }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Penilaian
            </a>

            <button
                type="button"
                onclick="showDeleteModal()"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus
            </button>
        </div>
    </div>

    <!-- Informasi Karyawan & Periode -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Karyawan Info -->
            <div class="flex items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-blue-700 uppercase tracking-wider">Karyawan</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $karyawan->nama }}</p>
                    <p class="text-sm text-gray-600">{{ $karyawan->email }}</p>
                </div>
            </div>

            <!-- Periode Info -->
            <div class="flex items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-blue-700 uppercase tracking-wider">Periode Penilaian</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $periodeLabel }}</p>
                    <p class="text-sm text-gray-600">Bulan {{ $bulan }}, Tahun {{ $tahun }}</p>
                </div>
            </div>

            <!-- Tanggal Penilaian -->
            @if($penilaianList->first())
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs font-medium text-blue-700 uppercase tracking-wider">Tanggal Input</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">
                            {{ $penilaianList->first()->tanggal_penilaian->format('d M Y') }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ $penilaianList->first()->tanggal_penilaian->format('H:i') }} WIB
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Dinilai Oleh -->
        @php
            $firstPenilaian = $penilaianList->first();
            $dinilaiOleh = null;

            if ($firstPenilaian) {
                if ($firstPenilaian->dinilai_oleh_supervisor_id) {
                    $dinilaiOleh = $firstPenilaian->dinilaiOlehSupervisor;
                    $rolePenilai = 'Supervisor';
                } elseif ($firstPenilaian->created_by_super_admin_id) {
                    $dinilaiOleh = $firstPenilaian->createdBySuperAdmin;
                    $rolePenilai = 'Super Admin';
                } elseif ($firstPenilaian->created_by_hrd_id) {
                    $dinilaiOleh = $firstPenilaian->createdByHRD;
                    $rolePenilai = 'HRD';
                }
            }
        @endphp

        @if($dinilaiOleh)
            <div class="mt-4 pt-4 border-t border-blue-200">
                <p class="text-xs font-medium text-blue-700 uppercase tracking-wider">Dinilai Oleh</p>
                <p class="text-sm text-gray-700 mt-1">
                    <strong>{{ $dinilaiOleh->nama }}</strong> ({{ $rolePenilai }})
                </p>
            </div>
        @endif
    </div>

    <!-- Penilaian Detail: Grouped by Kriteria -->
    @if($penilaianGrouped->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak Ada Data Penilaian</h3>
                <p class="mt-1 text-sm text-gray-500">Data penilaian tidak ditemukan untuk periode ini.</p>
            </div>
        </div>
    @else
        <!-- Loop through each Kriteria -->
        <div class="space-y-6">
            @foreach($penilaianGrouped as $kriteriaId => $penilaianItems)
                @php
                    $kriteriaInfo = $kriteriaList->get($kriteriaId);
                    $totalNilai = 0;
                    $countSubKriteria = $penilaianItems->count();
                @endphp

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Kriteria Header -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">
                                    {{ $kriteriaInfo->nama_kriteria ?? 'Kriteria' }}
                                </h2>
                                @if($kriteriaInfo && $kriteriaInfo->deskripsi)
                                    <p class="text-sm text-gray-600 mt-1">{{ $kriteriaInfo->deskripsi }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm font-semibold rounded-full">
                                    Bobot: {{ $kriteriaInfo->bobot ?? '0' }}%
                                </span>
                                <p class="text-xs text-gray-600 mt-2">{{ $countSubKriteria }} Sub-Kriteria</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Kriteria Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Sub-Kriteria
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Bobot
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Tipe Input
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Nilai
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Catatan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($penilaianItems as $item)
                                    @php
                                        $totalNilai += $item->nilai;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <!-- Sub-Kriteria Name -->
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $item->subKriteria->nama_kriteria ?? '-' }}
                                                </p>
                                                @if($item->subKriteria && $item->subKriteria->deskripsi)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $item->subKriteria->deskripsi }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Bobot -->
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                                                {{ $item->subKriteria->bobot ?? '0' }}%
                                            </span>
                                        </td>

                                        <!-- Tipe Input -->
                                        <td class="px-6 py-4 text-center">
                                            @if($item->subKriteria)
                                                @if($item->subKriteria->tipe_input === 'angka')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Angka
                                                    </span>
                                                @elseif($item->subKriteria->tipe_input === 'rating')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                        Rating
                                                    </span>
                                                @elseif($item->subKriteria->tipe_input === 'dropdown')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-purple-100 text-purple-800 text-xs font-medium rounded">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Dropdown
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>

                                        <!-- Nilai with Visual Display -->
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <!-- Numeric Value -->
                                                <span class="text-lg font-bold text-gray-900">
                                                    {{ number_format($item->nilai, 2) }}
                                                </span>

                                                <!-- Visual Representation -->
                                                @if($item->subKriteria && $item->subKriteria->tipe_input === 'rating')
                                                    <div class="flex items-center mt-1">
                                                        @for($star = 1; $star <= 5; $star++)
                                                            <svg class="w-4 h-4 {{ $star <= $item->nilai ? 'text-yellow-400' : 'text-gray-300' }}"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Catatan -->
                                        <td class="px-6 py-4">
                                            @if($item->catatan)
                                                <p class="text-sm text-gray-700">{{ $item->catatan }}</p>
                                            @else
                                                <span class="text-sm text-gray-400 italic">Tidak ada catatan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <!-- Table Footer with Summary -->
                            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                                        Rata-rata Nilai:
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-base font-bold rounded">
                                            {{ $countSubKriteria > 0 ? number_format($totalNilai / $countSubKriteria, 2) : '0.00' }}
                                        </span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal: Konfirmasi Hapus -->
<div id="modal-delete" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Background Overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="hideDeleteModal()"></div>

        <!-- Modal Content -->
        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <!-- Icon -->
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" onclick="hideDeleteModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-bold leading-6 text-gray-900">
                        Hapus Penilaian
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">
                            Apakah Anda yakin ingin menghapus semua penilaian untuk <strong>{{ $karyawan->nama }}</strong> periode <strong>{{ $periodeLabel }}</strong>?
                        </p>
                        <p class="text-sm text-red-600 font-semibold mt-2">
                            Tindakan ini tidak dapat dibatalkan!
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form action="{{ route('penilaian.destroy', [$karyawan->id, $bulan, $tahun]) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-semibold text-white bg-red-600 border border-transparent rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Hapus
                    </button>
                </form>
                <button type="button" onclick="hideDeleteModal()"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-5 right-5 z-50 space-y-3"></div>

<script>
    // ========================================
    // Delete Modal Functions
    // ========================================
    function showDeleteModal() {
        document.getElementById('modal-delete').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideDeleteModal() {
        document.getElementById('modal-delete').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // ========================================
    // Toast Notification System
    // ========================================
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');

        const bgColor = type === 'success' ? 'bg-green-500' :
                        type === 'error' ? 'bg-red-500' :
                        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';

        const icon = type === 'success' ?
            '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' :
            type === 'error' ?
            '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
            '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';

        const toast = document.createElement('div');
        toast.className = `flex items-center w-full max-w-sm p-4 text-white ${bgColor} rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-0 opacity-100`;
        toast.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-white bg-opacity-20">
                ${icon}
            </div>
            <div class="ml-3 text-sm font-medium">${message}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-white hover:bg-opacity-20 transition-colors" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        `;

        toastContainer.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // ========================================
    // Show Flash Messages as Toast
    // ========================================
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast("{{ $error }}", 'error');
            @endforeach
        @endif
    });
</script>
@endsection
