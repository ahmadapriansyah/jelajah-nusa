@extends('layouts.wisatawan', ['title' => 'Pembayaran Transfer — Jelajah Nusa'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <div>
        <span class="text-xs font-mono font-bold text-sky-600 uppercase tracking-wider">{{ $pemesanan->display_kode }}</span>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Pembayaran Transfer Bank</h1>
        <p class="text-xs sm:text-sm text-slate-500">Selesaikan pembayaran sebelum batas waktu berakhir untuk mengonfirmasi tiket perjalanan Anda</p>
    </div>

    <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs text-sky-400 font-bold uppercase tracking-wider block mb-1">Status Pemesanan Saat Ini:</span>
            <span class="text-xl font-extrabold capitalize text-white">{{ $pemesanan->status }} Pembayaran</span>
            <p class="text-xs text-slate-400 mt-1">Total Tagihan: <strong class="text-white">{{ $pemesanan->total_biaya_format }}</strong> &bull; Sisa: <strong class="text-sky-400 font-mono">{{ $pemesanan->sisa_tagihan_format }}</strong></p>
        </div>

        @if($pemesanan->batas_bayar && $pemesanan->status === 'menunggu')
            <div class="text-right sm:text-right bg-white/10 backdrop-blur p-3.5 rounded-2xl border border-white/15">
                <span class="text-[11px] text-slate-300 block font-medium">Batas Waktu Transfer:</span>
                <span class="text-base font-extrabold text-amber-300 font-mono block">
                    {{ $pemesanan->batas_bayar->format('d/m/Y H:i') }} WIB
                </span>
                <span class="text-[10px] text-slate-400 block mt-0.5">
                    ({{ $pemesanan->batas_bayar->diffForHumans() }})
                </span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        
        <div class="md:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Rekening Resmi Tujuan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Silakan transfer via ATM, Mobile Banking, atau Internet Banking</p>
            </div>

            <div class="p-5 rounded-2xl bg-gradient-to-tr from-sky-50 to-indigo-50/60 border border-sky-100 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-sky-800 uppercase tracking-wider">{{ $pengaturan->nama_bank }}</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                </div>
                
                <div>
                    <span class="text-[11px] text-slate-400 block font-medium">Nomor Rekening:</span>
                    <div class="flex items-center justify-between mt-0.5">
                        <span class="text-lg font-extrabold font-mono text-slate-900 tracking-wider">{{ $pengaturan->no_rekening }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-sky-200/60">
                    <span class="text-[11px] text-slate-400 block font-medium">Atas Nama:</span>
                    <strong class="text-xs text-slate-800 block">{{ $pengaturan->atas_nama }}</strong>
                </div>
            </div>

            <div class="text-xs text-slate-500 space-y-2 leading-relaxed">
                <h4 class="font-bold text-slate-800">Petunjuk Pembayaran:</h4>
                <ol class="list-decimal list-inside space-y-1 pl-1">
                    <li>Transfer sesuai nominal sisa tagihan (<strong class="text-sky-600 font-mono">{{ $pemesanan->sisa_tagihan_format }}</strong>).</li>
                    <li>Simpan bukti transfer berupa struk fisik atau tangkapan layar (screenshot m-banking).</li>
                    <li>Unggah foto/PDF bukti transfer pada formulir di samping.</li>
                    <li>Admin akan memverifikasi dan tiket otomatis terbit.</li>
                </ol>
            </div>
        </div>

        <div class="md:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
            
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Konfirmasi Bukti Transfer</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kirimkan data transfer untuk diverifikasi oleh admin travel</p>
            </div>

            @foreach($pemesanan->pembayaran->where('status', 'ditolak') as $tolak)
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs space-y-1">
                    <div class="flex items-center gap-2 text-rose-800 font-bold">
                        <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Pembayaran Sebelumnya Ditolak</span>
                    </div>
                    <p class="text-rose-700 pl-6">Alasan Admin: <strong>{{ $tolak->catatan_admin }}</strong></p>
                    <p class="text-[11px] text-rose-600 pl-6">Silakan unggah ulang bukti transfer yang jelas dan valid di bawah ini.</p>
                </div>
            @endforeach

            @if($pemesanan->sisa_tagihan > 0 && $pemesanan->status !== 'dibatalkan')
                <form action="{{ route('pembayaran.store', $pemesanan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Pemesanan</label>
                        <input type="text" value="{{ $pemesanan->display_kode }} ({{ $pemesanan->judul_trip }})" disabled class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-600 font-bold">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominal Transfer (Rp)</label>
                            <input type="text" 
                                   value="Rp {{ number_format((int)$pemesanan->sisa_tagihan, 0, ',', '.') }}" 
                                   readonly 
                                   tabindex="-1"
                                   class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-700 cursor-not-allowed select-none focus:outline-none">
                            <input type="hidden" 
                                   name="nominal" 
                                   value="{{ (int)$pemesanan->sisa_tagihan }}">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Transfer Dilakukan</label>
                            <input type="date" 
                                   name="tanggal_bayar" 
                                   value="{{ old('tanggal_bayar', now()->toDateString()) }}" 
                                   required 
                                   max="{{ now()->toDateString() }}" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Unggah Foto / PDF Bukti Transfer</label>
                        <input type="file" 
                               name="bukti_transfer" 
                               required 
                               accept="image/png,image/jpeg,image/jpg,application/pdf" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        <span class="text-[11px] text-slate-400 mt-1 block">Format diperbolehkan: JPG, PNG, atau PDF. Ukuran berkas maksimal 5 MB.</span>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                            Kirim Bukti Pembayaran
                        </button>
                    </div>
                </form>
            @else
                <div class="p-6 bg-slate-50 rounded-2xl text-center text-xs text-slate-500 space-y-2">
                    @if($pemesanan->is_lunas)
                        <svg class="w-8 h-8 text-emerald-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <strong class="text-slate-900 block text-sm">Pembayaran Pesanan Ini Telah Lunas</strong>
                        <p>Tiket digital Anda sudah aktif. Anda dapat melihat dan mengunduhnya di halaman Detail Pesanan.</p>
                        <div class="pt-2">
                            <a href="{{ route('pesanan-saya.show', $pemesanan->id) }}" class="px-4 py-2 bg-sky-600 text-white font-bold rounded-xl inline-block text-xs">Lihat Tiket Saya</a>
                        </div>
                    @else
                        <p>Pesanan ini telah dibatalkan.</p>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>
@endsection