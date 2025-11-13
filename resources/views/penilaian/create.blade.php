@extends('layouts.dashboard')

@section('title', 'Tambah Penilaian - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('penilaian.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Penilaian Karyawan
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah Penilaian</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Tambah Penilaian Karyawan</h2>
        <p class="mt-2 text-sm text-gray-600">Input penilaian kinerja karyawan berdasarkan kriteria yang telah ditentukan</p>
    </div>

    {{-- Selection Form: Pilih Karyawan & Periode --}}
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
                            @foreach($karyawanList as $karyawanItem)
                                <option value="{{ $karyawanItem->id }}" {{ request('karyawan_id') == $karyawanItem->id ? 'selected' : '' }}>
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
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $namaBulan)
                                <option value="{{ $index + 1 }}" {{ $bulan == ($index + 1) ? 'selected' : '' }}>
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
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                        <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Load Form Penilaian
                    </button>
                    @if($karyawan)
                        <a href="{{ route('penilaian.create') }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($karyawan && $kriteria->isNotEmpty())
        {{-- Info Karyawan & Periode --}}
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                        <span class="text-white font-bold text-xl">
                            {{ strtoupper(substr($karyawan->nama, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $karyawan->nama }}</h3>
                        <p class="text-sm text-gray-600">{{ $karyawan->email }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            <strong>Periode:</strong>
                            @php
                                $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            @endphp
                            {{ $namaBulan[$bulan] }} {{ $tahun }}
                        </p>
                    </div>
                </div>
                @if($existingPenilaian->isNotEmpty())
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
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

            {{-- Loop Through Kriteria --}}
            @foreach($kriteria as $kriteriaIndex => $kriteriaItem)
                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
                    {{-- Kriteria Header --}}
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ $kriteriaIndex + 1 }}</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $kriteriaItem->nama_kriteria }}</h3>
                                    @if($kriteriaItem->deskripsi)
                                        <p class="text-sm text-gray-600">{{ $kriteriaItem->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $kriteriaItem->tipe_kriteria === 'benefit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($kriteriaItem->tipe_kriteria) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Bobot: {{ number_format($kriteriaItem->bobot, 2) }}%</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Kriteria List --}}
                    <div class="p-6 space-y-6">
                        @forelse($kriteriaItem->subKriteria as $subIndex => $subItem)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <div class="mb-3">
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">
                                        {{ $subItem->nama_kriteria }}
                                        <span class="text-red-500">*</span>
                                        <span class="ml-2 text-xs font-normal text-gray-500">(Bobot: {{ number_format($subItem->bobot, 2) }}%)</span>
                                    </label>
                                    @if($subItem->deskripsi)
                                        <p class="text-xs text-gray-600 mb-2">{{ $subItem->deskripsi }}</p>
                                    @endif
                                </div>

                                {{-- Hidden Fields --}}
                                <input type="hidden" name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][id_kriteria]" value="{{ $kriteriaItem->id }}">
                                <input type="hidden" name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][id_sub_kriteria]" value="{{ $subItem->id }}">

                                {{-- Dynamic Input Based on Tipe Input --}}
                                @if($subItem->tipe_input === 'angka')
                                    {{-- Input Angka --}}
                                    <div>
                                        <input type="number"
                                            name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                            id="nilai_{{ $kriteriaItem->id }}_{{ $subItem->id }}"
                                            min="{{ $subItem->nilai_min }}"
                                            max="{{ $subItem->nilai_max }}"
                                            step="0.01"
                                            value="{{ $existingPenilaian->get($subItem->id)->nilai ?? '' }}"
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
                                            @for($star = $subItem->nilai_min; $star <= $subItem->nilai_max; $star++)
                                                <label class="cursor-pointer">
                                                    <input type="radio"
                                                        name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                                        value="{{ $star }}"
                                                        {{ ($existingPenilaian->get($subItem->id)->nilai ?? '') == $star ? 'checked' : '' }}
                                                        required
                                                        class="sr-only peer"
                                                        onchange="updateStarDisplay(this)">
                                                    <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-300 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                </label>
                                            @endfor
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">
                                            Pilih rating dari {{ $subItem->nilai_min }} sampai {{ $subItem->nilai_max }} bintang
                                        </p>
                                    </div>

                                @elseif($subItem->tipe_input === 'dropdown')
                                    {{-- Input Dropdown --}}
                                    <div>
                                        <select name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][nilai]"
                                            id="nilai_{{ $kriteriaItem->id }}_{{ $subItem->id }}"
                                            required
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="">Pilih opsi...</option>
                                            @foreach($subItem->dropdownOptions as $option)
                                                <option value="{{ $option->nilai_tetap }}"
                                                    {{ ($existingPenilaian->get($subItem->id)->nilai ?? '') == $option->nilai_tetap ? 'selected' : '' }}>
                                                    {{ $option->nama_kriteria }} (Nilai: {{ $option->nilai_tetap }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($subItem->dropdownOptions->isEmpty())
                                            <p class="mt-1 text-xs text-red-600">
                                                <svg class="inline-block w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
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
                                    <textarea name="penilaian[{{ $kriteriaIndex }}_{{ $subIndex }}][catatan]"
                                        rows="2"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Tambahkan catatan jika diperlukan...">{{ $existingPenilaian->get($subItem->id)->catatan ?? '' }}</textarea>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <p>Belum ada sub-kriteria untuk kriteria ini</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach

            {{-- Action Buttons --}}
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 p-6">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('penilaian.index') }}"
                        class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                        <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>

                    <button type="submit"
                        class="px-8 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-sm">
                        <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Kriteria Aktif</h3>
            <p class="mt-2 text-sm text-gray-600">
                Tidak ada kriteria penilaian yang aktif saat ini.<br>
                Silakan tambahkan kriteria terlebih dahulu di menu Kriteria.
            </p>
            <div class="mt-6">
                <a href="{{ route('kriteria.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Kelola Kriteria
                </a>
            </div>
        </div>
    @endif

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

        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                @foreach($errors->all() as $error)
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
                    firstEmpty.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
    </script>
@endsection
