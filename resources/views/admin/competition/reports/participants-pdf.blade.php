<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Induk & Daftar Peserta Lomba - SUA BHAKTI BERKARYA IV</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #0f172a;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 10px;
            line-height: 1.35;
        }

        /* Floating On-Screen Action Bar */
        .no-print-bar {
            background: #0f172a;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .btn-print {
            background: #dc2626;
            color: white;
            padding: 7px 18px;
            border: none;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: #b91c1c;
        }

        .btn-close {
            background: #334155;
            color: white;
            padding: 7px 14px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 12px;
            margin-left: 8px;
        }

        /* Kop Surat Resmi */
        .kop-wrapper {
            margin-bottom: 15px;
        }

        .kop-logo-row {
            text-align: center;
            margin-bottom: 8px;
        }

        .kop-logo-img {
            max-height: 52px;
            width: auto;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
            line-height: 1.25;
        }

        .kop-text .org-intl {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0 0 2px 0;
        }

        .kop-text .org-title {
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .kop-text .org-sub {
            font-size: 11px;
            font-weight: 800;
            color: #980000;
            text-transform: uppercase;
            margin: 2px 0 0 0;
        }

        .kop-text .org-addr {
            font-size: 8.5px;
            color: #475569;
            margin: 4px 0 0 0;
        }

        .kop-divider {
            border-top: 2.5px solid #0f172a;
            border-bottom: 1px solid #0f172a;
            height: 2px;
            margin-top: 8px;
            margin-bottom: 14px;
        }

        /* Document Title */
        .doc-title {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-title h2 {
            font-size: 12.5px;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-title .sub {
            font-size: 10px;
            font-weight: 700;
            color: #980000;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Meta Filter Box */
        .meta-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 9.5px;
        }

        .meta-item {
            margin: 2px 0;
        }

        .meta-item strong {
            color: #334155;
        }

        /* Table Data */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #64748b;
            padding: 5px 6px;
            vertical-align: top;
        }

        th {
            background-color: #f1f5f9;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5px;
            color: #0f172a;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        .font-bold { font-weight: 700; }
        .font-extrabold { font-weight: 800; }

        .order-badge {
            background: #0f172a;
            color: #ffffff;
            font-family: monospace;
            font-weight: 800;
            font-size: 9px;
            padding: 2px 5px;
            border-radius: 4px;
            display: inline-block;
        }

        .member-list {
            margin: 0;
            padding-left: 0;
            list-style: none;
        }

        .member-list li {
            margin-bottom: 2px;
            line-height: 1.25;
        }

        .member-leader {
            font-weight: 700;
            color: #0f172a;
        }

        .member-role {
            font-size: 7.5px;
            background: #fef3c7;
            color: #92400e;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: 800;
            display: inline-block;
            margin-left: 2px;
        }

        /* Signatures Section */
        .signatures {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .sign-col {
            width: 220px;
            text-align: center;
            font-size: 9.5px;
        }

        .sign-title {
            margin-bottom: 5px;
            color: #334155;
        }

        .sign-space {
            height: 60px;
        }

        .sign-name {
            font-weight: 800;
            text-decoration: underline;
            color: #0f172a;
        }

        .sign-id {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
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

    <!-- On-screen Print Bar -->
    <div class="no-print-bar">
        <div>
            <strong>Mode Pracetak: Buku Induk & Lembar Daftar Peserta Lomba</strong>
            <div style="font-size: 10px; opacity: 0.85; margin-top: 2px;">
                Dokumen siap cetak format A4 ber-Kop Resmi SMAN 1 Ciawi & Palang Merah Indonesia.
            </div>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">
                🖨️ Cetak Dokumen (Print / PDF)
            </button>
            <button class="btn-close" onclick="window.close()">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Header (Kop Logo Resmi PMR & PMI) -->
    <div class="kop-wrapper">
        <div class="kop-logo-row">
            <img src="{{ asset('images/logo_kop_laporan_pmr.png') }}" alt="Logo Resmi Palang Merah Indonesia & SMAN 1 Ciawi" class="kop-logo-img">
        </div>
        <div class="kop-text">
            <div class="org-intl">Gerakan Palang Merah dan Bulan Sabit Merah Internasional</div>
            <div class="org-title">PALANG MERAH REMAJA (PMR) WIRA UNIT SMAN 1 CIAWI</div>
            <div class="org-sub">PANITIA PELAKSANA SUA BHAKTI BERKARYA (SBB) IV TAHUN 2026</div>
            <div class="org-addr">Sekretariat: Jl. Veteran III Banjarwaru, Ciawi, Bogor, Jawa Barat 16720 • Telp: 0838-7250-6817 • Email: pmrwirasman1c@gmail.com</div>
        </div>
        <div class="kop-divider"></div>
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        <h2>BUKU INDUK & DAFTAR PESERTA LOMBA PER KATEGORI</h2>
        <div class="sub">
            TINGKAT: PMR {{ strtoupper($level ?: 'SEMUA TINGKAT') }} — CABANG LOMBA: {{ strtoupper($selectedCategory?->name ?? 'SEMUA CABANG') }}
        </div>
    </div>

    <!-- Metadata Box -->
    <div class="meta-box">
        <div class="meta-item"><strong>Ajang Kegiatan:</strong> {{ $event?->title ?? 'Sua Bhakti Berkarya IV' }}</div>
        <div class="meta-item"><strong>Jenjang Tingkat:</strong> PMR {{ $level ?: 'Semua Jenjang' }}</div>
        <div class="meta-item"><strong>Cabang Lomba:</strong> {{ $selectedCategory?->name ?? 'Semua Cabang' }}</div>
        <div class="meta-item"><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
        <div class="meta-item"><strong>Total Regu Terindeks:</strong> {{ $teams->count() }} Regu Terdaftar</div>
        <div class="meta-item"><strong>Lokasi Lomba:</strong> {{ $event?->location ?? 'Kampus SMAN 1 Ciawi' }}</div>
    </div>

    <!-- Table of Participants -->
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 70px;">No. Urut</th>
                <th style="width: 140px;">Sekolah Kontingen</th>
                <th style="width: 85px;">Nama Regu</th>
                <th>Daftar Nama Anggota Siswa (Ketua & Anggota)</th>
                <th style="width: 95px;">Cabang Lomba</th>
                <th style="width: 110px;">Pembina / WhatsApp</th>
                <th style="width: 55px;">Status</th>
                <th style="width: 70px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teams as $idx => $team)
                @php
                    $reg = $team->registration;
                    $cat = $team->category;
                    $members = is_array($team->members_list) ? $team->members_list : [];
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $idx + 1 }}</td>
                    <td class="text-center">
                        @if($team->order_number)
                            <span class="order-badge">{{ $team->order_number }}</span>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="font-bold">{{ $reg?->school_name ?? '-' }}</div>
                        <div style="font-size: 8px; color: #64748b;">
                            Tingkat: PMR {{ $cat?->level ?: ($reg?->level ?: '-') }}
                        </div>
                    </td>
                    <td>
                        <div class="font-extrabold">{{ $team->team_name ?: 'Regu ' . ($team->team_label ?: 'A') }}</div>
                        @if($team->team_label)
                            <div style="font-size: 8px; color: #b45309; font-weight: 700;">Regu {{ $team->team_label }}</div>
                        @endif
                    </td>
                    <td>
                        @if(!empty($members) && count($members) > 0)
                            <ul class="member-list">
                                @foreach($members as $mIdx => $m)
                                    @php
                                        $mName = is_array($m) ? ($m['name'] ?? ($m['nama'] ?? '-')) : (string) $m;
                                        $mRole = is_array($m) && !empty($m['role']) ? $m['role'] : ($mIdx === 0 ? 'Ketua' : 'Anggota');
                                        $isLeader = (stripos($mRole, 'ketua') !== false || $mIdx === 0);
                                    @endphp
                                    <li class="{{ $isLeader ? 'member-leader' : '' }}">
                                        {{ $mIdx + 1 }}. {{ $mName }}
                                        @if($isLeader)
                                            <span class="member-role">Ketua</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Data nama anggota belum diinput</span>
                        @endif
                    </td>
                    <td>
                        <div class="font-bold">{{ $cat?->name ?? 'Cabang Lomba' }}</div>
                        <div style="font-size: 8px; color: #64748b;">{{ $cat?->gender_category ?: 'Umum / Beregu' }}</div>
                    </td>
                    <td>
                        <div class="font-bold">{{ $reg?->advisor_name ?? '-' }}</div>
                        <div style="font-size: 8px; color: #475569;">{{ $reg?->advisor_phone ?? '-' }}</div>
                    </td>
                    <td class="text-center font-bold" style="font-size: 8px;">
                        @if($reg?->status === 'verified')
                            <span style="color: #15803d;">Verif</span>
                        @else
                            <span style="color: #b45309;">Pending</span>
                        @endif
                        <br>
                        @if($reg?->is_checked_in)
                            <span style="color: #1d4ed8; font-weight: 800;">[Hadir]</span>
                        @else
                            <span style="color: #94a3b8;">[Belum]</span>
                        @endif
                    </td>
                    <td style="height: 32px; vertical-align: bottom; font-size: 7.5px; color: #94a3b8; text-align: center;">
                        ..................
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data peserta ditemukan untuk filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures Block -->
    <div class="signatures">
        <div class="sign-col">
            <div class="sign-title">Mengetahui,<br>Pembina PMR Wira SMAN 1 Ciawi</div>
            <div class="sign-space"></div>
            <div class="sign-name">Drs. H. Mulyadi, M.Pd</div>
            <div class="sign-id">NIP. 19680512 199403 1 004</div>
        </div>

        <div class="sign-col">
            <div class="sign-title">Bogor, {{ now()->translatedFormat('d F Y') }}<br>Ketua Panitia SBB IV 2026</div>
            <div class="sign-space"></div>
            <div class="sign-name">Raka Raditya Pratama</div>
            <div class="sign-id">NRI. 242510001</div>
        </div>
    </div>

</body>
</html>
