<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Terverifikasi Sah - {{ $registration->registration_code }}</title>
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
            max-width: 640px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.07);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #065f46 0%, #047857 50%, #064e3b 100%);
            background-color: #047857;
            padding: 26px 22px 20px 22px;
            text-align: center;
            color: #ffffff;
        }
        .logo-box {
            background-color: #ffffff;
            padding: 8px 18px;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            margin-bottom: 14px;
        }
        .logo-img {
            height: 48px;
            max-height: 52px;
            width: auto;
            display: block;
            border: 0;
            margin: 0 auto;
        }
        .header-title {
            font-size: 20px;
            font-weight: 900;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
            margin: 0 0 4px 0;
        }
        .header-subtitle {
            color: #a7f3d0;
            font-size: 13px;
            font-weight: 600;
            margin: 0;
            letter-spacing: 0.3px;
        }
        .content {
            padding: 26px 24px;
            line-height: 1.6;
            font-size: 14px;
            color: #1e293b;
        }
        /* CELEBRATION STATUS CARD */
        .status-verified-card {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border: 2px solid #34d399;
            border-radius: 14px;
            padding: 18px 20px;
            text-align: center;
            margin-bottom: 22px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
        }
        .verified-pill {
            display: inline-block;
            background-color: #059669;
            color: #ffffff;
            font-weight: 800;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 4px 14px;
            border-radius: 9999px;
            text-transform: uppercase;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.25);
        }
        .status-title {
            color: #064e3b;
            font-size: 16px;
            font-weight: 900;
            margin: 0 0 6px 0;
            line-height: 1.3;
        }
        .status-sub {
            color: #047857;
            font-size: 12.5px;
            margin: 0;
            font-weight: 500;
            line-height: 1.5;
        }
        /* ACTION BUTTONS */
        .btn-container {
            text-align: center;
            margin: 20px 0 24px 0;
        }
        .btn {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 12px;
            text-decoration: none;
            margin: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }
        .btn-green {
            background-color: #059669;
            color: #ffffff !important;
        }
        .btn-dark {
            background-color: #0f172a;
            color: #ffffff !important;
        }
        .btn-blue {
            background-color: #0284c7;
            color: #ffffff !important;
        }
        /* INFO TABLE */
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 20px;
        }
        .info-card-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .meta-table td {
            padding: 7px 0;
            vertical-align: top;
            border-bottom: 1px dashed #e2e8f0;
        }
        .meta-table tr:last-child td {
            border-bottom: none;
        }
        .meta-label {
            width: 38%;
            color: #64748b;
            font-weight: 600;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 700;
        }
        /* NOTICE BOX */
        .notice-box {
            background-color: #fffbeb;
            border: 1.5px solid #fde68a;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .notice-box-title {
            color: #92400e;
            font-size: 13px;
            font-weight: 800;
            margin: 0 0 6px 0;
        }
        .notice-box ol {
            margin: 0;
            padding-left: 18px;
            font-size: 12px;
            color: #78350f;
            line-height: 1.6;
        }
        /* HELP DESK NOTE BOX */
        .help-box {
            background-color: #eff6ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .help-box-title {
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 800;
            margin: 0 0 5px 0;
        }
        .help-box-text {
            color: #1e40af;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            font-weight: 500;
        }
        /* ATTACHMENTS TRAY */
        .attachment-tray {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 22px;
        }
        .attachment-tray-header {
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pdf-chip-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        .pdf-chip-row td {
            background-color: #ffffff;
            border: 1.5px solid #a7f3d0;
            border-radius: 10px;
            padding: 10px 14px;
            vertical-align: middle;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
        }
        @media screen and (max-width: 600px) {
            .container {
                border-radius: 0 !important;
                border: none !important;
            }
            .content {
                padding: 18px 14px !important;
            }
            .btn {
                display: block !important;
                margin: 8px 0 !important;
                width: auto !important;
                padding: 13px !important;
                font-size: 13px !important;
            }
            .logo-img {
                height: 40px !important;
            }
            .header-title {
                font-size: 17px !important;
            }
            .meta-table td {
                display: block !important;
                width: 100% !important;
                padding: 3px 0 !important;
            }
            .meta-label {
                font-size: 11px !important;
                color: #64748b !important;
            }
            .meta-val {
                margin-bottom: 8px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 20px 10px; background-color: #f1f5f9;">

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-box">
                <img src="{{ isset($message) ? $message->embed(public_path('images/logo_pmi_sman1ciawi.png')) : asset('images/logo_pmi_sman1ciawi.png') }}" 
                     alt="Logo PMI | SMA Negeri 1 Ciawi" 
                     class="logo-img" />
            </div>
            <h1 class="header-title">
                {{ !empty($registration->event->title) ? strtoupper($registration->event->title) : 'SUA BHAKTI BERKARYA TAHUN 2026' }}
            </h1>
            <p class="header-subtitle">
                Konfirmasi Verifikasi Pendaftaran &amp; Pembayaran Sah &bull; PMR Wira SMAN 1 Ciawi
            </p>
        </div>

        <!-- Body -->
        <div class="content">

            <!-- Verified Celebration Card -->
            <div class="status-verified-card">
                <div class="verified-pill">&#9679; STATUS: TERVERIFIKASI SAH &amp; LUNAS</div>
                <h2 class="status-title">Selamat! Pendaftaran &amp; Pembayaran Kontingen Anda Dinyatakan SAH</h2>
                <p class="status-sub">
                    Bukti pembayaran telah diverifikasi resmi oleh Bendahara Panitia. Berkas fisik <strong>e-Kwitansi Sah</strong> dan <strong>Kartu Peserta Resmi</strong> siap diunduh &amp; dicetak di bawah ini.
                </p>
            </div>

            <div style="margin-bottom: 18px; font-size: 14px; color: #475569;">
                Kepada Yth. Bapak/Ibu Pembina PMR &amp; Kontingen<br>
                <strong style="color: #0f172a; font-size: 16px;">{{ $registration->school_name }}</strong>
            </div>

            <!-- Action Buttons -->
            <div class="btn-container">
                <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn btn-green">
                    &#11015;&#65039; Unduh e-Kwitansi Sah (PDF)
                </a>
                <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn btn-dark">
                    &#11015;&#65039; Unduh Kartu Peserta Resmi (PDF)
                </a>
                <a href="{{ url('/lomba/status?code=' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn btn-blue">
                    &#128269; Cek Status Web
                </a>
            </div>

            <!-- Notice Box: Petunjuk Penting Bagi Kontingen -->
            <div class="notice-box">
                <h4 class="notice-box-title">&#9888; Petunjuk Penting Bagi Kontingen:</h4>
                <ol>
                    <li>Harap unduh dan cetak <strong>e-Kwitansi Sah</strong> serta <strong>Kartu Peserta Resmi</strong> sebelum menghadiri kegiatan.</li>
                    <li>Kartu Peserta wajib dicetak dan dikenakan oleh seluruh anggota regu saat pelaksanaan lomba di SMA Negeri 1 Ciawi.</li>
                    <li>e-Kwitansi ini adalah bukti pembayaran yang <strong>SAH &amp; RESMI</strong> dari Panitia Pelaksana tanpa perlu stempel basah manual.</li>
                </ol>
            </div>

            <!-- Help Desk Note Box -->
            <div class="help-box">
                <h4 class="help-box-title">&#128233; Ada Pertanyaan Seputar Pendaftaran atau Pelaksanaan Lomba?</h4>
                <p class="help-box-text">
                    Bila ada pertanyaan seputar pendaftaran lomba, silakan kirim email ke: <a href="mailto:pmrsman1ciawibogor@gmail.com" style="color: #1e40af; font-weight: 800; text-decoration: underline;">pmrsman1ciawibogor@gmail.com</a> atau hubungi WhatsApp Humas Panitia: <strong style="color: #1e3a8a;">0812-9214-3079</strong>.
                </p>
            </div>

            <!-- Attachments Tray -->
            <div class="attachment-tray">
                <div class="attachment-tray-header">
                    &#128206; Berkas Resmi Terlampir (Siap Diunduh Langsung):
                </div>
                <table class="pdf-chip-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr class="pdf-chip-row">
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="width: 38px; vertical-align: middle;">
                                        <div style="background-color: #dcfce7; color: #059669; width: 34px; height: 34px; border-radius: 6px; text-align: center; line-height: 34px; font-size: 11px; font-weight: 900;">
                                            PDF
                                        </div>
                                    </td>
                                    <td style="padding-left: 10px; vertical-align: middle;">
                                        <div style="font-size: 13px; font-weight: 700; color: #0f172a;">
                                            e-Kwitansi-Sah-{{ $registration->registration_code }}.pdf
                                        </div>
                                        <div style="font-size: 11px; color: #059669; font-weight: 600;">
                                            &#10004; Lunas &bull; Berstempel Sah Panitia
                                        </div>
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" style="background-color: #059669; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-block;">
                                            &#11015; Unduh
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="pdf-chip-row">
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="width: 38px; vertical-align: middle;">
                                        <div style="background-color: #dcfce7; color: #059669; width: 34px; height: 34px; border-radius: 6px; text-align: center; line-height: 34px; font-size: 11px; font-weight: 900;">
                                            PDF
                                        </div>
                                    </td>
                                    <td style="padding-left: 10px; vertical-align: middle;">
                                        <div style="font-size: 13px; font-weight: 700; color: #0f172a;">
                                            Kartu-Peserta-Resmi-{{ $registration->registration_code }}.pdf
                                        </div>
                                        <div style="font-size: 11px; color: #059669; font-weight: 600;">
                                            &#10004; Barcode Sah &bull; Siap Cetak
                                        </div>
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" style="background-color: #059669; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-block;">
                                            &#11015; Unduh
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p style="margin: 8px 0 0 0; font-size: 11px; color: #166534;">
                    &#10004; File PDF resmi di atas juga otomatis tersemat sebagai attachment berkas fisik di bagian bawah email ini.
                </p>
            </div>

            <!-- Ringkasan Data Kontingen -->
            <div class="info-card">
                <div class="info-card-title">Ringkasan Data Kontingen Terverifikasi</div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Kode Pendaftaran</td>
                        <td class="meta-val" style="font-family: monospace; color: #059669; font-size: 14px;">{{ $registration->registration_code }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Status Verifikasi</td>
                        <td class="meta-val">
                            <span style="background-color: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 4px; font-weight: 800; font-size: 11px;">
                                LUNAS / TERVERIFIKASI PANITIA
                            </span>
                        </td>
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
                        <td class="meta-label">Cabang Lomba &amp; Regu Resmi</td>
                        <td class="meta-val">
                            @if($registration->teams && $registration->teams->isNotEmpty())
                                @foreach($registration->teams as $team)
                                    <div>&bull; {{ $team->category->name ?? 'Lomba' }} @if($team->team_label)(Regu {{ $team->team_label }})@endif &mdash; <strong style="color: #059669;">Sah</strong></div>
                                @endforeach
                            @else
                                <em>Belum ada regu</em>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Waktu Verifikasi Panitia</td>
                        <td class="meta-val">
                            {{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y, H:i') : date('d F Y, H:i') }} WIB 
                            @if($registration->verified_by)
                                (Oleh: {{ $registration->verified_by }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Total Pembayaran Sah</td>
                        <td class="meta-val" style="color: #059669; font-size: 14px;">
                            Rp {{ number_format($registration->total_payment, 0, ',', '.') }} (Lunas / Pembayaran Sah)
                        </td>
                    </tr>
                </table>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px 0; font-weight: bold; color: #1e293b;">
                PANITIA PELAKSANA {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA TAHUN 2026') }}
            </p>
            <p style="margin: 0 0 10px 0;">Palang Merah Remaja (PMR) Wira SMA Negeri 1 Ciawi Kabupaten Bogor</p>
            <p style="margin: 0; font-size: 11px;">
                WhatsApp Panitia: <strong>0812-9214-3079 / 0857-1049-7412</strong> &bull; Portal: <a href="{{ config('app.url', 'https://pmrwirasmanic.or.id') }}" style="color: #059669; text-decoration: none; font-weight: bold;">pmrwirasmanic.or.id</a>
            </p>
        </div>
    </div>

</body>
</html>
