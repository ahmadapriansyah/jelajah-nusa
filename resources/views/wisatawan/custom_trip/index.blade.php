@extends('layouts.wisatawan', ['title' => 'Pengajuan Custom Trip — Jelajah Nusa'])

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Permohonan Custom Trip</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar perjalanan wisata khusus yang Anda ajukan untuk keluarga atau rombongan</p>
        </div>
        <a href="{{ route('custom-trip.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Custom Trip Baru
        </a>
    </div>

    <div class="space-y-4">
        @forelse($customTrips as $trip)
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs hover:border-sky-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                
                <div class="space-y-2 flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-md">Custom Trip #{{ $trip->id }}</span>
                        <span class="text-[11px] text-slate-400 font-medium">Diajukan pada {{ $trip->created_at->format('d M Y') }}</span>
                    </div>

                    <h3 class="text-lg font-extrabold text-slate-900 leading-snug">
                        {{ $trip->tujuan }}
                    </h3>

                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500">
                        <span>Rencana: <strong class="text-slate-800">{{ $trip->tanggal_diinginkan->translatedFormat('d F Y') }}</strong></span>
                        <span>&bull;</span>
                        <span>Durasi: <strong class="text-slate-800">{{ $trip->durasi_hari }} Hari</strong></span>
                        <span>&bull;</span>
                        <span>Peserta: <strong class="text-slate-800">{{ $trip->jumlah_peserta }} Orang</strong></span>
                        @if($trip->biaya_penawaran)
                            <span>&bull;</span>
                            <span>Penawaran: <strong class="text-emerald-600 font-mono">{{ $trip->biaya_penawaran_format }}</strong></span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col sm:items-end justify-between gap-3 flex-shrink-0">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $trip->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700' : ($trip->status === 'ditawarkan' ? 'bg-amber-50 text-amber-700' : ($trip->status === 'diajukan' ? 'bg-sky-50 text-sky-700' : 'bg-rose-50 text-rose-700')) }}">
                        {{ $trip->status === 'ditawarkan' ? 'Penawaran Tersedia' : $trip->status }}
                    </span>

                    <a href="{{ route('custom-trip.show', $trip->id) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Lihat Detail &amp; Penawaran &rarr;
                    </a>
                </div>

            </div>
        @empty
            <div class="bg-white p-16 rounded-3xl border border-slate-200/80 text-center space-y-3">
                <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <h3 class="text-base font-extrabold text-slate-800">Belum ada pengajuan custom trip</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Ingin berlibur bersama rombongan kantor atau keluarga? Ajukan perjalanan khusus sesuai tanggal dan anggaran Anda.</p>
                <div class="pt-3">
                    <a href="{{ route('custom-trip.create') }}" class="px-5 py-2.5 bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md inline-block">
                        Mulai Ajukan Custom Trip
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $customTrips->links() }}
    </div>

</div>
@endsection
