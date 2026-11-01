@extends('layouts.wisatawan', ['title' => 'Pesanan Saya — Jelajah Nusa'])

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Pesanan Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau status konfirmasi tiket, tagihan, dan bukti pembayaran perjalanan Anda</p>
        </div>
        <a href="{{ route('paket.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Pesan Trip Lainnya
        </a>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        @forelse($pesananList as $psn)
            @php
                $tglBerangkat = $psn->tanggal_berangkat;
            @endphp
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs hover:border-sky-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                
                <div class="space-y-2 flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-sky-600 bg-sky-50 px-2.5 py-0.5 rounded-md">{{ $psn->display_kode }}</span>
                        <span class="text-[11px] text-slate-400 font-medium">Dipesan pada {{ $psn->created_at->format('d M Y') }}</span>
                    </div>

                    <h3 class="text-lg font-extrabold text-slate-900 leading-snug">
                        {{ $psn->judul_trip }}
                    </h3>

                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500">
                        <span>Keberangkatan: <strong class="text-slate-800">{{ $tglBerangkat ? $tglBerangkat->translatedFormat('d F Y') : '—' }}</strong></span>
                        <span>&bull;</span>
                        <span>Peserta: <strong class="text-slate-800">{{ $psn->jumlah_peserta }} Orang</strong></span>
                        <span>&bull;</span>
                        <span>Total: <strong class="text-slate-900">{{ $psn->total_biaya_format }}</strong></span>
                    </div>
                </div>

                <div class="flex flex-col sm:items-end justify-between gap-3 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $psn->status === 'dikonfirmasi' ? 'bg-emerald-50 text-emerald-700' : ($psn->status === 'menunggu' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                            {{ $psn->status === 'dikonfirmasi' ? 'Tiket Dikonfirmasi' : ($psn->status === 'menunggu' ? 'Menunggu Pembayaran' : 'Dibatalkan') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($psn->status === 'menunggu')
                            <a href="{{ route('pembayaran.show', $psn->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition shadow-xs">
                                Bayar Sekarang
                            </a>
                        @endif
                        <a href="{{ route('pesanan-saya.show', $psn->id) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                            Detail &amp; Tiket &rarr;
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <!-- Empty State Baku PRD Section 27 -->
            <div class="bg-white p-16 rounded-3xl border border-slate-200/80 text-center space-y-3">
                <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <h3 class="text-base font-extrabold text-slate-800">Belum ada pesanan</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Pesanan Anda akan muncul di sini setelah Anda memesan paket wisata di Jelajah Nusa.</p>
                <div class="pt-3">
                    <a href="{{ route('paket.index') }}" class="px-5 py-2.5 bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md inline-block">
                        Jelajahi Paket Wisata
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $pesananList->links() }}
    </div>

</div>
@endsection
