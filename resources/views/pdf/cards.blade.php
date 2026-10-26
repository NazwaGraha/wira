<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta - {{ $registration->school_name }}</title>
    <style>
        @page {
            margin: 18px 20px;
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
            margin-bottom: 10px;
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
            border-spacing: 10px;
        }
        .card-cell {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .card-box {
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            padding: 10px;
            background-color: #ffffff;
            position: relative;
        }
        .lanyard-hole {
            width: 30px;
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
            margin-bottom: 6px;
        }
        .card-badge {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 9999px;
            display: inline-block;
            margin-bottom: 4px;
        }
        .order-box {
            background-color: #fef3c7;
            border: 1.5px solid #f59e0b;
            border-radius: 8px;
            padding: 5px;
            margin: 5px 0;
            text-align: center;
        }
        .order-label {
            font-size: 7.5px;
            font-weight: bold;
            color: #92400e;
            text-transform: uppercase;
        }
        .order-num {
            font-size: 20px;
            font-weight: bold;
            color: #451a03;
            font-family: monospace;
            margin: 1px 0;
        }
        .order-code {
            font-size: 7.5px;
            font-weight: bold;
            color: #b45309;
            font-family: monospace;
        }
        .school-name {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .team-title {
            font-size: 9px;
            font-weight: bold;
            color: #b91c1c;
            margin-top: 1px;
        }
        .cat-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 6px;
            margin: 5px 0;
            text-align: center;
        }
        .cat-label {
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .cat-val {
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 1px;
        }
        .members-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin: 5px 0;
            background-color: #ffffff;
            font-size: 7.5px;
        }
        .members-header {
            background-color: #f1f5f9;
            padding: 3px 6px;
            font-weight: bold;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }
        .member-row {
            padding: 2px 6px;
            border-bottom: 1px solid #f8fafc;
        }
        .card-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            margin-top: 5px;
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
    @endphp

    <div class="page-header">
        <div class="page-title">KARTU PESERTA RESMI &bull; {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA') }}</div>
        <div class="page-subtitle">Kontingen: {{ $registration->school_name }} ({{ $registration->teams->count() }} Regu Terdaftar)</div>
    </div>

    @php
        $teamsChunked = $registration->teams->chunk(2);
    @endphp

    @foreach($teamsChunked as $row)
        <table class="cards-table" cellpadding="0" cellspacing="0">
            <tr>
                @foreach($row as $team)
                    @php
                        $orderNum = $team->order_number ?: '-';
                        $fullCode = $team->full_order_code ?: ($team->category?->code ? $team->category->code . '-' . $orderNum : $registration->registration_code);
                        $members = is_array($team->members_list) ? $team->members_list : (is_string($team->members_list) ? json_decode($team->members_list, true) : []);
                        if (!is_array($members)) $members = [];
                        
                        $isReguB = str_starts_with(strtoupper($orderNum), 'B') || str_contains(strtoupper($team->team_label ?? ''), 'REGU B');
                        $isReguC = str_starts_with(strtoupper($orderNum), 'C') || str_contains(strtoupper($team->team_label ?? ''), 'REGU C');
                        $sessionLabel = $isReguB ? 'Sesi 2 (Regu B)' : ($isReguC ? 'Sesi 3 (Regu C)' : 'Sesi 1 (Regu A)');
                    @endphp
                    <td class="card-cell">
                        <div class="card-box">
                            <!-- Lubang Tali Id Card (Lanyard Hole) -->
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

                            <!-- Isi Kartu -->
                            <div style="text-align: center;">
                                <div class="card-badge">KARTU PESERTA RESMI</div>

                                <!-- Box Nomor Urut Tampil Skema 1 -->
                                <div class="order-box">
                                    <div class="order-label">Nomor Urut Tampil: {{ $sessionLabel }}</div>
                                    <div class="order-num">{{ $orderNum }}</div>
                                    <div class="order-code">KODE: {{ $fullCode }}</div>
                                </div>

                                <div class="school-name">{{ $registration->school_name }}</div>
                                <div class="team-title">{{ $team->team_name }}</div>

                                <div class="cat-box">
                                    <div class="cat-label">Cabang Perlombaan:</div>
                                    <div class="cat-val">
                                        {{ $team->category->name ?? 'Cabang Lomba' }} 
                                        &bull; {{ $team->category->gender_category ?? 'Umum' }}
                                        &bull; <strong>PMR {{ $registration->level }}</strong>
                                    </div>
                                </div>

                                <!-- Anggota Regu (Import Excel) -->
                                <div class="members-box" style="text-align: left;">
                                    <div class="members-header">
                                        ANGGOTA PESERTA: ({{ count($members) }} Orang)
                                    </div>
                                    @forelse(array_slice($members, 0, 5) as $mIdx => $m)
                                        @php
                                            $mName = is_array($m) ? ($m['name'] ?? ($m['nama'] ?? 'Peserta')) : $m;
                                            $mRole = is_array($m) ? ($m['role'] ?? ($m['peran'] ?? 'Anggota')) : 'Anggota';
                                            $mNis = is_array($m) ? ($m['nisn'] ?? ($m['nis'] ?? '')) : '';
                                        @endphp
                                        <div class="member-row">
                                            <strong>{{ $mIdx + 1 }}.</strong> {{ $mName }}
                                            @if($mNis)<span style="color: #64748b;">({{ $mNis }})</span>@endif
                                            - <span style="color: #991b1b; font-weight: bold;">{{ $mRole }}</span>
                                        </div>
                                    @empty
                                        <div class="member-row" style="color: #94a3b8; font-style: italic;">
                                            Daftar anggota tercatat di sistem panitia.
                                        </div>
                                    @endforelse
                                </div>
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
                @if($row->count() === 1)
                    <td class="card-cell"></td>
                @endif
            </tr>
        </table>
    @endforeach

</body>
</html>
