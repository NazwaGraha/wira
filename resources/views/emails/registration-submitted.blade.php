<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Pendaftaran Lomba - {{ $registration->registration_code }}</title>
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
            background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%);
            background-color: #991b1b;
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
            color: #fecaca;
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
        /* REFINED STATUS CARD */
        .status-card {
            background-color: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            border-radius: 14px;
            padding: 16px 18px;
            text-align: center;
            margin-bottom: 22px;
            box-shadow: 0 2px 6px rgba(22, 163, 74, 0.05);
        }
        .status-pill {
            display: inline-block;
            background-color: #16a34a;
            color: #ffffff;
            font-weight: 800;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 4px 14px;
            border-radius: 9999px;
            text-transform: uppercase;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2);
        }
        .status-title {
            color: #14532d;
            font-size: 15px;
            font-weight: 800;
            margin: 0 0 5px 0;
            line-height: 1.3;
        }
        .status-sub {
            color: #166534;
            font-size: 12px;
            margin: 0;
            font-weight: 500;
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
        .btn-whatsapp {
            background-color: #16a34a;
            color: #ffffff !important;
        }
        .btn-red {
            background-color: #991b1b;
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
            border: 1.5px solid #e2e8f0;
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
                Tanda Terima Pendaftaran Lomba Resmi &bull; PMR Wira SMAN 1 Ciawi Kab. Bogor
            </p>
        </div>

        <!-- Body -->
        <div class="content">

            <!-- Clean Status Card -->
            <div class="status-card">
                <div class="status-pill">&#9679; STATUS: PENDAFTARAN DITERIMA</div>
                <h2 class="status-title">Formulir &amp; Bukti Transfer Berhasil Masuk ke Sistem Panitia</h2>
                <p class="status-sub">
                    Kode Registrasi: <strong style="color: #991b1b; font-family: monospace; font-size: 13px;">{{ $registration->registration_code }}</strong> &bull; Estimasi Verifikasi Bendahara: <strong>2 x 24 Jam Kerja</strong>
                </p>
            </div>

            <div style="margin-bottom: 18px; font-size: 14px; color: #475569;">
                Kepada Yth. Bapak/Ibu Pembina PMR &amp; Kontingen<br>
                <strong style="color: #0f172a; font-size: 16px;">{{ $registration->school_name }}</strong>
            </div>

            <!-- WhatsApp Group Invitation Banner -->
            <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1.5px solid #86efac; border-radius: 14px; padding: 18px 20px; text-align: center; margin: 20px 0; box-shadow: 0 3px 10px rgba(22, 163, 74, 0.08);">
                <div style="display: inline-block; background-color: #16a34a; color: #ffffff; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 12px; border-radius: 9999px; margin-bottom: 8px;">
                    &#128172; WADAH KOMUNIKASI RESMI PESERTA
                </div>
                <h3 style="margin: 0 0 6px 0; font-size: 16px; font-weight: 900; color: #14532d;">
                    Grup WhatsApp Peserta Tingkat PMR {{ $registration->level }}
                </h3>
                <p style="margin: 0 0 14px 0; font-size: 13px; color: #166534; line-height: 1.5;">
                    Seluruh pendamping / pembina kontingen diwajibkan bergabung ke grup resmi untuk koordinasi teknis, informasi Technical Meeting (TM), dan pengundian nomor urut tampil.
                </p>
                <a href="{{ $registration->whatsapp_group_url }}" target="_blank" style="display: inline-block; background-color: #16a34a; color: #ffffff !important; font-weight: 900; font-size: 13px; padding: 12px 24px; border-radius: 12px; text-decoration: none; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.35); text-transform: uppercase; letter-spacing: 0.3px;">
                    &#128242; Gabung Grup WhatsApp (PMR {{ $registration->level }}) &rarr;
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="btn-container">
                <a href="{{ $registration->whatsapp_group_url }}" target="_blank" class="btn btn-whatsapp" style="background-color: #16a34a; color: #ffffff !important;">
                    &#128172; Join Grup WA ({{ $registration->level }})
                </a>
                <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn btn-red">
                    &#11015;&#65039; Unduh e-Kwitansi (PDF)
                </a>
                <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn btn-dark">
                    &#11015;&#65039; Unduh Kartu Peserta (PDF)
                </a>
                <a href="{{ url('/lomba/status?code=' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn btn-blue">
                    &#128269; Cek Status Web
                </a>
            </div>

            <!-- Help Desk Note Box -->
            <div class="help-box">
                <h4 class="help-box-title">&#128233; Ada Pertanyaan Seputar Pendaftaran atau Kendala Teknis?</h4>
                <p class="help-box-text">
                    Bila ada pertanyaan seputar pendaftaran lomba, silakan kirim email ke: <a href="mailto:pmrsman1ciawibogor@gmail.com" style="color: #1e40af; font-weight: 800; text-decoration: underline;">pmrsman1ciawibogor@gmail.com</a> atau hubungi WhatsApp Humas Panitia: <strong style="color: #1e3a8a;">0812-9214-3079</strong>.
                </p>
            </div>

            <!-- Attachments Tray -->
            <div class="attachment-tray">
                <div class="attachment-tray-header">
                    &#128206; Berkas Lampiran PDF Terlampir (Siap Unduh Langsung):
                </div>
                <table class="pdf-chip-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr class="pdf-chip-row">
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="width: 38px; vertical-align: middle;">
                                        <div style="background-color: #fee2e2; color: #dc2626; width: 34px; height: 34px; border-radius: 6px; text-align: center; line-height: 34px; font-size: 11px; font-weight: 900;">
                                            PDF
                                        </div>
                                    </td>
                                    <td style="padding-left: 10px; vertical-align: middle;">
                                        <div style="font-size: 13px; font-weight: 700; color: #0f172a;">
                                            e-Kwitansi-{{ $registration->registration_code }}.pdf
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">
                                            Tanda Terima Resmi Berstempel Panitia
                                        </div>
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" style="background-color: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-block;">
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
                                        <div style="background-color: #fee2e2; color: #dc2626; width: 34px; height: 34px; border-radius: 6px; text-align: center; line-height: 34px; font-size: 11px; font-weight: 900;">
                                            PDF
                                        </div>
                                    </td>
                                    <td style="padding-left: 10px; vertical-align: middle;">
                                        <div style="font-size: 13px; font-weight: 700; color: #0f172a;">
                                            Kartu-Peserta-{{ $registration->registration_code }}.pdf
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">
                                            Kartu Tanda Peserta Lomba Siap Cetak
                                        </div>
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" style="background-color: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-block;">
                                            &#11015; Unduh
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p style="margin: 8px 0 0 0; font-size: 11px; color: #166534;">
                    &#10004; File PDF di atas juga otomatis tersemat sebagai attachment berkas fisik di bagian bawah email ini.
                </p>
            </div>

            <!-- Ringkasan Data Kontingen -->
            <div class="info-card">
                <div class="info-card-title">Ringkasan Data Kontingen</div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Kode Pendaftaran</td>
                        <td class="meta-val" style="font-family: monospace; color: #991b1b; font-size: 14px;">{{ $registration->registration_code }}</td>
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
                        <td class="meta-label">Cabang Lomba Diikuti</td>
                        <td class="meta-val">
                            @if($registration->teams && $registration->teams->isNotEmpty())
                                @foreach($registration->teams as $team)
                                    <div>&bull; {{ $team->category->name ?? 'Lomba' }} @if($team->team_label)(Regu {{ $team->team_label }})@endif</div>
                                @endforeach
                            @else
                                <em>Belum ada regu</em>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Waktu Pendaftaran</td>
                        <td class="meta-val">{{ $registration->created_at ? $registration->created_at->translatedFormat('d F Y, H:i') : date('d F Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Total Biaya Pendaftaran</td>
                        <td class="meta-val" style="color: #15803d; font-size: 14px;">Rp {{ number_format($registration->total_payment, 0, ',', '.') }} (Bukti Transfer Terlampir)</td>
                    </tr>
                </table>
            </div>

            <!-- Petunjuk Langkah Selanjutnya -->
            <div class="info-card" style="background-color: #fffbeb; border-color: #fde68a;">
                <div class="info-card-title" style="color: #92400e; border-color: #fef3c7;">Informasi Langkah Selanjutnya</div>
                <ol style="margin: 0; padding-left: 18px; font-size: 13px; color: #78350f; line-height: 1.7;">
                    <li>Panitia akan memverifikasi bukti transfer pembayaran Anda dalam kurun waktu <strong>2 x 24 jam kerja</strong>.</li>
                    <li>Setelah pembayaran diverifikasi sah, status kontingen Anda akan otomatis berganti menjadi <strong>LUNAS / SAH</strong> dan email konfirmasi final akan dikirimkan.</li>
                    <li>Anda dapat memantau status pendaftaran secara mandiri kapan saja melalui menu <strong>Cek Status</strong> di website dengan memasukkan Kode Registrasi: <strong style="font-family: monospace;">{{ $registration->registration_code }}</strong>.</li>
                </ol>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px 0; font-weight: bold; color: #1e293b;">
                PANITIA PELAKSANA {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA TAHUN 2026') }}
            </p>
            <p style="margin: 0 0 10px 0;">Palang Merah Remaja (PMR) Wira SMA Negeri 1 Ciawi Kabupaten Bogor</p>
            <p style="margin: 0; font-size: 11px;">
                WhatsApp Panitia: <strong>0812-9214-3079 / 0857-1049-7412</strong> &bull; Portal: <a href="{{ config('app.url', 'https://pmrwirasmanic.or.id') }}" style="color: #991b1b; text-decoration: none; font-weight: bold;">pmrwirasmanic.or.id</a>
            </p>
        </div>
    </div>

</body>
</html>
