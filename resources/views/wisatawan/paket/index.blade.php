@extends('layouts.wisatawan', ['title' => 'Katalog Paket Wisata — Jelajah Nusa'])

@section('content')
<!-- Hero Section with Search -->
<div class="relative bg-gradient-to-b from-sky-500 via-sky-600 to-indigo-700 text-white py-16 sm:py-24 px-4 overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
    
    <div class="max-w-4xl mx-auto text-center relative z-10 space-y-4">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-white/15 backdrop-blur text-sky-100 border border-white/20 uppercase tracking-wider">
            Eksplorasi Keindahan Nusantara
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
            Temukan Petualangan Tak Terlupakan
        </h1>
        <p class="text-sm sm:text-base text-sky-100 max-w-2xl mx-auto leading-relaxed">
            Pilihan open trip dan paket wisata reguler ke Bali, Bromo, Labuan Bajo, hingga Raja Ampat dengan fasilitas terlengkap dan jaminan kenyamanan.
        </p>

        <!-- Search Form in Hero -->
        <div class="pt-6 max-w-2xl mx-auto">
            <form action="{{ route('paket.index') }}" method="GET" class="bg-white p-2 rounded-2xl shadow-xl flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" 
                           name="cari" 
                           value="{{ $cari }}" 
                           placeholder="Mau jalan-jalan ke mana? (contoh: Bali, Bromo)" 
                           class="w-full pl-11 pr-4 py-3 bg-transparent text-slate-800 text-sm focus:outline-none placeholder-slate-400">
                </div>
                <button type="submit" class="px-7 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition">
                    Cari Paket
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Main Catalog Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Filter Bar (F9-02, US-03) -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('paket.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <input type="hidden" name="cari" value="{{ $cari }}">
            
            <!-- Filter Destinasi -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Destinasi</label>
                <select name="destinasi" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                    <option value="">Semua Destinasi</option>
                    @foreach($destinasiList as $dst)
                        <option value="{{ $dst }}" {{ $destinasi === $dst ? 'selected' : '' }}>{{ $dst }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Layanan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Layanan</label>
                <select name="layanan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                    <option value="">Semua Layanan</option>
                    <option value="open_trip" {{ $layanan === 'open_trip' ? 'selected' : '' }}>Open Trip</option>
                    <option value="paket_reguler" {{ $layanan === 'paket_reguler' ? 'selected' : '' }}>Paket Reguler</option>
                </select>
            </div>

            <!-- Filter Tanggal -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" min="{{ now()->toDateString() }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
            </div>

            <!-- Urutan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Urutkan</label>
                <select name="urutan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                    <option value="terbaru" {{ $urutan === 'terbaru' ? 'selected' : '' }}>Paling Baru</option>
                    <option value="termurah" {{ $urutan === 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
                    <option value="termahal" {{ $urutan === 'termahal' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('paket.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Package Cards Grid (US-03) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($paketList as $pkt)
            @php
                $jadwalTerdekat = $pkt->jadwal_mendatang ?? null;

                // LOGIKA PENENTUAN GURU URL GAMBAR YANG FLEKSIBEL
                $imgUrl = null;
                if (!empty($pkt->foto)) {
                    if (Str::startsWith($pkt->foto, ['http://', 'https://'])) {
                        $imgUrl = $pkt->foto;
                    } elseif (file_exists(public_path('storage/paket/' . $pkt->foto))) {
                        $imgUrl = asset('storage/paket/' . $pkt->foto);
                    } elseif (file_exists(public_path('storage/' . $pkt->foto))) {
                        $imgUrl = asset('storage/' . $pkt->foto);
                    } elseif (Str::startsWith($pkt->foto, 'images/')) {
                        $imgUrl = asset($pkt->foto);
                    }
                }
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between group">
                
                <div>
                    <!-- Photo Cover Penyesuaian -->
                    <div class="h-52 bg-slate-800 relative overflow-hidden">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $pkt->nama_paket }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-sky-600 via-indigo-700 to-slate-900 flex flex-col items-center justify-center p-6 text-center text-white">
                                <svg class="w-10 h-10 text-sky-300/80 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs font-extrabold uppercase tracking-widest text-sky-200">{{ $pkt->destinasi }}</span>
                            </div>
                        @endif

                        <div class="absolute top-3.5 left-3.5 flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-md shadow-xs {{ $pkt->jenis_layanan === 'open_trip' ? 'bg-indigo-600/90 text-white' : 'bg-emerald-600/90 text-white' }}">
                                {{ $pkt->jenis_layanan === 'open_trip' ? 'Open Trip' : 'Paket Reguler' }}
                            </span>
                        </div>

                        <div class="absolute bottom-3.5 right-3.5">
                            <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-slate-950/80 text-white backdrop-blur-md">
                                {{ $pkt->durasi_label ?? ($pkt->durasi . ' Hari') }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 space-y-3">
                        <div class="flex items-center gap-1.5 text-xs text-sky-600 font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $pkt->destinasi }}</span>
                        </div>

                        <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-sky-600 transition leading-snug">
                            <a href="{{ route('paket.show', $pkt->id) }}">
                                {{ $pkt->nama_paket }}
                            </a>
                        </h3>

                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $pkt->deskripsi ?: 'Jelajahi keindahan destinasi wisata dengan itinerary lengkap dan panduan pemandu berpengalaman.' }}
                        </p>

                        <!-- Jadwal Terdekat & Sisa Kuota (US-03) -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            @if($jadwalTerdekat)
                                <div class="text-slate-600">
                                    <span class="text-[10px] text-slate-400 block font-medium">Berangkat:</span>
                                    <strong class="text-slate-900">{{ \Carbon\Carbon::parse($jadwalTerdekat->tanggal_berangkat)->translatedFormat('d M Y') }}</strong>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block font-medium">Sisa Kuota:</span>
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[11px] {{ ($jadwalTerdekat->sisa_kuota ?? 0) <= 3 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ $jadwalTerdekat->sisa_kuota ?? 0 }} Kursi
                                    </span>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs italic">Belum ada jadwal buka</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Footer Price & Button -->
                <div class="p-6 pt-0">
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Mulai dari</span>
                            <span class="text-lg font-extrabold text-sky-600">
                                {{ $pkt->harga_format ?? ('Rp ' . number_format($pkt->harga, 0, ',', '.')) }}
                            </span>
                            <span class="text-[10px] text-slate-400 block">/ orang</span>
                        </div>
                        <a href="{{ route('paket.show', $pkt->id) }}" class="px-4 py-2 bg-slate-900 hover:bg-sky-600 text-white text-xs font-bold rounded-xl transition shadow-xs">
                            Pesan Sekarang
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white p-16 rounded-3xl border border-slate-200/80 text-center space-y-3">
                <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-base font-extrabold text-slate-800">Tidak ada paket wisata yang cocok</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Coba ubah kata kunci pencarian atau reset filter untuk melihat seluruh katalog kami.</p>
                <div class="pt-2">
                    <a href="{{ route('paket.index') }}" class="px-4 py-2 bg-sky-600 text-white rounded-xl text-xs font-bold inline-block">Reset Filter</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $paketList->links() }}
    </div>

    <!-- Custom Trip Promo Banner -->
    <div class="bg-gradient-to-r from-indigo-900 via-slate-900 to-sky-950 p-8 sm:p-10 rounded-3xl text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl">
        <div class="space-y-2 text-center md:text-left">
            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/20 text-sky-300">
                Layanan Khusus Rombongan
            </span>
            <h2 class="text-2xl font-extrabold">Ingin Jadwal Fleksibel atau Destinasi Khusus?</h2>
            <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                Ajukan Custom Trip untuk rombongan keluarga, instansi kantor, atau gathering komunitas. Kami siapkan armada, penginapan, dan itinerary privat sesuai keinginan Anda.
            </p>
        </div>
        <a href="{{ route('custom-trip.create') }}" class="px-6 py-3 bg-white hover:bg-sky-50 text-slate-900 font-extrabold text-sm rounded-xl shadow-lg transition flex-shrink-0">
            Ajukan Custom Trip Sekarang &rarr;
        </a>
    </div>

</div>
@endsection