@extends('layouts.wisatawan', ['title' => 'Form Pemesanan Paket — Jelajah Nusa'])

@section('content')
@php
    $maxSeats = $jadwal ? $jadwal->sisa_kuota : ($customTrip ? $customTrip->jumlah_peserta : 1);
    $unitPrice = $jadwal ? (float)$jadwal->paket->harga : 0;
    $customPrice = $customTrip ? (float)$customTrip->biaya_penawaran : 0;
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" 
     x-data="bookingFormManager({{ $maxSeats }}, {{ $unitPrice }}, {{ $customPrice }}, {{ $customTrip ? 'true' : 'false' }})">
    
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Form Pemesanan &amp; Data Peserta</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi data seluruh peserta dan unggah foto identitas (KTP/Paspor) untuk penerbitan tiket</p>
    </div>

    <!-- Ringkasan Paket Terpilih -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            @if($jadwal)
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase bg-sky-50 text-sky-700">Jadwal Open Trip / Reguler</span>
                <h3 class="text-lg font-extrabold text-slate-900 mt-1">{{ $jadwal->paket->nama_paket }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Tanggal: <strong>{{ $jadwal->tanggal_berangkat->translatedFormat('l, d F Y') }}</strong> &bull; Sisa Kuota: <strong class="text-emerald-600">{{ $jadwal->sisa_kuota }} Kursi</strong>
                </p>
            @elseif($customTrip)
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase bg-indigo-50 text-indigo-700">Custom Trip Disetujui</span>
                <h3 class="text-lg font-extrabold text-slate-900 mt-1">Custom Trip: {{ $customTrip->tujuan }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Tanggal: <strong>{{ $customTrip->jadwal_penawaran ? $customTrip->jadwal_penawaran->format('d/m/Y') : $customTrip->tanggal_diinginkan->format('d/m/Y') }}</strong> &bull; {{ $customTrip->jumlah_peserta }} Peserta
                </p>
            @endif
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 block font-medium">Tarif Satuan</span>
            <span class="text-xl font-extrabold text-sky-600">
                {{ $jadwal ? $jadwal->paket->harga_format : 'Rp ' . number_format($customTrip->biaya_penawaran, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <form action="{{ route('pemesanan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($jadwal)
            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
        @elseif($customTrip)
            <input type="hidden" name="custom_trip_id" value="{{ $customTrip->id }}">
        @endif

        <!-- Pemilihan Jumlah Peserta -->
        @if($jadwal)
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Berapa Banyak Peserta yang Ingin Didaftarkan?</h3>
                        <p class="text-xs text-slate-400">Pilih jumlah kursi (maksimal sisa kuota yang tersedia: {{ $jadwal->sisa_kuota }} kursi)</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="kurangiPeserta()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-lg flex items-center justify-center transition">
                            -
                        </button>
                        <span class="text-lg font-extrabold text-slate-900 min-w-[24px] text-center" x-text="pesertaList.length"></span>
                        <button type="button" @click="tambahPeserta()" class="w-9 h-9 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-lg flex items-center justify-center transition">
                            +
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulir Data Peserta & Unggah KTP (F9-05, US-05, BR-13) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-extrabold text-slate-900">Data dan Dokumen Identitas Peserta</h3>
                <span class="text-xs text-slate-500 font-medium">Diperlukan untuk asuransi dan manifes perjalanan</span>
            </div>

            <template x-for="(peserta, index) in pesertaList" :key="index">
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-bold" x-text="index + 1"></span>
                            <h4 class="text-sm font-extrabold text-slate-900" x-text="'Peserta ' + (index + 1) + (index === 0 ? ' (Pemesan Utama)' : '')"></h4>
                        </div>
                        <button type="button" @click="hapusPeserta(index)" x-show="pesertaList.length > 1 && !isCustom" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                            Hapus
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap (Sesuai KTP/Paspor)</label>
                            <input type="text" :name="'peserta[' + index + '][nama_lengkap]'" x-model="peserta.nama_lengkap" required placeholder="Nama lengkap" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Identitas</label>
                            <select :name="'peserta[' + index + '][jenis_identitas]'" x-model="peserta.jenis_identitas" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800">
                                <option value="ktp">KTP (NIK 16 Digit)</option>
                                <option value="paspor">Paspor (Wisatawan Mancanegara)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Identitas</label>
                            <input type="text" :name="'peserta[' + index + '][no_identitas]'" x-model="peserta.no_identitas" required placeholder="Contoh: 337201..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp / HP (Opsional)</label>
                            <input type="text" :name="'peserta[' + index + '][no_hp]'" x-model="peserta.no_hp" placeholder="08xxxxxxxx" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800">
                        </div>

                        <!-- Unggah Berkas KTP/Paspor (BR-13) -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Unggah Foto KTP / Paspor</label>
                            <input type="file" :name="'peserta[' + index + '][file_identitas]'" required accept="image/png,image/jpeg,image/jpg" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                            <span class="text-[11px] text-slate-400 mt-1 block">Format JPG/PNG, ukuran maksimum 5 MB. Dokumen disimpan di server privat terenkripsi.</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Ringkasan Biaya & Konfirmasi Pesanan (F9-06, W-08) -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-md space-y-4">
            <h3 class="text-base font-extrabold text-slate-900 pb-3 border-b border-slate-100">Ringkasan Rincian Biaya</h3>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-600">
                    <span>Jumlah Peserta:</span>
                    <strong class="text-slate-900" x-text="pesertaList.length + ' Orang'"></strong>
                </div>
                <template x-if="!isCustom">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Biaya per Peserta:</span>
                        <strong class="text-slate-900" x-text="formatRupiah(unitPrice)"></strong>
                    </div>
                </template>
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-sm">
                    <span class="font-extrabold text-slate-900">Total Biaya Pemesanan:</span>
                    <span class="text-2xl font-extrabold text-sky-600 font-mono" x-text="formatRupiah(totalBiaya)"></span>
                </div>
            </div>

            <div class="p-4 bg-sky-50 rounded-2xl text-xs text-sky-800 space-y-1">
                <strong>Catatan Penting:</strong>
                <p>Setelah mengonfirmasi pemesanan, kursi akan ditahan selama 24 jam. Anda dapat mengunggah bukti transfer bank untuk diverifikasi oleh admin travel agar tiket digital diterbitkan.</p>
            </div>

            <div class="pt-2 flex items-center justify-between gap-4">
                <a href="{{ route('paket.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-7 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-sky-500/25 transition">
                    Konfirmasi Pemesanan &rarr;
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function bookingFormManager(maxSeats, unitPrice, customPrice, isCustom) {
        return {
            maxSeats: maxSeats,
            unitPrice: unitPrice,
            customPrice: customPrice,
            isCustom: isCustom,
            pesertaList: isCustom 
                ? Array.from({ length: maxSeats }, () => ({ nama_lengkap: '', jenis_identitas: 'ktp', no_identitas: '', no_hp: '' }))
                : [{ nama_lengkap: '{{ auth()->user()->nama_lengkap ?? '' }}', jenis_identitas: 'ktp', no_identitas: '', no_hp: '{{ auth()->user()->no_hp ?? '' }}' }],
            
            get totalBiaya() {
                if (this.isCustom) {
                    return this.customPrice;
                }
                return this.pesertaList.length * this.unitPrice;
            },

            tambahPeserta() {
                if (this.pesertaList.length < this.maxSeats) {
                    this.pesertaList.push({ nama_lengkap: '', jenis_identitas: 'ktp', no_identitas: '', no_hp: '' });
                } else {
                    alert('Jumlah peserta telah mencapai batas sisa kuota kursi yang tersedia (' + this.maxSeats + ' kursi).');
                }
            },

            kurangiPeserta() {
                if (this.pesertaList.length > 1) {
                    this.pesertaList.pop();
                }
            },

            hapusPeserta(index) {
                if (this.pesertaList.length > 1) {
                    this.pesertaList.splice(index, 1);
                }
            },

            formatRupiah(amount) {
                return 'Rp ' + Number(amount).toLocaleString('id-ID');
            }
        }
    }
</script>
@endsection
