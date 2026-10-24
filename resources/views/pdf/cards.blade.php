<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta - {{ $registration->school_name }}</title>
    <style>
        @page {
            margin: 20px 24px;
            size: a4 portrait;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .page-header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .page-title {
            font-size: 13px;
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
            border-radius: 12px;
            padding: 12px;
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
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .card-badge {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 9999px;
            display: inline-block;
            margin-bottom: 6px;
        }
        .school-name {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .team-title {
            font-size: 10px;
            font-weight: bold;
            color: #b91c1c;
            margin-top: 2px;
        }
        .cat-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 8px;
            margin: 8px 0;
            text-align: center;
        }
        .cat-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .cat-val {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .card-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            margin-top: 6px;
            font-size: 8px;
            color: #64748b;
        }
        .card-footer td {
            vertical-align: middle;
            font-size: 8px;
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
                    <td class="card-cell">
                        <div class="card-box">
                            <!-- Lubang Tali Id Card (Lanyard Hole) -->
                            <div class="lanyard-hole"></div>

                            <!-- Header Kartu -->
                            <table class="card-header-table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 25px; vertical-align: middle;">
                                        @if($logoBase64)
                                            <img src="data:image/png;base64,{{ $logoBase64 }}" style="height: 24px; width: auto;" alt="Logo">
                                        @endif
                                    </td>
                                    <td style="vertical-align: middle; padding-left: 6px;">
                                        <div style="font-size: 9px; font-weight: bold; color: #0f172a; text-transform: uppercase;">
                                            {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA') }}
                                        </div>
                                        <div style="font-size: 8px; font-weight: bold; color: #b91c1c;">
                                            PMR WIRA SMAN 1 CIAWI
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Isi Kartu -->
                            <div style="text-align: center;">
                                <div class="card-badge">KARTU PESERTA LOMBA</div>
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
                            </div>

                            <!-- Footer Kartu -->
                            <table class="card-footer" style="width: 100%;" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="text-align: left;">
                                        <div><strong>KODE:</strong> <span style="font-family: monospace;">{{ $registration->registration_code }}</span></div>
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
