@extends('layouts.admin', ['title' => 'Kelayakan Keberangkatan'])

@section('content')
<div class="py-6 space-y-6">
    
    <!-- Title & Subtitle (Frame 107:2446) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelayakan Keberangkatan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pemantauan rasio peserta vs kapasitas armada, titik impas operasional, dan keputusan keberangkatan</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold">
                <span class="w-2.5 h-2.5 rounded-full bg-black"></span>
                Garis hitam vertikal = Batas minimal peserta berangkat
            </span>
        </div>
    </div>

    <!-- 2 Column Master Detail Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Tabel Kelayakan per Jadwal (7.5 Cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-extrabold text-slate-900">Jadwal Keberangkatan Mendatang</h3>
                <span class="text-xs font-bold text-slate-400">{{ count($evaluasiJadwal) }} Jadwal Terjadwal</span>
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Paket &amp; Tanggal</th>
                            <th class="py-3 px-3 w-44">Peserta vs Kapasitas</th>
                            <th class="py-3 px-2 text-center">Min/Kap</th>
                            <th class="py-3 px-3">Estimasi vs Biaya</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-2 text-right">Pilih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($evaluasiJadwal as $ev)
                            @php
                                $j = $ev['jadwal'];
                                $isSelected = $selectedEvaluasi && $selectedEvaluasi['jadwal']->id === $j->id;
                            @endphp
                            <tr class="hover:bg-slate-50/60 {{ $isSelected ? 'bg-sky-50/50 font-bold' : '' }}">
                                <td class="py-3.5 px-4">
                                    <span class="block font-bold text-slate-900">{{ $j->paket->nama_paket ?? 'Trip' }}</span>
                                    <span class="block text-[11px] text-slate-400 font-normal">Tgl: {{ $j->tanggal_berangkat->translatedFormat('d F Y') }}</span>
                                </td>
                                
                                <!-- Progress Bar with Black Minimal Threshold Line (Frame 107:2446) -->
                                <td class="py-3.5 px-3">
                                    <div class="w-full bg-slate-100 h-4 rounded-full relative overflow-visible border border-slate-200">
                                        <!-- Fill bar (peserta aktif) -->
                                        <div class="h-full rounded-full transition-all {{ $ev['persen_kapasitas'] >= 100 ? 'bg-emerald-500' : ($ev['status'] === 'Layak dijalankan' ? 'bg-sky-500' : 'bg-amber-400') }}" 
                                             style="width: {{ $ev['persen_kapasitas'] }}%"></div>
                                        
                                        <!-- Minimal Threshold Line (Garis Hitam) -->
                                        @if($ev['persen_min'] > 0 && $ev['persen_min'] < 100)
                                            <div class="absolute top-[-3px] bottom-[-3px] w-1 bg-black z-10 shadow-xs" 
                                                 style="left: {{ $ev['persen_min'] }}%" 
                                                 title="Batas Minimal: {{ $ev['min_peserta'] }} orang"></div>
                                        @endif
                                    </div>
                                    <span class="block text-[10px] text-slate-500 mt-1 font-mono text-center">
                                        {{ $ev['peserta_aktif'] }} dari {{ $ev['kuota'] }} kursi
                                    </span>
                                </td>

                                <td class="py-3.5 px-2 text-center font-mono">
                                    <span class="font-bold text-slate-800">{{ $ev['min_peserta'] }}</span> / <span class="text-slate-400">{{ $ev['kuota'] }}</span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="block font-mono text-[11px] font-bold text-slate-800">
                                        Rp {{ number_format($ev['estimasi_pendapatan'] / 1000000, 1) }} jt
                                    </span>
                                    <span class="block text-[10px] text-slate-400">
                                        vs Rp {{ number_format($ev['biaya_operasional'] / 1000000, 1) }} jt
                                    </span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $ev['status'] === 'Penuh' ? 'bg-indigo-50 text-indigo-700' : ($ev['status'] === 'Layak dijalankan' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                                        {{ $ev['status'] }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-2 text-right">
                                    <a href="{{ route('admin.kelayakan.index', ['selected' => $j->id]) }}" class="px-2.5 py-1 bg-white border border-slate-200 hover:border-sky-500 hover:text-sky-600 text-slate-700 rounded-lg text-[11px] font-bold">
                                        Tinjau
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada jadwal keberangkatan aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Panel Tindakan & Keputusan Admin (4.5 Cols, Frame 107:2446) -->
        <div class="lg:col-span-4 space-y-4">
            @if($selectedEvaluasi)
                @php
                    $selJ = $selectedEvaluasi['jadwal'];
                @endphp
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
                    
                    <div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $selectedEvaluasi['status'] === 'Layak dijalankan' || $selectedEvaluasi['status'] === 'Penuh' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $selectedEvaluasi['status'] }}
                        </span>
                        <h3 class="text-base font-extrabold text-slate-900 mt-1">{{ $selJ->paket->nama_paket ?? 'Trip' }}</h3>
                        <p class="text-xs text-slate-500">Berangkat: {{ $selJ->tanggal_berangkat->translatedFormat('d F Y') }} &bull; Armada: {{ $selJ->kendaraan ? $selJ->kendaraan->jenis_kendaraan : 'Belum ditetapkan' }}</p>
                    </div>

                    <!-- Detail Progress Peserta -->
                    <div class="p-4 bg-slate-50 rounded-2xl space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Peserta Terdaftar:</span>
                            <span class="font-extrabold text-slate-900">{{ $selectedEvaluasi['peserta_aktif'] }} Orang ({{ $selectedEvaluasi['peserta_dikonfirmasi'] }} Lunas)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Batas Minimal Keberangkatan:</span>
                            <span class="font-bold text-slate-800">{{ $selectedEvaluasi['min_peserta'] }} Orang</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kapasitas Armada:</span>
                            <span class="font-bold text-slate-800">{{ $selectedEvaluasi['kuota'] }} Kursi</span>
                        </div>
                        <div class="border-t border-slate-200 pt-2 flex justify-between">
                            <span class="text-slate-500">Estimasi Laba/Rugi:</span>
                            <span class="font-bold {{ $selectedEvaluasi['untung_rugi'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $selectedEvaluasi['untung_rugi'] >= 0 ? '+ ' : '' }}Rp {{ number_format($selectedEvaluasi['untung_rugi'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Panel Tindakan Keputusan Admin (F5-05, BR-09, US-11) -->
                    <form action="{{ route('admin.kelayakan.keputusan', $selJ->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Perbarui Biaya Operasional (Rp)</label>
                            <input type="number" name="biaya_operasional" value="{{ old('biaya_operasional', (int)$selJ->biaya_operasional) }}" min="0" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keputusan Status Keberangkatan</label>
                            <select name="status_keberangkatan" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800">
                                <option value="dijadwalkan" {{ $selJ->status_keberangkatan === 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan (Masih Menunggu Pendaftar)</option>
                                <option value="dikonfirmasi" {{ $selJ->status_keberangkatan === 'dikonfirmasi' ? 'selected' : '' }}>Konfirmasi Keberangkatan (Pasti Berangkat)</option>
                                <option value="ditunda" {{ $selJ->status_keberangkatan === 'ditunda' ? 'selected' : '' }}>Tunda Keberangkatan (Memicu Pindah Jadwal)</option>
                                <option value="dibatalkan" {{ $selJ->status_keberangkatan === 'dibatalkan' ? 'selected' : '' }}>Batalkan Trip (Memicu Refund Penuh)</option>
                            </select>
                        </div>

                        <div class="p-3 bg-amber-50 border border-amber-200/80 rounded-xl text-[11px] text-amber-800">
                            <strong>Catatan:</strong> Keputusan menunda atau membatalkan akan menandai pesanan peserta terdampak untuk proses refund atau pemindahan jadwal.
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-md">
                            Simpan Keputusan Keberangkatan
                        </button>
                    </form>

                </div>
            @endif
        </div>
    </div>
</div>
@endsection
