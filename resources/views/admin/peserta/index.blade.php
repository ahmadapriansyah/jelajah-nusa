@extends('layouts.admin', ['title' => 'Data Peserta Wisata'])

@section('content')
<div class="py-6 space-y-6" x-data="{ editModal: false, editData: { id: null, nama_lengkap: '', jenis_identitas: 'ktp', no_identitas: '', no_hp: '' } }">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Seluruh Peserta Wisata</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar manifest penumpang, verifikasi dokumen KTP/Paspor, dan nomor kontak peserta</p>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <form action="{{ route('admin.peserta.index') }}" method="GET" class="relative max-w-md w-full">
                <input type="text" 
                       name="cari" 
                       value="{{ $search }}" 
                       placeholder="Cari nama peserta, NIK/Paspor, nomor HP..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-700">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <span class="text-xs font-bold text-slate-500">Total: {{ $pesertaList->total() }} Peserta</span>
        </div>

        <div class="overflow-x-auto border border-slate-100 rounded-2xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Nama Peserta</th>
                        <th class="py-3 px-3">Identitas</th>
                        <th class="py-3 px-3">No. Kontak</th>
                        <th class="py-3 px-3">Kode Pemesanan</th>
                        <th class="py-3 px-3">Paket / Trip</th>
                        <th class="py-3 px-3">Tiket</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pesertaList as $pst)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $pst->nama_lengkap }}
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">
                                    {{ strtoupper($pst->jenis_identitas) }}: {{ $pst->no_identitas }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-slate-600">
                                {{ $pst->no_hp ?: '—' }}
                            </td>
                            <td class="py-3.5 px-3 font-mono font-bold text-sky-600">
                                {{ $pst->pemesanan ? $pst->pemesanan->display_kode : '—' }}
                            </td>
                            <td class="py-3.5 px-3 max-w-[180px] truncate text-slate-700">
                                {{ $pst->pemesanan ? $pst->pemesanan->judul_trip : '—' }}
                            </td>
                            <td class="py-3.5 px-3">
                                @if($pst->tiket)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        {{ $pst->tiket->kode_tiket }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">— Belum terbit —</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-right space-x-2">
                                <a href="{{ route('dokumen.identitas', $pst->id) }}" target="_blank" class="text-sky-600 hover:text-sky-800 font-bold">
                                    Lihat KTP
                                </a>
                                <span class="text-slate-200">|</span>
                                <button type="button" 
                                        @click="editModal = true; editData = { id: {{ $pst->id }}, nama_lengkap: '{{ addslashes($pst->nama_lengkap) }}', jenis_identitas: '{{ $pst->jenis_identitas }}', no_identitas: '{{ addslashes($pst->no_identitas) }}', no_hp: '{{ addslashes($pst->no_hp ?? '') }}' }" 
                                        class="text-slate-600 hover:text-slate-900 font-bold">
                                    Ubah
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada data peserta yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $pesertaList->links() }}
        </div>
    </div>

    <!-- Modal Ubah Data Peserta -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50" style="display: none;">
        <div @click.away="editModal = false" class="bg-white max-w-md w-full p-6 rounded-3xl shadow-xl space-y-4">
            <h3 class="text-base font-extrabold text-slate-900">Ubah Data Peserta</h3>
            <form :action="'{{ url('admin/peserta') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" x-model="editData.nama_lengkap" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Identitas</label>
                        <select name="jenis_identitas" x-model="editData.jenis_identitas" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                            <option value="ktp">KTP</option>
                            <option value="paspor">Paspor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Identitas</label>
                        <input type="text" name="no_identitas" x-model="editData.no_identitas" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" x-model="editData.no_hp" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-sky-600 text-white text-xs font-bold rounded-xl">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
