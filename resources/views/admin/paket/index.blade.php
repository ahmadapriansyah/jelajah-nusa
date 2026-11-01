@extends('layouts.admin', ['title' => 'Kelola Paket Wisata & Jadwal'])

@section('content')
<div class="py-6 space-y-6">
    
    <!-- Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Paket Wisata &amp; Jadwal</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola katalog paket perjalanan reguler, open trip, dan jadwal keberangkatan armada</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.paket.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-sky-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Paket Wisata
            </a>
        </div>
    </div>

    <!-- 2 Column Master-Detail Layout (Frame 107:1960) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Paket List (5 Columns) -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Search & Total Badge -->
            <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3">
                <form action="{{ route('admin.paket.index') }}" method="GET" class="relative flex-1">
                    <input type="text" 
                           name="cari" 
                           value="{{ $search }}" 
                           placeholder="Cari nama paket/destinasi..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-700">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-600">
                    {{ $paketList->count() }} Paket
                </span>
            </div>

            <!-- List of Package Cards -->
            <div class="space-y-3">
                @forelse($paketList as $p)
                    <div class="bg-white p-5 rounded-3xl border transition-all relative overflow-hidden {{ $selectedPaket && $selectedPaket->id === $p->id ? 'border-sky-500 ring-2 ring-sky-500/20 shadow-md' : 'border-slate-200/80 hover:border-slate-300 shadow-xs' }}">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <a href="{{ route('admin.paket.index', ['selected' => $p->id, 'cari' => $search]) }}" class="block flex-1 group">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $p->jenis_layanan === 'open_trip' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ $p->jenis_layanan === 'open_trip' ? 'Open Trip' : 'Paket Reguler' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">
                                        {{ $p->durasi_label }}
                                    </span>
                                    @if(!$p->status_aktif)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-600">Nonaktif</span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-sky-600 transition">{{ $p->nama_paket }}</h3>
                                <p class="text-xs text-slate-400">{{ $p->destinasi }}</p>
                            </a>
                            <div class="text-right">
                                <span class="block text-[10px] text-slate-400 font-medium">Mulai dari</span>
                                <span class="block text-sm font-extrabold text-sky-600">{{ $p->harga_format }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-100 text-xs text-slate-500">
                            <span>{{ $p->jadwal_count }} Jadwal Aktif</span>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.paket.toggle-aktif', $p->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold {{ $p->status_aktif ? 'text-amber-600 hover:text-amber-700' : 'text-emerald-600 hover:text-emerald-700' }}">
                                        {{ $p->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <span class="text-slate-200">|</span>
                                <a href="{{ route('admin.paket.edit', $p->id) }}" class="text-xs font-semibold text-sky-600 hover:text-sky-700">Ubah</a>
                                <span class="text-slate-200">|</span>
                                <form action="{{ route('admin.paket.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus data paket ini? Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 text-center text-slate-400 text-xs">
                        Tidak ada paket wisata ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Detail Paket & Jadwal Keberangkatan (7 Columns) -->
        <div class="lg:col-span-7 space-y-6">
            @if($selectedPaket)
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                    
                    <!-- Selected Package Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase {{ $selectedPaket->jenis_layanan === 'open_trip' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ $selectedPaket->jenis_layanan === 'open_trip' ? 'Open Trip' : 'Paket Reguler' }}
                            </span>
                            <h2 class="text-xl font-extrabold text-slate-900 mt-2">{{ $selectedPaket->nama_paket }}</h2>
                            <p class="text-xs text-slate-500 mt-1">Destinasi: {{ $selectedPaket->destinasi }} &bull; Durasi: {{ $selectedPaket->durasi_label }} &bull; Meeting Point: {{ $selectedPaket->meeting_point }} (Pk {{ substr($selectedPaket->jam_kumpul, 0, 5) }} WIB)</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400 block">Tarif per Peserta</span>
                            <span class="text-xl font-extrabold text-sky-600">{{ $selectedPaket->harga_format }}</span>
                        </div>
                    </div>

                    <!-- Jadwal Keberangkatan Section -->
                    <div x-data="{ showAddJadwal: false }">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Jadwal Keberangkatan</h3>
                                <p class="text-xs text-slate-400">Daftar tanggal dan pemantauan sisa kuota kursi</p>
                            </div>
                            <button @click="showAddJadwal = !showAddJadwal" class="px-3.5 py-1.5 bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Jadwal
                            </button>
                        </div>

                        <!-- Add Schedule Form Toggle (F2-04) -->
                        <div x-show="showAddJadwal" x-transition class="mb-6 p-5 bg-slate-50 border border-slate-200 rounded-2xl" style="display: none;">
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Tambah Jadwal Baru</h4>
                            <form action="{{ route('admin.jadwal.store', $selectedPaket->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Tanggal Berangkat</label>
                                        <input type="date" name="tanggal_berangkat" required min="{{ now()->addDay()->toDateString() }}" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Kuota Kursi</label>
                                        <input type="number" name="kuota" required min="1" placeholder="30" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Min. Peserta</label>
                                        <input type="number" name="min_peserta" required min="1" placeholder="15" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Pilih Armada Kendaraan</label>
                                        <select name="kendaraan_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl">
                                            <option value="">— Belum Ditetapkan —</option>
                                            @foreach($kendaraanTersedia as $k)
                                                <option value="{{ $k->id }}">{{ $k->jenis_kendaraan }} ({{ $k->kapasitas }} kursi)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Biaya Operasional (Rp)</label>
                                        <input type="number" name="biaya_operasional" placeholder="0" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 pt-2">
                                    <button type="button" @click="showAddJadwal = false" class="px-3 py-1.5 bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">Batal</button>
                                    <button type="submit" class="px-4 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl shadow-xs">Simpan Jadwal</button>
                                </div>
                            </form>
                        </div>

                        <!-- Schedule Table -->
                        <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="py-3 px-4">Tanggal</th>
                                        <th class="py-3 px-3">Kuota</th>
                                        <th class="py-3 px-3">Sisa Kursi</th>
                                        <th class="py-3 px-3">Min. Peserta</th>
                                        <th class="py-3 px-4">Armada Kendaraan</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @forelse($selectedPaket->jadwal as $j)
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-3 px-4 font-bold text-slate-900">
                                                {{ $j->tanggal_berangkat->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="py-3 px-3">{{ $j->kuota }}</td>
                                            <td class="py-3 px-3">
                                                <span class="px-2 py-0.5 rounded-full font-bold {{ $j->sisa_kuota <= 3 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-700' }}">
                                                    {{ $j->sisa_kuota }} kursi
                                                </span>
                                            </td>
                                            <td class="py-3 px-3">{{ $j->min_peserta }}</td>
                                            <td class="py-3 px-4 text-slate-600">
                                                {{ $j->kendaraan ? $j->kendaraan->jenis_kendaraan : '— Belum ditetapkan —' }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $j->status_keberangkatan === 'dikonfirmasi' ? 'bg-emerald-50 text-emerald-700' : ($j->status_keberangkatan === 'ditunda' ? 'bg-amber-50 text-amber-700' : ($j->status_keberangkatan === 'dibatalkan' ? 'bg-rose-50 text-rose-700' : 'bg-sky-50 text-sky-700')) }}">
                                                    {{ $j->status_keberangkatan }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-right">
                                                <form action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-xs">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-slate-400">
                                                Belum ada jadwal keberangkatan untuk paket ini. Klik tombol di atas untuk menambah jadwal.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Itinerary Overview Preview -->
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-sm font-extrabold text-slate-900 mb-3">Ringkasan Itinerary</h3>
                        <div class="space-y-2">
                            @if(is_array($selectedPaket->itinerary))
                                @foreach($selectedPaket->itinerary as $it)
                                    <div class="p-3 bg-slate-50 rounded-xl text-xs flex gap-3">
                                        <span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 font-extrabold h-fit">H-{{ $it['hari'] ?? $loop->iteration }}</span>
                                        <div>
                                            <h4 class="font-bold text-slate-900">{{ $it['judul'] ?? '' }}</h4>
                                            <p class="text-slate-500 mt-0.5">{{ $it['aktivitas'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            @else
                <div class="bg-white p-12 rounded-3xl border border-slate-200/80 text-center text-slate-400 text-xs">
                    Pilih salah satu paket di sebelah kiri untuk melihat jadwal dan detail.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
