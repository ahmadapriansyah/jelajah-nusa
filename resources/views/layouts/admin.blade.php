<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel' }} — Jelajah Nusa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex overflow-hidden text-slate-800" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden" style="display: none;"></div>

    <!-- Sidebar (260px) -->
    <aside class="fixed inset-y-0 left-0 z-50 w-[260px] bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 transform lg:static lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <!-- Brand / Header -->
        <div class="h-16 flex items-center justify-between px-6 bg-slate-950/60 border-b border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-sky-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <polygon points="12,7 15,15 12,13 9,15" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold text-white tracking-tight">Jelajah Nusa</span>
                    <span class="block text-[10px] font-medium text-sky-400 tracking-wider uppercase">Travel Operations</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 text-sm font-medium">
            <!-- 1. Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            <!-- 2. Paket & Jadwal -->
            <a href="{{ route('admin.paket.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.paket.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Paket &amp; Jadwal</span>
            </a>

            <!-- 3. Kendaraan -->
            <a href="{{ route('admin.kendaraan.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.kendaraan.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Armada Kendaraan</span>
            </a>

            <!-- 4. Pemesanan -->
            <a href="{{ route('admin.pemesanan.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.pemesanan.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Pemesanan</span>
            </a>

            <!-- 5. Peserta -->
            <a href="{{ route('admin.peserta.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.peserta.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Data Peserta</span>
            </a>

            <!-- 6. Custom Trip -->
            <a href="{{ route('admin.custom-trip.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.custom-trip.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Custom Trip</span>
            </a>

            <!-- 7. Verifikasi Pembayaran -->
            <a href="{{ route('admin.pembayaran.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.pembayaran.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Verifikasi Bayar</span>
            </a>

            <!-- 8. Kelayakan Berangkat -->
            <a href="{{ route('admin.kelayakan.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.kelayakan.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Kelayakan Berangkat</span>
            </a>

            <!-- 9. Pengembalian & Reschedule -->
            <a href="{{ route('admin.pengembalian.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.pengembalian.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Refund &amp; Pindah</span>
            </a>

            <!-- 10. Tiket -->
            <a href="{{ route('admin.tiket.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.tiket.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <span>Manajemen Tiket</span>
            </a>

            <!-- 11. Laporan -->
            <a href="{{ route('admin.laporan.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.laporan.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Laporan Bulanan</span>
            </a>

            <div class="pt-4 mt-4 border-t border-slate-800/80">
                <span class="px-3 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Sistem</span>
            </div>

            <!-- 12. Pengaturan -->
            <a href="{{ route('admin.pengaturan.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.pengaturan.*') ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Pengaturan &amp; User</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 bg-slate-950/80 border-t border-slate-800 text-xs text-slate-400 flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-200">Jelajah Nusa v1.0</p>
                <p class="text-[11px] text-slate-500">© 2026 Universitas Duta Bangsa</p>
            </div>
            <a href="{{ route('paket.index') }}" target="_blank" title="Lihat Website Wisatawan" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100/70">
        
        <!-- Header (Global Search & Profile) -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-10">
            <div class="flex items-center gap-4 flex-1 max-w-xl">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                
                <!-- Global Search Form (F13-01) -->
                <form action="{{ route('admin.cari') }}" method="GET" class="w-full">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" 
                               name="q" 
                               value="{{ request()->query('q') }}"
                               placeholder="Cari pemesanan (#PSN...), nama peserta, paket..." 
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition text-slate-700 placeholder-slate-400">
                    </div>
                </form>
            </div>

            <!-- Admin Profile Menu -->
            <div class="flex items-center gap-4" x-data="{ profileOpen: false }">
                <div class="relative">
                    <button @click="profileOpen = !profileOpen" 
                            @click.away="profileOpen = false"
                            class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold uppercase shadow-sm">
                            {{ substr(auth()->user()->nama_lengkap ?? 'A', 0, 1) }}
                        </div>
                        <div class="text-left hidden md:block">
                            <span class="block text-xs font-bold text-slate-700 leading-tight">{{ auth()->user()->nama_lengkap ?? 'Admin Travel' }}</span>
                            <span class="block text-[11px] text-sky-600 font-medium">Administrator</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="profileOpen" 
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50 text-xs font-medium" 
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-slate-100 text-slate-500">
                            Masuk sebagai <strong>{{ auth()->user()->username }}</strong>
                        </div>
                        <a href="{{ route('profil.show') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        <a href="{{ route('admin.pengaturan.index') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pengaturan
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 hover:bg-rose-50 text-rose-600 text-left">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages, Popups & Alerts -->
        @if(session('sukses') || session('success') || session('status'))
            <!-- Popup Modal Berhasil (Konfirmasi / Aksi Sukses) -->
            <div x-data="{ 
                    open: true, 
                    progress: 100,
                    init() {
                        let duration = 4000;
                        let step = 50;
                        let decrement = 100 / (duration / step);
                        let timer = setInterval(() => {
                            this.progress -= decrement;
                            if (this.progress <= 0) {
                                this.open = false;
                                clearInterval(timer);
                            }
                        }, step);
                    }
                 }" 
                 x-show="open" 
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-7 text-center overflow-hidden"
                     @click.away="open = false"
                     x-transition:enter="transition cubic-bezier(0.175, 0.885, 0.32, 1.275) duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-90 translate-y-4">
                    
                    <!-- Tombol Silang X -->
                    <button @click="open = false" class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <!-- Icon Centang Berhasil Beranimasi -->
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 ring-8 ring-emerald-500/5">
                        <div class="w-11 h-11 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                            <svg class="w-6 h-6 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Judul & Pesan Notifikasi -->
                    <h3 class="text-lg font-bold text-slate-900 mb-1.5">Berhasil Diproses!</h3>
                    <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
                        {{ session('sukses') ?? session('success') ?? session('status') }}
                    </p>

                    <!-- Tombol Aksi Oke -->
                    <button @click="open = false" 
                            type="button" 
                            class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/40 transition-all">
                        Oke, Lanjutkan
                    </button>

                    <!-- Bar Penghitung Waktu Otomatis Tertutup -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100">
                        <div class="h-full bg-emerald-500 transition-all duration-75" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <!-- Popup Modal Error / Penolakan -->
            <div x-data="{ open: true }" 
                 x-show="open" 
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-7 text-center overflow-hidden"
                     @click.away="open = false"
                     x-transition:enter="transition cubic-bezier(0.175, 0.885, 0.32, 1.275) duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-90 translate-y-4">
                    
                    <button @click="open = false" class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-600 ring-8 ring-rose-500/5">
                        <div class="w-11 h-11 rounded-xl bg-rose-500 flex items-center justify-center text-white shadow-lg shadow-rose-500/30">
                            <svg class="w-6 h-6 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-1.5">Perhatian</h3>
                    <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
                        {{ session('error') }}
                    </p>

                    <button @click="open = false" 
                            type="button" 
                            class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 active:scale-[0.98] text-white text-sm font-bold rounded-xl shadow-lg shadow-rose-600/25 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        @endif

        <div class="px-6 pt-4">
            @if($errors->any())
                <div x-data="{ show: true }" x-show="show" class="mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl shadow-sm text-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Mohon perbaiki kesalahan berikut:
                        </span>
                        <button @click="show = false" class="text-amber-600 hover:text-amber-900">&times;</button>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs pl-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Scrollable Main Content -->
        <main class="flex-1 overflow-y-auto px-6 pb-12">
            @yield('content')
        </main>
    </div>

</body>
</html>
