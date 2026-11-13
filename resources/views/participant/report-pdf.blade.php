<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai - {{ $registration->school_name }} ({{ $registration->registration_code }})</title>
    <style>
        @page {
            margin: 28px 32px;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #dc2626;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h1 {
            font-size: 14px;
            font-weight: bold;
            color: #991b1b;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h2 {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }
        .header-title p {
            font-size: 8.5px;
            color: #64748b;
            margin: 0;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            font-size: 10px;
            padding: 2px 0;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-wira {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .badge-madya {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 6px;
            border: 1px solid #0f172a;
            text-align: center;
        }
        .data-table td {
            font-size: 9px;
            padding: 6px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .score-box {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        .footer-note {
            margin-top: 16px;
            padding: 8px 12px;
            background-color: #fffbeb;
            border: 1px dashed #f59e0b;
            border-radius: 6px;
            font-size: 8.5px;
            color: #92400e;
        }
        .signature-table {
            width: 100%;
            margin-top: 24px;
        }
        .signature-table td {
            font-size: 9.5px;
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <table class="header-table">
        <tr>
            <td style="width: 70px; text-align: left; vertical-align: middle;">
                <img src="{{ public_path('images/logo-pmi.png') }}" alt="PMI" style="height: 52px;" onerror="this.style.display='none'">
            </td>
            <td class="header-title" style="vertical-align: middle;">
                <h1>LAPORAN RESMI NILAI PESERTA LOMBA</h1>
                <h2>SUA BHAKTI BERKARYA IV TAHUN 2026</h2>
                <p>Palang Merah Remaja Wira SMA Negeri 1 Ciawi Bogor • Jln. Veteran III Desa Banjarwaru Kec. Ciawi</p>
            </td>
            <td style="width: 70px; text-align: right; vertical-align: middle;">
                <img src="{{ public_path('images/logo-pmr.png') }}" alt="PMR" style="height: 52px;" onerror="this.style.display='none'">
            </td>
        </tr>
    </table>

    <!-- Info Kontingen Sekolah -->
    <div class="info-card">
        <table class="info-table">
            <tr>
                <td style="width: 22%; font-weight: bold; color: #64748b;">Asal Sekolah:</td>
                <td style="width: 43%; font-weight: bold; font-size: 11px; color: #0f172a;">{{ $registration->school_name }}</td>
                <td style="width: 18%; font-weight: bold; color: #64748b;">No. Registrasi:</td>
                <td style="width: 17%; font-weight: bold; font-family: monospace; font-size: 10px; color: #991b1b;">{{ $registration->registration_code }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #64748b;">Tingkatan:</td>
                <td>
                    <span class="badge {{ strtolower($registration->level) === 'wira' ? 'badge-wira' : 'badge-madya' }}">
                        TINGKAT {{ strtoupper($registration->level) }}
                    </span>
                </td>
                <td style="font-weight: bold; color: #64748b;">Waktu Cetak:</td>
                <td style="color: #475569; font-size: 9px;">{{ $printedAt }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #64748b;">Pembina Pendamping:</td>
                <td style="color: #334155;">{{ $registration->advisor_name ?? '-' }} ({{ $registration->advisor_phone ?? '-' }})</td>
                <td style="font-weight: bold; color: #64748b;">Total Lomba:</td>
                <td style="font-weight: bold; color: #0f172a;">{{ $teamsData->count() }} Cabang</td>
            </tr>
        </table>
    </div>

    <!-- Tabel Rekap Nilai -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width: 25%;">CABANG LOMBA</th>
                <th style="width: 9%;">NO. UNDIAN</th>
                <th style="width: 16%;">NAMA REGU</th>
                <th style="width: 26%;">ANGGOTA REGU</th>
                <th style="width: 9%;">WAKTU</th>
                <th style="width: 11%;">NILAI AKHIR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teamsData as $idx => $t)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                    <td>
                        <strong style="color: #0f172a;">{{ $t['category_name'] }}</strong>
                    </td>
                    <td style="text-align: center; font-weight: bold; font-family: monospace; color: #1e40af;">
                        {{ $t['order_number'] }}
                    </td>
                    <td>{{ $t['team_name'] }}</td>
                    <td>
                        @if(!empty($t['member_names']))
                            @foreach($t['member_names'] as $mIdx => $mName)
                                <div>{{ $mIdx + 1 }}. {{ $mName }}</div>
                            @endforeach
                        @else
                            <span style="color: #94a3b8; font-style: italic;">(Daftar anggota belum diisi)</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-family: monospace;">
                        {{ $t['time_recorded'] ?: '-' }}
                    </td>
                    <td style="text-align: center;">
                        @if($t['is_disqualified'])
                            <span style="color: #dc2626; font-weight: bold;">Diskualifikasi</span>
                        @elseif($t['has_score'])
                            <span class="score-box">{{ number_format($t['final_score'], 2, ',', '.') }}</span>
                        @else
                            <span style="color: #64748b; font-style: italic; font-size: 8px;">Belum Dinilai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 14px; color: #64748b;">
                        Tidak ada data mata lomba yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Catatan & Ketentuan -->
    <div class="footer-note">
        <strong>Ketentuan & Kerahasiaan Nilai:</strong>
        <br>
        1. Lembar ini adalah rekapitulasi resmi nilai yang diperoleh kontingen sekolah yang bersangkutan pada ajang Sua Bhakti Berkarya IV 2026.
        <br>
        2. Demi menjunjung tinggi sportivitas dan objektivitas, dokumen ini tidak memuat peringkat (ranking) ataupun perolehan juara. Pengumuman Juara Umum, Juara Kategori, dan pembagian piala akan disampaikan secara resmi oleh Dewan Juri pada Acara Penutupan.
    </div>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Pembina PMR Sekolah</strong>
                <br><br><br><br>
                <u>( {{ $registration->advisor_name ?? '..................................................' }} )</u>
            </td>
            <td>
                Bogor, {{ date('d F Y') }}<br>
                <strong>Ketua Panitia Pelaksana SBB IV</strong>
                <br><br><br><br>
                <u>( Panitia Pelaksana SBB IV 2026 )</u>
            </td>
        </tr>
    </table>

</body>
</html>
