<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>e-Kwitansi - {{ $registration->registration_code }}</title>
    <style>
        @page {
            margin: 24px 30px;
            size: a4 portrait;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 11px;
            font-weight: bold;
            color: #b91c1c;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .header-address {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }
        .badge-box {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 6px 12px;
            text-align: center;
        }
        .code-title {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
        }
        .code-val {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            font-family: monospace;
            margin: 2px 0;
        }
        .status-badge {
            font-size: 8px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 9999px;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-verified {
            background-color: #059669;
            color: #ffffff;
        }
        .status-pending {
            background-color: #d97706;
            color: #ffffff;
        }
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 12px 0 16px 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .meta-table td {
            padding: 3px 4px;
            vertical-align: top;
            font-size: 10px;
        }
        .meta-label {
            width: 32%;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        .meta-val {
            color: #0f172a;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 7px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .items-table tfoot td {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 2px solid #0f172a;
            padding: 8px;
        }
        .footer-table {
            width: 100%;
            margin-top: 20px;
        }
        .stamp-box {
            border: 2px dashed #059669;
            color: #059669;
            background-color: #ecfdf5;
            padding: 6px 12px;
            border-radius: 8px;
            text-align: center;
            display: inline-block;
        }
        .stamp-pending {
            border: 2px dashed #d97706;
            color: #d97706;
            background-color: #fffbeb;
            padding: 6px 12px;
            border-radius: 8px;
            text-align: center;
            display: inline-block;
        }
    </style>
</head>
<body>

    @php
        $logoPath = file_exists(public_path('images/logo_pmi_sman1ciawi.png')) 
            ? public_path('images/logo_pmi_sman1ciawi.png') 
            : public_path('images/logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- Kop Surat Resmi -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 15%; vertical-align: middle;">
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" style="height: 48px; width: auto;" alt="Logo">
                @endif
            </td>
            <td style="width: 55%; vertical-align: middle; padding-left: 10px;">
                <div class="header-title">PANITIA {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA TAHUN 2026') }}</div>
                <div class="header-subtitle">PMR WIRA SMAN 1 CIAWI KAB. BOGOR</div>
                <div class="header-address">Jl. Veteran III No. 01 Ciawi, Bogor &bull; Email: pmrwirasman1c@gmail.com &bull; Web: pmrwirasmanic.or.id</div>
            </td>
            <td style="width: 30%; vertical-align: middle; text-align: right;">
                <div class="badge-box">
                    <div class="code-title">TANDA TERIMA REGISTRASI</div>
                    <div class="code-val">{{ $registration->registration_code }}</div>
                    @if($registration->status === 'verified')
                        <span class="status-badge status-verified">&#10004; LUNAS / SAH</span>
                    @else
                        <span class="status-badge status-pending">&#9203; MENUNGGU VERIFIKASI</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        e-Kwitansi Tanda Terima Pendaftaran Lomba
    </div>

    <!-- Data Kontingen / Peserta -->
    <table class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="meta-label">Nama Sekolah / Unit</td>
            <td class="meta-val">: {{ $registration->school_name }}</td>
            <td class="meta-label">Kode Pendaftaran</td>
            <td class="meta-val">: {{ $registration->registration_code }}</td>
        </tr>
        <tr>
            <td class="meta-label">Jenjang Tingkat PMR</td>
            <td class="meta-val">: PMR {{ $registration->level }} ({{ $registration->level == 'Mula' ? 'SD / MI' : ($registration->level == 'Madya' ? 'SMP / MTs' : 'SMA / SMK / MA') }})</td>
            <td class="meta-label">Gelombang</td>
            <td class="meta-val">: {{ ($registration->registration_wave ?? 1) == 2 ? ($registration->event->wave2_name ?: 'Gelombang 2') : ($registration->event->wave1_name ?: 'Gelombang 1') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Pembina / Pendamping</td>
            <td class="meta-val">: {{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</td>
            <td class="meta-label">Tanggal Daftar</td>
            <td class="meta-val">: {{ $registration->created_at ? $registration->created_at->translatedFormat('d F Y, H:i') : date('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Alamat Email Pembina</td>
            <td class="meta-val">: {{ $registration->advisor_email ?: '-' }}</td>
            <td class="meta-label">Status Verifikasi</td>
            <td class="meta-val">: 
                @if($registration->status === 'verified')
                    <strong style="color: #059669;">LUNAS (Terverifikasi Panitia)</strong>
                @else
                    <strong style="color: #d97706;">Pending (Menunggu Pengecekan Bukti Transfer)</strong>
                @endif
            </td>
        </tr>
    </table>

    <!-- Rincian Cabang Lomba -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">No</th>
                <th style="width: 44%;">Cabang Mata Lomba</th>
                <th style="width: 25%;">Nama Regu / Keterangan</th>
                <th style="width: 25%; text-align: right;">Biaya Pendaftaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registration->teams as $index => $team)
                @php
                    $teamFee = $team->category ? $team->category->getFeeForWave($registration->registration_wave ?: 1) : floatval($registration->event->registration_fee ?: 150000);
                @endphp
                <tr>
                    <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $team->category->name ?? 'Cabang Lomba' }} ({{ $team->category->gender_category ?? 'Umum' }})</td>
                    <td style="color: #475569;">{{ $team->team_name }}</td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold;">Rp {{ number_format($teamFee, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-size: 10px; text-transform: uppercase;">Total Biaya Pendaftaran:</td>
                <td style="text-align: right; font-size: 12px; font-family: monospace; color: #b91c1c;">Rp {{ number_format($registration->total_payment, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan & Pengesahan -->
    <table class="footer-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                @if($registration->status === 'verified')
                    <div class="stamp-box">
                        <div style="font-size: 11px; font-weight: bold;">&#10004; SAH & TERVERIFIKASI</div>
                        <div style="font-size: 8px; font-family: monospace; margin-top: 2px;">
                            {{ $registration->verified_at ? $registration->verified_at->format('d/m/Y H:i') : date('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                @else
                    <div class="stamp-pending">
                        <div style="font-size: 11px; font-weight: bold;">&#9203; TANDA TERIMA SISTEM</div>
                        <div style="font-size: 8px; font-family: monospace; margin-top: 2px;">
                            Menunggu Verifikasi Bendahara
                        </div>
                    </div>
                @endif
                <div style="font-size: 8px; color: #94a3b8; margin-top: 8px;">
                    * e-Kwitansi ini diterbitkan otomatis oleh Sistem Registrasi PMR WIRA SMAN 1 CIAWI.
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: center;">
                <div style="font-size: 10px; color: #475569; margin-bottom: 45px;">
                    Bogor, {{ $registration->created_at ? $registration->created_at->translatedFormat('d F Y') : date('d F Y') }}<br>
                    <strong>Panitia Pelaksana,</strong>
                </div>
                <div style="font-size: 10px; font-weight: bold; border-top: 1px solid #0f172a; display: inline-block; padding-top: 3px; min-width: 170px;">
                    PANITIA {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA') }}
                </div>
                <div style="font-size: 8px; color: #64748b;">PMR Wira SMAN 1 Ciawi</div>
            </td>
        </tr>
    </table>

</body>
</html>
