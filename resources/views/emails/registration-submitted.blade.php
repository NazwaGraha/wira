<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Lomba Diterima - {{ $registration->registration_code }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-text-size-adjust: 100%;
        }
        .container {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #b91c1c 0%, #880808 100%);
            background-color: #991b1b;
            padding: 28px 24px 22px 24px;
            text-align: left;
            color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-box {
            background-color: #ffffff;
            padding: 6px 14px;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }
        .logo-img {
            height: 38px;
            max-height: 42px;
            width: auto;
            display: block;
            border: 0;
        }
        .header-title {
            font-size: 20px;
            font-weight: 900;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.15;
            text-align: right;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .badge-status {
            display: inline-block;
            background-color: #ffffff;
            color: #991b1b;
            font-weight: 800;
            font-size: 11px;
            padding: 5px 18px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .header-subtitle {
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            margin-top: 8px;
            letter-spacing: 0.3px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        .content {
            padding: 32px 28px;
            line-height: 1.7;
            font-size: 14px;
            color: #1e293b;
        }
        .pending-banner {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fde68a;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin-bottom: 26px;
        }
        .pending-stamp {
            display: inline-block;
            background-color: #d97706;
            color: #ffffff;
            font-weight: 900;
            font-size: 11px;
            letter-spacing: 1px;
            padding: 4px 14px;
            border-radius: 9999px;
            margin-bottom: 10px;
        }
        .pending-title {
            color: #92400e;
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 6px 0;
        }
        .pending-desc {
            color: #78350f;
            font-size: 13px;
            margin: 0;
            line-height: 1.6;
        }
        .btn-action-container {
            text-align: center;
            margin: 24px 0 30px 0;
        }
        .btn-action {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 13px;
            text-decoration: none;
            margin: 4px;
        }
        .btn-status {
            background-color: #dc2626;
            color: #ffffff !important;
        }
        .btn-receipt {
            background-color: #0f172a;
            color: #ffffff !important;
        }
        .btn-cards {
            background-color: #2563eb;
            color: #ffffff !important;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 22px;
        }
        .info-card-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .meta-table td {
            padding: 6px 0;
            vertical-align: top;
        }
        .meta-label {
            width: 36%;
            color: #64748b;
            font-weight: 600;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 700;
        }
        .attachment-note {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 22px;
            font-size: 13px;
            color: #166534;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body style="margin: 0; padding: 24px 12px; background-color: #f1f5f9;">

    <div class="container">
        <!-- Header -->
        <div class="header">
            <table class="header-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="vertical-align: middle; text-align: left;">
                        <div class="logo-box">
                            <img src="{{ isset($message) ? $message->embed(public_path('images/logo_pmi_sman1ciawi.png')) : asset('images/logo_pmi_sman1ciawi.png') }}" 
                                 alt="PMI | SMAN 1 Ciawi" 
                                 class="logo-img" />
                        </div>
                    </td>
                    <td style="vertical-align: middle; text-align: right;">
                        <div class="header-title">
                            {{ !empty($registration->event->title) ? strtoupper($registration->event->title) : 'SUA BHAKTI BERKARYA' }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 16px;">
                        <div class="badge-status">
                            &#9432; TANDA TERIMA PENDAFTARAN LOMBA RESMI
                        </div>
                        <div class="header-subtitle">
                            PMR Wira SMA Negeri 1 Ciawi &bull; Kab. Bogor
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Body -->
        <div class="content">

            <!-- Pending Notification Banner -->
            <div class="pending-banner">
                <span class="pending-stamp">STATUS: PENDAFTARAN DITERIMA</span>
                <h2 class="pending-title">&#10004; Formulir & Bukti Transfer Berhasil Dikirim</h2>
                <p class="pending-desc">
                    Terima kasih! Formulir pendaftaran dan bukti transfer kontingen <strong>{{ $registration->school_name }}</strong> telah masuk ke sistem panitia dan saat ini dalam antrean verifikasi bendahara.
                </p>
            </div>

            <div style="margin-bottom: 16px; font-size: 14px; color: #475569;">
                Kepada Yth. Bapak/Ibu Pembina PMR & Kontingen<br>
                <strong style="color: #0f172a; font-size: 16px;">{{ $registration->school_name }}</strong>
            </div>

            <!-- Attachment Highlight Box -->
            <div class="attachment-note">
                <strong>&#128206; Berkas Lampiran PDF Terlampir:</strong><br>
                Kami telah melampirkan berkas dokumen resmi pada email ini:
                <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                    <li><strong>e-Kwitansi Tanda Terima Pendaftaran (PDF)</strong></li>
                    <li><strong>Kartu Peserta Lomba (PDF)</strong></li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="btn-action-container">
                <a href="{{ url('/lomba/status?code=' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn-action btn-status">
                    &#128269; Cek Status Pendaftaran Online
                </a>
                <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn-action btn-receipt">
                    &#128196; Buka e-Kwitansi Web
                </a>
                <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn-action btn-cards">
                    &#129525; Buka Kartu Peserta
                </a>
            </div>

            <!-- Ringkasan Data Kontingen -->
            <div class="info-card">
                <div class="info-card-title">Ringkasan Data Kontingen</div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Kode Pendaftaran</td>
                        <td class="meta-val" style="font-family: monospace; color: #b91c1c; font-size: 15px;">{{ $registration->registration_code }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Nama Sekolah / Unit</td>
                        <td class="meta-val">{{ $registration->school_name }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jenjang Tingkat PMR</td>
                        <td class="meta-val">PMR {{ $registration->level }} ({{ $registration->level == 'Mula' ? 'SD / MI' : ($registration->level == 'Madya' ? 'SMP / MTs' : 'SMA / SMK / MA') }})</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Pembina / Pendamping</td>
                        <td class="meta-val">{{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Email Terdaftar</td>
                        <td class="meta-val">{{ $registration->advisor_email }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Waktu Pendaftaran</td>
                        <td class="meta-val">{{ $registration->created_at ? $registration->created_at->translatedFormat('d F Y, H:i') : date('d F Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Total Pembayaran</td>
                        <td class="meta-val" style="color: #b91c1c; font-size: 14px;">Rp {{ number_format($registration->total_payment, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Petunjuk Panitia -->
            <div class="info-card" style="background-color: #fffbeb; border-color: #fde68a;">
                <div class="info-card-title" style="color: #92400e; border-color: #fef3c7;">Informasi Langkah Selanjutnya</div>
                <ol style="margin: 0; padding-left: 18px; font-size: 13px; color: #78350f; line-height: 1.7;">
                    <li>Panitia akan memverifikasi bukti transfer pembayaran Anda dalam kurun waktu <strong>1x24 jam kerja</strong>.</li>
                    <li>Setelah pembayaran dinyatakan valid, status kontingen Anda akan otomatis berganti menjadi <strong>LUNAS / SAH</strong> dan email verifikasi final akan dikirimkan.</li>
                    <li>Anda dapat memantau status pendaftaran secara mandiri kapan saja melalui menu <strong>Cek Status</strong> di website dengan memasukkan Kode Pendaftaran: <strong style="font-family: monospace;">{{ $registration->registration_code }}</strong>.</li>
                </ol>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px 0; font-weight: bold; color: #1e293b;">PANITIA PELAKSANA {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA TAHUN 2026') }}</p>
            <p style="margin: 0 0 10px 0;">Palang Merah Remaja (PMR) Wira SMA Negeri 1 Ciawi Kabupaten Bogor</p>
            <p style="margin: 0; font-size: 11px;">
                WhatsApp Panitia: <strong>0812-9214-3079 / 0857-1049-7412</strong> &bull; Portal: <a href="{{ config('app.url', 'https://pmrwirasmanic.or.id') }}" style="color: #b91c1c; text-decoration: none; font-weight: bold;">pmrwirasmanic.or.id</a>
            </p>
        </div>
    </div>

</body>
</html>
