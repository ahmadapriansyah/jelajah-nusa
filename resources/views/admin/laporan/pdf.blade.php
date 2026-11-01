<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Operasional & Pendapatan — Jelajah Nusa</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; margin: 0; padding: 20px; }
        .header { border-bottom: 2px solid #0284c7; padding-bottom: 12px; margin-bottom: 20px; }
        .brand { font-size: 20px; font-weight: bold; color: #0f172a; }
        .subbrand { font-size: 11px; color: #0284c7; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; margin-top: 10px; color: #0f172a; }
        .period { font-size: 11px; color: #64748b; margin-top: 2px; }
        .grid-summary { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .grid-summary td { width: 25%; padding: 10px; border: 1px solid #e2e8f0; background-color: #f8fafc; text-align: center; }
        .stat-label { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .stat-value { font-size: 18px; font-weight: bold; color: #0f172a; margin-top: 4px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.data-table th { background-color: #0f172a; color: #ffffff; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        table.data-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        table.data-table tr:nth-child(even) td { background-color: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; font-size: 9px; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="brand">JELAJAH NUSA</div>
                    <div class="subbrand">Sistem Informasi Travel dan Agen Wisata</div>
                    <div class="title">LAPORAN OPERASIONAL &amp; PENDAPATAN</div>
                    <div class="period">Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 10px; color: #64748b;">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WIB</div>
                    <div style="font-size: 10px; color: #64748b;">Dicetak oleh: {{ auth()->user()->nama_lengkap ?? 'Administrator' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Ringkasan Kartu -->
    <table class="grid-summary">
        <tr>
            <td>
                <div class="stat-label">Jadwal Dijalankan</div>
                <div class="stat-value" style="color: #059669;">{{ $laporan['dijalankan'] }}</div>
            </td>
            <td>
                <div class="stat-label">Jadwal Ditunda</div>
                <div class="stat-value" style="color: #d97706;">{{ $laporan['ditunda'] }}</div>
            </td>
            <td>
                <div class="stat-label">Jadwal Dibatalkan</div>
                <div class="stat-value" style="color: #e11d48;">{{ $laporan['dibatalkan'] }}</div>
            </td>
            <td>
                <div class="stat-label">Total Wisatawan</div>
                <div class="stat-value" style="color: #0284c7;">{{ $laporan['total_peserta'] }} Org</div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; font-weight: bold; font-size: 12px; color: #0f172a;">
        PERINGKAT PAKET TERLARIS &amp; PENDAPATAN (TOTAL: {{ $laporan['total_pendapatan_format'] }})
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Rank</th>
                <th>Paket Wisata</th>
                <th>Destinasi</th>
                <th>Layanan</th>
                <th class="text-right">Total Peserta</th>
                <th class="text-right">Pendapatan Riil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan['paket_terlaris'] as $p)
                <tr>
                    <td class="text-center" style="font-weight: bold;">{{ $p['peringkat'] }}</td>
                    <td style="font-weight: bold;">{{ $p['nama_paket'] }}</td>
                    <td>{{ $p['destinasi'] }}</td>
                    <td>{{ $p['jenis_layanan'] }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($p['peserta'], 0, ',', '.') }} orang</td>
                    <td class="text-right" style="font-weight: bold; color: #059669;">{{ $p['pendapatan_format'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada aktivitas pemesanan atau pendapatan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini diterbitkan secara otomatis oleh Sistem Informasi Travel dan Agen Wisata (Jelajah Nusa) &bull; Universitas Duta Bangsa
    </div>

</body>
</html>
