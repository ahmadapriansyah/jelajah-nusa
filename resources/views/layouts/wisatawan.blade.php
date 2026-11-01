<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Jelajah Nusa' }} — Jelajah Nusantara Bersama Kami</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col text-slate-800 bg-slate-50">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200/80 shadow-xs" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Logo Brand -->
                <a href="{{ route('paket.index') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <polygon points="12,7 15,15 12,13 9,15" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold text-slate-900 tracking-tight group-hover:text-sky-600 transition">Jelajah Nusa</span>
                        <span class="block text-[11px] font-semibold text-sky-600 -mt-1 tracking-wider">Tour &amp; Travel Agency</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1 text-sm font-semibold text-slate-600">
                    <a href="{{ route('paket.index') }}" 
                       class="px-4 py-2 rounded-xl transition {{ request()->routeIs('paket.*') ? 'bg-sky-50 text-sky-600 font-bold' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                        Paket Wisata
                    </a>
                    <a href="{{ route('custom-trip.index') }}" 
                       class="px-4 py-2 rounded-xl transition {{ request()->routeIs('custom-trip.*') ? 'bg-sky-50 text-sky-600 font-bold' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                        Custom Trip
                    </a>
                    <a href="{{ route('tiket.verifikasi.kode') }}" 
                       class="px-4 py-2 rounded-xl transition {{ request()->routeIs('tiket.verifikasi*') ? 'bg-sky-50 text-sky-600 font-bold' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                        Cek Keaslian Tiket
                    </a>
                    @auth
                        <a href="{{ route('pesanan-saya.index') }}" 
                           class="px-4 py-2 rounded-xl transition {{ request()->routeIs('pesanan-saya.*') ? 'bg-sky-50 text-sky-600 font-bold' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                            Pesanan Saya
                        </a>
                    @endauth
                </nav>

                <!-- Auth / User Action -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition shadow-sm">
                                Panel Admin
                            </a>
                        @endif

                        <div class="relative" x-data="{ userMenu: false }">
                            <button @click="userMenu = !userMenu" @click.away="userMenu = false" class="flex items-center gap-2.5 p-1.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white transition">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold uppercase">
                                    {{ substr(auth()->user()->nama_lengkap ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-xs font-bold text-slate-800 max-w-[120px] truncate">{{ auth()->user()->nama_lengkap }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <!-- User Dropdown -->
                            <div x-show="userMenu" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 text-xs font-medium" style="display: none;">
                                <div class="px-4 py-2 border-b border-slate-100 text-slate-500">
                                    {{ auth()->user()->email }}
                                </div>
                                <a href="{{ route('pesanan-saya.index') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Pesanan Saya
                                </a>
                                <a href="{{ route('custom-trip.index') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Pengajuan Custom Trip
                                </a>
                                <a href="{{ route('profil.show') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profil &amp; Password
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-rose-600 hover:bg-rose-50 text-left">
                                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-slate-700 hover:text-sky-600 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-md shadow-sky-500/20 hover:shadow-lg hover:shadow-sky-500/30 transition transform hover:-translate-y-0.5">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-slate-200 py-4 space-y-2 text-sm font-semibold" style="display: none;">
                <a href="{{ route('paket.index') }}" class="block px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100">Paket Wisata</a>
                <a href="{{ route('custom-trip.index') }}" class="block px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100">Custom Trip</a>
                <a href="{{ route('tiket.verifikasi.kode') }}" class="block px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100">Cek Keaslian Tiket</a>
                @auth
                    <a href="{{ route('pesanan-saya.index') }}" class="block px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100">Pesanan Saya</a>
                    <a href="{{ route('profil.show') }}" class="block px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100">Profil Saya</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-xl text-sky-600 font-bold bg-sky-50">Panel Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 rounded-xl">Keluar (Logout)</button>
                    </form>
                @else
                    <div class="pt-3 border-t border-slate-100 flex flex-col gap-2 px-4">
                        <a href="{{ route('login') }}" class="w-full py-2.5 text-center text-slate-700 border border-slate-200 rounded-xl">Masuk</a>
                        <a href="{{ route('register') }}" class="w-full py-2.5 text-center bg-sky-600 text-white rounded-xl shadow-sm">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Alerts / Toast -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
        @if(session('sukses'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-sm text-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('sukses') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-600 hover:text-emerald-900 text-lg font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm text-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-rose-600 hover:text-rose-900 text-lg font-bold">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" class="mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl shadow-sm text-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold">Perhatian:</span>
                    <button @click="show = false" class="text-amber-600 hover:text-amber-900 text-lg font-bold">&times;</button>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-sm mt-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-sky-500 flex items-center justify-center text-white font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polygon points="12,7 15,15 12,13 9,15" fill="currentColor"/></svg>
                        </div>
                        <span class="text-lg font-bold text-white">Jelajah Nusa</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Platform resmi pemesanan paket wisata terpercaya, open trip, dan custom trip ke destinasi terbaik Indonesia.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3 text-xs tracking-wider uppercase">Layanan Kami</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('paket.index') }}?layanan=open_trip" class="hover:text-white transition">Open Trip Reguler</a></li>
                        <li><a href="{{ route('custom-trip.create') }}" class="hover:text-white transition">Pengajuan Custom Trip</a></li>
                        <li><a href="{{ route('tiket.verifikasi.kode') }}" class="hover:text-white transition">Verifikasi Tiket QR</a></li>
                        <li><a href="{{ route('pesanan-saya.index') }}" class="hover:text-white transition">Riwayat Pemesanan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3 text-xs tracking-wider uppercase">Metode Pembayaran</h4>
                    <p class="text-xs text-slate-400 mb-2">Transfer Bank Resmi:</p>
                    <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/60 text-xs">
                        <span class="block font-bold text-sky-400">Bank Mandiri</span>
                        <span class="block text-white font-mono">138-00-8899221-1</span>
                        <span class="block text-[11px] text-slate-400">a.n PT Jelajah Nusa Tour &amp; Travel</span>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3 text-xs tracking-wider uppercase">Bantuan &amp; Kontak</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Hubungi customer service kami untuk informasi detail paket, keberangkatan, dan rombongan wisata.
                    </p>
                    <p class="text-xs font-semibold text-white mt-2">WhatsApp: +62 812-3456-7890</p>
                    <p class="text-xs text-slate-400">Email: info@jelajahnusa.com</p>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-12 pt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Jelajah Nusa — Sistem Informasi Travel dan Agen Wisata. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
