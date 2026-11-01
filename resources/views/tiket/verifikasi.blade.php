@extends('layouts.wisatawan', ['title' => 'Verifikasi Keaslian Tiket — Jelajah Nusa'])

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    
    <div class="text-center space-y-2">
        <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Verifikasi Keaslian Tiket Perjalanan</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">Sistem validasi digital resmi untuk memeriksa keaslian tiket, status aktif, dan identitas manifes penumpang.</p>
    </div>

    <!-- Verification Search Box -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('tiket.verifikasi.kode') }}" method="GET" class="space-y-4">
            <div>
                <label for="kode" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Tiket Wisatawan</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </span>
                    <input type="text" 
                           id="kode" 
                           name="kode" 
                           value="{{ old('kode', $kodeCek) }}" 
                           required 
                           placeholder="Contoh: JN-PSN1041-01" 
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-800 uppercase focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                Periksa Keaslian Tiket
            </button>
        </form>

        <!-- Hasil Verifikasi (US-14, T-13, Section 27) -->
        @if($hasil)
            <div class="mt-6 pt-6 border-t border-slate-100">
                @if($hasil['valid'])
                    <!-- TIKET VALID -->
                    <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto shadow-md shadow-emerald-500/20">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        
                        <div>
                            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">Hasil Pemeriksaan Sistem</span>
                            <h2 class="text-2xl font-black text-emerald-950 tracking-tight mt-0.5">TIKET VALID</h2>
                            <p class="text-xs text-emerald-700 mt-1">Tiket terdaftar secara sah dan aktif dalam database operasional Jelajah Nusa.</p>
                        </div>

                        <div class="bg-white/80 rounded-2xl p-4 border border-emerald-100 text-left text-xs space-y-2 text-slate-800">
                            <div class="flex justify-between py-1 border-b border-emerald-50">
                                <span class="text-slate-400">Kode Tiket:</span>
                                <strong class="font-mono text-sky-700">{{ $hasil['tiket']->kode_tiket }}</strong>
                            </div>
                            <div class="flex justify-between py-1 border-b border-emerald-50">
                                <span class="text-slate-400">Nama Peserta:</span>
                                <strong class="text-slate-900">{{ $hasil['nama_peserta'] }}</strong>
                            </div>
                            <div class="flex justify-between py-1 border-b border-emerald-50">
                                <span class="text-slate-400">Paket Perjalanan:</span>
                                <strong class="text-slate-900">{{ $hasil['nama_paket'] }}</strong>
                            </div>
                            <div class="flex justify-between py-1 border-b border-emerald-50">
                                <span class="text-slate-400">Tanggal Berangkat:</span>
                                <strong>{{ $hasil['tanggal_berangkat'] }}</strong>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400">Pemesan Utama:</span>
                                <span>{{ $hasil['pemesan'] }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- TIKET TIDAK VALID -->
                    <div class="p-6 rounded-2xl bg-rose-50 border border-rose-200 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-rose-500 text-white flex items-center justify-center mx-auto shadow-md shadow-rose-500/20">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        
                        <div>
                            <span class="text-xs font-bold uppercase tracking-widest text-rose-700">Hasil Pemeriksaan Sistem</span>
                            <h2 class="text-2xl font-black text-rose-950 tracking-tight mt-0.5">TIKET TIDAK VALID</h2>
                            <p class="text-xs text-rose-700 mt-1 font-semibold">
                                TIKET TIDAK VALID — kode tidak ditemukan atau tiket sudah dibatalkan.
                            </p>
                        </div>

                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Pastikan Anda memasukkan kode tiket dengan benar atau hubungi admin agen travel untuk verifikasi manual.
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
