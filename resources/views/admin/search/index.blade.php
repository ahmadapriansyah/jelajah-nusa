@extends('layouts.admin', ['title' => 'Hasil Pencarian Global'])

@section('content')
<div class="py-6 space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Hasil Pencarian Global</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Kata kunci: <strong class="text-sky-600 font-mono">"{{ $q }}"</strong></p>
    </div>

    @if(empty($q) || (count($pemesanan) === 0 && count($peserta) === 0 && count($paket) === 0))
        <div class="bg-white p-12 rounded-3xl border border-slate-200/80 text-center text-slate-400 text-sm">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Tidak ada hasil untuk pencarian Anda. Coba gunakan kata kunci kode pesanan (#PSN...), nama peserta, atau nama destinasi.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Kelompok 1: Pemesanan -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pemesanan ({{ count($pemesanan) }})</h3>
                </div>
                <div class="space-y-2">
                    @forelse($pemesanan as $psn)
                        <a href="{{ route('admin.pemesanan.index', ['selected' => $psn->id]) }}" class="p-3 bg-slate-50 hover:bg-sky-50 rounded-2xl block transition border border-slate-100">
                            <span class="text-xs font-mono font-bold text-sky-600 block">{{ $psn->display_kode }}</span>
                            <span class="text-xs font-bold text-slate-900 block truncate">{{ $psn->judul_trip }}</span>
                            <span class="text-[11px] text-slate-500 block">Pemesan: {{ $psn->user->nama_lengkap ?? '—' }} ({{ $psn->jumlah_peserta }} org)</span>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Tidak ada pemesanan cocok.</p>
                    @endforelse
                </div>
            </div>

            <!-- Kelompok 2: Data Peserta -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Peserta Wisata ({{ count($peserta) }})</h3>
                </div>
                <div class="space-y-2">
                    @forelse($peserta as $pst)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <strong class="text-slate-900 block">{{ $pst->nama_lengkap }}</strong>
                                <span class="text-[11px] font-mono text-slate-500">{{ strtoupper($pst->jenis_identitas) }}: {{ $pst->no_identitas }}</span>
                            </div>
                            <a href="{{ route('dokumen.identitas', $pst->id) }}" target="_blank" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-sky-600 hover:bg-sky-50">
                                KTP
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Tidak ada peserta cocok.</p>
                    @endforelse
                </div>
            </div>

            <!-- Kelompok 3: Paket Wisata -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Paket Wisata ({{ count($paket) }})</h3>
                </div>
                <div class="space-y-2">
                    @forelse($paket as $pkt)
                        <a href="{{ route('admin.paket.index', ['selected' => $pkt->id]) }}" class="p-3 bg-slate-50 hover:bg-sky-50 rounded-2xl block transition border border-slate-100">
                            <span class="text-xs font-bold text-slate-900 block truncate">{{ $pkt->nama_paket }}</span>
                            <span class="text-[11px] text-slate-500 block">{{ $pkt->destinasi }} &bull; {{ $pkt->harga_format }}</span>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Tidak ada paket cocok.</p>
                    @endforelse
                </div>
            </div>

        </div>
    @endif
</div>
@endsection
