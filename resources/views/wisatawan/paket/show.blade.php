@extends('layouts.wisatawan', ['title' => $paket->nama_paket . ' — Jelajah Nusa'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('paket.index') }}" class="hover:text-sky-600">&larr; Kembali ke Katalog</a>
        <span>/</span>
        <span class="text-slate-400">{{ $paket->destinasi }}</span>
        <span>/</span>
        <span class="text-slate-800 truncate">{{ $paket->nama_paket }}</span>
    </div>

    <!-- Header Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Image & Trip Highlights (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Hero Photo -->
            <div class="h-80 sm:h-96 rounded-3xl overflow-hidden bg-slate-900 relative shadow-lg">
                @if($paket->foto)
                    <img src="{{ asset('storage/' . $paket->foto) }}" alt="{{ $paket->nama_paket }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-tr from-sky-600 via-indigo-700 to-slate-900 flex flex-col items-center justify-center p-8 text-center text-white">
                        <svg class="w-16 h-16 text-sky-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h2 class="text-2xl font-extrabold uppercase tracking-widest">{{ $paket->destinasi }}</h2>
                    </div>
                @endif

                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider backdrop-blur-md text-white {{ $paket->jenis_layanan === 'open_trip' ? 'bg-indigo-600/90' : 'bg-emerald-600/90' }}">
                        {{ $paket->jenis_layanan === 'open_trip' ? 'Open Trip' : 'Paket Reguler' }}
                    </span>
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-slate-950/80 text-white backdrop-blur-md">
                        {{ $paket->durasi_label }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-lg font-extrabold text-slate-900">Tentang Paket Wisata Ini</h3>
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $paket->deskripsi ?: 'Nikmati perjalanan mengagumkan dengan paket wisata eksklusif yang dirancang untuk kenyamanan dan pengalaman liburan terbaik Anda.' }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100 text-xs">
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <span class="text-slate-400 block font-medium mb-1">Titik Kumpul (Meeting Point):</span>
                        <strong class="text-slate-800 text-sm block">{{ $paket->meeting_point }}</strong>
                        <span class="text-sky-600 font-bold block mt-1">Pukul {{ substr($paket->jam_kumpul, 0, 5) }} WIB</span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <span class="text-slate-400 block font-medium mb-1">Durasi Tour:</span>
                        <strong class="text-slate-800 text-sm block">{{ $paket->durasi_hari }} Hari {{ max(0, $paket->durasi_hari - 1) }} Malam</strong>
                        <span class="text-slate-500 block mt-1">Destinasi: {{ $paket->destinasi }}</span>
                    </div>
                </div>
            </div>

            <!-- Itinerary per Hari (F9-03) -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-lg font-extrabold text-slate-900">Rencana Perjalanan (Itinerary)</h3>
                
                <div class="space-y-4">
                    @if(is_array($paket->itinerary))
                        @foreach($paket->itinerary as $it)
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-sky-600 text-white font-extrabold text-xs">
                                        Hari ke-{{ $it['hari'] ?? $loop->iteration }}
                                    </span>
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $it['judul'] ?? '' }}</h4>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed pl-1">{{ $it['aktivitas'] ?? '' }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Fasilitas Termasuk & Tidak Termasuk -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-lg font-extrabold text-slate-900">Fasilitas Perjalanan</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <div class="space-y-2">
                        <span class="font-bold text-emerald-700 uppercase tracking-wider block text-[11px] pb-1 border-b border-emerald-100">
                            &check; Fasilitas Termasuk (Included):
                        </span>
                        <div class="text-slate-700 leading-relaxed whitespace-pre-line pl-1">{{ $paket->fasilitas }}</div>
                    </div>

                    @if($paket->fasilitas_tidak_termasuk)
                        <div class="space-y-2">
                            <span class="font-bold text-rose-700 uppercase tracking-wider block text-[11px] pb-1 border-b border-rose-100">
                                &times; Tidak Termasuk (Excluded):
                            </span>
                            <div class="text-slate-700 leading-relaxed whitespace-pre-line pl-1">{{ $paket->fasilitas_tidak_termasuk }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Jadwal Keberangkatan & Pemesanan (5 Cols, F9-04, US-04) -->
        <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
            
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-md space-y-6">
                
                <div>
                    <span class="text-xs text-slate-400 font-medium block">Harga Paket Mulai Dari</span>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-3xl font-extrabold text-sky-600">{{ $paket->harga_format }}</span>
                        <span class="text-xs text-slate-400">/ orang</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-sm font-extrabold text-slate-900 mb-1">Pilih Jadwal Keberangkatan</h4>
                    <p class="text-xs text-slate-400 mb-4">Pilih tanggal untuk melihat sisa kuota dan status kelayakan keberangkatan</p>

                    <div class="space-y-3">
                        @forelse($paket->jadwal as $jdw)
                            @php
                                $aktif = $jdw->peserta_aktif;
                                $min = $jdw->min_peserta;
                                $memenuhiMin = $aktif >= $min;
                            @endphp
                            <div class="p-4 rounded-2xl border transition-all {{ $jdw->sisa_kuota <= 0 ? 'bg-slate-100 border-slate-200 opacity-60' : 'bg-slate-50 border-slate-200 hover:border-sky-500' }}">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div>
                                        <span class="text-sm font-bold text-slate-900 block">{{ $jdw->tanggal_berangkat->translatedFormat('l, d F Y') }}</span>
                                        <span class="text-[11px] text-slate-500 block">Armada: {{ $jdw->kendaraan ? $jdw->kendaraan->jenis_kendaraan : 'Bus/Armada Pariwisata' }}</span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $jdw->sisa_kuota <= 0 ? 'bg-slate-200 text-slate-600' : ($jdw->sisa_kuota <= 3 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800') }}">
                                        {{ $jdw->sisa_kuota <= 0 ? 'Penuh' : "Sisa {$jdw->sisa_kuota} Kursi" }}
                                    </span>
                                </div>

                                <!-- Status Open Trip: X dari minimal Y peserta (F9-04, US-04) -->
                                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $memenuhiMin ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                                        <span class="text-slate-600 font-medium">
                                            <strong>{{ $aktif }}</strong> dari min. <strong>{{ $min }}</strong> peserta
                                        </span>
                                    </div>
                                    <span class="text-[11px] font-bold {{ $memenuhiMin ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $memenuhiMin ? 'Pasti Berangkat' : 'Menunggu Kuota' }}
                                    </span>
                                </div>

                                <!-- Booking Button -->
                                @if($jdw->sisa_kuota > 0)
                                    <div class="mt-3 pt-2">
                                        <a href="{{ route('pemesanan.create', ['jadwal_id' => $jdw->id]) }}" class="w-full block py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold text-center rounded-xl shadow-xs transition">
                                            Pesan Tanggal Ini &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-6 bg-slate-50 rounded-2xl text-center text-xs text-slate-400">
                                Saat ini belum ada jadwal keberangkatan aktif untuk paket ini. Anda dapat mengajukan tanggal sendiri melalui formulir Custom Trip.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('custom-trip.create') }}" class="block text-center text-xs font-bold text-sky-600 hover:text-sky-700">
                        Mau berangkat di tanggal lain? Ajukan Custom Trip &rarr;
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
