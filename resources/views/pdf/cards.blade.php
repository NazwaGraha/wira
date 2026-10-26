<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta Resmi - {{ $registration->school_name }}</title>
    <style>
        @page {
            margin: 15px 18px;
            size: a4 portrait;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }
        .page-header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }
        .page-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .page-subtitle {
            font-size: 9px;
            color: #b91c1c;
            font-weight: bold;
            text-transform: uppercase;
        }
        .cards-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px;
        }
        .card-cell {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .card-box {
            border: 2px dashed #94a3b8;
            border-radius: 14px;
            padding: 12px;
            background-color: #ffffff;
            position: relative;
        }
        .lanyard-hole {
            width: 32px;
            height: 6px;
            background-color: #e2e8f0;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin: 0 auto 6px auto;
        }
        .card-header-table {
            width: 100%;
            border-bottom: 2px solid #b91c1c;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .card-badge {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2.5px 8px;
            border-radius: 9999px;
            display: inline-block;
            margin-bottom: 4px;
        }

        /* TEMA WARNA SESUAI TINGKATAN: Mula = Hijau, Madya = Biru, Wira = Kuning */
        .order-box-mula {
            background-color: #dcfce7;
            border: 1.5px solid #16a34a;
            color: #14532d;
        }
        .order-box-madya {
            background-color: #e0f2fe;
            border: 1.5px solid #0284c7;
            color: #075985;
        }
        .order-box-wira {
            background-color: #fef3c7;
            border: 1.5px solid #f59e0b;
            color: #78350f;
        }

        .data-box {
            border-radius: 8px;
            padding: 6px 8px;
            margin: 5px 0;
            text-align: center;
        }
        .data-label {
            font-size: 7.5px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }
        .participant-name {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .school-name {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .order-num {
            font-size: 20px;
            font-weight: bold;
            font-family: monospace;
            margin: 1px 0;
        }
        .cat-code {
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            font-family: monospace;
            margin-top: 1px;
        }
        .card-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            margin-top: 6px;
            font-size: 7.5px;
            color: #64748b;
        }
        .card-footer td {
            vertical-align: middle;
            font-size: 7.5px;
        }
    </style>
</head>
<body>

    @php
        $logoPath = file_exists(public_path('images/logo.png')) ? public_path('images/logo.png') : null;
        $logoBase64 = $logoPath ? base64_encode(file_get_contents($logoPath)) : null;

        // Ekstraksi kartu: 1 KARTU = 1 NAMA PESERTA
        $individualCards = [];
        $levelStr = strtolower(trim($registration->level ?? ''));

        if ($levelStr === 'mula') {
            $themeBoxClass = 'order-box-mula';
            $levelBadge = 'PMR Mula &bull; Hijau';
            $nameColor = '#15803d';
        } elseif ($levelStr === 'madya') {
            $themeBoxClass = 'order-box-madya';
            $levelBadge = 'PMR Madya &bull; Biru';
            $nameColor = '#0369a1';
        } else {
            $themeBoxClass = 'order-box-wira';
            $levelBadge = 'PMR Wira &bull; Kuning';
            $nameColor = '#b45309';
        }

        foreach ($registration->teams as $team) {
            $members = is_array($team->members_list) 
                ? $team->members_list 
                : (is_string($team->members_list) ? json_decode($team->members_list, true) : []);
            if (!is_array($members)) $members = [];

            $catCode = $team->category?->code ?: 'LOMBA';
            $orderNum = $team->order_number ?: '-';

            if (!empty($members)) {
                foreach ($members as $mIdx => $m) {
                    $name = is_array($m) ? ($m['name'] ?? ($m['nama'] ?? 'Peserta')) : $m;
                    $role = is_array($m) ? ($m['role'] ?? ($m['peran'] ?? 'Anggota')) : 'Anggota';
                    $nisn = is_array($m) ? ($m['nisn'] ?? ($m['nis'] ?? '')) : '';

                    $individualCards[] = [
                        'team' => $team,
                        'participant_name' => trim($name),
                        'role' => $role,
                        'nisn' => $nisn,
                        'school_name' => $registration->school_name,
                        'order_number' => $orderNum,
                        'category_code' => $catCode,
                        'category_name' => $team->category?->name ?? 'Cabang Lomba',
                    ];
                }
            } else {
                $individualCards[] = [
                    'team' => $team,
                    'participant_name' => 'Peserta ' . ($team->team_label ?: $team->team_name),
                    'role' => 'Peserta Regu',
                    'nisn' => '',
                    'school_name' => $registration->school_name,
                    'order_number' => $orderNum,
                    'category_code' => $catCode,
                    'category_name' => $team->category?->name ?? 'Cabang Lomba',
                ];
            }
        }

        $cardsChunked = array_chunk($individualCards, 2);
    @endphp

    <div class="page-header">
        <div class="page-title">KARTU PESERTA RESMI &bull; {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA') }}</div>
        <div class="page-subtitle">Kontingen: {{ $registration->school_name }} ({{ count($individualCards) }} Kartu Peserta - 1 Kartu per Nama)</div>
    </div>

    @foreach($cardsChunked as $row)
        <table class="cards-table" cellpadding="0" cellspacing="0">
            <tr>
                @foreach($row as $card)
                    <td class="card-cell">
                        <div class="card-box">
                            <div class="lanyard-hole"></div>

                            <!-- Header Kartu -->
                            <table class="card-header-table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 25px; vertical-align: middle;">
                                        @if($logoBase64)
                                            <img src="data:image/png;base64,{{ $logoBase64 }}" style="height: 22px; width: auto;" alt="Logo">
                                        @endif
                                    </td>
                                    <td style="vertical-align: middle; padding-left: 6px;">
                                        <div style="font-size: 8.5px; font-weight: bold; color: #0f172a; text-transform: uppercase;">
                                            {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA') }}
                                        </div>
                                        <div style="font-size: 7.5px; font-weight: bold; color: #b91c1c;">
                                            PMR WIRA SMAN 1 CIAWI BOGOR
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Isi Kartu Sesuai 4 Elemen -->
                            <div style="text-align: center;">
                                <div class="card-badge">KARTU PESERTA RESMI &bull; {!! $levelBadge !!}</div>

                                <!-- Box 1: Nama Peserta -->
                                <div class="data-box {{ $themeBoxClass }}" style="margin-top: 6px;">
                                    <div class="data-label">Nama Peserta Lomba:</div>
                                    <div class="participant-name" style="color: {{ $nameColor }};">
                                        {{ $card['participant_name'] }}
                                    </div>
                                    @if($card['role'])
                                        <div style="font-size: 7px; color: #475569; font-weight: bold;">
                                            {{ $card['role'] }} @if($card['nisn'])(NISN: {{ $card['nisn'] }})@endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Box 2: Sekolah -->
                                <div class="data-box {{ $themeBoxClass }}">
                                    <div class="data-label">Asal Sekolah / Kontingen:</div>
                                    <div class="school-name">{{ $card['school_name'] }}</div>
                                </div>

                                <!-- Box 3 & 4: No. Urut dan Mata Lomba -->
                                <table style="width: 100%; border-collapse: separate; border-spacing: 5px; margin: 4px 0;">
                                    <tr>
                                        <td style="width: 50%; vertical-align: middle;">
                                            <div class="data-box {{ $themeBoxClass }}" style="margin: 0; padding: 4px;">
                                                <div class="data-label">No. Urut:</div>
                                                <div class="order-num">{{ $card['order_number'] }}</div>
                                            </div>
                                        </td>
                                        <td style="width: 50%; vertical-align: middle;">
                                            <div class="data-box {{ $themeBoxClass }}" style="margin: 0; padding: 4px;">
                                                <div class="data-label">Mata Lomba:</div>
                                                <div class="cat-code">{{ $card['category_code'] }}</div>
                                                <div style="font-size: 6.5px; color: #475569;">{{ $card['category_name'] }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Footer Kartu -->
                            <table class="card-footer" style="width: 100%;" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="text-align: left;">
                                        <div><strong>REG:</strong> <span style="font-family: monospace;">{{ $registration->registration_code }}</span></div>
                                        <div>Pembina: {{ $registration->advisor_name }}</div>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="color: #059669; font-weight: bold;">&#10004; TERCATAT RESMI</div>
                                        <div>Bogor, {{ date('Y') }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                @endforeach
                @if(count($row) === 1)
                    <td class="card-cell"></td>
                @endif
            </tr>
        </table>
    @endforeach

</body>
</html>
