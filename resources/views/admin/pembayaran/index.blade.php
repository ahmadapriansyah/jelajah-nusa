@extends('layouts.admin', ['title' => 'Verifikasi Pembayaran'])

@section('content')
<div class="py-6 space-y-6" x-data="{ tolakModal: false, tolakId: null }">
    
    <!-- Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Verifikasi Pembayaran Transfer</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pemeriksaan bukti transfer bank, perbandingan tagihan, dan konfirmasi otomatis tiket digital</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('admin.pembayaran.index', ['status' => 'menunggu']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'menunggu' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600' }}">
                Menunggu ({{ \App\Models\Pembayaran::where('status', 'menunggu')->count() }})
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'diverifikasi']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'diverifikasi' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600' }}">
                Diverifikasi
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'ditolak']) }}" class="px-3 py-1.5 rounded-xl font-bold {{ $status === 'ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600' }}">
                Ditolak
            </a>
        </div>
    </div>

    <!-- 3-Column Verification Layout (Frame 107:1806) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Kolom 1 (Kiri): Antrean Pembayaran (3.5 Cols) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Antrean Masuk ({{ $antrean->count() }})</h3>
            </div>

            <div class="space-y-2.5 max-h-[70vh] overflow-y-auto pr-1">
                @forelse($antrean as $bayar)
                    <a href="{{ route('admin.pembayaran.index', ['selected' => $bayar->id, 'status' => $status]) }}" 
                       class="block p-3.5 rounded-2xl border transition-all {{ $selectedPembayaran && $selectedPembayaran->id === $bayar->id ? 'border-sky-500 bg-sky-50/60 ring-2 ring-sky-500/20' : 'border-slate-100 bg-slate-50 hover:border-slate-300' }}">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <span class="text-xs font-extrabold text-slate-900 truncate">{{ $bayar->pemesanan->user->nama_lengkap ?? 'Wisatawan' }}</span>
                            <span class="text-xs font-extrabold text-sky-600 font-mono">{{ $bayar->nominal_format }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 truncate mb-1.5">{{ $bayar->pemesanan->judul_trip }}</p>
                        <div class="flex items-center justify-between text-[10px] text-slate-400">
                            <span>Tgl: {{ $bayar->tanggal_bayar->format('d/m/Y') }}</span>
                            <span class="px-2 py-0.5 rounded-full font-bold uppercase {{ $bayar->status === 'diverifikasi' ? 'bg-emerald-100 text-emerald-800' : ($bayar->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ $bayar->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="py-12 text-center text-slate-400 text-xs">
                        Tidak ada antrean pembayaran dalam status ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Kolom 2 (Tengah): Pratinjau Bukti Transfer (4.5 Cols) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pratinjau Bukti Transfer</h3>
                @if($selectedPembayaran)
                    <a href="{{ route('dokumen.bukti', $selectedPembayaran->id) }}" target="_blank" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                        Buka Tab Baru &rarr;
                    </a>
                @endif
            </div>

            @if($selectedPembayaran)
                <div class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 flex items-center justify-center min-h-[350px] p-2">
                    <img src="{{ route('dokumen.bukti', $selectedPembayaran->id) }}" 
                         alt="Bukti Transfer" 
                         class="max-h-[420px] w-auto object-contain rounded-xl shadow-lg">
                </div>
                <div class="text-[11px] text-slate-400 text-center">
                    Berkas: <span class="font-mono text-slate-600">{{ basename($selectedPembayaran->bukti_transfer) }}</span>
                </div>
            @else
                <div class="py-24 text-center text-slate-400 text-xs">
                    Pilih pembayaran untuk memeriksa bukti transfer.
                </div>
            @endif
        </div>

        <!-- Kolom 3 (Kanan): Perbandingan Tagihan & Aksi (4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            @if($selectedPembayaran)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
                    
                    <div>
                        <span class="text-xs font-mono font-bold text-sky-600">{{ $selectedPembayaran->pemesanan->display_kode }}</span>
                        <h3 class="text-base font-extrabold text-slate-900 mt-0.5">{{ $selectedPembayaran->pemesanan->judul_trip }}</h3>
                        <p class="text-xs text-slate-500">{{ $selectedPembayaran->pemesanan->jumlah_peserta }} Peserta &bull; Tgl Unggah: {{ $selectedPembayaran->created_at->translatedFormat('d F Y H:i') }} WIB</p>
                    </div>

                    <!-- Perbandingan Nominal Tagihan vs Transfer (Frame 107:1806) -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Tagihan Pesanan:</span>
                            <span class="font-bold text-slate-800">{{ $selectedPembayaran->pemesanan->total_biaya_format }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Nominal Transfer Masuk:</span>
                            <span class="text-sm font-extrabold text-sky-600 font-mono">{{ $selectedPembayaran->nominal_format }}</span>
                        </div>
                        
                        <div class="pt-2 border-t border-slate-200/80">
                            @if((float)$selectedPembayaran->nominal >= (float)$selectedPembayaran->pemesanan->total_biaya)
                                <div class="flex items-center gap-2 text-emerald-700 bg-emerald-100/70 p-2.5 rounded-xl text-xs font-bold">
                                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span>Nominal Sesuai (Lunas)</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-amber-800 bg-amber-100/70 p-2.5 rounded-xl text-xs font-bold">
                                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span>Pembayaran Bertahap / Ada Selisih</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($selectedPembayaran->status === 'ditolak')
                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs space-y-1">
                            <span class="font-bold text-rose-800 block">Alasan Penolakan:</span>
                            <p class="text-rose-700">{{ $selectedPembayaran->catatan_admin }}</p>
                        </div>
                    @endif

                    @if($selectedPembayaran->status === 'diverifikasi')
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs">
                            <span class="font-bold text-emerald-800 block mb-1">Sudah Diverifikasi</span>
                            <p class="text-emerald-700">Diverifikasi pada: {{ $selectedPembayaran->diverifikasi_pada ? $selectedPembayaran->diverifikasi_pada->format('d/m/Y H:i') : '—' }} WIB</p>
                        </div>
                    @endif

                    <!-- Action Buttons (F4-02, BR-06) -->
                    @if($selectedPembayaran->status === 'menunggu')
                        <div class="pt-2 flex items-center gap-3">
                            <button type="button" 
                                    @click="tolakModal = true; tolakId = {{ $selectedPembayaran->id }}" 
                                    class="w-1/2 py-2.5 bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs font-bold rounded-xl transition border border-slate-200 hover:border-rose-300">
                                Tolak Pembayaran
                            </button>

                            <form action="{{ route('admin.pembayaran.verifikasi', $selectedPembayaran->id) }}" method="POST" class="w-1/2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                                    Verifikasi &amp; Terbitkan Tiket
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tolak Pembayaran (Frame 107:1096, BR-06, US-09, T-09) -->
    <div x-show="tolakModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" style="display: none;">
        <div @click.away="tolakModal = false" class="bg-white max-w-md w-full p-6 sm:p-8 rounded-3xl shadow-2xl space-y-5">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            
            <div class="text-center">
                <h3 class="text-lg font-extrabold text-slate-900">Tolak Pembayaran Ini?</h3>
                <p class="text-xs text-slate-500 mt-1">Wisatawan akan menerima pemberitahuan dan alasan penolakan ini, serta diberikan kesempatan mengunggah bukti baru.</p>
            </div>

            <form :action="'{{ url('admin/pembayaran') }}/' + tolakId + '/tolak'" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alasan Penolakan (Wajib Diisi)</label>
                    <textarea name="catatan_admin" required rows="3" placeholder="Contoh: Foto bukti transfer buram / nominal tidak sesuai dengan mutasi rekening..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="tolakModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                        Konfirmasi Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
