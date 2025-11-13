@extends('layouts.dashboard')

@section('title', 'Edit Penilaian Karyawan')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Penilaian</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Penilaian Karyawan</h1>
        <p class="mt-1 text-sm text-gray-600">Ubah penilaian karyawan untuk periode yang dipilih</p>
    </div>

    <!-- Info Card: Karyawan & Periode (Read-only) -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-semibold text-blue-800">Informasi Penilaian</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p><strong>Karyawan:</strong> {{ $karyawan->nama }} ({{ $karyawan->email }})</p>
                    <p class="mt-1"><strong>Periode:</strong>
                        @php
                            $namaBulan = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        {{ $namaBulan[$bulan] }} {{ $tahun }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    @if($kriteria->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak Ada Kriteria Aktif</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada kriteria penilaian yang aktif saat ini.</p>
                <div class="mt-6">
                    <a href="{{ route('penilaian.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Main Form -->
        <form action="{{ route('penilaian.update', [$karyawan->id, $bulan, $tahun]) }}" method="POST" id="form-edit-penilaian">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Form Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Formulir Penilaian</h2>
                    <p class="mt-1 text-sm text-gray-600">Ubah nilai untuk setiap sub-kriteria di bawah ini</p>
                </div>

                <!-- Form Body: Loop Kriteria & Sub-Kriteria -->
                <div class="px-6 py-6 space-y-8">
                    @foreach($kriteria as $kriteriaIndex => $kriteriaItem)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <!-- Kriteria Header -->
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-5 py-3 border-b border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">{{ $kriteriaItem->nama_kriteria }}</h3>
                                        @if($kriteriaItem->deskripsi)
                                            <p class="text-xs text-gray-600 mt-1">{{ $kriteriaItem->deskripsi }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                                        Bobot: {{ $kriteriaItem->bobot }}%
                                    </span>
                                </div>
                            </div>

                            <!-- Sub-Kriteria List -->
                            <div class="divide-y divide-gray-200">
                                @foreach($kriteriaItem->subKriteria as $subIndex => $subItem)
                                    <div class="px-5 py-4 hover:bg-gray-50 transition-colors duration-150">
                                        <!-- Sub-Kriteria Info -->
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <label class="text-sm font-semibold text-gray-800">
                                                    {{ $subItem->nama_kriteria }}
                                                    <span class="text-red-500">*</span>
                                                </label>
                                                @if($subItem->deskripsi)
                                                    <p class="text-xs text-gray-500 mt-1">{{ $subItem->deskripsi }}</p>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded ml-3">
                                                Bobot: {{ $subItem->bobot }}%
                                            </span>
                                        </div>

                                        <!-- Hidden Fields -->
                                        <input type="hidden" name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][id_kriteria]" value="{{ $kriteriaItem->id }}">
                                        <input type="hidden" name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][id_sub_kriteria]" value="{{ $subItem->id }}">

                                        <!-- Dynamic Input Based on tipe_input -->
                                        <div class="mt-3">
                                            @php
                                                $existingNilai = $existingPenilaian->get($subItem->id)->nilai ?? '';
                                                $existingCatatan = $existingPenilaian->get($subItem->id)->catatan ?? '';
                                            @endphp

                                            @if($subItem->tipe_input === 'angka')
                                                <!-- Input Type: Angka (Number) -->
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-1">
                                                        <input
                                                            type="number"
                                                            name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                                            id="nilai_{{ $kriteriaIndex }}_{{ $subIndex }}"
                                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                            min="{{ $subItem->nilai_min }}"
                                                            max="{{ $subItem->nilai_max }}"
                                                            step="0.01"
                                                            value="{{ old('penilaian.'.$kriteriaIndex.'_'.$subIndex.'.nilai', $existingNilai) }}"
                                                            placeholder="Masukkan nilai"
                                                            required
                                                        >
                                                    </div>
                                                    <span class="text-xs text-gray-500 whitespace-nowrap">
                                                        Min: {{ $subItem->nilai_min }} - Max: {{ $subItem->nilai_max }}
                                                    </span>
                                                </div>

                                            @elseif($subItem->tipe_input === 'rating')
                                                <!-- Input Type: Rating (Stars) -->
                                                <div class="flex items-center space-x-2">
                                                    @for($star = $subItem->nilai_min; $star <= $subItem->nilai_max; $star++)
                                                        <label class="cursor-pointer">
                                                            <input
                                                                type="radio"
                                                                name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                                                value="{{ $star }}"
                                                                class="sr-only peer"
                                                                onchange="updateStarDisplay(this)"
                                                                {{ old('penilaian.'.$kriteriaIndex.'_'.$subIndex.'.nilai', $existingNilai) == $star ? 'checked' : '' }}
                                                                required
                                                            >
                                                            <svg class="w-8 h-8 {{ old('penilaian.'.$kriteriaIndex.'_'.$subIndex.'.nilai', $existingNilai) >= $star ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors duration-150"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        </label>
                                                    @endfor
                                                    <span class="ml-3 text-xs text-gray-500">
                                                        ({{ $subItem->nilai_min }} - {{ $subItem->nilai_max }} bintang)
                                                    </span>
                                                </div>

                                            @elseif($subItem->tipe_input === 'dropdown')
                                                <!-- Input Type: Dropdown (Select) -->
                                                @if($subItem->dropdownOptions && $subItem->dropdownOptions->count() > 0)
                                                    <select
                                                        name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                                        id="nilai_{{ $kriteriaIndex }}_{{ $subIndex }}"
                                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                        required
                                                    >
                                                        <option value="">-- Pilih Opsi --</option>
                                                        @foreach($subItem->dropdownOptions as $option)
                                                            <option value="{{ $option->nilai_tetap }}"
                                                                {{ old('penilaian.'.$kriteriaIndex.'_'.$subIndex.'.nilai', $existingNilai) == $option->nilai_tetap ? 'selected' : '' }}>
                                                                {{ $option->nama_kriteria }} (Nilai: {{ $option->nilai_tetap }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <div class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2">
                                                        <strong>Peringatan:</strong> Sub-kriteria ini tidak memiliki opsi dropdown. Silakan tambahkan opsi terlebih dahulu.
                                                    </div>
                                                @endif
                                            @endif
                                        </div>

                                        <!-- Optional: Catatan -->
                                        <div class="mt-3">
                                            <label class="text-xs font-medium text-gray-600 block mb-1">
                                                Catatan (Opsional)
                                            </label>
                                            <textarea
                                                name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][catatan]"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                rows="2"
                                                placeholder="Tambahkan catatan khusus (opsional)"
                                            >{{ old('penilaian.'.$kriteriaIndex.'_'.$subIndex.'.catatan', $existingCatatan) }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Form Footer: Action Buttons -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <a href="{{ route('penilaian.show', [$karyawan->id, $bulan, $tahun]) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Penilaian
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-5 right-5 z-50 space-y-3"></div>

<script>
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
    // Star Rating Display Update
    // ========================================
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

    // ========================================
    // Form Validation Before Submit
    // ========================================
    document.getElementById('form-edit-penilaian')?.addEventListener('submit', function(e) {
        const requiredInputs = this.querySelectorAll('[required]');
        let hasError = false;
        let firstErrorElement = null;

        requiredInputs.forEach(input => {
            // Check if input is radio
            if (input.type === 'radio') {
                const radioName = input.name;
                const radioGroup = document.querySelectorAll(`input[name="${radioName}"]`);
                const isChecked = Array.from(radioGroup).some(radio => radio.checked);

                if (!isChecked && !hasError) {
                    hasError = true;
                    firstErrorElement = input;
                }
            } else {
                // Regular input/select/textarea
                if (!input.value.trim() && !hasError) {
                    hasError = true;
                    firstErrorElement = input;
                }
            }
        });

        if (hasError) {
            e.preventDefault();
            showToast('Mohon lengkapi semua field yang wajib diisi', 'error');

            // Scroll to first error
            if (firstErrorElement) {
                firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => {
                    if (firstErrorElement.type !== 'radio') {
                        firstErrorElement.focus();
                    }
                }, 500);
            }
        }
    });

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
