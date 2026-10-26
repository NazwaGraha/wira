<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta Lomba (100x150 mm) - {{ $registration->school_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Screen Dimensions */
        .card-preview {
            width: 440px;
            height: 660px; /* Rasio 100 x 150 mm */
            position: relative;
            background-image: url('{{ asset("images/template_kartu_peserta.jpg") }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        /* Lubang Klip Tali Gantungan (Lanyard) */
        .lanyard-hole {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 34px;
            height: 7px;
            background: rgba(15, 23, 42, 0.65);
            border: 1.5px solid rgba(255, 255, 255, 0.7);
            border-radius: 9999px;
            z-index: 20;
        }

        .box-overlay {
            position: absolute;
            border-radius: 14px;
            border: 2.5px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 10;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.35);
        }

        /* Posisi Koordinat Presisi Kotak Data */
        .box-nama {
            top: 319px;
            left: 49px;
            width: 342px;
            height: 49px;
            padding: 0 12px;
        }
        .box-sekolah {
            top: 419px;
            left: 59px;
            width: 326px;
            height: 49px;
            padding: 0 10px;
        }
        .box-nourut {
            top: 506px;
            left: 78px;
            width: 134px;
            height: 58px;
        }
        .box-matalomba {
            top: 506px;
            left: 251px;
            width: 124px;
            height: 58px;
            padding: 0 6px;
        }

        /* TEMA WARNA SESUAI TINGKATAN: MULA = HIJAU, MADYA = BIRU, WIRA = KUNING */
        .theme-mula {
            background: linear-gradient(180deg, #10b981 0%, #059669 50%, #047857 100%) !important;
        }
        .text-mula {
            font-family: 'Fredoka', sans-serif;
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(6, 78, 59, 0.95), 0 0 8px rgba(16, 185, 129, 0.6) !important;
        }

        .theme-madya {
            background: linear-gradient(180deg, #0284c7 0%, #0369a1 50%, #075985 100%) !important;
        }
        .text-madya {
            font-family: 'Fredoka', sans-serif;
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(12, 74, 110, 0.95), 0 0 8px rgba(14, 165, 233, 0.6) !important;
        }

        .theme-wira {
            background: linear-gradient(180deg, #facc15 0%, #eab308 50%, #ca8a04 100%) !important;
            border-color: #ffffff !important;
        }
        .text-wira {
            font-family: 'Fredoka', sans-serif;
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(113, 63, 18, 0.95), 0 0 10px rgba(161, 98, 7, 0.8), 0 1px 2px #78350f !important;
        }

        /* PRINT STYLES */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .cards-container {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .card-wrapper {
                page-break-after: always !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin: 0 auto 15mm auto !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
            }
            .card-preview {
                width: 100mm !important;
                height: 150mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }

            /* Rescaling Koordinat Cetak untuk Ukuran Nyata 100mm x 150mm (100mm = 378px, scale factor = 378/682 = 0.5542) */
            .box-nama {
                top: 72.5mm !important;
                left: 11.2mm !important;
                width: 77.5mm !important;
                height: 11.2mm !important;
            }
            .box-sekolah {
                top: 95.3mm !important;
                left: 13.4mm !important;
                width: 74.0mm !important;
                height: 11.2mm !important;
            }
            .box-nourut {
                top: 115.0mm !important;
                left: 17.8mm !important;
                width: 30.5mm !important;
                height: 13.2mm !important;
            }
            .box-matalomba {
                top: 115.0mm !important;
                left: 57.0mm !important;
                width: 28.2mm !important;
                height: 13.2mm !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-6 flex flex-col items-center min-h-screen text-slate-800">

    @php
        // 1. Ekstraksi kartu peserta perorangan: 1 KARTU = 1 NAMA PESERTA
        $individualCards = [];
        $levelStr = strtolower(trim($registration->level ?? ''));

        // Tetapkan tema warna berdasarkan tingkatan (Mula = Hijau, Madya = Biru, Wira = Kuning)
        if ($levelStr === 'mula') {
            $themeClass = 'theme-mula';
            $textClass = 'text-mula';
            $levelBadgeLabel = 'PMR Mula (SD) &bull; Hijau';
            $levelBadgeStyle = 'bg-emerald-100 text-emerald-800 border-emerald-300';
            $badgeColor = '#10b981';
        } elseif ($levelStr === 'madya') {
            $themeClass = 'theme-madya';
            $textClass = 'text-madya';
            $levelBadgeLabel = 'PMR Madya (SMP) &bull; Biru';
            $levelBadgeStyle = 'bg-sky-100 text-sky-800 border-sky-300';
            $badgeColor = '#0284c7';
        } else {
            $themeClass = 'theme-wira';
            $textClass = 'text-wira';
            $levelBadgeLabel = 'PMR Wira (SMA) &bull; Kuning';
            $levelBadgeStyle = 'bg-amber-100 text-amber-900 border-amber-300';
            $badgeColor = '#facc15';
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
                    $class = is_array($m) ? ($m['class_grade'] ?? ($m['kelas'] ?? '')) : '';

                    $individualCards[] = [
                        'team' => $team,
                        'participant_name' => trim($name),
                        'role' => $role,
                        'nisn' => $nisn,
                        'class' => $class,
                        'school_name' => $registration->school_name,
                        'order_number' => $orderNum,
                        'category_code' => $catCode,
                        'category_name' => $team->category?->name ?? 'Cabang Lomba',
                    ];
                }
            } else {
                // Fallback jika belum upload nama anggota, tetap buat 1 kartu untuk regu
                $individualCards[] = [
                    'team' => $team,
                    'participant_name' => 'Peserta ' . ($team->team_label ?: $team->team_name),
                    'role' => 'Peserta Regu',
                    'nisn' => '',
                    'class' => '',
                    'school_name' => $registration->school_name,
                    'order_number' => $orderNum,
                    'category_code' => $catCode,
                    'category_name' => $team->category?->name ?? 'Cabang Lomba',
                ];
            }
        }
    @endphp

    <!-- Action Bar (Tidak Dicetak) -->
    <div class="no-print max-w-5xl w-full mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('lomba.status', ['code' => $registration->registration_code]) }}" class="text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Status
                </a>
                <span class="text-slate-300">&bull;</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-black uppercase tracking-wider {{ $levelBadgeStyle }}">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $badgeColor }};"></span>
                    {!! $levelBadgeLabel !!}
                </span>
            </div>
            <div class="text-xs text-slate-500 mt-1">
                Kontingen: <strong class="text-slate-800">{{ $registration->school_name }}</strong> &bull; Total: <strong class="text-red-600">{{ count($individualCards) }} Lembar Kartu Peserta</strong> (1 Kartu per Nama Peserta)
            </div>
        </div>
        
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="window.print()" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print text-amber-400"></i> Cetak Semua Kartu (100&times;150 mm)
            </button>
        </div>
    </div>

    <!-- Petunjuk Cetak (Tidak Dicetak) -->
    <div class="no-print max-w-5xl w-full mb-6 bg-sky-50 border border-sky-200 rounded-xl p-3.5 text-xs text-sky-900 flex items-start gap-2.5">
        <i class="fa-solid fa-circle-info text-sky-600 text-base shrink-0 mt-0.5"></i>
        <div>
            <strong>Petunjuk Mencetak:</strong> Ukuran desain kartu adalah <strong>100 &times; 150 mm</strong> (Standar Plastik ID Card Lanyard B4 / A6). 
            Pada dialog cetak browser, pilih <em>Layout: Portrait</em>, centang <em>"Background graphics" (Grafis latar belakang)</em>, dan atur <em>Margins: None</em> agar warna gradasi tercetak tajam.
        </div>
    </div>

    <!-- Cards Container (Grid Tampilan Web & Print) -->
    <div class="cards-container max-w-5xl w-full grid grid-cols-1 md:grid-cols-2 gap-8 justify-items-center items-start">
        @foreach($individualCards as $cIdx => $card)
            @php
                $nameStr = $card['participant_name'];
                $nameLen = strlen($nameStr);
                $nameSize = $nameLen > 24 ? 'text-[16px]' : ($nameLen > 18 ? 'text-[18px]' : 'text-[20px]');

                $schoolStr = $card['school_name'];
                $schoolLen = strlen($schoolStr);
                $schoolSize = $schoolLen > 26 ? 'text-[14px]' : ($schoolLen > 20 ? 'text-[16px]' : 'text-[18px]');

                $roleStr = $card['role'];
                $isLeader = strcasecmp($roleStr, 'Ketua') === 0 || strcasecmp($roleStr, 'Ketua Regu') === 0;
            @endphp

            <div class="card-wrapper flex flex-col items-center">
                <!-- Info Header Mini (No-Print) -->
                <div class="no-print w-[440px] flex items-center justify-between text-[11px] text-slate-500 font-bold mb-2 px-1">
                    <span>
                        <i class="fa-solid fa-user-tag text-slate-400"></i> Kartu #{{ $cIdx + 1 }}: 
                        <span class="text-slate-800">{{ $nameStr }}</span>
                        @if($isLeader)<span class="text-red-600 font-black ml-1">(Ketua)</span>@endif
                    </span>
                    <span class="font-mono text-slate-400 font-normal">100 &times; 150 mm</span>
                </div>

                <!-- Kartu Peserta Resmi Sesuai Template Gambar -->
                <div class="card-preview">
                    <!-- Lubang Tali Lanyard -->
                    <div class="lanyard-hole"></div>

                    <!-- Kotak 1: NAMA PESERTA -->
                    <div class="box-overlay box-nama {{ $themeClass }}">
                        <span class="{{ $textClass }} {{ $nameSize }} font-black uppercase tracking-wide truncate">
                            {{ $nameStr }}
                        </span>
                    </div>

                    <!-- Kotak 2: SEKOLAH -->
                    <div class="box-overlay box-sekolah {{ $themeClass }}">
                        <span class="{{ $textClass }} {{ $schoolSize }} font-black uppercase tracking-wide truncate">
                            {{ $schoolStr }}
                        </span>
                    </div>

                    <!-- Kotak 3: NO. URUT -->
                    <div class="box-overlay box-nourut {{ $themeClass }}">
                        <span class="{{ $textClass }} text-[30px] font-black tracking-wider font-mono">
                            {{ $card['order_number'] }}
                        </span>
                    </div>

                    <!-- Kotak 4: MATA LOMBA -->
                    <div class="box-overlay box-matalomba {{ $themeClass }}">
                        <span class="{{ $textClass }} text-[14px] font-black uppercase tracking-tight">
                            {{ $card['category_code'] }}
                        </span>
                    </div>
                </div>

                <!-- Footer Mini Keterangan (No-Print) -->
                <div class="no-print text-center text-xs text-slate-400 mt-2 font-medium">
                    {{ $card['category_name'] }} &bull; Regu: {{ $card['team']->team_label ?: $card['team']->team_name }}
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
