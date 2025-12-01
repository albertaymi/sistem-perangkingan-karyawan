@extends('layouts.dashboard')

@section('title', 'Kelola Akun - Sistem Perangkingan Karyawan')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Kelola Akun</h2>
        <p class="mt-2 text-sm text-gray-600">Manajemen akun pengguna sistem perangkingan karyawan</p>
    </div>

    {{-- Filter & Search Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('users.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            Cari User
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Nama, Username, atau NIK">
                    </div>

                    {{-- Filter Role --}}
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Role
                        </label>
                        <select name="role" id="role"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Semua Role</option>
                            @if (auth()->user()->isSuperAdmin())
                                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super
                                    Admin</option>
                            @endif
                            <option value="hrd" {{ request('role') == 'hrd' ? 'selected' : '' }}>HRD</option>
                            <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor
                            </option>
                            <option value="karyawan" {{ request('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                        </select>
                    </div>

                    {{-- Filter Status Approval --}}
                    <div>
                        <label for="status_approval" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Approval
                        </label>
                        <select name="status_approval" id="status_approval"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_approval') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="approved" {{ request('status_approval') == 'approved' ? 'selected' : '' }}>
                                Approved</option>
                            <option value="rejected" {{ request('status_approval') == 'rejected' ? 'selected' : '' }}>
                                Rejected</option>
                        </select>
                    </div>

                    {{-- Filter Status Akun --}}
                    <div>
                        <label for="status_akun" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Akun
                        </label>
                        <select name="status_akun" id="status_akun"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status_akun') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ request('status_akun') == 'tidak_aktif' ? 'selected' : '' }}>
                                Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 shadow-sm cursor-pointer">
                        <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('users.index') }}"
                        class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                        Reset
                    </a>
                    <button type="button" data-modal-target="modal-tambah-user" data-modal-toggle="modal-tambah-user"
                        class="ml-auto px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-sm cursor-pointer">
                        <svg class="inline-block w-5 h-5 mr-2 -mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah User
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama / Username
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            NIK
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Divisi / Jabatan
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status Approval
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status Akun
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-semibold">
                                            {{ strtoupper(substr($user->nama, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->nama }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->nik }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->divisi }}</div>
                                <div class="text-sm text-gray-500">{{ $user->jabatan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->role == 'super_admin')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Super Admin
                                    </span>
                                @elseif($user->role == 'hrd')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        HRD
                                    </span>
                                @elseif($user->role == 'supervisor')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Supervisor
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Karyawan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->status_approval == 'pending')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($user->status_approval == 'approved')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->status_akun == 'aktif')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    {{-- Tombol Approve/Reject (untuk pending dan rejected) --}}
                                    @if ($user->status_approval == 'pending')
                                        <button type="button" data-modal-target="modal-approve-{{ $user->id }}"
                                            data-modal-toggle="modal-approve-{{ $user->id }}"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 cursor-pointer"
                                            title="Setujui">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Setujui
                                        </button>
                                        <button type="button" data-modal-target="modal-reject-{{ $user->id }}"
                                            data-modal-toggle="modal-reject-{{ $user->id }}"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer"
                                            title="Tolak">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Tolak
                                        </button>
                                    @elseif($user->status_approval == 'rejected')
                                        <button type="button" data-modal-target="modal-approve-{{ $user->id }}"
                                            data-modal-toggle="modal-approve-{{ $user->id }}"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 cursor-pointer"
                                            title="Setujui Kembali">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Setujui
                                        </button>
                                    @endif

                                    {{-- Tombol Edit --}}
                                    @if (!($user->isSuperAdmin() && auth()->user()->isHRD()))
                                        <button type="button" onclick="editUser({{ $user->id }})"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 cursor-pointer"
                                            title="Edit">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </button>
                                    @endif

                                    {{-- Tombol Hapus --}}
                                    @if (!($user->isSuperAdmin() && auth()->user()->isHRD()))
                                        <button type="button" data-modal-target="modal-delete-{{ $user->id }}"
                                            data-modal-toggle="modal-delete-{{ $user->id }}"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer"
                                            title="Hapus">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Approve untuk user pending atau rejected --}}
                        @if ($user->status_approval == 'pending' || $user->status_approval == 'rejected')
                            <tr>
                                <td colspan="8" class="p-0">
                                    <div id="modal-approve-{{ $user->id }}" tabindex="-1" aria-hidden="true"
                                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <div class="relative p-4 w-full max-w-md max-h-full">
                                            <div class="relative bg-white rounded-lg shadow">
                                                <div
                                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        Konfirmasi Persetujuan
                                                    </h3>
                                                    <button type="button"
                                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                                                        data-modal-toggle="modal-approve-{{ $user->id }}">
                                                        <svg class="w-3 h-3" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 14 14">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <form action="{{ route('users.approve', $user->id) }}" method="POST"
                                                    class="p-4 md:p-5">
                                                    @csrf
                                                    <div class="mb-4 text-center">
                                                        <svg class="mx-auto mb-4 text-green-600 w-12 h-12" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <p class="text-sm text-gray-600 mb-2">
                                                            @if ($user->status_approval == 'rejected')
                                                                Apakah Anda yakin ingin menyetujui kembali user:
                                                            @else
                                                                Apakah Anda yakin ingin menyetujui pendaftaran:
                                                            @endif
                                                        </p>
                                                        <p class="text-base font-semibold text-gray-900">
                                                            {{ $user->nama }}</p>
                                                        @if ($user->status_approval == 'rejected' && $user->rejection_reason)
                                                            <div
                                                                class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-left">
                                                                <p class="text-xs font-semibold text-red-800 mb-1">Alasan
                                                                    Penolakan Sebelumnya:</p>
                                                                <p class="text-xs text-red-700">
                                                                    {{ $user->rejection_reason }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <button type="submit"
                                                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 cursor-pointer">
                                                            Ya, Setujui
                                                        </button>
                                                        <button type="button"
                                                            data-modal-toggle="modal-approve-{{ $user->id }}"
                                                            class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        {{-- Modal Reject untuk setiap user pending --}}
                        @if ($user->status_approval == 'pending')
                            <tr>
                                <td colspan="8" class="p-0">
                                    <div id="modal-reject-{{ $user->id }}" tabindex="-1" aria-hidden="true"
                                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <div class="relative p-4 w-full max-w-md max-h-full">
                                            <div class="relative bg-white rounded-lg shadow">
                                                <div
                                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        Tolak Pendaftaran
                                                    </h3>
                                                    <button type="button"
                                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                                                        data-modal-toggle="modal-reject-{{ $user->id }}">
                                                        <svg class="w-3 h-3" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 14 14">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <form action="{{ route('users.reject', $user->id) }}" method="POST"
                                                    class="p-4 md:p-5">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <p class="text-sm text-gray-600 mb-3">
                                                            Anda akan menolak pendaftaran:
                                                            <strong>{{ $user->nama }}</strong>
                                                        </p>
                                                        <label for="rejection_reason_{{ $user->id }}"
                                                            class="block text-sm font-medium text-gray-700 mb-2">
                                                            Alasan Penolakan <span class="text-red-500">*</span>
                                                        </label>
                                                        <textarea name="rejection_reason" id="rejection_reason_{{ $user->id }}" rows="4" required
                                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                                            placeholder="Masukkan alasan penolakan..."></textarea>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <button type="submit"
                                                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer">
                                                            Tolak
                                                        </button>
                                                        <button type="button"
                                                            data-modal-toggle="modal-reject-{{ $user->id }}"
                                                            class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        {{-- Modal Delete untuk semua user (kecuali yang tidak bisa dihapus) --}}
                        @if (!($user->isSuperAdmin() && auth()->user()->isHRD()))
                            <tr>
                                <td colspan="8" class="p-0">
                                    <div id="modal-delete-{{ $user->id }}" tabindex="-1" aria-hidden="true"
                                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <div class="relative p-4 w-full max-w-md max-h-full">
                                            <div class="relative bg-white rounded-lg shadow">
                                                <div
                                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        Konfirmasi Hapus
                                                    </h3>
                                                    <button type="button"
                                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                                                        data-modal-toggle="modal-delete-{{ $user->id }}">
                                                        <svg class="w-3 h-3" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 14 14">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                    class="p-4 md:p-5">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="mb-4 text-center">
                                                        <svg class="mx-auto mb-4 text-red-600 w-12 h-12" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                            </path>
                                                        </svg>
                                                        <p class="text-sm text-gray-600 mb-2">
                                                            Apakah Anda yakin ingin menghapus user:
                                                        </p>
                                                        <p class="text-base font-semibold text-gray-900 mb-2">
                                                            {{ $user->nama }}</p>
                                                        <p class="text-xs text-gray-500">Data yang dihapus dapat dipulihkan
                                                            kembali (soft delete)</p>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <button type="submit"
                                                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer">
                                                            Ya, Hapus
                                                        </button>
                                                        <button type="button"
                                                            data-modal-toggle="modal-delete-{{ $user->id }}"
                                                            class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                                <p class="mt-2 text-sm">Tidak ada data user</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
    {{-- Modal Tambah User --}}
    <div id="modal-tambah-user" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Tambah User Baru
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                        data-modal-toggle="modal-tambah-user">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form action="{{ route('users.store') }}" method="POST" class="p-4 md:p-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {{-- Nama --}}
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="nama" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan nama lengkap">
                            @error('nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Username --}}
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username" id="username" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan username">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pr-10"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" onclick="togglePassword('password')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                                    <svg id="password-eye" class="h-5 w-5 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pr-10"
                                    placeholder="Ulangi password">
                                <button type="button" onclick="togglePassword('password_confirmation')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                                    <svg id="password_confirmation-eye" class="h-5 w-5 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIK --}}
                        <div>
                            <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">
                                NIK <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nik" id="nik" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan NIK">
                            @error('nik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role" id="role" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Pilih Role</option>
                                <option value="hrd">HRD</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="karyawan">Karyawan</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Divisi --}}
                        <div>
                            <label for="divisi" class="block text-sm font-medium text-gray-700 mb-2">
                                Divisi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="divisi" id="divisi" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan divisi">
                            @error('divisi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jabatan --}}
                        <div>
                            <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-2">
                                Jabatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="jabatan" id="jabatan" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan jabatan">
                            @error('jabatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status Akun --}}
                        <div>
                            <label for="status_akun" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Akun <span class="text-red-500">*</span>
                            </label>
                            <select name="status_akun" id="status_akun" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                            @error('status_akun')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button type="submit"
                            class="flex-1 px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-sm cursor-pointer">
                            Simpan
                        </button>
                        <button type="button" data-modal-toggle="modal-tambah-user"
                            class="flex-1 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit User --}}
    <div id="modal-edit-user" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Edit User
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer"
                        data-modal-toggle="modal-edit-user">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form id="form-edit-user" method="POST" class="p-4 md:p-5">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {{-- Nama --}}
                        <div>
                            <label for="edit_nama" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="edit_nama" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan nama lengkap">
                        </div>

                        {{-- Username --}}
                        <div>
                            <label for="edit_username" class="block text-sm font-medium text-gray-700 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username" id="edit_username" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan username">
                        </div>

                        {{-- Password (Optional) --}}
                        <div>
                            <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-gray-400 text-xs">(kosongkan jika tidak ingin mengubah)</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="edit_password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pr-10"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" onclick="togglePassword('edit_password')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                                    <svg id="edit_password-eye" class="h-5 w-5 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password
                            </label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="edit_password_confirmation"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pr-10"
                                    placeholder="Ulangi password">
                                <button type="button" onclick="togglePassword('edit_password_confirmation')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                                    <svg id="edit_password_confirmation-eye" class="h-5 w-5 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- NIK --}}
                        <div>
                            <label for="edit_nik" class="block text-sm font-medium text-gray-700 mb-2">
                                NIK <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nik" id="edit_nik" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan NIK">
                        </div>

                        {{-- Role --}}
                        <div>
                            <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role" id="edit_role" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Pilih Role</option>
                                <option value="hrd">HRD</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="karyawan">Karyawan</option>
                            </select>
                        </div>

                        {{-- Divisi --}}
                        <div>
                            <label for="edit_divisi" class="block text-sm font-medium text-gray-700 mb-2">
                                Divisi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="divisi" id="edit_divisi" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan divisi">
                        </div>

                        {{-- Jabatan --}}
                        <div>
                            <label for="edit_jabatan" class="block text-sm font-medium text-gray-700 mb-2">
                                Jabatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="jabatan" id="edit_jabatan" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan jabatan">
                        </div>

                        {{-- Status Akun --}}
                        <div>
                            <label for="edit_status_akun" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Akun <span class="text-red-500">*</span>
                            </label>
                            <select name="status_akun" id="edit_status_akun" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button type="submit"
                            class="flex-1 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 shadow-sm cursor-pointer">
                            Update
                        </button>
                        <button type="button" data-modal-toggle="modal-edit-user"
                            class="flex-1 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150 cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Fungsi toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');

            if (field.type === 'password') {
                field.type = 'text';
                eye.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            `;
            } else {
                field.type = 'password';
                eye.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
            }
        }

        // Fungsi edit user
        async function editUser(userId) {
            try {
                // Fetch data user
                const response = await fetch(`/users/${userId}/edit`);
                const user = await response.json();

                // Set form action
                document.getElementById('form-edit-user').action = `/users/${userId}`;

                // Populate form fields
                document.getElementById('edit_nama').value = user.nama;
                document.getElementById('edit_username').value = user.username;
                document.getElementById('edit_nik').value = user.nik;
                document.getElementById('edit_role').value = user.role;
                document.getElementById('edit_divisi').value = user.divisi;
                document.getElementById('edit_jabatan').value = user.jabatan;
                document.getElementById('edit_status_akun').value = user.status_akun;

                // Clear password fields
                document.getElementById('edit_password').value = '';
                document.getElementById('edit_password_confirmation').value = '';

                // Show backdrop terlebih dahulu
                showBackdrop('modal-edit-user');

                // Show modal
                const modalElement = document.getElementById('modal-edit-user');
                modalElement.classList.remove('hidden');
                modalElement.classList.add('flex');
                modalElement.setAttribute('aria-modal', 'true');
                modalElement.setAttribute('role', 'dialog');
                modalElement.removeAttribute('aria-hidden');

                // Setup event listener untuk menutup modal (hanya sekali)
                if (!modalElement.hasAttribute('data-listeners-attached')) {
                    setupModalCloseListeners('modal-edit-user');
                    modalElement.setAttribute('data-listeners-attached', 'true');
                }
            } catch (error) {
                console.error('Error fetching user data:', error);
                showToast('Gagal mengambil data user', 'error');
            }
        }

        // Fungsi untuk membuat dan menampilkan backdrop
        function showBackdrop(modalId) {
            // Cek apakah backdrop sudah ada
            let backdrop = document.getElementById(`backdrop-${modalId}`);

            if (!backdrop) {
                // Buat backdrop baru
                backdrop = document.createElement('div');
                backdrop.id = `backdrop-${modalId}`;
                backdrop.className = 'fixed inset-0 z-40 bg-gray-900/50 transition-opacity';
                backdrop.setAttribute('modal-backdrop', '');

                // Tutup modal saat klik backdrop
                backdrop.addEventListener('click', function() {
                    closeModal(modalId);
                });

                document.body.appendChild(backdrop);
            }

            // Tampilkan backdrop
            backdrop.classList.remove('hidden');

            // Disable body scroll
            document.body.style.overflow = 'hidden';
        }

        // Fungsi untuk menyembunyikan backdrop
        function hideBackdrop(modalId) {
            const backdrop = document.getElementById(`backdrop-${modalId}`);
            if (backdrop) {
                backdrop.classList.add('hidden');
            }

            // Enable body scroll
            document.body.style.overflow = '';
        }

        // Fungsi untuk setup close listeners pada modal
        function setupModalCloseListeners(modalId) {
            const modalElement = document.getElementById(modalId);
            const closeButtons = modalElement.querySelectorAll('[data-modal-toggle]');

            closeButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeModal(modalId);
                });
            });

            // Close on modal click (backdrop area)
            modalElement.addEventListener('click', function(event) {
                if (event.target === modalElement) {
                    closeModal(modalId);
                }
            });

            // Close on ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !modalElement.classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }

        // Fungsi untuk menutup modal
        function closeModal(modalId) {
            const modalElement = document.getElementById(modalId);
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');

            // Sembunyikan backdrop
            hideBackdrop(modalId);
        }

        // Fungsi show custom toast notification
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
@endpush
