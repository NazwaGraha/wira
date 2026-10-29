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

        /* Top Header Elements (Naik 2 cm) */
        .header-box {
            position: absolute;
            top: 88px;
            left: 78px;
            display: flex;
            align-items: center;
            background: #ffffff;
            padding: 6px 12px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(203, 213, 225, 0.7);
        }

        .header-box img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .panitia-text {
            position: absolute;
            top: 54px;
            left: 270px;
            width: 160px;
            line-height: 1.15;
        }

        .panitia-text .title {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.3px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .panitia-text .subtitle {
            font-size: 9px;
            font-weight: 800;
            color: #dc2626;
            margin-top: 3px;
            text-transform: uppercase;
        }

        .panitia-text .address {
            font-size: 7.2px;
            color: #475569;
            margin-top: 3px;
            line-height: 1.25;
            font-weight: 500;
        }

        .badge-bukti {
            position: absolute;
            top: 62px;
            right: 78px;
            width: 180px;
            background: #ffffff;
            border-radius: 14px;
            padding: 10px 12px;
            text-align: center;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .badge-bukti .label {
            font-size: 8.5px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #334155;
            text-transform: uppercase;
        }

        .badge-bukti .code-pill {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #0f172a;
            margin: 5px auto;
            display: inline-block;
        }

        .badge-bukti .date {
            font-size: 9px;
            color: #475569;
            font-weight: 500;
        }

        .badge-bukti .status {
            font-size: 8px;
            color: #16a34a;
            font-weight: 800;
            letter-spacing: 1.5px;
            margin-top: 3px;
        }

        /* Divider Line */
        .header-line {
            position: absolute;
            top: 200px;
            left: 78px;
            right: 78px;
            border-bottom: 2px solid #0284c7;
            opacity: 0.8;
        }

        /* Document Title */
        .title-section {
            position: absolute;
            top: 220px;
            left: 78px;
            right: 78px;
            text-align: center;
        }

        .title-section h1 {
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #034f75;
            text-transform: uppercase;
        }

        .title-section .decor-line {
            width: 140px;
            height: 2px;
            background: #0284c7;
            margin: 4px auto 0;
            border-radius: 2px;
            opacity: 0.5;
        }

        /* Info Card */
        .info-card {
            position: absolute;
            top: 258px;
            left: 78px;
            right: 78px;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(4px);
            border-radius: 12px;
            padding: 8px 16px;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.08);
            border: 1px solid #e0f2fe;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 140px 12px 1fr;
            row-gap: 3px;
            font-size: 8.8px;
            line-height: 1.35;
        }

        .info-label {
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            font-size: 8.2px;
        }

        .info-sep {
            font-weight: 700;
            color: #0f172a;
            text-align: center;
        }

        .info-val {
            font-weight: 600;
            color: #0f172a;
        }

        .info-val.highlight {
            font-weight: 800;
            color: #0284c7;
        }

        .gelombang-badge {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
            padding: 0.5px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            display: inline-block;
        }

        /* Grouped Table Card Container */
        .table-container {
            position: absolute;
            top: 370px;
            left: 78px;
            right: 78px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(2, 132, 199, 0.08);
            border: 1px solid #e2e8f0;
            padding: 7px 10px 5px;
        }

        .grouped-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .grouped-table thead th {
            background: #f0f9ff;
            color: #0369a1;
            font-size: 7.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 5px 6px;
            border-bottom: 1.5px solid #bae6fd;
        }

        .grouped-table thead th:first-child { border-top-left-radius: 6px; }
        .grouped-table thead th:last-child { border-top-right-radius: 6px; }
        .grouped-table thead th.th-no { width: 32px; text-align: center; }
        .grouped-table thead th.th-lomba { text-align: left; padding-left: 8px; }
        .grouped-table thead th.th-regu { width: 85px; text-align: center; }
        .grouped-table thead th.th-biaya { width: 105px; text-align: right; padding-right: 8px; }
        .grouped-table thead th.th-total { width: 110px; text-align: right; padding-right: 8px; }

        .grouped-table tbody tr:nth-child(even) { background: #f8fafc; }
        .grouped-table tbody tr:nth-child(odd) { background: #ffffff; }

        .grouped-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #f1f5f9;
        }

        .grouped-table td.col-no {
            font-weight: 700;
            color: #475569;
            text-align: center;
            font-size: 8.5px;
        }

        .grouped-table td.col-lomba {
            font-weight: 600;
            color: #0f172a;
            font-size: 8.5px;
            padding-left: 8px;
        }

        .grouped-table td.col-regu {
            text-align: center;
        }

        .grouped-table .regu-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            padding: 1.5px 8px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 8.5px;
        }

        .grouped-table td.col-biaya {
            font-weight: 600;
            color: #334155;
            text-align: right;
            font-size: 8.5px;
            white-space: nowrap;
            padding-right: 8px;
        }

        .grouped-table td.col-total {
            font-weight: 800;
            color: #0f172a;
            text-align: right;
            font-size: 9px;
            white-space: nowrap;
            padding-right: 8px;
        }

        /* Total Bar Full Width */
        .total-bar {
            margin-top: 5px;
            padding: 5px 12px;
            background: #f8fafc;
            border-top: 1.5px solid #0284c7;
            border-radius: 0 0 8px 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-bar .summary-tag {
            font-size: 8.5px;
            font-weight: 800;
            color: #0369a1;
            background: #e0f2fe;
            padding: 2.5px 10px;
            border-radius: 10px;
            border: 1px solid #bae6fd;
            letter-spacing: 0.3px;
        }

        .total-bar .total-text {
            font-size: 9.5px;
            font-weight: 800;
            color: #334155;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .total-bar .total-amount {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            margin-left: 10px;
        }

        /* Signature Block (Diturunkan 5 cm) */
        .sig-block {
            position: absolute;
            top: 855px;
            right: 78px;
            width: 200px;
            text-align: center;
        }

        .sig-block .loc-date {
            font-size: 8.5px;
            color: #475569;
            font-weight: 500;
        }

        .sig-block .role {
            font-size: 8.5px;
            color: #475569;
            margin-top: 1px;
            font-weight: 500;
        }

        .sig-block .inst-title {
            margin-top: 36px;
            font-size: 8.8px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            border-bottom: 1px solid #64748b;
            padding-bottom: 2px;
            line-height: 1.25;
            display: inline-block;
        }

        .sig-block .inst-sub {
            font-size: 8px;
            color: #475569;
            margin-top: 2px;
            font-weight: 600;
        }

        /* QR Code on Lifebuoy (Diturunkan 1 cm) */
        .qr-wrapper {
            position: absolute;
            top: 848px;
            left: 297px;
            width: 65px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .qr-wrapper img {
            width: 60px;
            height: 60px;
            display: block;
            object-fit: contain;
        }

        .qr-wrapper span {
            font-size: 5.5px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: 0.5px;
            margin-top: 3px;
            text-transform: uppercase;
        }

        /* Stamp Lunas (Diturunkan 3 cm) */
        .stamp-box {
            position: absolute;
            top: 845px;
            left: 104px;
            width: 135px;
            height: 135px;
            pointer-events: none;
            transform: rotate(-10deg);
        }

        .stamp-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Receipt URL */
        .receipt-url {
            position: absolute;
            bottom: 22px;
            left: 78px;
            font-size: 7.2px;
            color: #475569;
            font-family: monospace;
            font-weight: 600;
        }

        /* Print Media Styles */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                height: 100% !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .kwitansi-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                justify-content: center !important;
                align-items: flex-start !important;
                overflow: hidden !important;
            }

            .kwitansi-canvas {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 auto !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
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
            
            <!-- Header Brand -->
            <div class="header-box">
                <img src="{{ file_exists(public_path('images/logo_pmi_sman1ciawi.png')) ? asset('images/logo_pmi_sman1ciawi.png') : asset('images/logo.png') }}" 
                     alt="Logo PMI & SMAN 1 Ciawi">
            </div>

            <!-- Panitia Info -->
            <div class="panitia-text">
                <div class="title">PANITIA SUA<br>BHAKTI<br>BERKARYA IV<br>TAHUN 2026</div>
                <div class="subtitle">PMR WIRA SMAN 1 CIAWI<br>KAB. BOGOR</div>
                <div class="address">Jl. Veteran III No. 01 Ciawi, Bogor •<br>Email: pmrwirasman1c@gmail.com</div>
            </div>

            <!-- Bukti Pembayaran Card -->
            <div class="badge-bukti">
                <div class="label">BUKTI PEMBAYARAN</div>
                <div class="code-pill">{{ $registration->registration_code }}</div>
                <div class="date">{{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y') : ($registration->created_at ? $registration->created_at->translatedFormat('d F Y') : date('d F Y')) }}</div>
                <div class="status">✓ LUNAS</div>
            </div>

            <!-- Divider Line -->
            <div class="header-line"></div>

            <!-- Section Title -->
            <div class="title-section">
                <h1>KWITANSI PENDAFTARAN RESMI</h1>
                <div class="decor-line"></div>
            </div>

            <!-- Data Kontingen Box -->
            <div class="info-card">
                <div class="info-grid">
                    <div class="info-label">TELAH DITERIMA DARI</div>
                    <div class="info-sep">:</div>
                    <div class="info-val highlight">{{ $registration->school_name }}</div>

                    <div class="info-label">TINGKAT KONTINGEN</div>
                    <div class="info-sep">:</div>
                    <div class="info-val highlight" style="color: #0369a1;">
                        PMR {{ $registration->level }} ({{ $registration->level == 'Mula' ? 'SD / MI' : ($registration->level == 'Madya' ? 'SMP / MTs' : 'SMA / SMK / MA') }})
                    </div>

                    <div class="info-label">NAMA PEMBINA / PENDAMPING</div>
                    <div class="info-sep">:</div>
                    <div class="info-val">{{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</div>

                    <div class="info-label">ALAMAT EMAIL</div>
                    <div class="info-sep">:</div>
                    <div class="info-val">{{ $registration->advisor_email ?: '-' }}</div>

                    <div class="info-label">GELOMBANG PENDAFTARAN</div>
                    <div class="info-sep">:</div>
                    <div class="info-val">
                        <span class="gelombang-badge">
                            {{ ($registration->registration_wave ?? 1) == 2 ? ($registration->event->wave2_name ?: 'Gelombang 2') : ($registration->event->wave1_name ?: 'Gelombang 1') }}
                        </span>
                    </div>

                    <div class="info-label">UNTUK PEMBAYARAN</div>
                    <div class="info-sep">:</div>
                    <div class="info-val">Registrasi Peserta Lomba {{ $registration->event->title ?? 'SUA BHAKTI BERKARYA IV TAHUN 2026' }}</div>
                </div>
            </div>

            <!-- Table Cabang Mata Lomba & Biaya (Format Excel Grouped By Cabang Lomba) -->
            <div class="table-container">
                @php
                    $groupedCategories = $registration->teams->groupBy(function($team) {
                        return $team->competition_category_id ?: ($team->category ? $team->category->id : $team->team_name);
                    });
                    $totalRegisteredTeams = $registration->teams->count();
                    $calculatedTotalPayment = 0;
                @endphp

                <table class="grouped-table">
                    <thead>
                        <tr>
                            <th class="th-no">NO</th>
                            <th class="th-lomba">CABANG MATA LOMBA</th>
                            <th class="th-regu">JUMLAH REGU</th>
                            <th class="th-biaya">BIAYA PER REGU</th>
                            <th class="th-total">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedCategories as $catId => $teamsGroup)
                            @php
                                $firstTeam = $teamsGroup->first();
                                $category = $firstTeam->category;
                                $categoryName = $category ? $category->name : 'Mata Lomba';
                                $gender = $category ? $category->gender_category : 'Umum';
                                $branchLabel = ($gender && $gender !== '-' && !str_contains($categoryName, $gender)) 
                                    ? "{$categoryName} ({$gender})" 
                                    : $categoryName;
                                $teamCount = $teamsGroup->count();
                                $feePerTeam = $category 
                                    ? $category->getFeeForWave($registration->registration_wave ?: 1) 
                                    : floatval($registration->event->registration_fee ?: 150000);
                                $subtotal = $teamCount * $feePerTeam;
                                $calculatedTotalPayment += $subtotal;
                            @endphp
                            <tr>
                                <td class="col-no">{{ $loop->iteration }}</td>
                                <td class="col-lomba">{{ $branchLabel }}</td>
                                <td class="col-regu"><span class="regu-badge">{{ $teamCount }}</span></td>
                                <td class="col-biaya">Rp {{ number_format($feePerTeam, 0, ',', '.') }}</td>
                                <td class="col-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 12px; color: #64748b;">Belum ada cabang lomba terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Total Summary Bar -->
                <div class="total-bar">
                    <div class="summary-tag">TOTAL TERDAFTAR : {{ $totalRegisteredTeams }} Regu Mata Lomba</div>
                    <div>
                        <span class="total-text">Total Pembayaran</span>
                        <span class="total-amount">Rp {{ number_format($registration->total_payment > 0 ? $registration->total_payment : $calculatedTotalPayment, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Signature Box on the bottom right -->
            <div class="sig-block">
                <div class="loc-date">Bogor, {{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y') : ($registration->created_at ? $registration->created_at->translatedFormat('d F Y') : date('d F Y')) }}</div>
                <div class="role">Bendahara Panitia Pelaksana,</div>
                <div class="inst-title">PANITIA {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA IV') }}<br>TAHUN 2026</div>
                <div class="inst-sub">PMR Wira SMAN 1 Ciawi</div>
            </div>

            <!-- Lifebuoy QR Code Box -->
            <div class="qr-wrapper">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode(route('lomba.kwitansi', ['code' => $registration->registration_code])) }}" 
                     alt="QR Validasi" 
                     loading="lazy">
                <span>SCAN VALIDASI</span>
            </div>

            <!-- Stempel Lunas Overlay over Maskot -->
            <div class="stamp-box">
                <img src="{{ file_exists(public_path('images/kwitansi/stempel_lunas_transparan.png')) ? asset('images/kwitansi/stempel_lunas_transparan.png') : asset('images/kwitansi/stempel_lunas.jpg') }}" 
                     alt="Stempel Lunas">
            </div>

            <!-- Footer Direct URL -->
            <div class="receipt-url">
                {{ route('lomba.kwitansi', ['code' => $registration->registration_code]) }}
            </div>

        </div>
    </div>

</body>
</html>
