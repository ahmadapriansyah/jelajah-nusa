@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.wisatawan', ['title' => 'Profil Saya — Jelajah Nusa'])

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Profil Pengguna</h1>
        <p class="text-sm text-slate-500">Kelola informasi data pribadi dan keamanan kata sandi akun Anda</p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
        <form method="POST" action="{{ route('profil.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                    {{ substr($user->nama_lengkap ?? 'U', 0, 1) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $user->nama_lengkap }}</h3>
                    <p class="text-xs text-slate-500 font-mono">@<span>{{ $user->username }}</span> &bull; <span class="uppercase font-semibold text-sky-600">{{ $user->role }}</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username</label>
                    <input type="text" value="{{ $user->username }}" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500 cursor-not-allowed">
                    <span class="text-[11px] text-slate-400 mt-1 block">Username tidak dapat diubah</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <h4 class="text-sm font-bold text-slate-900 mb-1">Ganti Password (Opsional)</h4>
                <p class="text-xs text-slate-500 mb-4">Kosongkan jika Anda tidak ingin mengubah password akun Anda saat ini.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password Saat Ini</label>
                        <input type="password" name="current_password" placeholder="••••••••" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password Baru</label>
                        <input type="password" name="new_password" placeholder="Min. 8 karakter" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ulangi Password Baru</label>
                        <input type="password" name="new_password_confirmation" placeholder="Ulangi password baru" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
