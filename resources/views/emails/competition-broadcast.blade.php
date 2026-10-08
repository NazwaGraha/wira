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
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #880808 0%, #dc2626 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .logo-text {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #fecaca;
            margin-bottom: 8px;
        }
        .title {
            font-size: 24px;
            font-weight: 900;
            line-height: 1.3;
            margin: 0;
            color: #ffffff;
        }
        .content {
            padding: 36px 32px;
            line-height: 1.7;
            font-size: 15px;
            color: #1e293b;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .message-body {
            margin-bottom: 24px;
            color: #334155;
            line-height: 1.8;
            font-size: 15px;
        }
        .message-body p {
            margin: 0 0 14px 0;
        }
        .message-body img {
            max-width: 100% !important;
            height: auto;
            border-radius: 8px;
            display: inline-block;
        }
        .message-body img[style*="float: left"],
        .message-body img[style*="float:left"] {
            margin: 4px 16px 14px 0 !important;
        }
        .message-body img[style*="float: right"],
        .message-body img[style*="float:right"] {
            margin: 4px 0 14px 16px !important;
        }
        .message-body img.note-float-center,
        .message-body img[style*="margin-left: auto"],
        .message-body img[style*="margin-left:auto"],
        .message-body img[style*="margin: auto"],
        .message-body img[style*="margin:auto"] {
            display: block !important;
            margin-left: auto !important;
            margin-right: auto !important;
            float: none !important;
        }
        .cta-container {
            text-align: center;
            margin: 32px 0 24px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #dc2626;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 15px;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }
        .card-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin: 24px 0;
            font-size: 13px;
            color: #475569;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px 32px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }
        .badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }
    </style>
</head>
<body style="padding: 24px 12px; background-color: #f1f5f9;">

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-text">PALANG MERAH REMAJA &bull; PMI</div>
            <h1 class="title">{{ $headline ?: 'SUA BHAKTI BERKARYA' }}</h1>
            <div class="badge">Official Announcement & Information</div>
        </div>

        <!-- Body -->
        <div class="content">
            @if(!empty($recipientInfo['school_name']))
                <div class="greeting">
                    Yth. Bapak/Ibu Pembina & Kontingen PMR<br>
                    <span style="color: #dc2626; font-size: 17px;">{{ $recipientInfo['school_name'] }}</span>
                </div>
            @else
                <div class="greeting">
                    Yth. Bapak/Ibu Pembina, Pelatih, & Pengurus Unit PMR
                </div>
            @endif

            <div class="message-body">{!! $contentBody !!}</div>

            @if(!empty($buttonText) && !empty($buttonUrl))
                <div class="cta-container">
                    <a href="{{ $buttonUrl }}" class="cta-button" target="_blank">
                        {{ $buttonText }} &rarr;
                    </a>
                </div>
            @endif

            @if(!empty($notes))
                <div class="card-box">
                    <strong>Catatan Panitia:</strong><br>
                    {!! nl2br(e($notes)) !!}
                </div>
            @endif

            <p style="margin-top: 24px; font-size: 14px; color: #475569;">
                Salam Kemanusiaan,<br>
                <strong>Panitia Pelaksana SUA BHAKTI BERKARYA</strong><br>
                <span style="color: #64748b; font-size: 13px;">Palang Merah Remaja (PMR) WIRA</span>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px 0; font-weight: 700; color: #334155;">
                PMR WIRA &bull; SUA BHAKTI BERKARYA
            </p>
            <p style="margin: 0 0 8px 0;">
                Email ini dikirimkan resmi kepada seluruh kontak sekolah dan pembina unit PMR yang terdaftar pada database sistem kegiatan kami.
            </p>
            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                &copy; {{ date('Y') }} Panitia SUA BHAKTI BERKARYA. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </div>

</body>
</html>
