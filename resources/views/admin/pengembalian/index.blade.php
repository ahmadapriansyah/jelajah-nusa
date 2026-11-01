@extends('layouts.admin', ['title' => 'Refund & Pindah Jadwal'])

@section('content')
<div class="py-6 space-y-6" x-data="{ 
    jenisPengembalian: 'refund',
    selectedPemesananId: '',
    pemesananList: @js($pemesananTerdampak),
    get selectedPemesanan() {
        return this.pemesananList.find(p => p.id == this.selectedPemesananId) || null;
    }
}">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pencatatan Refund &amp; Pemindahan Jadwal</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola pengembalian dana dan pemindahan tanggal bagi jadwal yang ditunda atau dibatalkan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Proses Pengembalian / Reschedule</h3>
                <p class="text-xs text-slate-400">Pilih pemesanan terdampak dan jenis tindakan</p>
            </div>

            <form action="{{ route('admin.pengembalian.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Pemesanan Terdampak</label>
                    <select name="pemesanan_id" x-model="selectedPemesananId" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                        <option value="">— Pilih Pemesanan —</option>
                        @foreach($pemesananTerdampak as $psn)
                            <option value="{{ $psn->id }}">
                                {{ $psn->display_kode }} &bull; {{ $psn->user->nama_lengkap ?? 'Wisatawan' }} ({{ $psn->judul_trip }}) - Terbayar: {{ $psn->total_terbayar_format }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="selectedPemesanan && jenisPengembalian === 'refund'" class="p-3.5 bg-slate-50 border border-sky-100 rounded-2xl space-y-2">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tujuan Refund Wisatawan</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-sky-100 text-sky-800" x-text="selectedPemesanan?.metode_refund || selectedPemesanan?.metode_pembayaran || 'DANA/Bank'"></span>
                    </div>
                    <div class="text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Penyedia / Bank:</span>
                            <span class="font-bold text-slate-800" x-text="selectedPemesanan?.nama_bank_refund || 'DANA / Bank BCA'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Rekening / DANA:</span>
                            <span class="font-mono font-bold text-sky-600" x-text="selectedPemesanan?.nomor_rekening_refund || selectedPemesanan?.user?.no_hp || '—'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Atas Nama:</span>
                            <span class="font-semibold text-slate-800" x-text="selectedPemesanan?.nama_pemilik_refund || selectedPemesanan?.user?.nama_lengkap || '—'"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Tindakan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer text-xs font-bold" :class="jenisPengembalian === 'refund' ? 'bg-sky-50 border-sky-500 text-sky-700' : 'bg-slate-50 text-slate-700'">
                            <input type="radio" name="jenis" value="refund" x-model="jenisPengembalian" class="text-sky-600">
                            Refund Dana
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer text-xs font-bold" :class="jenisPengembalian === 'pindah_jadwal' ? 'bg-sky-50 border-sky-500 text-sky-700' : 'bg-slate-50 text-slate-700'">
                            <input type="radio" name="jenis" value="pindah_jadwal" x-model="jenisPengembalian" class="text-sky-600">
                            Pindah Jadwal
                        </label>
                    </div>
                </div>

                <div x-show="jenisPengembalian === 'refund'">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominal Refund (Rp)</label>
                    <input type="number" name="nominal" min="1" placeholder="Maks. total terbayar pemesan" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-mono">
                    <p class="text-[11px] text-slate-400 mt-1">Nominal tidak boleh melebihi total yang telah dibayar wisatawan.</p>
                </div>

                <div x-show="jenisPengembalian === 'refund'">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bukti Transfer Refund (Resi/Screenshot)</label>
                    <input type="file" name="bukti_transfer" accept="image/*" class="w-full px-3.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200">
                    <p class="text-[11px] text-slate-400 mt-1">Upload foto/lampiran bukti transfer balik ke wisatawan.</p>
                </div>

                <div x-show="jenisPengembalian === 'pindah_jadwal'" style="display: none;">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Jadwal Baru yang Tersedia</label>
                    <select name="jadwal_baru_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                        <option value="">— Pilih Jadwal Pengganti —</option>
                        @foreach($jadwalTersedia as $jb)
                            <option value="{{ $jb->id }}">
                                {{ $jb->paket->nama_paket ?? 'Trip' }} &bull; {{ $jb->tanggal_berangkat->format('d/m/Y') }} (Sisa {{ $jb->sisa_kuota }} kursi)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alasan Pengembalian / Reschedule</label>
                    <textarea name="alasan" required rows="3" placeholder="Contoh: Jadwal ditunda akibat cuaca buruk di laut perairan Komodo..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                    Simpan &amp; Proses Pengembalian
                </button>
            </form>
        </div>

        <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-extrabold text-slate-900">Riwayat Pengembalian &amp; Reschedule</h3>
                <span class="text-xs text-slate-400">{{ $riwayat->total() }} Data Tercatat</span>
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Kode Pesanan</th>
                            <th class="py-3 px-3">Pemesan</th>
                            <th class="py-3 px-2">Jenis</th>
                            <th class="py-3 px-3">Nominal / Jadwal Baru</th>
                            <th class="py-3 px-3">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($riwayat as $rw)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-3 text-slate-400">
                                    {{ $rw->tanggal_pengembalian->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3 font-mono font-bold text-sky-600">
                                    {{ $rw->pemesanan ? $rw->pemesanan->display_kode : '—' }}
                                </td>
                                <td class="py-3 px-3 text-slate-800 font-bold">
                                    {{ $rw->pemesanan->user->nama_lengkap ?? '—' }}
                                </td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $rw->jenis === 'refund' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800' }}">
                                        {{ $rw->jenis === 'refund' ? 'Refund' : 'Reschedule' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    @if($rw->jenis === 'refund')
                                        <span class="font-bold text-emerald-600">{{ $rw->nominal_format }}</span>
                                        @if($rw->bukti_transfer)
                                            <a href="{{ asset('storage/' . $rw->bukti_transfer) }}" target="_blank" class="block text-[10px] text-sky-600 hover:underline mt-0.5">Lihat Resi</a>
                                        @endif
                                    @else
                                        <span class="text-slate-700">Tgl: {{ $rw->jadwalBaru ? $rw->jadwalBaru->tanggal_berangkat->format('d/m/Y') : '—' }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-slate-500 max-w-[150px] truncate" title="{{ $rw->alasan }}">
                                    {{ $rw->alasan }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada catatan refund atau pemindahan jadwal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $riwayat->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

```