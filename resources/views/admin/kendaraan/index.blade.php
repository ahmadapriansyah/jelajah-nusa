@extends('layouts.admin', ['title' => 'Kelola Armada Kendaraan'])

@section('content')
<div class="py-6 space-y-6" x-data="{ editModalOpen: false, editData: { id: null, jenis_kendaraan: '', kapasitas: '', plat_nomor: '', status: 'tersedia' } }">
    
    <!-- Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Armada Kendaraan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar armada bus, elf, mobil, dan speedboat penunjang operasional tour</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Form Tambah Kendaraan (4 Cols, Frame 107:2972) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Tambah Armada Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Input data armada kendaraan operasional</p>
            </div>

            <form action="{{ route('admin.kendaraan.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Kendaraan</label>
                    <input type="text" name="jenis_kendaraan" required placeholder="Contoh: Toyota Hiace Premio" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kapasitas Kursi</label>
                    <input type="number" name="kapasitas" required min="1" max="100" placeholder="14" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                    <p class="text-[11px] text-slate-400 mt-1">Kapasitas dipakai untuk menghitung kelayakan keberangkatan.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Plat Polisi (Opsional)</label>
                    <input type="text" name="plat_nomor" placeholder="AD 0000 XX" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 uppercase">
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika bukan kendaraan darat (speedboat/kapal).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Ketersediaan</label>
                    <select name="status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                        <option value="tersedia">Tersedia (Ready Jalan)</option>
                        <option value="tidak_tersedia">Tidak Tersedia / Perbaikan</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition">
                        Simpan Kendaraan
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Tabel Armada Kendaraan (8 Cols, Frame 107:2972) -->
        <div class="lg:col-span-8 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Daftar Armada Kendaraan</h3>
                    <p class="text-xs text-slate-400">Total armada: {{ $kendaraanList->count() }} unit terdaftar</p>
                </div>
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Jenis Kendaraan</th>
                            <th class="py-3 px-3">Kapasitas</th>
                            <th class="py-3 px-3">Plat Nomor</th>
                            <th class="py-3 px-3">Dipakai Jadwal</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($kendaraanList as $k)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $k->jenis_kendaraan }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-0.5 rounded-lg bg-sky-50 text-sky-800 font-bold">{{ $k->kapasitas }} kursi</span>
                                </td>
                                <td class="py-3.5 px-3 font-mono text-slate-700">
                                    {{ $k->plat_nomor ?: '—' }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-600">
                                    {{ $k->jadwal_count }} kali
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $k->status === 'tersedia' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $k->status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right space-x-2">
                                    <button type="button" 
                                            @click="editModalOpen = true; editData = { id: {{ $k->id }}, jenis_kendaraan: '{{ addslashes($k->jenis_kendaraan) }}', kapasitas: {{ $k->kapasitas }}, plat_nomor: '{{ addslashes($k->plat_nomor ?? '') }}', status: '{{ $k->status }}' }" 
                                            class="text-sky-600 hover:text-sky-800 font-bold">
                                        Ubah
                                    </button>
                                    <span class="text-slate-200">|</span>
                                    <form action="{{ route('admin.kendaraan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kendaraan ini? Tindakan ini tidak dapat dibatalkan.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada armada kendaraan terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Ubah Kendaraan -->
    <div x-show="editModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50" style="display: none;">
        <div @click.away="editModalOpen = false" class="bg-white max-w-md w-full p-6 rounded-3xl shadow-xl space-y-4">
            <h3 class="text-base font-extrabold text-slate-900">Ubah Data Kendaraan</h3>
            <form :action="'{{ url('admin/kendaraan') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kendaraan</label>
                    <input type="text" name="jenis_kendaraan" x-model="editData.jenis_kendaraan" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kapasitas Kursi</label>
                    <input type="number" name="kapasitas" x-model="editData.kapasitas" required min="1" max="100" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Plat Nomor</label>
                    <input type="text" name="plat_nomor" x-model="editData.plat_nomor" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm uppercase">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" x-model="editData.status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        <option value="tersedia">Tersedia</option>
                        <option value="tidak_tersedia">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-sky-600 text-white text-xs font-bold rounded-xl">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
