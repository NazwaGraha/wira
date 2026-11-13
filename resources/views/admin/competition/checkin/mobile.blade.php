<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Ulang Peserta - {{ $event->name ?? 'PMR Wira SMAN 1 Ciawi' }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: { 950: '#070b14' }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @keyframes laserSweep {
            0% { top: 6%; opacity: 0.2; }
            50% { opacity: 1; }
            100% { top: 94%; opacity: 0.2; }
        }
        .laser-line {
            animation: laserSweep 2s ease-in-out infinite alternate;
        }
        #reader video {
            object-fit: cover !important;
            border-radius: 1.25rem !important;
            width: 100% !important;
            height: 100% !important;
        }
        #reader__scan_region {
            border-radius: 1.25rem !important;
            overflow: hidden !important;
        }
        #reader__dashboard_section_csr,
        #reader__dashboard_section_swaplink,
        #reader__status_span {
            display: none !important;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans select-none pb-24 overflow-x-hidden">

    <!-- Top App Bar (Header Ramping & Ergonomis) -->
    <header class="bg-slate-900 border-b border-slate-800 px-3 sm:px-4 py-2.5 shrink-0 flex items-center justify-between sticky top-0 z-30 shadow-md">
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-red-600 to-rose-700 flex items-center justify-center text-white text-sm shadow-md shrink-0">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="text-[9px] font-black text-rose-400 uppercase tracking-wider truncate">MEJA DAFTAR ULANG</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[8px] font-bold shrink-0">
                        ● Panitia
                    </span>
                </div>
                <h1 class="text-xs font-black text-white truncate max-w-[150px] sm:max-w-[220px]">
                    {{ $event?->name ?? 'Sua Bhakti Berkarya IV' }}
                </h1>
            </div>
        </div>

        <!-- Event Selector & Action Buttons -->
        <div class="flex items-center gap-1.5 shrink-0">
            @if(isset($allEvents) && $allEvents->count() > 1)
                <select onchange="window.location.href='?event_id=' + this.value" class="text-[11px] font-bold bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-rose-500 max-w-[110px] sm:max-w-[160px] truncate">
                    @foreach($allEvents as $ev)
                        <option value="{{ $ev->id }}" {{ ($event?->id == $ev->id) ? 'selected' : '' }}>
                            {{ $ev->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <a href="{{ route('admin.competition-checkin.index') }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-xs transition border border-slate-700 active:scale-95 shrink-0" title="Beralih ke Tampilan Desktop Admin">
                <i class="fa-solid fa-desktop"></i>
            </a>

            <a href="{{ route('admin.dashboard') }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 flex items-center justify-center text-xs transition border border-slate-700 active:scale-95 shrink-0" title="Kembali ke Dashboard">
                <i class="fa-solid fa-house"></i>
            </a>
        </div>
    </header>

    <!-- Bilah Statistik Presensi Kehadiran Real-Time -->
    <div class="bg-slate-900/90 border-b border-slate-800 px-3.5 py-2.5 shrink-0">
        <div class="grid grid-cols-3 gap-2 text-center">
            <!-- Total Lunas -->
            <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-1.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total Kontingen</span>
                <span id="stat-total" class="font-mono font-black text-sm sm:text-base text-white block">{{ $totalVerified }}</span>
                <span class="text-[8.5px] text-slate-500 font-semibold">Terverifikasi</span>
            </div>

            <!-- Sudah Hadir -->
            <div class="bg-emerald-950/20 border border-emerald-500/30 rounded-xl p-1.5">
                <span class="text-[9px] font-bold text-emerald-400 uppercase tracking-wider block">Sudah Hadir</span>
                <span id="stat-checked" class="font-mono font-black text-sm sm:text-base text-emerald-400 block">{{ $totalCheckedIn }}</span>
                <span id="stat-rate" class="text-[8.5px] text-emerald-500 font-bold">{{ $attendanceRate }}%</span>
            </div>

            <!-- Belum Hadir -->
            <div class="bg-amber-950/20 border border-amber-500/30 rounded-xl p-1.5">
                <span class="text-[9px] font-bold text-amber-400 uppercase tracking-wider block">Belum Hadir</span>
                <span id="stat-pending" class="font-mono font-black text-sm sm:text-base text-amber-400 block">{{ $totalPending }}</span>
                <span class="text-[8.5px] text-amber-500 font-semibold">Menunggu</span>
            </div>
        </div>

        <!-- Attendance Progress Bar -->
        <div class="mt-2 flex items-center gap-2">
            <div class="flex-1 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                <div id="stat-progressbar" class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
            </div>
            <span id="stat-rate-text" class="text-[9.5px] font-bold text-slate-400 shrink-0 font-mono">{{ $attendanceRate }}% Kehadiran</span>
        </div>
    </div>

    <!-- Main Content Area with 3 Modes -->
    <main class="flex-1 p-3 sm:p-4 max-w-lg mx-auto w-full">

        <!-- ================= MODE 1: SCANNER MEJA REGISTRASI ================= -->
        <div id="view-scan" class="space-y-3.5">
            
            <!-- Camera Viewfinder Box -->
            <div class="relative w-full aspect-square max-w-[340px] sm:max-w-[360px] mx-auto rounded-3xl overflow-hidden bg-slate-900 border-2 border-slate-800 shadow-2xl flex flex-col justify-center items-center">
                <!-- HTML5 QR Code Container -->
                <div id="reader" class="w-full h-full overflow-hidden rounded-2xl"></div>

                <!-- Laser & Targeting Overlay -->
                <div class="absolute inset-5 pointer-events-none flex flex-col justify-between">
                    <!-- Laser line -->
                    <div class="absolute left-0 right-0 h-0.5 bg-rose-500 shadow-[0_0_12px_#f43f5e] laser-line"></div>
                    <!-- Corner brackets -->
                    <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-rose-500 rounded-tl-lg"></div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-rose-500 rounded-tr-lg"></div>
                    <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-rose-500 rounded-bl-lg"></div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-rose-500 rounded-br-lg"></div>
                </div>

                <!-- Floating Controls inside Viewfinder Top-Right Corner (Ergonomis) -->
                <div class="absolute top-3 right-3 flex items-center gap-2 z-10">
                    <!-- Audio Voice Test (Tes Suara Notifikasi) -->
                    <button type="button" onclick="testAudioVoice()" id="btnAudioTest" class="w-9 h-9 rounded-full bg-black/60 hover:bg-black/80 backdrop-blur-md text-white flex items-center justify-center text-xs border border-white/20 active:scale-95 transition shadow-lg" title="Tes Suara Notifikasi (Perempuan / Laki-laki)">
                        <i class="fa-solid fa-volume-high text-emerald-400"></i>
                    </button>

                    <!-- Torch (Senter) -->
                    <button type="button" onclick="toggleTorch()" id="btnTorch" class="btn-torch-toggle w-9 h-9 rounded-full bg-black/60 hover:bg-black/80 backdrop-blur-md text-white flex items-center justify-center text-xs border border-white/20 active:scale-95 transition shadow-lg" title="Senter / Flashlight">
                        <i class="fa-solid fa-bolt text-yellow-400"></i>
                    </button>

                    <!-- Switch Camera (Putar Kamera Depan/Belakang) -->
                    <button type="button" onclick="switchCameraFacing()" id="btnSwitchCamera" class="w-9 h-9 rounded-full bg-black/60 hover:bg-black/80 backdrop-blur-md text-white flex items-center justify-center text-xs border border-white/20 active:scale-95 transition shadow-lg" title="Ganti Kamera Depan / Belakang">
                        <i class="fa-solid fa-camera-rotate text-rose-400"></i>
                    </button>
                </div>

                <!-- Hidden select for device switching if available -->
                <select id="cameraSelect" onchange="onCameraSelectChange(this.value)" class="hidden"></select>

                <!-- Instruction text at bottom of viewfinder -->
                <div class="absolute bottom-3 left-0 right-0 text-center pointer-events-none px-4">
                    <span class="inline-block px-3 py-1 bg-black/75 backdrop-blur-xs text-slate-300 text-[10px] font-bold rounded-full border border-slate-700/80">
                        Arahkan ke QR Code Kwitansi / Kartu Peserta
                    </span>
                </div>
            </div>

            <!-- Integrated Search Capsule (Terkunci Rapi, Tidak Melebar) -->
            <div class="relative flex items-center w-full bg-slate-900 border border-slate-800 rounded-2xl p-1 shadow-inner focus-within:ring-2 focus-within:ring-rose-500 focus-within:border-transparent transition">
                <div class="pl-3 pr-1 text-slate-500 text-xs shrink-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" id="manualCodeInput" onkeydown="if(event.key==='Enter') handleManualLookup()" placeholder="Ketik No. Registrasi / Nama Sekolah..." class="w-full bg-transparent px-2 py-2 text-xs text-white uppercase font-mono tracking-wider focus:outline-none placeholder:text-slate-500 placeholder:normal-case placeholder:font-sans">
                <button type="button" onclick="handleManualLookup()" class="px-3.5 py-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black text-xs rounded-xl transition shadow-md active:scale-95 shrink-0 flex items-center gap-1.5">
                    <span>Cari</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>
            </div>

            <!-- Panduan Format Registrasi -->
            <div class="p-2.5 bg-slate-900/70 border border-slate-800 rounded-2xl flex items-center justify-between text-xs gap-2">
                <div class="flex items-center gap-2 text-slate-400 min-w-0">
                    <i class="fa-solid fa-qrcode text-rose-400 shrink-0 text-sm"></i>
                    <span class="text-[11px] truncate">Format Kode: <strong class="text-slate-200 font-mono">SBB-W...</strong> atau <strong class="text-slate-200 font-mono">SBB-M...</strong></span>
                </div>
                <button type="button" onclick="switchTab('schools')" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[10px] rounded-xl transition border border-slate-700 shrink-0">
                    Lihat Semua &rarr;
                </button>
            </div>

            <!-- Camera Error Notice Banner -->
            <div id="cameraNotice" class="hidden p-3 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-xs text-rose-300 flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 mt-0.5 shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <strong class="block font-bold text-white mb-0.5">Izin Akses Kamera Diperlukan</strong>
                    <span id="cameraNoticeText">Kamera tidak aktif. Berikan izin browser atau gunakan pencarian manual dengan nomor registrasi di atas.</span>
                </div>
            </div>

        </div>

        <!-- ================= MODE 2: DAFTAR SEKOLAH (LIST KONTINGEN) ================= -->
        <div id="view-schools" class="hidden space-y-3">
            <!-- Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px] font-bold">
                <button type="button" onclick="filterSchools('all')" id="flt-all" class="px-3 py-1.5 rounded-xl bg-slate-800 text-white border border-slate-700 shrink-0 transition">
                    Semua ({{ $registrations->count() }})
                </button>
                <button type="button" onclick="filterSchools('pending')" id="flt-pending" class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/30 shrink-0 transition">
                    Belum Hadir ({{ $registrations->where('is_checked_in', false)->count() }})
                </button>
                <button type="button" onclick="filterSchools('checked_in')" id="flt-checked_in" class="px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0 transition">
                    Sudah Hadir ({{ $registrations->where('is_checked_in', true)->count() }})
                </button>
            </div>

            <!-- Search Inside School List -->
            <div class="relative flex items-center bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs">
                <i class="fa-solid fa-magnifying-glass text-slate-500 mr-2"></i>
                <input type="text" id="schoolListSearch" oninput="searchSchoolList(this.value)" placeholder="Cari nama sekolah / pembina..." class="bg-transparent text-white w-full focus:outline-none placeholder:text-slate-500">
            </div>

            <!-- List Cards -->
            <div class="space-y-2" id="schoolCardsContainer">
                @forelse($registrations as $reg)
                    <div class="school-card bg-slate-900 border border-slate-800/80 hover:border-slate-700 rounded-2xl p-3 transition active:scale-[0.99] cursor-pointer" 
                         data-code="{{ $reg->registration_code }}"
                         data-name="{{ strtolower($reg->school_name) }}"
                         data-advisor="{{ strtolower($reg->advisor_name ?? '') }}"
                         data-status="{{ $reg->is_checked_in ? 'checked_in' : 'pending' }}"
                         onclick="inspectRegistration('{{ $reg->registration_code }}')">
                        
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase font-mono {{ strtolower($reg->level) === 'wira' ? 'bg-red-500/20 text-rose-400 border border-rose-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30' }}">
                                        {{ strtoupper($reg->level) }}
                                    </span>
                                    <span class="font-mono text-[10px] text-slate-400 font-bold">
                                        {{ $reg->registration_code }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-white text-xs sm:text-sm truncate">
                                    {{ $reg->school_name }}
                                </h4>
                                <div class="text-[10.5px] text-slate-400 mt-0.5 truncate">
                                    <i class="fa-solid fa-user-tie text-[9px] mr-1 text-slate-500"></i>{{ $reg->advisor_name ?? 'Pembina (-)' }}
                                    <span class="mx-1">•</span>
                                    <i class="fa-solid fa-users text-[9px] mr-1 text-slate-500"></i>{{ $reg->teams->count() }} Regu
                                </div>
                            </div>

                            <div class="shrink-0 text-right">
                                @if($reg->is_checked_in)
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 block">
                                        ✓ Hadir
                                    </span>
                                    <span class="text-[8.5px] text-slate-500 block mt-1 font-mono">
                                        {{ $reg->checked_in_at ? $reg->checked_in_at->format('H:i') : '' }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30 block">
                                        Menunggu
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-slate-900 rounded-2xl border border-slate-800 text-slate-400 text-xs">
                        Belum ada kontingen terverifikasi pada event ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ================= MODE 3: REKAPITULASI KEHADIRAN ================= -->
        <div id="view-recap" class="hidden space-y-3">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 text-xs space-y-3">
                <h3 class="font-black text-white text-sm flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-rose-500"></i>
                    <span>Rekapitulasi Kehadiran Hari-H</span>
                </h3>

                <!-- Tingkat Wira vs Madya -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <div class="bg-slate-950 p-3 rounded-xl border border-slate-800/80">
                        <div class="flex items-center justify-between text-slate-400 text-[10px] font-bold uppercase mb-1">
                            <span>Tingkat Wira (SMA)</span>
                            <span class="text-rose-400">{{ $wiraCount }}</span>
                        </div>
                        <div class="text-sm font-black text-white">
                            {{ $registrations->where('level', 'wira')->where('is_checked_in', true)->count() }} / {{ $wiraCount }} Hadir
                        </div>
                    </div>

                    <div class="bg-slate-950 p-3 rounded-xl border border-slate-800/80">
                        <div class="flex items-center justify-between text-slate-400 text-[10px] font-bold uppercase mb-1">
                            <span>Tingkat Madya (SMP)</span>
                            <span class="text-blue-400">{{ $madyaCount }}</span>
                        </div>
                        <div class="text-sm font-black text-white">
                            {{ $registrations->where('level', 'madya')->where('is_checked_in', true)->count() }} / {{ $madyaCount }} Hadir
                        </div>
                    </div>
                </div>

                <!-- Fast Actions -->
                <div class="pt-2 border-t border-slate-800 flex gap-2">
                    <a href="{{ route('admin.competition-participants.index') }}" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-center rounded-xl transition text-[11px] border border-slate-700 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-users-viewfinder"></i> Data Peserta & Regu
                    </a>
                    <a href="{{ route('admin.competition-checkin.index') }}" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-center rounded-xl transition text-[11px] border border-slate-700 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-desktop"></i> Tabel Admin Lengkap
                    </a>
                </div>
            </div>
        </div>

    </main>

    <!-- ================= MODAL HASIL VERIFIKASI PESERTA (BOTTOM SHEET) ================= -->
    <div id="modalResult" onclick="if(event.target === this) closeResultModal()" class="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-3 sm:p-4 hidden transition-opacity duration-200">
        <div class="bg-slate-900 border border-slate-700/80 rounded-2xl sm:rounded-3xl w-full max-w-sm sm:max-w-md shadow-2xl max-h-[92dvh] flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            
            <!-- Status Header Bar -->
            <div class="p-3 border-b border-slate-800 bg-slate-950/60 shrink-0">
                <div id="resBadgeWrapper" class="px-3 py-2 rounded-xl border flex items-center justify-between gap-2 transition-all">
                    <div class="flex items-center gap-2 min-w-0">
                        <div id="resIcon" class="w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 shadow-inner">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="min-w-0">
                            <div id="resStatusBadge" class="text-[11px] sm:text-xs font-black uppercase tracking-wide truncate">
                                PESERTA TERVERIFIKASI
                            </div>
                            <div id="resMessage" class="text-[9.5px] sm:text-[10px] text-slate-400 truncate">
                                Terdaftar resmi & lunas
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="closeResultModal()" class="w-6 h-6 rounded-full bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center shrink-0 transition active:scale-95 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Content Body -->
            <div class="p-3.5 overflow-y-auto space-y-2.5 text-xs">
                
                <!-- Hero School Card -->
                <div class="bg-black/90 border border-slate-700/80 rounded-xl px-3.5 py-3 text-center shadow-inner relative">
                    <div class="flex items-center justify-between text-[9px] text-slate-400 font-mono font-semibold tracking-wider mb-1">
                        <span id="resLevelBadge" class="px-1.5 py-0.5 rounded font-black text-rose-400 bg-rose-500/20 border border-rose-500/30">
                            WIRA
                        </span>
                        <span id="resCode" class="text-slate-300 font-bold font-mono">SBB-W54342H</span>
                    </div>
                    
                    <h3 id="resSchoolName" class="font-black text-base sm:text-lg text-white leading-tight uppercase select-all block py-1">
                        SMAN 1 CIAWI BOGOR
                    </h3>

                    <div class="flex items-center justify-center gap-2 text-[10px] font-bold text-slate-300 mt-1">
                        <span id="resTeamSummary" class="text-emerald-400 font-bold">5 Regu Lomba</span>
                        <span class="text-slate-500">•</span>
                        <span id="resFee" class="text-slate-300 font-mono">Rp 500.000 (Lunas)</span>
                    </div>
                </div>

                <!-- Info Grid Kompak -->
                <div class="bg-slate-950/80 rounded-xl p-2.5 border border-slate-800 text-[11px] space-y-1.5">
                    <!-- Pembina -->
                    <div class="flex items-center justify-between gap-2 pb-1.5 border-b border-slate-800/70">
                        <span class="text-slate-400 flex items-center gap-1 text-[10.5px] shrink-0">
                            <i class="fa-solid fa-user-tie text-slate-500 text-[10px]"></i> Pembina:
                        </span>
                        <div class="flex items-center gap-1.5 text-right min-w-0">
                            <span id="resAdvisor" class="font-bold text-white text-[11px] truncate max-w-[170px]">Ibu Siti Rahmawati</span>
                            <a id="resAdvisorWa" href="#" target="_blank" class="shrink-0 px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1 hover:bg-emerald-500/30 transition">
                                <i class="fa-brands fa-whatsapp"></i> Chat
                            </a>
                        </div>
                    </div>

                    <!-- Status Presensi Sebelumnya -->
                    <div class="flex items-center justify-between gap-2" id="rowCheckinStatus">
                        <span class="text-slate-400 flex items-center gap-1 text-[10.5px] shrink-0">
                            <i class="fa-solid fa-clock-rotate-left text-slate-500 text-[10px]"></i> Presensi:
                        </span>
                        <div class="text-right">
                            <span id="resCheckinText" class="font-bold text-amber-400">Belum Daftar Ulang</span>
                        </div>
                    </div>
                </div>

                <!-- Daftar Regu / Cabang Lomba -->
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Regu Terdaftar:</span>
                    <div id="resTeamsList" class="flex flex-wrap gap-1 max-h-24 overflow-y-auto p-1 bg-slate-950/60 rounded-xl border border-slate-800/70">
                        <!-- Dynamic team badges -->
                    </div>
                </div>

                <!-- Catatan Panitia Meja Registrasi (Serah Terima Kit) -->
                <div>
                    <label for="checkinNotes" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                        Catatan Serah Terima Kit Hari-H (Opsional):
                    </label>
                    <input type="text" id="checkinNotes" placeholder="Contoh: 18 ID Card & Kit lomba diterima lengkap" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-1 focus:ring-rose-500">
                </div>

                <!-- Hidden Input ID -->
                <input type="hidden" id="currentRegistrationId" value="">
                <input type="hidden" id="currentRegistrationCode" value="">

                <!-- Action Buttons -->
                <div class="space-y-1.5 pt-1.5">
                    <!-- Tombol Konfirmasi Hadir -->
                    <button type="button" id="btnConfirmCheckIn" onclick="submitCheckIn()" class="w-full py-2.5 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-xs sm:text-sm rounded-xl shadow-md transition active:scale-[0.98] flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                        <span>Konfirmasi Daftar Ulang (Check-In)</span>
                    </button>

                    <!-- Tombol Batalkan Check-in (Bila sudah hadir) -->
                    <button type="button" id="btnCancelCheckIn" onclick="submitCancelCheckIn()" class="hidden w-full py-2 bg-rose-950/60 hover:bg-rose-900 text-rose-300 font-bold text-xs rounded-xl border border-rose-800/60 transition flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Batalkan Status Daftar Ulang (Keliru)</span>
                    </button>

                    <div class="grid grid-cols-2 gap-1.5">
                        <a id="btnParticipantCards" href="#" target="_blank" class="py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-[11px] rounded-lg border border-slate-700 transition flex items-center justify-center gap-1 active:scale-95">
                            <i class="fa-solid fa-id-badge text-[10px] text-blue-400"></i> Kartu Peserta
                        </a>
                        <button type="button" onclick="closeResultModal()" class="py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] rounded-lg border border-slate-700 transition flex items-center justify-center gap-1 active:scale-95">
                            <i class="fa-solid fa-qrcode text-[10px]"></i> Tutup / Scan Lagi
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- ================= MODAL ERROR / PESERTA TIDAK BISA DIVERIFIKASI ================= -->
    <div id="modalError" onclick="if(event.target === this) closeErrorModal()" class="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-3 sm:p-4 hidden transition-opacity duration-200">
        <div class="bg-slate-900 border border-rose-500/40 rounded-2xl sm:rounded-3xl w-full max-w-sm shadow-2xl p-5 flex flex-col items-center text-center animate-in zoom-in-95 duration-200">
            <div class="w-14 h-14 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-2xl mb-3 shadow-lg shadow-rose-950/50">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <span class="px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30 text-[10px] font-black uppercase tracking-wider mb-1">
                PESERTA TIDAK BISA DIVERIFIKASI
            </span>
            <h3 class="text-sm sm:text-base font-black text-white mt-1">Silahkan Hubungi Panitia</h3>
            <p id="modalErrorMessage" class="text-xs text-slate-300 mt-2 mb-4 leading-relaxed px-2">
                Nomor Registrasi atau Sekolah tidak ditemukan atau belum diverifikasi lunas dalam sistem.
            </p>
            <button type="button" onclick="closeErrorModal()" class="w-full py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black text-xs rounded-xl shadow-md transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fa-solid fa-rotate-right text-xs"></i>
                <span>Tutup & Scan Lagi</span>
            </button>
        </div>
    </div>

    <!-- Bottom Navigation Bar (Mobile Native Style) -->
    <nav class="fixed bottom-0 left-0 right-0 bg-slate-900/95 border-t border-slate-800 backdrop-blur-md px-4 py-1.5 z-40 flex justify-around items-center max-w-lg mx-auto shadow-2xl">
        <!-- Tab 1: Meja Scan QR -->
        <button type="button" onclick="switchTab('scan')" id="tab-btn-scan" class="flex flex-col items-center gap-1 text-rose-500 font-bold transition py-1">
            <div class="w-10 h-7 rounded-xl flex items-center justify-center text-base">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <span class="text-[10px]">Meja Scan</span>
        </button>

        <!-- Tab 2: Daftar Sekolah -->
        <button type="button" onclick="switchTab('schools')" id="tab-btn-schools" class="flex flex-col items-center gap-1 text-slate-400 hover:text-amber-400 font-bold transition py-1">
            <div class="w-10 h-7 rounded-xl flex items-center justify-center text-base">
                <i class="fa-solid fa-school"></i>
            </div>
            <span class="text-[10px]">Daftar Sekolah</span>
        </button>

        <!-- Tab 3: Rekap Kehadiran -->
        <button type="button" onclick="switchTab('recap')" id="tab-btn-recap" class="flex flex-col items-center gap-1 text-slate-400 hover:text-blue-400 font-bold transition py-1">
            <div class="w-10 h-7 rounded-xl flex items-center justify-center text-base">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <span class="text-[10px]">Rekap</span>
        </button>
    </nav>

    <!-- JavaScript App Logic -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const eventId = {{ $event?->id ?? 'null' }};

        // State Kamera & Scanner
        let html5QrCode = null;
        let isScanning = false;
        let isProcessing = false;
        let isTorchOn = false;
        let currentFacingMode = "environment";
        let currentCameraId = null;
        let availableCameras = [];

        // ================= AUDIO NOTIFICATION SYSTEM (LOUD & HUMAN VOICE) =================
        const audioValid = new Audio("{{ asset('audio/peserta-valid.mp3') }}");
        const audioInvalid = new Audio("{{ asset('audio/peserta-invalid.mp3') }}");
        audioValid.preload = 'auto';
        audioInvalid.preload = 'auto';

        let audioUnlocked = false;
        function unlockAudio() {
            if (audioUnlocked) return;
            audioUnlocked = true;
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
            } catch (e) {}

            [audioValid, audioInvalid].forEach(a => {
                try {
                    a.volume = 0.01;
                    const p = a.play();
                    if (p !== undefined) {
                        p.then(() => {
                            a.pause();
                            a.currentTime = 0;
                            a.volume = 1.0;
                        }).catch(() => {});
                    }
                } catch (e) {}
            });
        }
        document.addEventListener('touchstart', unlockAudio, { passive: true, once: true });
        document.addEventListener('click', unlockAudio, { passive: true, once: true });

        // Web Audio API Synthesizer (Loud Crisp Chime & Buzzer)
        function playBeep(type = 'success') {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (type === 'success') {
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1760, ctx.currentTime + 0.18);
                    gain.gain.setValueAtTime(0.85, ctx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.01, ctx.currentTime + 0.18);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.18);
                } else {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(320, ctx.currentTime);
                    osc.frequency.linearRampToValueAtTime(160, ctx.currentTime + 0.35);
                    gain.gain.setValueAtTime(0.85, ctx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.35);
                }
            } catch (e) {}
        }

        // Web Speech API Fallback
        function speakVoice(text, isFemale = true) {
            if (!('speechSynthesis' in window)) return;
            try {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 1.0;
                utterance.pitch = isFemale ? 1.25 : 0.75;
                utterance.volume = 1.0;

                const voices = window.speechSynthesis.getVoices();
                if (voices && voices.length) {
                    const idVoice = voices.find(v => {
                        const name = (v.name || '').toLowerCase();
                        const lang = (v.lang || '').toLowerCase();
                        if (!lang.startsWith('id')) return false;
                        return isFemale 
                            ? (name.includes('female') || name.includes('wanita') || name.includes('perempuan') || name.includes('gadis') || name.includes('damayanti'))
                            : (name.includes('male') || name.includes('pria') || name.includes('laki') || name.includes('ardi'));
                    });
                    if (idVoice) utterance.voice = idVoice;
                }
                window.speechSynthesis.speak(utterance);
            } catch (e) {
                console.warn('Speech synthesis error:', e);
            }
        }

        // Main Audio Notification:
        // Valid = Suara Perempuan: "Peserta Terverifikasi"
        // Invalid = Suara Laki-laki: "Peserta Tidak Bisa Diverifikasi Silahkan Hubungi Panitia"
        function playNotification(type = 'success') {
            unlockAudio();
            playBeep(type);

            const audio = (type === 'success') ? audioValid : audioInvalid;
            if (audio) {
                audio.currentTime = 0;
                audio.volume = 1.0;
                const p = audio.play();
                if (p !== undefined) {
                    p.catch(err => {
                        console.warn('Audio play blocked/failed, fallback to SpeechSynthesis:', err);
                        speakVoice(
                            type === 'success' 
                                ? 'Peserta Terverifikasi' 
                                : 'Peserta Tidak Bisa Diverifikasi Silahkan Hubungi Panitia', 
                            type === 'success'
                        );
                    });
                }
            } else {
                speakVoice(
                    type === 'success' 
                        ? 'Peserta Terverifikasi' 
                        : 'Peserta Tidak Bisa Diverifikasi Silahkan Hubungi Panitia', 
                    type === 'success'
                );
            }
        }

        let lastAudioTestSuccess = false;
        function testAudioVoice() {
            unlockAudio();
            if (!lastAudioTestSuccess) {
                playNotification('success');
                lastAudioTestSuccess = true;
            } else {
                playNotification('error');
                lastAudioTestSuccess = false;
            }
        }

        // ================= TAB NAVIGATION SWITCHER =================
        function switchTab(tab) {
            document.getElementById('view-scan').classList.add('hidden');
            document.getElementById('view-schools').classList.add('hidden');
            document.getElementById('view-recap').classList.add('hidden');

            document.getElementById('tab-btn-scan').className = 'flex flex-col items-center gap-1 text-slate-400 hover:text-rose-400 font-bold transition py-1';
            document.getElementById('tab-btn-schools').className = 'flex flex-col items-center gap-1 text-slate-400 hover:text-amber-400 font-bold transition py-1';
            document.getElementById('tab-btn-recap').className = 'flex flex-col items-center gap-1 text-slate-400 hover:text-blue-400 font-bold transition py-1';

            if (tab === 'scan') {
                document.getElementById('view-scan').classList.remove('hidden');
                document.getElementById('tab-btn-scan').className = 'flex flex-col items-center gap-1 text-rose-500 font-bold transition py-1';
                if (!isScanning) startCameraScanner();
            } else if (tab === 'schools') {
                document.getElementById('view-schools').classList.remove('hidden');
                document.getElementById('tab-btn-schools').className = 'flex flex-col items-center gap-1 text-amber-400 font-bold transition py-1';
            } else if (tab === 'recap') {
                document.getElementById('view-recap').classList.remove('hidden');
                document.getElementById('tab-btn-recap').className = 'flex flex-col items-center gap-1 text-blue-400 font-bold transition py-1';
            }
        }

        // ================= QR SCANNER & CAMERA CONTROLS =================
        document.addEventListener('DOMContentLoaded', () => {
            initScanner();
        });

        async function initScanner() {
            try {
                availableCameras = await Html5Qrcode.getCameras();
                populateCameraSelect(availableCameras);
            } catch (e) {
                console.warn('Gagal membaca daftar kamera:', e);
            }
            startCameraScanner();
        }

        function populateCameraSelect(devices) {
            const selectEl = document.getElementById('cameraSelect');
            if (!selectEl || !devices || !devices.length) return;
            selectEl.innerHTML = '';
            devices.forEach((dev, idx) => {
                const opt = document.createElement('option');
                opt.value = dev.id;
                opt.text = dev.label || `Kamera ${idx + 1}`;
                if (dev.label && dev.label.toLowerCase().includes('back')) {
                    opt.selected = true;
                    currentCameraId = dev.id;
                }
                selectEl.appendChild(opt);
            });
        }

        async function startCameraScanner(targetCamera = null) {
            if (html5QrCode && isScanning) {
                try {
                    await html5QrCode.stop();
                } catch (e) {}
                isScanning = false;
            }

            html5QrCode = new Html5Qrcode("reader");

            const config = {
                fps: 15,
                qrbox: (viewfinderWidth, viewfinderHeight) => {
                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    const boxSize = Math.floor(minEdge * 0.85);
                    return { width: boxSize, height: boxSize };
                },
                aspectRatio: 1.0
            };

            const onScanSuccess = (decodedText) => {
                if (!isProcessing) {
                    isProcessing = true;

                    // Ekstraksi jika barcode memuat URL lengkap (misal: .../kwitansi/{code} atau .../kartu-peserta/{code})
                    let cleanCode = decodedText ? decodedText.trim() : '';
                    try {
                        if (cleanCode.startsWith('http://') || cleanCode.startsWith('https://')) {
                            const urlObj = new URL(cleanCode);
                            const pathSegments = urlObj.pathname.split('/').filter(Boolean);
                            if (pathSegments.length > 0) {
                                cleanCode = pathSegments[pathSegments.length - 1];
                            }
                        } else if (cleanCode.includes('/')) {
                            const parts = cleanCode.split('/').filter(Boolean);
                            if (parts.length > 0) {
                                cleanCode = parts[parts.length - 1].split('?')[0].split('#')[0];
                            }
                        }
                    } catch (e) {
                        console.warn('URL parsing fallback:', e);
                    }

                    lookupCode(cleanCode || decodedText);
                }
            };

            const onScanError = (errorMessage) => {
                // Ignore silent continuous frame scanning mismatch
            };

            let target = targetCamera || currentCameraId;
            if (!target) {
                target = { facingMode: currentFacingMode };
            }

            try {
                await html5QrCode.start(target, config, onScanSuccess, onScanError);
                isScanning = true;
                const notice = document.getElementById('cameraNotice');
                if (notice) notice.classList.add('hidden');

                // Optimasi Safari iOS / WebKit
                const videoEl = document.querySelector('#reader video');
                if (videoEl) {
                    videoEl.setAttribute('playsinline', 'true');
                    videoEl.setAttribute('webkit-playsinline', 'true');
                }
            } catch (err) {
                console.warn("Gagal memulai kamera scanner:", err);
                // Fallback kamera depan/laptop
                if (!currentCameraId && currentFacingMode === "environment") {
                    try {
                        currentFacingMode = "user";
                        await html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, onScanError);
                        isScanning = true;
                        return;
                    } catch (fallbackErr) {}
                }

                const notice = document.getElementById('cameraNotice');
                if (notice) notice.classList.remove('hidden');
            }
        }

        async function switchCameraFacing() {
            currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
            currentCameraId = null;
            await startCameraScanner();
        }

        function onCameraSelectChange(cameraId) {
            currentCameraId = cameraId;
            startCameraScanner(cameraId);
        }

        async function toggleTorch() {
            if (!html5QrCode || !isScanning) return;
            try {
                isTorchOn = !isTorchOn;
                await html5QrCode.applyVideoConstraints({
                    advanced: [{ torch: isTorchOn }]
                });
                document.querySelectorAll('.btn-torch-toggle, #btnTorch').forEach(el => {
                    el.classList.toggle('text-yellow-400', isTorchOn);
                    el.classList.toggle('border-yellow-400/50', isTorchOn);
                });
            } catch (err) {
                alert("Lampu senter (torch) hanya didukung pada kamera belakang smartphone.");
            }
        }

        // ================= LOOKUP & VERIFIKASI PESERTA =================
        function handleManualLookup() {
            const val = document.getElementById('manualCodeInput').value.trim();
            if (!val) {
                alert('Silakan masukkan Nomor Registrasi atau Nama Sekolah.');
                return;
            }
            lookupCode(val);
        }

        function inspectRegistration(code) {
            lookupCode(code);
        }

        async function lookupCode(code) {
            try {
                const url = `{{ route('admin.competition-checkin.lookup') }}?code=${encodeURIComponent(code)}`;
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!data.success) {
                    playNotification('error');
                    showErrorModal(data.message || 'Peserta Tidak Bisa Diverifikasi. Silahkan Hubungi Panitia.');
                    return;
                }

                playNotification('success');
                showResultModal(data.registration);
            } catch (err) {
                playNotification('error');
                showErrorModal('Gagal memverifikasi pendaftaran: ' + err.message);
            }
        }

        function showResultModal(reg) {
            document.getElementById('currentRegistrationId').value = reg.id;
            document.getElementById('currentRegistrationCode').value = reg.registration_code;

            // Header Hero
            document.getElementById('resCode').innerText = reg.registration_code;
            document.getElementById('resSchoolName').innerText = reg.school_name;
            
            const lvlBadge = document.getElementById('resLevelBadge');
            if (lvlBadge) {
                lvlBadge.innerText = reg.level || 'WIRA';
                lvlBadge.className = (reg.level && reg.level.toLowerCase() === 'madya')
                    ? 'px-1.5 py-0.5 rounded font-black text-blue-400 bg-blue-500/20 border border-blue-500/30'
                    : 'px-1.5 py-0.5 rounded font-black text-rose-400 bg-rose-500/20 border border-rose-500/30';
            }

            document.getElementById('resTeamSummary').innerText = `${reg.teams_count} Regu Lomba`;
            document.getElementById('resFee').innerText = `Rp ${reg.total_payment || reg.total_fee} (Lunas)`;

            // Pembina
            document.getElementById('resAdvisor').innerText = reg.advisor_name || 'Pembina (-)';
            const waBtn = document.getElementById('resAdvisorWa');
            if (waBtn) {
                if (reg.advisor_phone) {
                    let phone = reg.advisor_phone.replace(/[^0-9]/g, '');
                    if (phone.startsWith('0')) phone = '62' + phone.substring(1);
                    waBtn.href = `https://wa.me/${phone}`;
                    waBtn.classList.remove('hidden');
                } else {
                    waBtn.classList.add('hidden');
                }
            }

            // Status Presensi
            const badgeWrapper = document.getElementById('resBadgeWrapper');
            const statusBadge = document.getElementById('resStatusBadge');
            const iconEl = document.getElementById('resIcon');
            const checkinText = document.getElementById('resCheckinText');
            const btnConfirm = document.getElementById('btnConfirmCheckIn');
            const btnCancel = document.getElementById('btnCancelCheckIn');
            const notesInput = document.getElementById('checkinNotes');

            notesInput.value = reg.checkin_notes || '';

            if (reg.is_checked_in) {
                badgeWrapper.className = 'px-3 py-2 rounded-xl border flex items-center justify-between gap-2 bg-amber-500/10 border-amber-500/40 text-amber-300';
                statusBadge.innerText = 'SUDAH DAFTAR ULANG SEBELUMNYA';
                iconEl.className = 'w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 shadow-inner bg-amber-500/20 text-amber-400';
                iconEl.innerHTML = '<i class="fa-solid fa-clock-rotate-left"></i>';
                checkinText.innerHTML = `<span class="text-emerald-400 font-bold">✓ Sudah Hadir</span> (${reg.checked_in_at || ''})`;

                btnConfirm.classList.add('hidden');
                btnCancel.classList.remove('hidden');
            } else {
                badgeWrapper.className = 'px-3 py-2 rounded-xl border flex items-center justify-between gap-2 bg-emerald-500/10 border-emerald-500/40 text-emerald-300';
                statusBadge.innerText = 'PESERTA TERVERIFIKASI ✓ SIAP CHECK-IN';
                iconEl.className = 'w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 shadow-inner bg-emerald-500/20 text-emerald-400';
                iconEl.innerHTML = '<i class="fa-solid fa-check"></i>';
                checkinText.innerHTML = '<span class="text-amber-400 font-bold">Belum Daftar Ulang</span>';

                btnConfirm.classList.remove('hidden');
                btnCancel.classList.add('hidden');
            }

            // Teams List Badges
            const teamsContainer = document.getElementById('resTeamsList');
            teamsContainer.innerHTML = '';
            if (reg.teams && reg.teams.length) {
                reg.teams.forEach(t => {
                    const badge = document.createElement('span');
                    badge.className = 'inline-block px-2 py-0.5 bg-slate-900 text-slate-300 text-[9.5px] font-bold rounded-lg border border-slate-700/80 truncate max-w-full';
                    badge.innerHTML = `<i class="fa-solid fa-flag text-rose-400 text-[8px] mr-1"></i>${t.category} (${t.name})`;
                    teamsContainer.appendChild(badge);
                });
            } else {
                teamsContainer.innerHTML = '<span class="text-slate-500 text-[9px] p-1">Belum ada regu yang terdaftar</span>';
            }

            // Kartu Peserta Link
            const cardsBtn = document.getElementById('btnParticipantCards');
            if (cardsBtn) {
                cardsBtn.href = `/kartu-peserta/${reg.registration_code}`;
            }

            document.getElementById('modalResult').classList.remove('hidden');
        }

        function closeResultModal() {
            document.getElementById('modalResult').classList.add('hidden');
            setTimeout(() => {
                isProcessing = false;
            }, 600);
        }

        function showErrorModal(message) {
            const msgEl = document.getElementById('modalErrorMessage');
            if (msgEl) msgEl.innerText = message;
            document.getElementById('modalError').classList.remove('hidden');
        }

        function closeErrorModal() {
            document.getElementById('modalError').classList.add('hidden');
            setTimeout(() => {
                isProcessing = false;
            }, 600);
        }

        // ================= SUBMIT CHECK-IN & BATALKAN =================
        async function submitCheckIn() {
            const regId = document.getElementById('currentRegistrationId').value;
            const notes = document.getElementById('checkinNotes').value.trim();
            if (!regId) return;

            const btn = document.getElementById('btnConfirmCheckIn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...';

            try {
                const res = await fetch("{{ route('admin.competition-checkin.process') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        registration_id: regId,
                        notes: notes
                    })
                });

                const data = await res.json();
                if (data.success) {
                    playBeep('success');
                    alert(data.message);
                    closeResultModal();
                    window.location.reload();
                } else {
                    playBeep('error');
                    alert(data.message || 'Gagal melakukan check-in.');
                }
            } catch (err) {
                playBeep('error');
                alert('Gagal: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-circle-check text-sm"></i> <span>Konfirmasi Daftar Ulang (Check-In)</span>';
            }
        }

        async function submitCancelCheckIn() {
            const regId = document.getElementById('currentRegistrationId').value;
            if (!regId) return;

            if (!confirm('Apakah Anda yakin ingin membatalkan status daftar ulang kontingen ini?')) return;

            try {
                const res = await fetch(`/admin/competition-checkin/${regId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                    closeResultModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal membatalkan daftar ulang.');
                }
            } catch (err) {
                alert('Gagal: ' + err.message);
            }
        }

        // ================= TAB DAFTAR SEKOLAH FILTER & SEARCH =================
        function filterSchools(status) {
            document.querySelectorAll('#view-schools button[id^="flt-"]').forEach(btn => {
                btn.className = 'px-3 py-1.5 rounded-xl bg-slate-900 text-slate-400 border border-slate-800 shrink-0 transition';
            });
            const activeBtn = document.getElementById(`flt-${status}`);
            if (activeBtn) {
                activeBtn.className = 'px-3 py-1.5 rounded-xl bg-slate-800 text-white border border-slate-700 shrink-0 transition font-black';
            }

            const cards = document.querySelectorAll('.school-card');
            cards.forEach(card => {
                if (status === 'all' || card.getAttribute('data-status') === status) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        function searchSchoolList(query) {
            const q = query.toLowerCase().trim();
            const cards = document.querySelectorAll('.school-card');
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const code = (card.getAttribute('data-code') || '').toLowerCase();
                const advisor = card.getAttribute('data-advisor') || '';
                if (!q || name.includes(q) || code.includes(q) || advisor.includes(q)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
