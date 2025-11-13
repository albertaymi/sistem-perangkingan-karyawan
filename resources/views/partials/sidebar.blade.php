{{-- Backdrop overlay untuk mobile --}}
<div id="sidebar-backdrop" class="fixed inset-0 z-30 bg-gray-900/50 hidden" aria-hidden="true"></div>

{{-- Sidebar dengan responsive toggle dan kategori menu --}}
<aside id="logo-sidebar"
    class="fixed left-0 top-16 z-40 h-[calc(100vh-4rem)] w-64 bg-white shadow-lg border-r border-gray-200 overflow-y-auto transition-transform -translate-x-full sm:translate-x-0"
    aria-label="Sidebar">
    <div class="flex flex-col h-full">
        {{-- Menu Items --}}
        <nav class="flex-1 px-4 py-6 space-y-2">
            {{-- Dashboard Menu --}}
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 transition-all duration-150 cursor-pointer">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Dashboard
            </a>

            @if (auth()->user()->isSuperAdmin() || auth()->user()->isHRD())
                {{-- Kelola Akun --}}
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Manajemen User</p>
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-all duration-150 cursor-pointer">
                        <svg class="h-5 w-5 mr-3 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        Kelola Akun
                    </a>
                </div>
            @endif

            @if (auth()->user()->isSuperAdmin() || auth()->user()->isHRD())
                {{-- Kelola Kriteria --}}
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kriteria</p>
                    <a href="{{ route('kriteria.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-all duration-150 cursor-pointer">
                        <svg class="h-5 w-5 mr-3 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        Kelola Kriteria
                    </a>
                </div>
            @endif

            @if (auth()->user()->isSuperAdmin() || auth()->user()->isHRD() || auth()->user()->isSupervisor())
                {{-- Input Penilaian --}}
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Penilaian</p>
                    <a href="{{ route('penilaian.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-all duration-150 cursor-pointer">
                        <svg class="h-5 w-5 mr-3 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Input Penilaian
                    </a>
                </div>
            @endif

            @if (auth()->user()->isSuperAdmin() || auth()->user()->isHRD())
                {{-- Perangkingan --}}
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Ranking</p>
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-all duration-150 cursor-pointer">
                        <svg class="h-5 w-5 mr-3 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Perangkingan
                    </a>
                </div>
            @endif

            {{-- Hasil Perangkingan --}}
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Laporan</p>
                <a href="#"
                    class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-all duration-150 cursor-pointer">
                    <svg class="h-5 w-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Hasil Perangkingan
                </a>
            </div>

            {{-- Pengaturan Akun --}}
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pengaturan</p>
                <a href="#"
                    class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-all duration-150 cursor-pointer">
                    <svg class="h-5 w-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Pengaturan Akun
                </a>
            </div>
        </nav>

        {{-- Logout Button at Bottom --}}
        <div class="p-4 border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg shadow-sm hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 cursor-pointer">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- JavaScript untuk responsive toggle sidebar --}}
@push('scripts')
    <script>
        // Toggle sidebar untuk mobile
        const sidebarToggle = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
        const sidebar = document.getElementById('logo-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        if (sidebarToggle && sidebar && backdrop) {
            // Toggle sidebar dan backdrop
            sidebarToggle.addEventListener('click', function() {
                const isOpen = !sidebar.classList.contains('-translate-x-full');

                if (isOpen) {
                    // Close sidebar
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                } else {
                    // Open sidebar (hanya di mobile)
                    if (window.innerWidth < 640) {
                        sidebar.classList.remove('-translate-x-full');
                        backdrop.classList.remove('hidden');
                    }
                }
            });

            // Close sidebar saat klik backdrop
            backdrop.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            });

            // Close sidebar saat klik di luar pada mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);
                const isClickOnBackdrop = backdrop.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggle && !isClickOnBackdrop && window.innerWidth < 640) {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                }
            });

            // Hide backdrop on resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 640) {
                    backdrop.classList.add('hidden');
                }
            });
        }
    </script>
@endpush
