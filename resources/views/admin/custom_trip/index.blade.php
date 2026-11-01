@extends('layouts.admin', ['title' => 'Custom Trip'])

@section('content')
<div class="py-6 space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Custom Trip</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar permohonan perjalanan khusus rombongan wisatawan dan penawaran biaya</p>
        </div>
    </div>

    <!-- 2 Column Layout (Frame 107:2281) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Tabel Pengajuan Masuk (7 Cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Pengajuan Masuk</h3>
                <div class="flex items-center gap-1.5 text-xs">
                    <a href="{{ route('admin.custom-trip.index') }}" class="px-2.5 py-1 rounded-lg font-bold {{ empty($status) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                    <a href="{{ route('admin.custom-trip.index', ['status' => 'diajukan']) }}" class="px-2.5 py-1 rounded-lg font-bold {{ $status === 'diajukan' ? 'bg-sky-600 text-white' : 'bg-sky-50 text-sky-700' }}">Diajukan</a>
                    <a href="{{ route('admin.custom-trip.index', ['status' => 'ditawarkan']) }}" class="px-2.5 py-1 rounded-lg font-bold {{ $status === 'ditawarkan' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700' }}">Ditawarkan</a>
                    <a href="{{ route('admin.custom-trip.index', ['status' => 'disetujui']) }}" class="px-2.5 py-1 rounded-lg font-bold {{ $status === 'disetujui' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700' }}">Disetujui</a>
                </div>
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-3">Pemohon</th>
                            <th class="py-3 px-3">Tujuan</th>
                            <th class="py-3 px-2 text-center">Orang</th>
                            <th class="py-3 px-3">Tgl Diinginkan</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-2 text-right">Pilih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($customTripList as $trip)
                            <tr class="hover:bg-slate-50/60 {{ $selectedTrip && $selectedTrip->id === $trip->id ? 'bg-sky-50/50 font-bold' : '' }}">
                                <td class="py-3.5 px-3">
                                    <span class="block font-bold text-slate-900">{{ $trip->user->nama_lengkap ?? '—' }}</span>
                                    <span class="block text-[11px] text-slate-400 font-normal">{{ $trip->user->no_hp }}</span>
                                </td>
                                <td class="py-3.5 px-3 font-bold text-slate-800">
                                    {{ $trip->tujuan }}
                                </td>
                                <td class="py-3.5 px-2 text-center">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 font-bold">{{ $trip->jumlah_peserta }}</span>
                                </td>
                                <td class="py-3.5 px-3 text-slate-600">
                                    {{ $trip->tanggal_diinginkan->translatedFormat('d M Y') }} ({{ $trip->durasi_hari }} Hari)
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $trip->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700' : ($trip->status === 'ditawarkan' ? 'bg-amber-50 text-amber-700' : ($trip->status === 'diajukan' ? 'bg-sky-50 text-sky-700' : 'bg-rose-50 text-rose-700')) }}">
                                        {{ $trip->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-2 text-right">
                                    <a href="{{ route('admin.custom-trip.index', ['selected' => $trip->id, 'status' => $status]) }}" class="px-2.5 py-1 bg-white border border-slate-200 hover:border-sky-500 hover:text-sky-600 text-slate-700 rounded-lg text-[11px] font-bold">
                                        Pilih
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada permohonan custom trip.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $customTripList->links() }}
            </div>
        </div>

        <!-- Right: Panel Penawaran (5 Cols, Frame 107:2281) -->
        <div class="lg:col-span-5 space-y-6">
            @if($selectedTrip)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                    
                    <div class="pb-4 border-b border-slate-100">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-sky-50 text-sky-700">Penawaran Paket</span>
                        <h3 class="text-lg font-extrabold text-slate-900 mt-1">{{ $selectedTrip->tujuan }}</h3>
                        <p class="text-xs text-slate-500">{{ $selectedTrip->jumlah_peserta }} Orang &bull; {{ $selectedTrip->durasi_hari }} Hari &bull; Pemohon: {{ $selectedTrip->user->nama_lengkap }}</p>
                    </div>

                    <!-- Kebutuhan Wisatawan -->
                    <div class="p-4 bg-slate-50 rounded-2xl text-xs space-y-1">
                        <span class="font-bold text-slate-700 block uppercase tracking-wider text-[11px]">Kebutuhan &amp; Permintaan Khusus:</span>
                        <p class="text-slate-600 leading-relaxed">{{ $selectedTrip->kebutuhan }}</p>
                    </div>

                    <!-- Formulir Penawaran Admin (F3-07) -->
                    <form action="{{ route('admin.custom-trip.penawaran', $selectedTrip->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tetapkan Armada Kendaraan</label>
                            <select name="kendaraan_id" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                                <option value="">— Pilih Kendaraan Tersedia —</option>
                                @foreach($kendaraanList as $knd)
                                    <option value="{{ $knd->id }}" {{ $selectedTrip->kendaraan_id === $knd->id ? 'selected' : '' }}>
                                        {{ $knd->jenis_kendaraan }} (Kapasitas {{ $knd->kapasitas }} kursi)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Keberangkatan Penawaran</label>
                            <input type="date" name="jadwal_penawaran" value="{{ old('jadwal_penawaran', $selectedTrip->jadwal_penawaran ? $selectedTrip->jadwal_penawaran->toDateString() : $selectedTrip->tanggal_diinginkan->toDateString()) }}" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fasilitas yang Ditawarkan</label>
                            <textarea name="fasilitas_penawaran" rows="3" required placeholder="• Armada Hiace Premio&#10;• Penginapan Villa privat&#10;• Tiket objek wisata &amp; catering" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">{{ old('fasilitas_penawaran', $selectedTrip->fasilitas_penawaran) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Total Biaya Penawaran Paket (Rp)</label>
                            <input type="number" name="biaya_penawaran" value="{{ old('biaya_penawaran', (int)$selectedTrip->biaya_penawaran) }}" required min="1" placeholder="Contoh: 15000000" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-bold">
                        </div>

                        <div class="pt-2 flex items-center justify-between gap-3">
                            <button type="submit" class="flex-1 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                                Kirim Penawaran Biaya
                            </button>
                        </div>
                    </form>

                    @if($selectedTrip->status === 'diajukan')
                        <form action="{{ route('admin.custom-trip.tolak', $selectedTrip->id) }}" method="POST" onsubmit="return confirm('Tolak permohonan custom trip ini?');" class="pt-2 border-t border-slate-100">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs font-bold rounded-xl transition">
                                Tolak Permohonan Ini
                            </button>
                        </form>
                    @endif

                </div>
            @else
                <div class="bg-white p-12 rounded-3xl border border-slate-200/80 text-center text-slate-400 text-xs">
                    Pilih permohonan custom trip di sebelah kiri untuk memberikan penawaran armada dan biaya.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
