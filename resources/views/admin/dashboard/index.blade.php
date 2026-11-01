@extends('layouts.admin', ['title' => 'Dashboard Operasional'])

@section('content')
<div class="py-6 space-y-6">
    
    <!-- Page Title & Subtitle -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Operasional</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Ringkasan operasional — {{ now()->translatedFormat('F Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem Operasional Normal
            </span>
        </div>
    </div>

    <!-- 4 Key Metric Cards (Frame 50:2) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Pemesanan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pemesanan</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($total_pemesanan, 0, ',', '.') }}</span>
                @if($persen_pemesanan !== null)
                    <span class="text-xs font-bold {{ $persen_pemesanan >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $persen_pemesanan >= 0 ? '+' : '' }}{{ $persen_pemesanan }}% bln lalu
                    </span>
                @else
                    <span class="text-xs font-bold text-slate-400">—</span>
                @endif
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Pemesanan aktif bulan ini</p>
        </div>

        <!-- Card 2: Pendapatan Bulan Ini -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pendapatan Bulan Ini</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-900">{{ $pendapatan_bulan_ini_format }}</span>
                @if($persen_pendapatan !== null)
                    <span class="text-xs font-bold {{ $persen_pendapatan >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $persen_pendapatan >= 0 ? '+' : '' }}{{ $persen_pendapatan }}% bln lalu
                    </span>
                @else
                    <span class="text-xs font-bold text-slate-400">—</span>
                @endif
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Pembayaran terverifikasi</p>
        </div>

        <!-- Card 3: Menunggu Verifikasi -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Menunggu Verifikasi</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-amber-600">{{ $jumlah_menunggu }}</span>
                <span class="text-xs font-medium text-slate-500">
                    {{ $jumlah_menunggu > 0 ? "Tertua {$umur_tertua_hari} hr lalu" : 'Semua beres' }}
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Perlu tindakan verifikasi admin</p>
        </div>

        <!-- Card 4: Keberangkatan Mendatang -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Keberangkatan Mendatang</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-900">{{ $keberangkatan_mendatang }}</span>
                @if($keberangkatan_belum_min > 0)
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                        {{ $keberangkatan_belum_min }} belum min
                    </span>
                @else
                    <span class="text-xs font-semibold text-emerald-600">Siap jalan</span>
                @endif
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Jadwal dalam 30 hari ke depan</p>
        </div>
    </div>

    <!-- Middle Section: Chart & Best Selling Packages -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: 6-Month Revenue Bar Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tren Pendapatan 6 Bulan Terakhir</h2>
                    <p class="text-xs text-slate-400">Total nominal pembayaran diverifikasi (dalam jutaan rupiah)</p>
                </div>
                <a href="{{ route('admin.laporan.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                    Lihat Laporan Detail &rarr;
                </a>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Right: Paket Terlaris (Top 4) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-extrabold text-slate-900">Paket Terlaris</h2>
                    <span class="text-[11px] font-semibold text-slate-400">Peringkat Peserta</span>
                </div>

                @if(count($paket_terlaris) > 0)
                    <div class="space-y-3.5">
                        @foreach($paket_terlaris as $item)
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:border-sky-200 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-6 h-6 rounded-full {{ $loop->first ? 'bg-amber-400 text-amber-950 font-extrabold' : 'bg-slate-200 text-slate-700 font-bold' }} flex items-center justify-center text-xs">
                                        {{ $item['peringkat'] }}
                                    </span>
                                    <div class="truncate">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $item['nama_paket'] }}</h4>
                                        <p class="text-[11px] text-slate-400">{{ $item['destinasi'] }} &bull; {{ $item['jenis_layanan'] }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 pl-2">
                                    <span class="block text-xs font-extrabold text-slate-800">{{ $item['peserta'] }} orang</span>
                                    <span class="block text-[10px] text-emerald-600 font-semibold">{{ $item['pendapatan_format'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-slate-400 text-xs">
                        Belum ada data paket terlaris pada periode ini.
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('admin.paket.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700">
                    Kelola Seluruh Paket Wisata &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Panel Perlu Tindakan (Action Needed) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Panel Perlu Tindakan</h2>
                <p class="text-xs text-slate-400">Pemberitahuan operasional mendesak yang memerlukan keputusan Anda</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Item 1: Bukti Transfer Menunggu -->
            <a href="{{ route('admin.pembayaran.index') }}" class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 hover:bg-amber-50 transition block group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-amber-800">Bukti Transfer Masuk</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900">{{ $jumlah_menunggu }} antrean</span>
                </div>
                <p class="text-xs text-amber-700 leading-relaxed">
                    {{ $jumlah_menunggu > 0 ? "Terdapat {$jumlah_menunggu} bukti pembayaran menunggu verifikasi. Umur tertua {$umur_tertua_hari} hari yang lalu." : 'Tidak ada antrean pembayaran menunggu saat ini.' }}
                </p>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-900 mt-3 group-hover:translate-x-1 transition-transform">
                    Buka Antrean &rarr;
                </span>
            </a>

            <!-- Item 2: Pengajuan Custom Trip -->
            <a href="{{ route('admin.custom-trip.index') }}" class="p-4 rounded-2xl bg-sky-50/60 border border-sky-200/80 hover:bg-sky-50 transition block group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-sky-800">Pengajuan Custom Trip</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-200 text-sky-900">{{ $custom_trip_baru->count() }} baru</span>
                </div>
                <p class="text-xs text-sky-700 leading-relaxed">
                    @if($custom_trip_baru->count() > 0)
                        Pengajuan terbaru: {{ $custom_trip_baru->first()->tujuan }} ({{ $custom_trip_baru->first()->jumlah_peserta }} peserta). Segera susun penawaran.
                    @else
                        Tidak ada pengajuan custom trip yang belum ditanggapi.
                    @endif
                </p>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-900 mt-3 group-hover:translate-x-1 transition-transform">
                    Tinjau Custom Trip &rarr;
                </span>
            </a>

            <!-- Item 3: Jadwal Belum Memenuhi Minimal -->
            <a href="{{ route('admin.kelayakan.index') }}" class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/80 hover:bg-rose-50 transition block group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-rose-800">Kelayakan Keberangkatan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-200 text-rose-900">{{ $jadwal_perlu_tindakan->count() }} belum siap</span>
                </div>
                <p class="text-xs text-rose-700 leading-relaxed">
                    @if($jadwal_perlu_tindakan->count() > 0)
                        {{ $jadwal_perlu_tindakan->first()->paket->nama_paket ?? 'Jadwal' }} tgl {{ $jadwal_perlu_tindakan->first()->tanggal_berangkat->format('d/m/Y') }} baru terisi {{ $jadwal_perlu_tindakan->first()->peserta_aktif }}/{{ $jadwal_perlu_tindakan->first()->min_peserta }} minimal.
                    @else
                        Seluruh jadwal keberangkatan mendatang telah memenuhi kuota minimal.
                    @endif
                </p>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-900 mt-3 group-hover:translate-x-1 transition-transform">
                    Tinjau Kelayakan &rarr;
                </span>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const chartData = @json($grafik_pendapatan);
        const labels = chartData.map(item => item.bulan);
        const dataValues = chartData.map(item => item.total_jt);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Juta Rp)',
                    data: dataValues,
                    backgroundColor: 'rgba(14, 165, 233, 0.85)',
                    hoverBackgroundColor: 'rgba(2, 132, 199, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Rp ' + context.parsed.y + ' Juta';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) { return 'Rp ' + value + ' jt'; },
                            font: { family: 'Plus Jakarta Sans', size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
