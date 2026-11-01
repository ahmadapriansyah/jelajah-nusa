@extends('layouts.admin', ['title' => 'Manajemen Tiket'])

@section('content')
<div class="py-6 space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Tiket Digital</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar voucher perjalanan aktif, cetak tiket resmi, dan validasi kode keaslian</p>
        </div>
    </div>

    <!-- 2 Column Layout (Frame 107:2678) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Tabel Tiket Terbit (7.5 Cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <form action="{{ route('admin.tiket.index') }}" method="GET" class="relative max-w-md w-full">
                    <input type="text" 
                           name="cari" 
                           value="{{ $search }}" 
                           placeholder="Cari kode tiket (JN-PSN...) atau nama peserta..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-700">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
                <span class="text-xs font-bold text-slate-500">Total: {{ $tiketList->total() }} Tiket</span>
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Kode Tiket</th>
                            <th class="py-3 px-3">Nama Peserta</th>
                            <th class="py-3 px-3">Paket / Trip</th>
                            <th class="py-3 px-3">Tgl Berangkat</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($tiketList as $tk)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3.5 px-4 font-mono font-bold text-sky-600">
                                    {{ $tk->kode_tiket }}
                                </td>
                                <td class="py-3.5 px-3 font-bold text-slate-900">
                                    {{ $tk->peserta->nama_lengkap ?? '—' }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-700 max-w-[170px] truncate">
                                    {{ $tk->pemesanan ? $tk->pemesanan->judul_trip : '—' }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-500">
                                    {{ $tk->pemesanan && $tk->pemesanan->tanggal_berangkat ? $tk->pemesanan->tanggal_berangkat->format('d/m/Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $tk->status === 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $tk->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="{{ route('admin.tiket.cetak', $tk->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Cetak PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada tiket yang terbit. Tiket diterbitkan otomatis saat pesanan lunas diverifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $tiketList->links() }}
            </div>
        </div>

        <!-- Right: Panel Verifikasi Tiket (4.5 Cols, Frame 107:2678, US-14, T-13) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Verifikasi Keaslian Tiket</h3>
                <p class="text-xs text-slate-400 mt-0.5">Masukkan kode tiket atau scan QR Code peserta saat boarding</p>
            </div>

            <form action="{{ route('admin.tiket.index') }}" method="GET" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kode Tiket</label>
                    <input type="text" 
                           name="verifikasi_kode" 
                           value="{{ $verifikasiKode }}" 
                           required 
                           placeholder="Contoh: JN-PSN1041-01" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 uppercase focus:outline-none focus:ring-2 focus:ring-sky-500/20">
                </div>

                <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                    Cek Keaslian Tiket
                </button>
            </form>

            @if($hasilVerifikasi)
                <div class="mt-4 p-5 rounded-2xl border transition-all {{ $hasilVerifikasi['valid'] ? 'bg-emerald-50/80 border-emerald-200' : 'bg-rose-50/80 border-rose-200' }}">
                    <div class="flex items-center gap-2 mb-2">
                        @if($hasilVerifikasi['valid'])
                            <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <h4 class="text-sm font-extrabold text-emerald-900 tracking-wide">{{ $hasilVerifikasi['status_teks'] }}</h4>
                        @else
                            <svg class="w-6 h-6 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <h4 class="text-sm font-extrabold text-rose-900 tracking-wide">{{ $hasilVerifikasi['status_teks'] }}</h4>
                        @endif
                    </div>
                    
                    <p class="text-xs mb-3 {{ $hasilVerifikasi['valid'] ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $hasilVerifikasi['pesan'] }}
                    </p>

                    @if($hasilVerifikasi['valid'])
                        <div class="space-y-1.5 text-xs text-slate-800 bg-white/80 p-3 rounded-xl border border-emerald-100">
                            <div><span class="text-slate-400">Nama Peserta:</span> <strong class="text-slate-900">{{ $hasilVerifikasi['nama_peserta'] }}</strong></div>
                            <div><span class="text-slate-400">No. Identitas:</span> <span class="font-mono">{{ $hasilVerifikasi['no_identitas'] }}</span></div>
                            <div><span class="text-slate-400">Paket Wisata:</span> <strong>{{ $hasilVerifikasi['nama_paket'] }}</strong></div>
                            <div><span class="text-slate-400">Tgl Berangkat:</span> {{ $hasilVerifikasi['tanggal_berangkat'] }}</div>
                            <div><span class="text-slate-400">Pemesan:</span> {{ $hasilVerifikasi['pemesan'] }}</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
