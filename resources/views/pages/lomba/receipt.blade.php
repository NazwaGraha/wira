<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pendaftaran - {{ $registration->registration_code }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap");

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 12px 40px;
        }

        /* Action Top Bar */
        .action-bar {
            width: 100%;
            max-width: 724px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-decoration: none;
            padding: 8px 14px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .btn-back:hover {
            color: #0f172a;
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #ffffff;
            background: #dc2626;
            border: none;
            padding: 9px 18px;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
            transition: all 0.2s;
        }

        .btn-print:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }

        /* Wrapper for mobile scale responsiveness */
        .kwitansi-wrapper {
            width: 100%;
            max-width: 724px;
            display: flex;
            justify-content: center;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        /* Canvas dimensions: 724 x 1024 exact match to background template */
        .kwitansi-canvas {
            position: relative;
            width: 724px;
            height: 1024px;
            min-width: 724px;
            min-height: 1024px;
            background-image: url("{{ asset('images/kwitansi/bg_kwitansi_pantai.png') }}");
            background-size: 724px 1024px;
            background-repeat: no-repeat;
            background-position: center;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }

        /* Top Browser / Print Header Meta */
        .top-meta-info {
            position: absolute;
            top: 80px;
            left: 54px;
            right: 54px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #475569;
            font-weight: 500;
        }

        /* Main Header Section */
        .header-section {
            position: absolute;
            top: 118px;
            left: 54px;
            right: 54px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .logo-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 7px 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(203, 213, 225, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 35px;
        }

        .logo-box img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .panitia-info {
            flex: 1;
            padding: 0 10px;
            text-align: left;
            margin-left: 8px;
        }

        .panitia-title {
            font-size: 13px;
            font-weight: 900;
            line-height: 1.3;
            color: #0f172a;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .panitia-sub {
            font-size: 10.5px;
            font-weight: 800;
            color: #dc2626;
            margin-top: 4px;
            line-height: 1.25;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .panitia-address {
            font-size: 7.8px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 600;
            line-height: 1.35;
        }

        .bukti-card {
            background: #ffffff;
            border: 1px solid rgba(203, 213, 225, 0.9);
            border-radius: 14px;
            padding: 10px 14px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            width: 178px;
            flex-shrink: 0;
        }

        .bukti-label {
            font-size: 8.5px;
            font-weight: 800;
            color: #475569;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .bukti-code {
            margin: 5px 0;
            border: 1px solid #cbd5e1;
            border-radius: 9999px;
            padding: 3.5px 10px;
            font-size: 11.5px;
            font-weight: 800;
            font-family: monospace;
            color: #0f172a;
            background: #ffffff;
            letter-spacing: 0.5px;
        }

        .bukti-date {
            font-size: 8.5px;
            color: #475569;
            font-weight: 600;
        }

        .bukti-status {
            font-size: 8.5px;
            font-weight: 800;
            color: #94a3b8;
            margin-top: 4px;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }

        /* Double Divider Line */
        .divider-container {
            position: absolute;
            top: 265px;
            left: 54px;
            right: 54px;
        }

        .divider-line-1 {
            border-bottom: 1.5px solid #0f172a;
        }

        .divider-line-2 {
            border-bottom: 0.8px solid #0f172a;
            margin-top: 1.5px;
        }

        /* Document Title */
        .doc-title-container {
            position: absolute;
            top: 288px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .doc-title {
            font-size: 19px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            display: inline-block;
        }

        .doc-title-underline {
            width: 380px;
            height: 1.5px;
            background: #38bdf8;
            margin: 3px auto 0;
            border-radius: 2px;
        }

        /* Data Kontingen Card */
        .data-card {
            position: absolute;
            top: 338px;
            left: 54px;
            right: 54px;
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 14px;
            padding: 13px 18px;
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            gap: 6.5px;
            font-size: 10px;
        }

        .data-row {
            display: flex;
            align-items: center;
        }

        .data-label {
            width: 190px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.2px;
        }

        .data-sep {
            margin-right: 10px;
            font-weight: 700;
            color: #0f172a;
        }

        .data-val {
            font-weight: 700;
            color: #0f172a;
            flex: 1;
        }

        .val-school {
            font-weight: 900;
            font-size: 11px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .val-level {
            font-weight: 800;
        }

        .val-email {
            font-family: monospace;
            font-weight: 600;
            color: #1e293b;
        }

        .badge-wave {
            display: inline-block;
            background: #ffffff;
            border: 1px solid #facc15;
            color: #854d0e;
            font-weight: 800;
            font-size: 9.5px;
            padding: 2px 9px;
            border-radius: 6px;
        }

        /* Table Section */
        .table-card {
            position: absolute;
            top: 528px;
            left: 54px;
            right: 54px;
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th {
            background: rgba(255, 255, 255, 0.65);
            padding: 8px 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #1e293b;
            border-bottom: 1px solid #cbd5e1;
            font-size: 9px;
        }

        th.col-no { width: 38px; text-align: center; border-right: 1px solid #cbd5e1; }
        th.col-lomba { text-align: left; border-right: 1px solid #cbd5e1; }
        th.col-regu { text-align: left; width: 190px; border-right: 1px solid #cbd5e1; }
        th.col-biaya { text-align: right; width: 110px; }

        td {
            padding: 7px 10px;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 500;
        }

        td.col-no { text-align: center; font-weight: 700; color: #64748b; border-right: 1px solid #f1f5f9; }
        td.col-lomba { font-weight: 700; color: #0f172a; border-right: 1px solid #f1f5f9; }
        td.col-regu { color: #475569; font-weight: 600; border-right: 1px solid #f1f5f9; }
        td.col-biaya { text-align: right; font-family: monospace; font-weight: 700; font-size: 10px; }

        .total-row {
            background: rgba(255, 255, 255, 0.95);
            border-top: 1.8px solid #0f172a;
        }

        .total-label {
            text-align: right;
            padding: 9px 12px;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.3px;
            color: #0f172a;
        }

        .total-amount {
            text-align: right;
            padding: 9px 12px;
            font-family: monospace;
            font-weight: 900;
            font-size: 11.5px;
            color: #0f172a;
        }

        /* Stempel Lunas Overlay on Maskot - Turun 2 cm di Dada & Ban Kuning Maskot */
        .stempel-overlay {
            position: absolute;
            left: 124px;
            top: 868px;
            width: 132px;
            height: 132px;
            z-index: 20;
            pointer-events: none;
            filter: drop-shadow(0 4px 10px rgba(14, 165, 233, 0.25));
        }

        .stempel-overlay img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Lifebuoy QR Code Box - Pas menutupi kotak putih pelampung */
        .lifebuoy-qr-box {
            position: absolute;
            left: 278px;
            top: 816px;
            width: 102px;
            height: 118px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 15;
            background: #ffffff;
            border-radius: 14px;
            padding: 6px 4px 4px 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .lifebuoy-qr-box img {
            width: 82px;
            height: 82px;
            object-fit: contain;
        }

        .qr-label {
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #0f172a;
            margin-top: 3px;
        }

        /* Signature Section (Right on sand) */
        .signature-box {
            position: absolute;
            right: 56px;
            top: 730px;
            width: 220px;
            text-align: center;
            z-index: 15;
        }

        .sig-date {
            font-size: 9.5px;
            color: #475569;
            font-weight: 600;
        }

        .sig-role {
            font-size: 9.5px;
            color: #475569;
            font-weight: 600;
            margin-top: 1px;
        }

        .sig-space {
            height: 52px;
        }

        .sig-name {
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 2px;
            display: inline-block;
            letter-spacing: 0.3px;
            line-height: 1.35;
        }

        .sig-sub {
            font-size: 8.5px;
            color: #475569;
            font-weight: 600;
            margin-top: 3px;
        }

        /* Footer URL */
        .footer-url {
            position: absolute;
            bottom: 12px;
            left: 54px;
            font-size: 8px;
            color: #64748b;
            font-family: monospace;
            font-weight: 500;
            letter-spacing: -0.2px;
        }

        /* Print Media Styles */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .kwitansi-wrapper {
                max-width: 100% !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .kwitansi-canvas {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 auto !important;
                page-break-inside: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar (Kembali & Tombol Cetak) -->
    <div class="no-print action-bar">
        <a href="{{ route('lomba.status', ['code' => $registration->registration_code]) }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Status</span>
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- Kwitansi Canvas Container -->
    <div class="kwitansi-wrapper">
        <div class="kwitansi-canvas">
            
            <!-- Top Meta Info (Browser / Doc Header) -->
            <div class="top-meta-info">
                <span>{{ $registration->verified_at ? $registration->verified_at->format('m/d/y, h:i A') : date('m/d/y, h:i A') }}</span>
                <span>Kwitansi Pendaftaran - {{ $registration->registration_code }}</span>
            </div>

            <!-- Header Section -->
            <div class="header-section">
                
                <!-- Left: Logo PMI & SMAN 1 Ciawi -->
                <div class="logo-box">
                    <img src="{{ file_exists(public_path('images/logo_pmi_sman1ciawi.png')) ? asset('images/logo_pmi_sman1ciawi.png') : asset('images/logo.png') }}" 
                         alt="Logo PMI & SMAN 1 Ciawi">
                </div>

                <!-- Middle: Panitia Information -->
                <div class="panitia-info">
                    <div class="panitia-title">
                        PANITIA SUA<br>
                        BHAKTI<br>
                        BERKARYA IV<br>
                        TAHUN 2026
                    </div>
                    <div class="panitia-sub">
                        PMR WIRA SMAN 1 CIAWI<br>
                        KAB. BOGOR
                    </div>
                    <div class="panitia-address">
                        Jl. Veteran III No. 01 Ciawi, Bogor •<br>
                        Email: pmrwirasman1c@gmail.com
                    </div>
                </div>

                <!-- Right: Bukti Pembayaran Card -->
                <div class="bukti-card">
                    <div class="bukti-label">BUKTI PEMBAYARAN</div>
                    <div class="bukti-code">{{ $registration->registration_code }}</div>
                    <div class="bukti-date">{{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y') : ($registration->created_at ? $registration->created_at->translatedFormat('d F Y') : date('d F Y')) }}</div>
                    <div class="bukti-status">✓ LUNAS</div>
                </div>

            </div>

            <!-- Header Divider Double Line -->
            <div class="divider-container">
                <div class="divider-line-1"></div>
                <div class="divider-line-2"></div>
            </div>

            <!-- Document Title -->
            <div class="doc-title-container">
                <h1 class="doc-title">KWITANSI PENDAFTARAN RESMI</h1>
                <div class="doc-title-underline"></div>
            </div>

            <!-- Data Kontingen Box -->
            <div class="data-card">
                <div class="data-row">
                    <span class="data-label">TELAH DITERIMA DARI</span>
                    <span class="data-sep">:</span>
                    <span class="data-val val-school">{{ $registration->school_name }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">TINGKAT KONTINGEN</span>
                    <span class="data-sep">:</span>
                    <span class="data-val val-level {{ $registration->level == 'Mula' ? 'text-emerald-700' : ($registration->level == 'Madya' ? 'text-blue-700' : 'text-amber-800') }}">
                        PMR {{ $registration->level }} ({{ $registration->level == 'Mula' ? 'SD / MI' : ($registration->level == 'Madya' ? 'SMP / MTs' : 'SMA / SMK / MA') }})
                    </span>
                </div>
                <div class="data-row">
                    <span class="data-label">NAMA PEMBINA / PENDAMPING</span>
                    <span class="data-sep">:</span>
                    <span class="data-val">{{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</span>
                </div>
                <div class="data-row">
                    <span class="data-label">ALAMAT EMAIL</span>
                    <span class="data-sep">:</span>
                    <span class="data-val val-email">{{ $registration->advisor_email ?: '-' }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">GELOMBANG PENDAFTARAN</span>
                    <span class="data-sep">:</span>
                    <span class="data-val">
                        <span class="badge-wave">
                            {{ ($registration->registration_wave ?? 1) == 2 ? ($registration->event->wave2_name ?: 'Gelombang 2') : ($registration->event->wave1_name ?: 'Gelombang 1') }}
                        </span>
                    </span>
                </div>
                <div class="data-row">
                    <span class="data-label">UNTUK PEMBAYARAN</span>
                    <span class="data-sep">:</span>
                    <span class="data-val">Registrasi Peserta Lomba {{ $registration->event->title ?? 'SUA BHAKTI BERKARYA IV TAHUN 2026' }}</span>
                </div>
            </div>

            <!-- Table Cabang Mata Lomba & Biaya -->
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th class="col-no">NO</th>
                            <th class="col-lomba">CABANG MATA LOMBA</th>
                            <th class="col-regu">REGU / KETERANGAN</th>
                            <th class="col-biaya">BIAYA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registration->teams as $index => $team)
                            @php
                                $teamFee = $team->category ? $team->category->getFeeForWave($registration->registration_wave ?: 1) : floatval($registration->event->registration_fee ?: 150000);
                            @endphp
                            <tr>
                                <td class="col-no">{{ $index + 1 }}</td>
                                <td class="col-lomba">{{ $team->category ? $team->category->name : 'Mata Lomba' }} ({{ $team->category ? $team->category->gender_category : 'Umum' }})</td>
                                <td class="col-regu">{{ $team->team_name }}</td>
                                <td class="col-biaya">Rp {{ number_format($teamFee, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 12px; color: #64748b;">Belum ada regu lomba terdaftar.</td>
                            </tr>
                        @endforelse
                        <tr class="total-row">
                            <td colspan="3" class="total-label">TOTAL PEMBAYARAN LUNAS :</td>
                            <td class="total-amount">Rp {{ number_format($registration->total_payment, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Stempel Lunas Overlay over Maskot in bottom left - Pas di Dada & Ban Kuning Maskot -->
            <div class="stempel-overlay">
                <img src="{{ file_exists(public_path('images/kwitansi/stempel_lunas_transparan.png')) ? asset('images/kwitansi/stempel_lunas_transparan.png') : asset('images/kwitansi/stempel_lunas.jpg') }}" 
                     alt="Stempel Terverifikasi Pembayaran Lunas">
            </div>

            <!-- Lifebuoy QR Code Box - Pas menutupi kotak putih pelampung -->
            <div class="lifebuoy-qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode(route('lomba.kwitansi', ['code' => $registration->registration_code])) }}" 
                     alt="QR Code Validasi" 
                     loading="lazy">
                <div class="qr-label">SCAN VALIDASI</div>
            </div>

            <!-- Signature Box on the bottom right -->
            <div class="signature-box">
                <div class="sig-date">Bogor, {{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y') : ($registration->created_at ? $registration->created_at->translatedFormat('d F Y') : date('d F Y')) }}</div>
                <div class="sig-role">Bendahara Panitia Pelaksana,</div>
                <div class="sig-space"></div>
                <div class="sig-name">PANITIA {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA IV') }}<br>TAHUN 2026</div>
                <div class="sig-sub">PMR Wira SMAN 1 Ciawi</div>
            </div>

            <!-- Footer Direct URL -->
            <div class="footer-url">
                {{ route('lomba.kwitansi', ['code' => $registration->registration_code]) }}
            </div>

        </div>
    </div>

</body>
</html>
