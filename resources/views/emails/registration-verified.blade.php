<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Kwitansi & Kartu Peserta - {{ $registration->registration_code }}</title>
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
            padding: 32px 28px;
            line-height: 1.7;
            font-size: 14px;
            color: #1e293b;
        }
        .verified-banner {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border: 1px solid #a7f3d0;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin-bottom: 26px;
        }
        .verified-stamp {
            display: inline-block;
            background-color: #059669;
            color: #ffffff;
            font-weight: 900;
            font-size: 11px;
            letter-spacing: 1px;
            padding: 4px 14px;
            border-radius: 9999px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .verified-title {
            font-size: 18px;
            font-weight: 900;
            color: #065f46;
            margin: 0 0 6px 0;
            line-height: 1.3;
        }
        .verified-desc {
            font-size: 13px;
            color: #047857;
            margin: 0;
            line-height: 1.6;
        }
        .btn-action-container {
            text-align: center;
            margin: 24px 0 28px 0;
        }
        .btn-receipt {
            display: inline-block;
            background-color: #059669;
            color: #ffffff !important;
            font-weight: 800;
            font-size: 13px;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 12px;
            margin: 5px 4px;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);
        }
        .btn-cards {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff !important;
            font-weight: 800;
            font-size: 13px;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 12px;
            margin: 5px 4px;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.25);
        }
        .btn-status {
            display: inline-block;
            background-color: #dc2626;
            color: #ffffff !important;
            font-weight: 800;
            font-size: 13px;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 12px;
            margin: 5px 4px;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 20px;
            margin: 20px 0;
        }
        .info-card-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
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
            padding: 6px 4px;
            vertical-align: top;
        }
        .meta-label {
            color: #64748b;
            font-weight: 600;
            width: 38%;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 700;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 800;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            text-align: left;
            text-transform: uppercase;
            font-size: 11px;
        }
        .items-table td {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            color: #334155;
        }
        .items-table tfoot td {
            background-color: #f8fafc;
            font-weight: 800;
            padding: 10px 12px;
        }
        .team-chip {
            display: inline-block;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        .notice-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 16px 18px;
            margin: 24px 0;
            font-size: 12px;
            color: #92400e;
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
        <!-- Header Sesuai Mockup Resmi -->
        <div class="header">
            <table class="header-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <!-- Kiri: Logo Resmi Gabungan (PMI | SMAN 1 Ciawi) -->
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
                            {{ !empty($registration->event->title) ? strtoupper($registration->event->title) : 'SUA BHAKTI BERKARYA' }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <!-- Baris Tengah: Badge Status & Subtitle -->
                    <td colspan="2" style="text-align: center; padding-top: 18px;">
                        <div class="badge-status">
                            &#9432; BUKTI VERIFIKASI RESMI & DOKUMEN PESERTA
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

            <!-- Verified Success Banner -->
            <div class="verified-banner">
                <span class="verified-stamp">STATUS: LUNAS / TERVERIFIKASI</span>
                <h2 class="verified-title">&#10004; Pendaftaran & Pembayaran Diterima!</h2>
                <p class="verified-desc">
                    Selamat! Bukti pembayaran dan pendaftaran kontingen <strong>{{ $registration->school_name }}</strong> telah diverifikasi dan disetujui secara resmi oleh Panitia Pelaksana.
                </p>
            </div>

            <div style="margin-bottom: 18px; font-size: 14px; color: #475569;">
                Kepada Yth. Bapak/Ibu Pembina PMR & Kontingen<br>
                <strong style="color: #0f172a; font-size: 16px;">{{ $registration->school_name }}</strong>
            </div>

            <p style="margin: 0 0 16px 0; font-size: 14px; line-height: 1.7; color: #334155;">
                Bersama ini kami lampirkan dokumen resmi <strong>e-Kwitansi Pembayaran (PDF)</strong> dan <strong>Kartu Peserta Lomba</strong> untuk kegiatan <strong>{{ $registration->event->title ?? 'SUA BHAKTI BERKARYA' }}</strong>. Silakan klik tombol di bawah ini untuk mencetak atau menyimpan dokumen Anda:
            </p>

            <!-- Action Buttons Sesuai Halaman Status -->
            <div class="btn-action-container">
                <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn-receipt">
                    &#128196; Cetak e-Kwitansi (PDF)
                </a>
                <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn-cards">
                    &#129525; Cetak Kartu Peserta
                </a>
                <a href="{{ url('/lomba/status?code=' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="btn-status">
                    &#128269; Cek Status Lengkap
                </a>
            </div>

            <!-- Ringkasan Data Kontingen -->
            <div class="info-card">
                <div class="info-card-title">Ringkasan Data Pendaftaran</div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Kode Pendaftaran</td>
                        <td class="meta-val" style="font-family: monospace; color: #b91c1c; font-size: 15px;">{{ $registration->registration_code }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Sekolah / Unit PMR</td>
                        <td class="meta-val">{{ $registration->school_name }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jenjang Tingkat PMR</td>
                        <td class="meta-val">PMR {{ $registration->level }} ({{ $registration->level == 'Mula' ? 'SD' : ($registration->level == 'Madya' ? 'SMP' : 'SMA') }})</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Pembina / Pendamping</td>
                        <td class="meta-val">{{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Alamat Email Terdaftar</td>
                        <td class="meta-val">{{ $registration->advisor_email }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Waktu Verifikasi</td>
                        <td class="meta-val">{{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y, H:i') : date('d F Y, H:i') }} WIB</td>
                    </tr>
                </table>
            </div>

            <!-- Rincian Kwitansi / Cabang Lomba -->
            <div class="info-card">
                <div class="info-card-title">Rincian Pembayaran Lomba (e-Kwitansi)</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 32px; text-align: center;">No</th>
                            <th>Mata Lomba</th>
                            <th>Regu</th>
                            <th style="text-align: right; width: 110px;">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registration->teams as $idx => $team)
                            <tr>
                                <td style="text-align: center; color: #94a3b8; font-weight: bold;">{{ $idx + 1 }}</td>
                                <td style="font-weight: 700; color: #0f172a;">
                                    {{ $team->category->name }} ({{ $team->category->gender_category }})
                                </td>
                                <td style="color: #64748b;">{{ $team->team_name }}</td>
                                <td style="text-align: right; font-family: monospace; font-weight: 700;">
                                    Rp {{ number_format($team->category->registration_fee ?: ($registration->event->registration_fee ?: 150000), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; text-transform: uppercase; font-size: 11px; color: #475569;">
                                Total Tagihan Lunas :
                            </td>
                            <td style="text-align: right; font-family: monospace; color: #059669; font-size: 14px; font-weight: 900;">
                                Rp {{ number_format($registration->total_payment, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Rincian Kartu Peserta -->
            <div class="info-card">
                <div class="info-card-title">Daftar Regu & Kartu Peserta ({{ $registration->teams->count() }} Regu)</div>
                @foreach($registration->teams as $team)
                    <div class="team-chip">
                        <div style="font-weight: 800; font-size: 13px; color: #0f172a;">
                            {{ $team->category->name }} &bull; {{ $team->category->gender_category }}
                        </div>
                        <div style="font-size: 12px; color: #b91c1c; font-weight: 700; margin-top: 2px;">
                            Nama Regu: {{ $team->team_name }}
                        </div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                            Tingkat: PMR {{ $registration->level }} &bull; Kode: {{ $registration->registration_code }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Petunjuk Penting -->
            <div class="notice-box">
                <strong style="font-size: 13px; display: block; margin-bottom: 6px;">&#9888; PETUNJUK PENTING BAGI KONTINGEN:</strong>
                <ol style="margin: 0; padding-left: 18px;">
                    <li style="margin-bottom: 4px;">Harap unduh dan cetak <strong>e-Kwitansi</strong> serta <strong>Kartu Peserta</strong> melalui tombol di atas sebelum menghadiri kegiatan.</li>
                    <li style="margin-bottom: 4px;">Kartu Peserta wajib dicetak dan dikenakan oleh seluruh anggota regu saat pelaksanaan lomba di SMA Negeri 1 Ciawi.</li>
                    <li style="margin-bottom: 4px;">e-Kwitansi ini adalah bukti pembayaran yang <strong>SAH dan RESMI</strong> dari Panitia Pelaksana Sua Bhakti Berkarya tanpa perlu legalisir stempel basah.</li>
                </ol>
            </div>

            <div style="margin-top: 28px; font-size: 13px; color: #475569; line-height: 1.6;">
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
                Email ini dikirimkan otomatis oleh sistem registrasi lomba resmi PMR Wira SMA Negeri 1 Ciawi.
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
