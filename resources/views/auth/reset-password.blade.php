@extends('layouts.wisatawan', ['title' => 'Password Baru — Jelajah Nusa'])

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Atur Kata Sandi Baru</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Buat kata sandi baru yang kuat dan aman untuk akun Anda.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password Baru</label>
                <input type="password" id="password" name="password" required placeholder="Min. 8 karakter" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ulangi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800">
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl shadow-md transition mt-4">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>
@endsection
