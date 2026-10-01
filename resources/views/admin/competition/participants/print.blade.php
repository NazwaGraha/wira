<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Presensi & Daftar Peserta Lomba - SUA BHAKTI BERKARYA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .header-text {
            text-align: center;
            flex-grow: 1;
            padding: 0 15px;
        }

        .header-text h1 {
            font-size: 14px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-text h2 {
            font-size: 12px;
            font-weight: 700;
            margin: 2px 0 0 0;
            color: #980000;
            text-transform: uppercase;
        }

        .header-text p {
            font-size: 9px;
            margin: 3px 0 0 0;
            color: #475569;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-title h3 {
            font-size: 13px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .meta-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 10px;
        }

        .meta-item {
            margin: 2px 0;
        }

        .meta-item strong {
            color: #334155;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #64748b;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            background-color: #f1f5f9;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            font-size: 9.5px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .font-mono { font-family: monospace; }

        .signature-box {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .sign-col {
            width: 200px;
            text-align: center;
            font-size: 10px;
        }

        .sign-space {
            height: 60px;
        }

        .no-print-bar {
            background: #0f172a;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .btn-print {
            background: #dc2626;
            color: white;
            padding: 6px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 12px;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Action Bar -->
    <div class="no-print-bar">
        <div>
            <strong>Mode Pracetak: Lembar Presensi & Roster Peserta Lomba</strong>
            <div style="font-size: 10px; opacity: 0.8;">Gunakan tombol di samping atau tekan Ctrl+P / Cmd+P untuk mencetak dokumen ini.</div>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">🖨️ Cetak Dokumen</button>
        </div>
    </div>

    <!-- Official Header -->
    <div class="header">
        <img src="/images/logo.png" alt="Logo PMR" class="header-logo" onerror="this.style.display='none'">
        <div class="header-text">
            <h1>PALANG MERAH REMAJA (PMR) WIRA</h1>
            <h2>SMA NEGERI 1 CIAWI - KABUPATEN BOGOR</h2>
            <p>Jalan Veteran III No. 45 Ciawi Bogor &bull; Email: pmr@sman1ciawi.sch.id &bull; Website: wira.nazwagraha.com</p>
        </div>
        <img src="/images/logo.png" alt="Logo PMI" class="header-logo" onerror="this.style.display='none'">
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        <h3>LEMBAR DAFTAR PESERTA & PRESENSI LOMBA</h3>
        <div style="font-size: 11px; font-weight: 700; color: #334155; margin-top: 3px;">
            AJANG KREASI DAN PRESTASI: {{ strtoupper($event?->title ?? 'SUA BHAKTI BERKARYA') }}
        </div>
    </div>

    <!-- Metadata Box -->
    <div class="meta-box">
        <div class="meta-item"><strong>Tingkat:</strong> PMR {{ $level ?: ($selectedCategory?->level ?? 'Semua Tingkat') }}</div>
        <div class="meta-item"><strong>Cabang Lomba:</strong> {{ $selectedCategory?->name ?? 'Semua Cabang' }}</div>
        <div class="meta-item"><strong>Kategori Gender:</strong> {{ $gender ?: ($selectedCategory?->gender_category ?? 'Semua Kategori') }}</div>
        <div class="meta-item"><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
        <div class="meta-item"><strong>Total Regu:</strong> {{ $teams->count() }} Regu Terverifikasi</div>
        <div class="meta-item"><strong>Lokasi Lomba:</strong> {{ $event?->location ?? 'Kampus SMAN 1 Ciawi' }}</div>
    </div>

    <!-- Participants Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 50px;">No. Urut</th>
                <th style="width: 140px;">Cabang & Tingkat</th>
                <th style="width: 180px;">Nama Regu / Asal Sekolah</th>
                <th>Daftar Nama Anggota Regu</th>
                <th style="width: 90px;">Pembina / Kontak</th>
                <th style="width: 70px;">Tanda Tangan Presensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teams as $index => $team)
                @php
                    $reg = $team->registration;
                    $cat = $team->category;
                    $members = is_array($team->members_list) ? $team->members_list : [];
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td class="text-center font-mono font-bold" style="font-size: 12px;">
                        {{ $team->order_number ?: '-' }}
                    </td>
                    <td>
                        <div class="font-bold">{{ $cat?->name ?? '-' }}</div>
                        <div style="font-size: 9px; color: #475569;">
                            PMR {{ $cat?->level }} &bull; {{ $cat?->gender_category }}
                        </div>
                    </td>
                    <td>
                        <div class="font-bold" style="font-size: 10.5px;">{{ $team->team_name }}</div>
                        <div style="font-size: 9px; color: #64748b;">
                            {{ $reg?->school_name ?? '-' }} ({{ $reg?->registration_code ?? '-' }})
                        </div>
                    </td>
                    <td>
                        @if(count($members) > 0)
                            <ol style="margin: 0; padding-left: 14px; font-size: 9.5px;">
                                @foreach($members as $m)
                                    <li>{{ is_array($m) ? ($m['name'] ?? '-') : $m }}</li>
                                @endforeach
                            </ol>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">(Daftar nama belum terlampir)</span>
                        @endif
                    </td>
                    <td>
                        <div class="font-bold">{{ $reg?->advisor_name ?? '-' }}</div>
                        <div style="font-size: 8.5px; color: #475569;">{{ $reg?->advisor_phone ?? '-' }}</div>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">
                        <div style="font-size: 8.5px; color: #cbd5e1;">( {{ $index + 1 }} )</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data peserta terverifikasi untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Official Signatures -->
    <div class="signature-box">
        <div class="sign-col">
            <div>Mengetahui,</div>
            <div class="font-bold">Ketua Panitia Pelaksana</div>
            <div class="sign-space"></div>
            <div class="font-bold" style="text-decoration: underline;">TOM CRUZ</div>
            <div style="font-size: 9px; color: #475569;">Ketua Umum PMR Wira</div>
        </div>

        <div class="sign-col">
            <div>Bogor, {{ now()->translatedFormat('d F Y') }}</div>
            <div class="font-bold">Koordinator Juri Cabang Lomba</div>
            <div class="sign-space"></div>
            <div class="font-bold" style="text-decoration: underline;">( .................................................. )</div>
            <div style="font-size: 9px; color: #475569;">NIP / ID Juri:</div>
        </div>
    </div>

</body>
</html>
