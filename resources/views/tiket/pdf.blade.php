<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-Tiket — {{ $tiket->kode_tiket }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 25px; line-height: 1.5; }
        .ticket-box { border: 2px solid #0284c7; border-radius: 12px; overflow: hidden; }
        .header { background-color: #0f172a; color: #ffffff; padding: 20px; }
        .brand { font-size: 20px; font-weight: bold; letter-spacing: -0.5px; }
        .brand-sub { font-size: 10px; color: #38bdf8; font-weight: bold; text-transform: uppercase; margin-top: 2px; }
        .ticket-code { font-size: 14px; font-weight: bold; color: #38bdf8; font-family: monospace; }
        .body-content { padding: 25px; background-color: #ffffff; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 8px 10px; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        .label { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; width: 35%; }
        .value { font-size: 12px; font-weight: bold; color: #0f172a; width: 65%; }
        .qr-section { text-align: center; padding: 15px; background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; margin-top: 15px; }
        .qr-code-img svg { max-width: 140px; height: auto; }
        .terms { margin-top: 25px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #64748b; line-height: 1.4; }
        .footer { margin-top: 15px; text-align: center; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>

    <div class="ticket-box">
        <!-- Ticket Header -->
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="brand">JELAJAH NUSA</div>
                        <div class="brand-sub">Official Boarding Pass &amp; Travel Voucher</div>
                    </td>
                    <td style="text-align: right;">
                        <div style="font-size: 10px; color: #94a3b8;">KODE TIKET:</div>
                        <div class="ticket-code">{{ $tiket->kode_tiket }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Ticket Body -->
        <div class="body-content">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 65%; vertical-align: top; padding-right: 20px;">
                        
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;">Paket Perjalanan:</div>
                            <div style="font-size: 16px; font-weight: bold; color: #0284c7; margin-top: 2px;">
                                {{ $tiket->pemesanan ? $tiket->pemesanan->judul_trip : 'Tour Jelajah Nusa' }}
                            </div>
                        </div>

                        <table class="info-table">
                            <tr>
                                <td class="label">Nama Peserta:</td>
                                <td class="value">{{ $tiket->peserta->nama_lengkap ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="label">No. Identitas ({{ strtoupper($tiket->peserta->jenis_identitas ?? 'KTP') }}):</td>
                                <td class="value" style="font-family: monospace;">{{ $tiket->peserta->no_identitas ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Tanggal Keberangkatan:</td>
                                <td class="value">
                                    {{ $tiket->pemesanan && $tiket->pemesanan->tanggal_berangkat ? $tiket->pemesanan->tanggal_berangkat->translatedFormat('l, d F Y') : '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Titik Kumpul (Meeting Point):</td>
                                <td class="value">
                                    {{ $tiket->pemesanan && $tiket->pemesanan->jadwal && $tiket->pemesanan->jadwal->paket ? $tiket->pemesanan->jadwal->paket->meeting_point : 'Sesuai Konfirmasi Admin' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Jam Kumpul:</td>
                                <td class="value">
                                    {{ $tiket->pemesanan && $tiket->pemesanan->jadwal && $tiket->pemesanan->jadwal->paket ? substr($tiket->pemesanan->jadwal->paket->jam_kumpul, 0, 5) . ' WIB' : 'Konfirmasi H-1' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Kode Pemesanan Induk:</td>
                                <td class="value" style="font-family: monospace;">{{ $tiket->pemesanan ? $tiket->pemesanan->display_kode : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Status Tiket:</td>
                                <td class="value" style="color: #059669; text-transform: uppercase;">{{ $tiket->status }} &check;</td>
                            </tr>
                        </table>

                    </td>
                    
                    <!-- QR Code Validation Box -->
                    <td style="width: 35%; vertical-align: top;">
                        <div class="qr-section">
                            <div style="font-size: 10px; font-weight: bold; color: #0f172a; margin-bottom: 8px;">PINDAI KEASLIAN (QR CODE)</div>
                            <div class="qr-code-img" style="text-align: center; margin: 0 auto;">
                                @if(!empty($tiket->qr_code_svg))
                                    <img src="data:image/svg+xml;base64,{{ base64_encode($tiket->qr_code_svg) }}" 
                                         alt="QR Code Tiket {{ $tiket->kode_tiket }}" 
                                         width="130" 
                                         height="130" 
                                         style="display: block; margin: 0 auto; border-radius: 4px;">
                                @else
                                    <div style="font-family: monospace; font-size: 12px; font-weight: bold; padding: 10px; background: #e2e8f0; border-radius: 6px;">
                                        {{ $tiket->kode_tiket }}
                                    </div>
                                @endif
                            </div>
                            <div style="font-size: 9px; color: #64748b; margin-top: 8px;">
                                Tunjukkan QR Code ini kepada Tour Leader / Crew saat registrasi keberangkatan.
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Syarat & Ketentuan Perjalanan -->
            <div class="terms">
                <strong>Ketentuan Perjalanan Wisata:</strong><br>
                1. Tiket ini merupakan bukti sah keikutsertaan perjalanan Jelajah Nusa dan wajib ditunjukkan bersama kartu identitas asli (KTP/Paspor).<br>
                2. Wisatawan diharapkan hadir di meeting point selambat-lambatnya 30 menit sebelum jam kumpul yang tertera.<br>
                3. Pembatalan atau reschedule tunduk pada kebijakan agen travel resmi Jelajah Nusa.<br>
                4. Untuk informasi darurat atau perubahan armada hubungi customer service kami di +62 812-3456-7890.
            </div>
        </div>
    </div>

    <div class="footer">
        Diterbitkan secara digital oleh PT Jelajah Nusa Nusantara &bull; Universitas Duta Bangsa &bull; {{ $tiket->tanggal_terbit ? $tiket->tanggal_terbit->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
