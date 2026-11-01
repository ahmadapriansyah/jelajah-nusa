@extends('layouts.admin', ['title' => 'Ubah Paket Wisata'])

@section('content')
<div class="py-6 max-w-4xl mx-auto space-y-6" x-data="itineraryEditManager(@js($paket->itinerary ?? []))">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.paket.index', ['selected' => $paket->id]) }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1 mb-1">
                &larr; Kembali ke Detail Paket
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ubah Paket Wisata</h1>
            <p class="text-xs text-slate-500">Perbarui data paket {{ $paket->nama_paket }}</p>
        </div>
    </div>

    <form action="{{ route('admin.paket.update', $paket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Informasi Dasar -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-extrabold">1</span>
                <h3 class="text-base font-extrabold text-slate-900">Informasi Dasar</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Paket Wisata</label>
                    <input type="text" name="nama_paket" value="{{ old('nama_paket', $paket->nama_paket) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Destinasi Utama</label>
                    <input type="text" name="destinasi" value="{{ old('destinasi', $paket->destinasi) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Layanan</label>
                    <select name="jenis_layanan" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                        <option value="open_trip" {{ old('jenis_layanan', $paket->jenis_layanan) === 'open_trip' ? 'selected' : '' }}>Open Trip (Gabungan / Publik)</option>
                        <option value="paket_reguler" {{ old('jenis_layanan', $paket->jenis_layanan) === 'paket_reguler' ? 'selected' : '' }}>Paket Reguler (Private Group)</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat / Ringkasan Tur</label>
                    <textarea name="deskripsi" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Harga dan Durasi -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-extrabold">2</span>
                <h3 class="text-base font-extrabold text-slate-900">Harga dan Durasi</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Harga per Peserta (Rp)</label>
                    <input type="number" name="harga" value="{{ old('harga', (int)$paket->harga) }}" required min="1" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Durasi Perjalanan (Hari)</label>
                    <input type="number" name="durasi_hari" value="{{ old('durasi_hari', $paket->durasi_hari) }}" required min="1" max="30" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>
            </div>
        </div>

        <!-- Bagian 3: Itinerary per Hari (Dinamis) -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-extrabold">3</span>
                    <h3 class="text-base font-extrabold text-slate-900">Itinerary per Hari</h3>
                </div>
                <button type="button" @click="tambahHari()" class="px-3 py-1.5 bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs font-bold rounded-xl transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Hari
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(hari, index) in hariList" :key="index">
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl relative space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-lg bg-sky-600 text-white font-bold text-xs" x-text="'Hari ke-' + (index + 1)"></span>
                            <button type="button" @click="hapusHari(index)" x-show="hariList.length > 1" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                                Hapus Hari Ini
                            </button>
                        </div>
                        <div>
                            <input type="text" :name="'itinerary[' + index + '][judul]'" x-model="hari.judul" required placeholder="Judul ringkas hari ini" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800">
                        </div>
                        <div>
                            <textarea :name="'itinerary[' + index + '][aktivitas]'" x-model="hari.aktivitas" required rows="2" placeholder="Rincian aktivitas..." class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800"></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Bagian 4: Fasilitas, Meeting Point & Jam Kumpul -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-extrabold">4</span>
                <h3 class="text-base font-extrabold text-slate-900">Fasilitas &amp; Meeting Point</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fasilitas Termasuk</label>
                    <textarea name="fasilitas" rows="4" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">{{ old('fasilitas', $paket->fasilitas) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fasilitas Tidak Termasuk</label>
                    <textarea name="fasilitas_tidak_termasuk" rows="4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">{{ old('fasilitas_tidak_termasuk', $paket->fasilitas_tidak_termasuk) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lokasi Meeting Point</label>
                    <input type="text" name="meeting_point" value="{{ old('meeting_point', $paket->meeting_point) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jam Kumpul</label>
                    <input type="time" name="jam_kumpul" value="{{ old('jam_kumpul', substr($paket->jam_kumpul, 0, 5)) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>
            </div>
        </div>

        <!-- Bagian 5: Foto Paket Wisata -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-extrabold">5</span>
                <h3 class="text-base font-extrabold text-slate-900">Foto Paket</h3>
            </div>

            @if($paket->foto)
                <div class="mb-3">
                    <p class="text-xs text-slate-500 mb-2">Foto saat ini:</p>
                    <img src="{{ asset('storage/' . $paket->foto) }}" alt="{{ $paket->nama_paket }}" class="w-48 h-28 object-cover rounded-xl border border-slate-200">
                </div>
            @endif

            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-sky-500 transition bg-slate-50/50">
                <div class="text-xs text-slate-600">
                    <label for="foto" class="relative cursor-pointer font-bold text-sky-600 hover:text-sky-500">
                        <span>Unggah foto baru (opsional)</span>
                        <input id="foto" name="foto" type="file" accept="image/png,image/jpeg,image/jpg" class="sr-only">
                    </label>
                    <p class="text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.paket.index', ['selected' => $paket->id]) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    function itineraryEditManager(initial) {
        return {
            hariList: initial && initial.length > 0 ? initial : [{ judul: '', aktivitas: '' }],
            tambahHari() {
                this.hariList.push({ judul: '', aktivitas: '' });
            },
            hapusHari(index) {
                if (this.hariList.length > 1) {
                    this.hariList.splice(index, 1);
                }
            }
        }
    }
</script>
@endsection
