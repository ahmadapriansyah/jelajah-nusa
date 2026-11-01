@extends('layouts.admin', ['title' => 'Laporan Operasional & Pendapatan'])

@section('content')
<div class="py-6 space-y-6">
    
    <!-- Title & Export Actions (Frame 107:2817) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporan Operasional &amp; Pendapatan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Rekapitulasi status perjalanan, perolehan peserta, pendapatan riil, dan ekspor dokumen</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.laporan.ekspor-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor Excel (CSV)
            </a>
            <a href="{{ route('admin.laporan.ekspor-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Ekspor PDF Resmi
            </a>
        </div>
    </div>

    <!-- Filter Periode Bar (Frame 107:2817) -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                </div>
                <span class="text-slate-400 mt-5">&mdash;</span>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                </div>
                <button type="submit" class="mt-5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                    Terapkan Filter
                </button>
            </div>

            <!-- Pintasan Filter Cepat -->
            <div class="flex items-center gap-2 self-end sm:self-auto text-xs font-bold">
                <span class="text-slate-400 font-normal">Pintasan:</span>
                <a href="{{ route('admin.laporan.index', ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->endOfMonth()->toDateString()]) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                    Bulan Ini
                </a>
                <a href="{{ route('admin.laporan.index', ['start_date' => now()->subMonth()->startOfMonth()->toDateString(), 'end_date' => now()->subMonth()->endOfMonth()->toDateString()]) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700">
                    Bulan Lalu
                </a>
            </div>
        </form>
    </div>

    <!-- 4 Key Metric Cards Laporan (Frame 107:2817) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Jadwal Dijalankan</span>
            <span class="text-2xl font-extrabold text-emerald-600">{{ $laporan['dijalankan'] }}</span>
            <p class="text-[11px] text-slate-400 mt-1">Keberangkatan sukses</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Jadwal Ditunda</span>
            <span class="text-2xl font-extrabold text-amber-600">{{ $laporan['ditunda'] }}</span>
            <p class="text-[11px] text-slate-400 mt-1">Dipindah jadwal / reschedule</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Jadwal Dibatalkan</span>
            <span class="text-2xl font-extrabold text-rose-600">{{ $laporan['dibatalkan'] }}</span>
            <p class="text-[11px] text-slate-400 mt-1">Trip dibatalkan &amp; direfund</p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Wisatawan</span>
            <span class="text-2xl font-extrabold text-sky-600">{{ $laporan['total_peserta'] }}</span>
            <p class="text-[11px] text-slate-400 mt-1">Peserta terdaftar aktif</p>
        </div>
    </div>

    <!-- Tabel Paket Terlaris & Pendapatan (Frame 107:2817) -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Performa Paket &amp; Total Pendapatan</h3>
                <p class="text-xs text-slate-400">Total pendapatan periode ini: <strong class="text-emerald-600 text-sm font-extrabold">{{ $laporan['total_pendapatan_format'] }}</strong></p>
            </div>
        </div>

        <div class="overflow-x-auto border border-slate-100 rounded-2xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Peringkat</th>
                        <th class="py-3 px-4">Paket Wisata</th>
                        <th class="py-3 px-3">Destinasi</th>
                        <th class="py-3 px-3">Layanan</th>
                        <th class="py-3 px-3 text-right">Peserta</th>
                        <th class="py-3 px-4 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($laporan['paket_terlaris'] as $row)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4">
                                <span class="w-6 h-6 rounded-full {{ $row['peringkat'] === 1 ? 'bg-amber-400 text-amber-950 font-extrabold' : 'bg-slate-100 text-slate-700 font-bold' }} flex items-center justify-center text-xs">
                                    {{ $row['peringkat'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $row['nama_paket'] }}
                            </td>
                            <td class="py-3.5 px-3 text-slate-600">
                                {{ $row['destinasi'] }}
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $row['jenis_layanan'] === 'Open Trip' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ $row['jenis_layanan'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right font-extrabold text-slate-800">
                                {{ number_format($row['peserta'], 0, ',', '.') }} orang
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-emerald-600">
                                {{ $row['pendapatan_format'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Tidak ada data aktivitas perjalanan pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
