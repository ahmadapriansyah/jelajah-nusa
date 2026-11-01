@extends('layouts.wisatawan', ['title' => 'Detail Pesanan ' . $pemesanan->display_kode])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="{ openRefundModal: false }">

    <!-- Pop-up / Alert Notifikasi Session (Sukses & Error) -->
    @if(session('sukses'))
        <div x-data="{ show: true }" x-show="show" class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-sm transition">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('sukses') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 font-bold text-base">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-between text-rose-800 text-xs font-bold shadow-sm transition">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 7.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-rose-500 hover:text-rose-700 font-bold text-base">&times;</button>
        </div>
    @endif
    
    <div class="flex items-center justify-between">
        <a href="{{ route('pesanan-saya.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
            &larr; Kembali ke Riwayat Pesanan
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $pemesanan->status === 'dikonfirmasi' ? 'bg-emerald-50 text-emerald-700' : ($pemesanan->status === 'menunggu' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
            {{ $pemesanan->status === 'dikonfirmasi' ? 'Dikonfirmasi (Tiket Aktif)' : ($pemesanan->status === 'menunggu' ? 'Menunggu Pembayaran' : 'Dibatalkan') }}
        </span>
    </div>

    @if($pemesanan->status === 'dibatalkan')
        <div class="p-6 bg-rose-50 border border-rose-200 rounded-3xl space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-rose-800 font-bold">
                    <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 7.707 7.293z" clip-rule="evenodd"/></svg>
                    <span class="text-sm">Perjalanan Dibatalkan (Kuota Tidak Terpenuhi)</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-700">Dibatalkan</span>
            </div>
            <p class="text-xs text-rose-700 leading-relaxed">
                Mohon maaf, perjalanan untuk trip ini dibatalkan karena kuota minimal tidak terpenuhi. Anda dapat mengajukan pengembalian dana 100% (Refund) ke rekening bank atau akun DANA Anda.
            </p>

            @if(!$pemesanan->refund)
                <div class="pt-2">
                    <button @click="openRefundModal = true" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Ajukan Pengembalian Dana (Refund)
                    </button>
                </div>
            @else
                <div class="p-3.5 bg-white/80 rounded-2xl border border-rose-200 text-xs space-y-1">
                    <span class="font-bold text-slate-800">Status Pengajuan Refund:</span>
                    <div class="text-slate-600">
                        Metode: <strong class="text-slate-900">{{ $pemesanan->refund->nama_bank ?? $pemesanan->refund->metode_pembayaran }}</strong> &bull; 
                        No: <strong class="font-mono text-slate-900">{{ $pemesanan->refund->nomor_rekening }}</strong> 
                        a.n. <strong class="text-slate-900">{{ $pemesanan->refund->nama_pemilik }}</strong>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <span class="text-xs font-mono font-bold text-sky-600">{{ $pemesanan->display_kode }}</span>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $pemesanan->judul_trip }}</h1>
                <p class="text-xs text-slate-500 mt-1">Dipesan pada: {{ $pemesanan->created_at->translatedFormat('d F Y H:i') }} WIB</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-xs text-slate-400 block font-medium">Total Biaya</span>
                <span class="text-2xl font-extrabold text-slate-900 font-mono">{{ $pemesanan->total_biaya_format }}</span>
            </div>
        </div>

        <!-- Detail Perjalanan Ringkas -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 text-xs">
            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 block font-medium">Tanggal Berangkat</span>
                <strong class="text-slate-900 text-sm block mt-0.5">
                    {{ $pemesanan->tanggal_berangkat ? $pemesanan->tanggal_berangkat->translatedFormat('d F Y') : '—' }}
                </strong>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 block font-medium">Jumlah Wisatawan</span>
                <strong class="text-slate-900 text-sm block mt-0.5">{{ $pemesanan->jumlah_peserta }} Orang</strong>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 block font-medium">Status Tagihan</span>
                <strong class="text-sm block mt-0.5 {{ $pemesanan->is_lunas ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $pemesanan->is_lunas ? 'Lunas' : 'Sisa ' . $pemesanan->sisa_tagihan_format }}
                </strong>
            </div>
        </div>
    </div>

    <!-- Tiket Digital Per Peserta (F10-04, Bagian 19) -->
    @if($pemesanan->tiket->count() > 0)
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Tiket / Voucher Digital Anda</h3>
                    <p class="text-xs text-slate-400">Unduh e-tiket PDF atau tunjukkan QR Code pada pemandu saat keberangkatan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($pemesanan->tiket as $tkt)
                    <div class="p-5 rounded-2xl bg-gradient-to-tr from-slate-900 to-indigo-950 text-white shadow-md relative overflow-hidden flex flex-col justify-between space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-white/20 text-sky-200">E-Tiket Resmi</span>
                                <h4 class="text-base font-extrabold mt-1 text-white">{{ $tkt->peserta->nama_lengkap ?? 'Peserta' }}</h4>
                                <p class="text-xs text-slate-400 font-mono">NIK: {{ $tkt->peserta->no_identitas ?? '—' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                {{ $tkt->status }}
                            </span>
                        </div>

                        <div class="pt-3 border-t border-white/10 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Kode Tiket:</span>
                                <span class="text-sm font-mono font-extrabold text-sky-300">{{ $tkt->kode_tiket }}</span>
                            </div>
                            <a href="{{ route('tiket.unduh', $tkt->id) }}" class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-400 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh PDF
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Manifest Peserta Terdaftar -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <h3 class="text-base font-extrabold text-slate-900 pb-3 border-b border-slate-100">Daftar Manifest Peserta ({{ $pemesanan->peserta->count() }} Orang)</h3>
        
        <div class="space-y-3">
            @foreach($pemesanan->peserta as $pst)
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between gap-3 text-xs">
                    <div>
                        <strong class="text-slate-900 text-sm block">{{ $pst->nama_lengkap }}</strong>
                        <span class="text-slate-500 font-mono">{{ strtoupper($pst->jenis_identitas) }}: {{ $pst->no_identitas }}</span>
                        @if($pst->no_hp)
                            <span class="text-slate-400 block mt-0.5">Kontak: {{ $pst->no_hp }}</span>
                        @endif
                    </div>
                    <a href="{{ route('dokumen.identitas', $pst->id) }}" target="_blank" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-sky-50 text-sky-600 rounded-xl font-bold text-xs transition">
                        KTP Tersimpan &check;
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Riwayat Pembayaran & Bukti Transfer -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Riwayat Pembayaran</h3>
                <p class="text-xs text-slate-400">Total terverifikasi: <strong class="text-emerald-600">{{ $pemesanan->total_terbayar_format }}</strong> dari {{ $pemesanan->total_biaya_format }}</p>
            </div>
            @if($pemesanan->status === 'menunggu' && $pemesanan->sisa_tagihan > 0)
                <a href="{{ route('pembayaran.show', $pemesanan->id) }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    + Unggah Bukti Bayar
                </a>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($pemesanan->pembayaran as $pby)
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                    <div>
                        <span class="text-sm font-extrabold font-mono text-slate-900">{{ $pby->nominal_format }}</span>
                        <span class="text-slate-500 block mt-0.5">Tgl Bayar: {{ $pby->tanggal_bayar->format('d/m/Y') }} &bull; Diunggah: {{ $pby->created_at->format('d/m/Y H:i') }}</span>
                        @if($pby->status === 'ditolak' && $pby->catatan_admin)
                            <p class="text-rose-600 font-semibold mt-1">Alasan Penolakan: {{ $pby->catatan_admin }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $pby->status === 'diverifikasi' ? 'bg-emerald-100 text-emerald-800' : ($pby->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $pby->status }}
                        </span>
                        <a href="{{ route('dokumen.bukti', $pby->id) }}" target="_blank" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:text-sky-600 rounded-xl font-bold">
                            Lihat Bukti
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-6 bg-slate-50 rounded-2xl text-center text-xs text-slate-400">
                    Belum ada bukti pembayaran yang diunggah untuk pesanan ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tombol Batalkan Pesanan Mandiri (F11-04, US-16, T-22) -->
    @if($pemesanan->status === 'menunggu')
        <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Batalkan Pemesanan Ini?</h4>
                <p class="text-xs text-slate-400">Kursi yang ditahan akan langsung dikembalikan ke kuota jadwal.</p>
            </div>
            <form action="{{ route('pesanan-saya.batal', $pemesanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Kuota akan dikembalikan.');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl transition border border-rose-200">
                    Batalkan Pesanan
                </button>
            </form>
        </div>
    @endif

    <div x-show="openRefundModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openRefundModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Form Pengajuan Refund</h3>
                    <p class="text-xs text-slate-400">Kode Pesanan: <span class="font-mono font-bold text-sky-600">{{ $pemesanan->display_kode }}</span></p>
                </div>
                <button @click="openRefundModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('pesanan.ajukan-refund', $pemesanan->id) }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Pengembalian</label>
                    <select name="nama_bank" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                        <option value="DANA">DANA (E-Wallet)</option>
                        <option value="OVO">OVO (E-Wallet)</option>
                        <option value="GoPay">GoPay (E-Wallet)</option>
                        <option value="Bank BCA">Bank BCA</option>
                        <option value="Bank BRI">Bank BRI</option>
                        <option value="Bank Mandiri">Bank Mandiri</option>
                        <option value="Bank BNI">Bank BNI</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rekening / No. DANA</label>
                    <input type="text" name="nomor_rekening" required placeholder="Contoh: 081234567890 / 5270123456" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Pemilik Akun / Rekening</label>
                    <input type="text" name="nama_pemilik" required placeholder="Sesuai nama terdaftar di DANA/Bank" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="openRefundModal = false" class="w-1/2 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="w-1/2 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md transition">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection