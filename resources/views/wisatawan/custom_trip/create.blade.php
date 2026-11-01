@extends('layouts.wisatawan', ['title' => 'Ajukan Custom Trip — Jelajah Nusa'])

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <div>
        <a href="{{ route('custom-trip.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Pengajuan
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Formulir Pengajuan Custom Trip</h1>
        <p class="text-xs sm:text-sm text-slate-500">Rencanakan liburan impian Anda sendiri. Tim travel kami akan menyusun rincian fasilitas dan penawaran biaya terbaik.</p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('custom-trip.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Destinasi Tujuan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Destinasi / Rute Perjalanan yang Diinginkan</label>
                <input type="text" 
                       name="tujuan" 
                       value="{{ old('tujuan') }}" 
                       required 
                       placeholder="Contoh: Dieng Plateau Family Gathering atau Derawan Island Hopping" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
            </div>

            <!-- Tanggal, Durasi, Jumlah Peserta Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Berangkat</label>
                    <input type="date" 
                           name="tanggal_diinginkan" 
                           value="{{ old('tanggal_diinginkan') }}" 
                           required 
                           min="{{ now()->addDay()->toDateString() }}" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Durasi (Hari)</label>
                    <input type="number" 
                           name="durasi_hari" 
                           value="{{ old('durasi_hari', 3) }}" 
                           required 
                           min="1" 
                           max="30" 
                           placeholder="3" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Estimasi Peserta</label>
                    <input type="number" 
                           name="jumlah_peserta" 
                           value="{{ old('jumlah_peserta', 10) }}" 
                           required 
                           min="1" 
                           max="500" 
                           placeholder="10" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>
            </div>

            <!-- Kebutuhan Fasilitas & Catatan Khusus -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rincian Permintaan &amp; Fasilitas Khusus</label>
                <textarea name="kebutuhan" 
                          rows="5" 
                          required 
                          placeholder="Jelaskan kebutuhan rombongan Anda, contoh:&#10;• Pilihan armada (Hiace / Bus Pariwisata AC)&#10;• Akomodasi penginapan (Hotel Bintang 4 / Villa Privat)&#10;• Makanan/catering halal 3x sehari&#10;• Kunjungan spot wisata tertentu atau kegiatan outbound" 
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">{{ old('kebutuhan') }}</textarea>
            </div>

            <div class="pt-3 flex items-center justify-between gap-4">
                <a href="{{ route('custom-trip.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                    Kirim Permohonan Custom Trip &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
