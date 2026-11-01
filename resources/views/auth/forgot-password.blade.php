@extends('layouts.wisatawan', ['title' => 'Lupa Password — Jelajah Nusa'])

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Atur Ulang Password</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Masukkan email akun Anda untuk menerima tautan pemulihan kata sandi.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Akun</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition text-slate-800">
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                Kirim Tautan Atur Ulang
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            Ingat password Anda? <a href="{{ route('login') }}" class="font-bold text-sky-600 hover:text-sky-700">Kembali ke Login</a>
        </div>
    </div>
</div>
@endsection
