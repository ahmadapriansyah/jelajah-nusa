@extends('layouts.admin', ['title' => 'Pengaturan & Manajemen User'])

@section('content')
<div class="py-6 space-y-6" x-data="{ tab: 'rekening', adminModal: false }">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan Sistem &amp; Pengguna</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Konfigurasi rekening bank pembayaran, batas waktu transfer, dan otorisasi pengguna</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="tab = 'rekening'" :class="tab === 'rekening' ? 'bg-sky-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50'" class="px-3.5 py-2 rounded-xl text-xs font-bold transition border border-slate-200">
                Rekening &amp; Batas Waktu
            </button>
            <button @click="tab = 'pengguna'" :class="tab === 'pengguna' ? 'bg-sky-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50'" class="px-3.5 py-2 rounded-xl text-xs font-bold transition border border-slate-200">
                Manajemen Pengguna
            </button>
            <button @click="tab = 'log'" :class="tab === 'log' ? 'bg-sky-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50'" class="px-3.5 py-2 rounded-xl text-xs font-bold transition border border-slate-200">
                Log Aktivitas
            </button>
        </div>
    </div>

    <!-- Tab 1: Pengaturan Rekening & Batas Waktu (F12-01, F12-02) -->
    <div x-show="tab === 'rekening'" class="space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs max-w-2xl">
            <h3 class="text-base font-extrabold text-slate-900 mb-1">Rekening Tujuan Transfer Pembayaran</h3>
            <p class="text-xs text-slate-400 mb-6">Informasi rekening yang akan tampil pada halaman instruksi transfer bagi wisatawan</p>

            <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Bank</label>
                    <input type="text" name="nama_bank" value="{{ old('nama_bank', $pengaturan->nama_bank) }}" required placeholder="Contoh: Bank Mandiri" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                    <input type="text" name="no_rekening" value="{{ old('no_rekening', $pengaturan->no_rekening) }}" required placeholder="Contoh: 138-00-8899221-1" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Atas Nama Rekening</label>
                    <input type="text" name="atas_nama" value="{{ old('atas_nama', $pengaturan->atas_nama) }}" required placeholder="Contoh: PT Jelajah Nusa Tour & Travel" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Batas Waktu Pembayaran (Jam)</label>
                    <input type="number" name="batas_bayar_jam" value="{{ old('batas_bayar_jam', $pengaturan->batas_bayar_jam) }}" required min="1" max="168" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                    <p class="text-[11px] text-slate-400 mt-1">Bawaan: 24 jam. Pesanan berstatus menunggu yang melewati batas waktu ini akan otomatis dibatalkan sistem dan kuota dikembalikan.</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab 2: Manajemen Pengguna (F11-03) -->
    <div x-show="tab === 'pengguna'" style="display: none;" class="space-y-6">
        
        <!-- Admin Section -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Akun Administrator</h3>
                    <p class="text-xs text-slate-400">Staf travel yang memiliki hak akses operasional admin</p>
                </div>
                <button type="button" @click="adminModal = true" class="px-3.5 py-1.5 bg-sky-600 text-white hover:bg-sky-700 text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Admin Baru
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($adminUsers as $adm)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $adm->nama_lengkap }}</h4>
                            <p class="text-[11px] text-slate-400 font-mono">@<span>{{ $adm->username }}</span> &bull; {{ $adm->email }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800">Admin</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Wisatawan Section -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Daftar Akun Wisatawan</h3>
                <p class="text-xs text-slate-400">Seluruh pengguna yang mendaftar mandiri via aplikasi web</p>
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-3">Username</th>
                            <th class="py-3 px-3">Email &amp; Kontak</th>
                            <th class="py-3 px-3">Terdaftar</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($wisatawanUsers as $wst)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $wst->nama_lengkap }}</td>
                                <td class="py-3.5 px-3 font-mono text-slate-600">@<span>{{ $wst->username }}</span></td>
                                <td class="py-3.5 px-3 text-slate-600">{{ $wst->email }} ({{ $wst->no_hp }})</td>
                                <td class="py-3.5 px-3 text-slate-400">{{ $wst->created_at->format('d/m/Y') }}</td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $wst->status_aktif ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $wst->status_aktif ? 'Aktif' : 'Dinonaktifkan' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <form action="{{ route('admin.pengaturan.user.toggle', $wst->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-bold {{ $wst->status_aktif ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                            {{ $wst->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $wisatawanUsers->links() }}
            </div>
        </div>
    </div>

    <!-- Tab 3: Log Aktivitas Sistem (F12-04, Auditabilitas) -->
    <div x-show="tab === 'log'" style="display: none;" class="space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Audit Log Aktivitas Terkini</h3>
                <p class="text-xs text-slate-400">Catatan riwayat perubahan status pemesanan, verifikasi pembayaran, dan akun</p>
            </div>

            <div class="space-y-3">
                @forelse($logAktivitas as $log)
                    <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl flex items-start justify-between gap-4 text-xs">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 font-mono font-bold text-[10px] uppercase">
                                    {{ $log->aksi }}
                                </span>
                                <span class="font-bold text-slate-900">{{ $log->user ? $log->user->nama_lengkap : 'Sistem Otomatis' }}</span>
                            </div>
                            <p class="text-slate-600">{{ $log->keterangan }}</p>
                        </div>
                        <div class="text-right text-[11px] text-slate-400 flex-shrink-0">
                            <span>{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                            <span class="block text-[10px] font-mono text-slate-300">IP: {{ $log->ip_address }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat aktivitas tercatat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Tambah Admin Baru -->
    <div x-show="adminModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50" style="display: none;">
        <div @click.away="adminModal = false" class="bg-white max-w-md w-full p-6 sm:p-8 rounded-3xl shadow-xl space-y-4">
            <h3 class="text-base font-extrabold text-slate-900">Buat Akun Admin Baru</h3>
            <form action="{{ route('admin.pengaturan.admin.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor HP</label>
                    <input type="text" name="no_hp" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ulangi Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="adminModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-sky-600 text-white text-xs font-bold rounded-xl">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
