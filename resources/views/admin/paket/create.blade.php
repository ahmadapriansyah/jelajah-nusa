@extends('layouts.admin', ['title' => 'Tambah Paket Wisata'])

@section('content')
<div class="py-6 max-w-4xl mx-auto space-y-6" x-data="itineraryManager()">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.paket.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1 mb-1">
                &larr; Kembali ke Daftar Paket
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tambah Paket Wisata Baru</h1>
            <p class="text-xs text-slate-500">Lengkapi formulir 5 bagian berikut untuk menerbitkan paket wisata baru</p>
        </div>
    </div>

    <form action="{{ route('admin.paket.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Bagian 1: Informasi Dasar -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-extrabold">1</span>
                <h3 class="text-base font-extrabold text-slate-900">Informasi Dasar</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Paket Wisata</label>
                    <input type="text" name="nama_paket" value="{{ old('nama_paket') }}" required placeholder="Contoh: Solo – Bali Eksotis 4H3M" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Destinasi Utama</label>
                    <input type="text" name="destinasi" value="{{ old('destinasi') }}" required placeholder="Contoh: Bali & Nusa Penida" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Layanan</label>
                    <select name="jenis_layanan" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                        <option value="open_trip" {{ old('jenis_layanan') === 'open_trip' ? 'selected' : '' }}>Open Trip (Gabungan / Publik)</option>
                        <option value="paket_reguler" {{ old('jenis_layanan') === 'paket_reguler' ? 'selected' : '' }}>Paket Reguler (Private Group)</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat / Ringkasan Tur</label>
                    <textarea name="deskripsi" rows="3" placeholder="Tuliskan daya tarik utama dan keunggulan tur ini..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">{{ old('deskripsi') }}</textarea>
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
                    <input type="number" name="harga" value="{{ old('harga') }}" required min="1" placeholder="Contoh: 3500000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Durasi Perjalanan (Hari)</label>
                    <input type="number" name="durasi_hari" value="{{ old('durasi_hari', 3) }}" required min="1" max="30" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
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
                            <input type="text" :name="'itinerary[' + index + '][judul]'" x-model="hari.judul" required placeholder="Judul ringkas (Contoh: Tiba di Bali & Explore Pesisir Selatan)" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800">
                        </div>
                        <div>
                            <textarea :name="'itinerary[' + index + '][aktivitas]'" x-model="hari.aktivitas" required rows="2" placeholder="Rincian jam, aktivitas objek wisata, dan makan..." class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800"></textarea>
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
                    <textarea name="fasilitas" rows="4" required placeholder="• Transportasi AC&#10;• Hotel Bintang 3&#10;• Tiket Masuk Objek Wisata&#10;• Makan sesuai jadwal" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">{{ old('fasilitas') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fasilitas Tidak Termasuk (Opsional)</label>
                    <textarea name="fasilitas_tidak_termasuk" rows="4" placeholder="• Pengeluaran pribadi & laundry&#10;• Tipping driver & guide&#10;• Watersport pribadi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">{{ old('fasilitas_tidak_termasuk') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lokasi Meeting Point</label>
                    <input type="text" name="meeting_point" value="{{ old('meeting_point') }}" required placeholder="Contoh: Halaman Stasiun Solo Balapan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jam Kumpul (WIB)</label>
                    <input type="time" name="jam_kumpul" value="{{ old('jam_kumpul', '16:00') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>
            </div>
        </div>

        <!-- Bagian 5: Foto Paket Wisata -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-extrabold">5</span>
                <h3 class="text-base font-extrabold text-slate-900">Foto Utama Paket Wisata</h3>
            </div>

            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-sky-500 transition bg-slate-50/50">
                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-3 text-xs text-slate-600">
                    <label for="foto" class="relative cursor-pointer font-bold text-sky-600 hover:text-sky-500 focus-within:outline-none">
                        <span>Pilih berkas foto</span>
                        <input id="foto" name="foto" type="file" accept="image/png,image/jpeg,image/jpg" class="sr-only">
                    </label>
                    <p class="text-slate-400 mt-1">Format JPG atau PNG (Maksimum 5 MB)</p>
                </div>
            </div>
        </div>

        <!-- Submit Controls -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.paket.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                Simpan Paket Wisata
            </button>
        </div>
    </form>
</div>

<script>
    function itineraryManager() {
        return {
            hariList: [
                { judul: 'Keberangkatan & Perjalanan', aktivitas: 'Kumpul di meeting point, briefing perjalanan malam menuju destinasi.' }
            ],
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
