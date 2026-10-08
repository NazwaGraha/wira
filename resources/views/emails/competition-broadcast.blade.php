<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $headline ?? 'Informasi Kegiatan & Lomba SUA BHAKTI BERKARYA' }}</title>
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
            font-size: 22px;
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
            padding: 34px 30px;
            line-height: 1.7;
            font-size: 14px;
            color: #1e293b;
        }
        .greeting {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .greeting-name {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            display: block;
            margin-top: 2px;
        }
        .message-body {
            margin-bottom: 24px;
            color: #334155;
            line-height: 1.8;
            font-size: 14px;
        }
        .message-body p {
            margin: 0 0 14px 0;
        }
        .message-body img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 8px;
        }
        .message-body img.note-float-center,
        .message-body img[style*="margin-left: auto"],
        .message-body img[style*="margin-left:auto"],
        .message-body img[style*="margin: auto"],
        .message-body img[style*="margin:auto"],
        .message-body p[style*="text-align: center"] img,
        .message-body p[style*="text-align:center"] img {
            display: block !important;
            margin: 16px auto !important;
            float: none !important;
            clear: both !important;
        }
        .message-body img[style*="float: left"],
        .message-body img[style*="float:left"] {
            float: left !important;
            margin: 4px 16px 14px 0 !important;
        }
        .message-body img[style*="float: right"],
        .message-body img[style*="float:right"] {
            float: right !important;
            margin: 4px 0 14px 16px !important;
        }
        .message-body table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 18px 0 !important;
            font-size: 13px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            overflow: hidden !important;
        }
        .message-body th {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            font-weight: 800 !important;
            padding: 10px 14px !important;
            border: 1px solid #cbd5e1 !important;
            text-align: left !important;
        }
        .message-body td {
            padding: 10px 14px !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
        }
        .cta-container {
            text-align: center;
            margin: 32px 0 24px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #b91c1c;
            color: #ffffff !important;
            font-weight: 800;
            font-size: 14px;
            padding: 14px 34px;
            text-decoration: none;
            border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(185, 28, 28, 0.35);
            text-transform: none;
            letter-spacing: 0.3px;
        }
        .card-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #b91c1c;
            border-radius: 12px;
            padding: 16px 20px;
            margin: 26px 0;
            font-size: 13px;
            color: #334155;
            line-height: 1.6;
        }
        .closing-section {
            margin-top: 32px;
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px 28px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }
    </style>
</head>
<body style="margin: 0; padding: 24px 12px; background-color: #f1f5f9;">

    <div class="container">
        <!-- Header -->
        <div class="header">
            <table class="header-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <!-- Kiri: Logo Resmi Palang Merah Indonesia & SMA Negeri 1 Ciawi -->
                    <td style="vertical-align: middle; text-align: left;">
                        <div class="logo-box">
                            <img src="{{ isset($message) ? $message->embed(public_path('images/logo_pmi_sman1ciawi.png')) : asset('images/logo_pmi_sman1ciawi.png') }}" 
                                 alt="Palang Merah Indonesia | SMA Negeri 1 Ciawi" 
                                 class="logo-img" />
                        </div>
                    </td>

                    <!-- Kanan: Judul Kegiatan SUA BHAKTI BERKARYA -->
                    <td style="vertical-align: middle; text-align: right;">
                        <div class="header-title">
                            SUA BHAKTI<br>BERKARYA
                        </div>
                    </td>
                </tr>
                <tr>
                    <!-- Baris Tengah: Badge Status & Identitas PMR Wira SMAN 1 Ciawi -->
                    <td colspan="2" style="text-align: center; padding-top: 18px;">
                        <div class="badge-status">
                            &#9432; INFORMASI RESMI KEGIATAN
                        </div>
                        <div class="header-subtitle">
                            PMR Wira SMA Negeri 1 Ciawi
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Body -->
        <div class="content">
            <div class="greeting">
                Kepada Yth.<br>
                <span class="greeting-name">
                    Pembina PMR & Kontingen {{ !empty($recipientInfo['school_name']) ? $recipientInfo['school_name'] : 'Sekolah / Unit PMR' }}
                </span>
            </div>

            <div class="message-body">
                {!! $contentBody !!}
            </div>

            @if(!empty($buttonText) && !empty($buttonUrl))
                <div class="cta-container">
                    <a href="{{ $buttonUrl }}" class="cta-button" target="_blank">
                        {{ $buttonText }}
                    </a>
                </div>
            @endif

            @if(!empty($notes))
                <div class="card-box">
                    <strong style="color: #0f172a; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">NOTICE :</strong><br>
                    {!! nl2br(e($notes)) !!}
                </div>
            @endif

            <div class="closing-section">
                Salam Kemanusiaan,<br>
                <strong style="color: #0f172a; font-size: 14px;">Panitia Pelaksana SUA BHAKTI BERKARYA</strong><br>
                <span style="color: #64748b;">PMR WIRA SMA Negeri 1 Ciawi</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px 0; font-weight: 800; color: #1e293b; font-size: 13px;">
                PMR WIRA SMA NEGERI 1 CIAWI &bull; SUA BHAKTI BERKARYA
            </p>
            <p style="margin: 0 0 10px 0; font-size: 12px; color: #64748b;">
                Email ini dikirimkan resmi kepada seluruh kontak sekolah dan pembina unit PMR yang terdaftar pada sistem kegiatan kami.
            </p>
            <p style="margin: 0 0 12px 0; font-size: 11px; color: #94a3b8;">
                WhatsApp Panitia: <strong>0812-9214-3079 / 0857-1049-7412</strong> &bull; Portal: <a href="https://wira.nazwagraha.com" style="color: #b91c1c; text-decoration: none; font-weight: bold;">wira.nazwagraha.com</a>
            </p>
            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                &copy; {{ date('Y') }} PMR Wira SMA Negeri 1 Ciawi. Hak Cipta Dilindungi.
            </p>
        </div>
    </div>

</body>
</html>
