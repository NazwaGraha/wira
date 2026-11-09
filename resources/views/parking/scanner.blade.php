<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Aplikasi Petugas Parkir Lapangan - SBB IV 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .laser-line {
            animation: laserScan 2s infinite ease-in-out;
        }
        @keyframes laserScan {
            0% { top: 12%; opacity: 0.3; }
            50% { top: 88%; opacity: 1; }
            100% { top: 12%; opacity: 0.3; }
        }
        #qr-reader {
            border: none !important;
            background: #000;
        }
        #qr-reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 1.5rem !important;
        }
        #qr-reader__scan_region {
            background: transparent !important;
        }
        #qr-reader__dashboard {
            display: none !important;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans select-none pb-20">

    <!-- Top App Bar -->
    <header class="bg-slate-900 border-b border-slate-800 px-4 py-3 shrink-0 flex items-center justify-between sticky top-0 z-30 shadow-md">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-600 to-rose-700 flex items-center justify-center text-white text-base shadow-md">
                <i class="fa-solid fa-square-parking"></i>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="text-[9px] font-black text-rose-400 uppercase tracking-wider block">PETUGAS PARKIR LAPANGAN</span>
                    @if(isset($officer) && $officer)
                        <span class="px-1.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[8px] font-bold">
                            <i class="fa-solid fa-circle-check"></i> Petugas
                        </span>
                    @elseif(isset($adminUser) && $adminUser)
                        <span class="px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 text-[8px] font-bold">
                            <i class="fa-solid fa-shield"></i> Panitia
                        </span>
                    @endif
                </div>
                <h1 class="text-xs font-black text-white truncate max-w-[170px] sm:max-w-[240px]">
                    {{ $activeLocation?->name ?? 'Pilih Kantong Parkir' }}
                </h1>
            </div>
        </div>

        <!-- Location Dropdown Selector & Quick Actions -->
        <div class="flex items-center gap-1.5">
            <button type="button" onclick="switchCameraFacing()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-rose-400 flex items-center justify-center text-xs transition border border-slate-700 active:scale-95" title="Ganti Kamera Depan / Belakang">
                <i class="fa-solid fa-camera-rotate"></i>
            </button>
            <button type="button" onclick="toggleTorch()" id="btnTorch" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-yellow-400 flex items-center justify-center text-xs transition border border-slate-700 active:scale-95" title="Senter / Flashlight">
                <i class="fa-solid fa-bolt"></i>
            </button>

            @if($locations->count() > 1)
                <select onchange="window.location.href='?location_id=' + this.value" class="text-[11px] font-bold bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-rose-500">
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $activeLocation?->id == $loc->id ? 'selected' : '' }}>
                            {{ $loc->code ?: $loc->name }}
                        </option>
                    @endforeach
                </select>
            @elseif($activeLocation)
                <div class="text-[10px] font-bold bg-slate-800 border border-slate-700 text-rose-300 rounded-xl px-2.5 py-1.5 flex items-center gap-1" title="Pos Terkunci Sesuai Tugas">
                    <i class="fa-solid fa-lock text-[9px] text-rose-400"></i>
                    <span>{{ $activeLocation->code ?: $activeLocation->name }}</span>
                </div>
            @endif

            @if(isset($officer) && $officer)
                <form action="{{ route('parking.logout') }}" method="POST" class="inline" onsubmit="return confirm('Keluar dari sistem petugas parkir?')">
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 flex items-center justify-center text-xs transition border border-slate-700 active:scale-95" title="Keluar / Logout ({{ $officer->name }})">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            @elseif(isset($adminUser) && $adminUser)
                <a href="{{ route('admin.parking.index') }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-amber-950 text-slate-400 hover:text-amber-400 flex items-center justify-center text-xs transition border border-slate-700 active:scale-95" title="Kembali ke Panel Admin">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            @endif
        </div>
    </header>

    <!-- Officer Profile & Assignment Notice Strip -->
    @if(isset($officer) && $officer)
        <div class="bg-slate-900/60 border-b border-slate-800/80 px-4 py-1.5 flex items-center justify-between text-[11px] text-slate-400">
            <div class="flex items-center gap-1.5 truncate">
                <i class="fa-solid fa-user-shield text-rose-400 text-xs"></i>
                <span>Petugas: <strong class="text-slate-200">{{ $officer->name }}</strong> ({{ $officer->username }})</span>
            </div>
            <div class="shrink-0 flex items-center gap-1 text-[10px] text-slate-400">
                <i class="fa-solid fa-location-dot text-rose-500"></i>
                <span>{{ $officer->locations->count() > 0 ? $officer->locations->count() . ' Pos Ditugaskan' : 'Semua Pos' }}</span>
            </div>
        </div>
    @endif

    @if($activeLocation)
        <!-- Live Occupancy Slot Counter Bar -->
        <div class="bg-slate-900/90 border-b border-slate-800/80 px-4 py-2.5 grid grid-cols-2 gap-3 shrink-0 backdrop-blur-md">
            <!-- Slot R4 (Mobil/Elf/Bus) -->
            <div class="bg-slate-950/80 p-2.5 rounded-2xl border border-slate-800 shadow-inner">
                <div class="flex items-center justify-between text-[10px] mb-1">
                    <span class="font-bold text-slate-300 flex items-center gap-1">
                        <i class="fa-solid fa-car-side text-blue-400"></i> Mobil (R4)
                    </span>
                    <span class="font-mono font-black text-slate-200" id="slot-count-r4">
                        {{ $activeLocation->parked_r4_count }} / {{ $activeLocation->capacity_r4 }}
                    </span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                    <div id="slot-bar-r4" class="h-full rounded-full bg-blue-500 transition-all duration-300" style="width: {{ $activeLocation->percent_r4 }}%"></div>
                </div>
                <div class="text-[9px] text-slate-400 mt-1 flex justify-between">
                    <span>Terparkir</span>
                    <span class="text-emerald-400 font-bold" id="slot-rem-r4">{{ $activeLocation->remaining_r4 }} Slot Sisa</span>
                </div>
            </div>

            <!-- Slot R2 (Motor) -->
            <div class="bg-slate-950/80 p-2.5 rounded-2xl border border-slate-800 shadow-inner">
                <div class="flex items-center justify-between text-[10px] mb-1">
                    <span class="font-bold text-slate-300 flex items-center gap-1">
                        <i class="fa-solid fa-motorcycle text-purple-400"></i> Motor (R2)
                    </span>
                    <span class="font-mono font-black text-slate-200" id="slot-count-r2">
                        {{ $activeLocation->parked_r2_count }} / {{ $activeLocation->capacity_r2 }}
                    </span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                    <div id="slot-bar-r2" class="h-full rounded-full bg-purple-500 transition-all duration-300" style="width: {{ $activeLocation->percent_r2 }}%"></div>
                </div>
                <div class="text-[9px] text-slate-400 mt-1 flex justify-between">
                    <span>Terparkir</span>
                    <span class="text-emerald-400 font-bold" id="slot-rem-r2">{{ $activeLocation->remaining_r2 }} Slot Sisa</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Area with 3 Modes -->
    <main class="flex-1 p-3 sm:p-4 max-w-lg mx-auto w-full">

        <!-- ================= MODE 1: SCAN & CHECK-IN ================= -->
        <div id="view-scan" class="space-y-3.5">
            
            <!-- Camera Toolbar (Switcher Depan/Belakang & Status) -->
            <div class="flex items-center justify-between gap-2 bg-slate-900/90 border border-slate-800 rounded-2xl p-2 px-3 shadow-md">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                    <div class="min-w-0">
                        <span class="text-[9px] uppercase font-black text-slate-400 block tracking-wider leading-none">Kamera Aktif</span>
                        <span id="cameraLabel" class="text-xs font-bold text-slate-200 truncate block">Kamera Belakang</span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <!-- Dropdown pilihan kamera jika terdeteksi banyak kamera (multi-lens smartphone / webcam) -->
                    <select id="cameraSelect" onchange="onCameraSelectChange(this.value)" class="hidden text-[11px] font-bold bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-rose-500 max-w-[130px] truncate">
                    </select>

                    <!-- Tombol Cepat Ganti Kamera (Depan / Belakang) -->
                    <button type="button" onclick="switchCameraFacing()" id="btnSwitchCamera" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-700 shadow-xs" title="Ganti Kamera Depan / Belakang">
                        <i class="fa-solid fa-camera-rotate text-rose-400 text-xs"></i>
                        <span class="text-[10px] font-bold hidden sm:inline">Ganti</span>
                    </button>

                    <!-- Tombol Flashlight / Senter -->
                    <button type="button" onclick="toggleTorch()" class="btn-torch-toggle w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-400 hover:text-yellow-400 flex items-center justify-center text-xs transition border border-slate-700" title="Senter / Flashlight">
                        <i class="fa-solid fa-bolt"></i>
                    </button>
                </div>
            </div>

            <!-- Notice jika kamera belum diizinkan / error -->
            <div id="cameraNotice" class="hidden p-3 bg-rose-500/10 border border-rose-500/30 rounded-2xl flex items-start gap-2.5 text-xs text-rose-300">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 mt-0.5 shrink-0"></i>
                <div class="flex-1">
                    <span id="cameraNoticeText">Kamera tidak aktif. Pastikan izin kamera browser telah diberikan.</span>
                    <button type="button" onclick="initScanner()" class="mt-1 block text-[11px] font-bold text-rose-400 underline hover:text-rose-200">
                        Coba Hubungkan Ulang Kamera
                    </button>
                </div>
            </div>

            <!-- Live Camera Container -->
            <div class="w-full aspect-square max-w-[340px] mx-auto bg-black rounded-3xl overflow-hidden border-2 border-slate-800 relative shadow-2xl flex items-center justify-center">
                <div id="qr-reader" class="w-full h-full object-cover"></div>

                <!-- Scanning Overlay Frame -->
                <div class="absolute inset-4 pointer-events-none border-2 border-dashed border-rose-500/60 rounded-2xl flex items-center justify-center">
                    <!-- Laser line -->
                    <div class="absolute left-0 right-0 h-0.5 bg-rose-500 shadow-[0_0_12px_#f43f5e] laser-line"></div>
                    <!-- Corner brackets -->
                    <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-rose-500 rounded-tl-lg"></div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-rose-500 rounded-tr-lg"></div>
                    <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-rose-500 rounded-bl-lg"></div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-rose-500 rounded-br-lg"></div>
                </div>

                <!-- Floating Quick Switch Button in Corner -->
                <div class="absolute top-3 right-3 flex items-center gap-1.5 z-10">
                    <button type="button" onclick="switchCameraFacing()" class="w-9 h-9 rounded-full bg-black/60 hover:bg-black/80 backdrop-blur-md text-white flex items-center justify-center text-xs border border-white/20 active:scale-95 transition shadow-lg" title="Ganti Kamera Depan / Belakang">
                        <i class="fa-solid fa-camera-rotate text-rose-400"></i>
                    </button>
                </div>

                <!-- Hint text -->
                <div class="absolute bottom-3 left-0 right-0 text-center pointer-events-none">
                    <span class="px-3 py-1 bg-black/75 backdrop-blur-xs text-slate-300 text-[10px] font-bold rounded-full border border-slate-700">
                        Arahkan ke QR Code Tiket Digital / Karcis
                    </span>
                </div>
            </div>

            <!-- Manual Input Fallback -->
            <div class="flex gap-2">
                <input type="text" id="manualCodeInput" placeholder="Ketik Plat No / Kode Tiket..." class="flex-1 bg-slate-900 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white font-mono uppercase focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-inner">
                <button type="button" onclick="handleManualVerify()" class="px-4 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black text-xs rounded-2xl transition shadow-md active:scale-95 shrink-0">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Cari Tiket
                </button>
            </div>

            <!-- Hint Karcis Dadakan -->
            <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-2xl flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-amber-300">
                    <i class="fa-solid fa-circle-info text-amber-400"></i>
                    <span>Kendaraan belum terdaftar / umum?</span>
                </div>
                <button type="button" onclick="switchTab('pos')" class="px-3 py-1 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-[11px] rounded-xl transition shadow-xs">
                    Karcis Dadakan &rarr;
                </button>
            </div>

        </div>

        <!-- ================= MODE 2: KARCIS DADAKAN (POS ON-THE-SPOT) ================= -->
        <div id="view-pos" class="hidden space-y-4">
            
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-400 block">KASIR GERBANG MASUK</span>
                        <h3 class="text-sm font-black text-white">Penerbitan Karcis On-The-Spot</h3>
                    </div>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 font-bold">
                        Langsung Check-In
                    </span>
                </div>

                <form id="formOnTheSpot" onsubmit="handleOnTheSpotSubmit(event)" class="space-y-4">
                    <!-- 1-Tap 4 Vehicle Categories Grid -->
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 block mb-2">1. Pilih Kategori Kendaraan:</label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <!-- Motor -->
                            <label class="pos-vehicle-card p-3 rounded-2xl border-2 border-slate-800 bg-slate-950/70 hover:border-purple-400 flex flex-col justify-between cursor-pointer transition relative" onclick="selectPosVehicle('r2_motor', {{ $fareSetting->fare_r2_motor ?? 5000 }})">
                                <input type="radio" name="pos_vehicle_type" value="r2_motor" class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <i class="fa-solid fa-motorcycle text-purple-400 text-base"></i>
                                    <span class="text-[9px] font-black text-purple-300 bg-purple-500/20 px-1.5 py-0.5 rounded">RODA 2</span>
                                </div>
                                <div class="font-black text-xs text-white">Sepeda Motor</div>
                                <div class="text-xs font-mono font-black text-purple-300 mt-1">Rp {{ number_format($fareSetting->fare_r2_motor ?? 5000, 0, ',', '.') }}</div>
                            </label>

                            <!-- Mobil -->
                            <label class="pos-vehicle-card p-3 rounded-2xl border-2 border-blue-500 bg-slate-950/70 flex flex-col justify-between cursor-pointer transition relative" onclick="selectPosVehicle('r4_car', {{ $fareSetting->fare_r4_car ?? 10000 }})">
                                <input type="radio" name="pos_vehicle_type" value="r4_car" checked class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <i class="fa-solid fa-car text-blue-400 text-base"></i>
                                    <span class="text-[9px] font-black text-blue-300 bg-blue-500/20 px-1.5 py-0.5 rounded">RODA 4</span>
                                </div>
                                <div class="font-black text-xs text-white">Mobil / MPV / SUV</div>
                                <div class="text-xs font-mono font-black text-blue-300 mt-1">Rp {{ number_format($fareSetting->fare_r4_car ?? 10000, 0, ',', '.') }}</div>
                            </label>

                            <!-- Minibus Elf -->
                            <label class="pos-vehicle-card p-3 rounded-2xl border-2 border-slate-800 bg-slate-950/70 hover:border-amber-400 flex flex-col justify-between cursor-pointer transition relative" onclick="selectPosVehicle('r4_elf', {{ $fareSetting->fare_r4_minibus ?? 25000 }})">
                                <input type="radio" name="pos_vehicle_type" value="r4_elf" class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <i class="fa-solid fa-van-shuttle text-amber-400 text-base"></i>
                                    <span class="text-[9px] font-black text-amber-300 bg-amber-500/20 px-1.5 py-0.5 rounded">RODA 4+</span>
                                </div>
                                <div class="font-black text-xs text-white">Minibus (Elf/HiAce)</div>
                                <div class="text-xs font-mono font-black text-amber-300 mt-1">Rp {{ number_format($fareSetting->fare_r4_minibus ?? 25000, 0, ',', '.') }}</div>
                            </label>

                            <!-- Bus Besar -->
                            <label class="pos-vehicle-card p-3 rounded-2xl border-2 border-slate-800 bg-slate-950/70 hover:border-rose-400 flex flex-col justify-between cursor-pointer transition relative" onclick="selectPosVehicle('r4_bus', {{ $fareSetting->fare_r6_bus ?? 50000 }})">
                                <input type="radio" name="pos_vehicle_type" value="r4_bus" class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <i class="fa-solid fa-bus text-rose-400 text-base"></i>
                                    <span class="text-[9px] font-black text-rose-300 bg-rose-500/20 px-1.5 py-0.5 rounded">RODA 6+</span>
                                </div>
                                <div class="font-black text-xs text-white">Bus Besar Wisata</div>
                                <div class="text-xs font-mono font-black text-rose-300 mt-1">Rp {{ number_format($fareSetting->fare_r6_bus ?? 50000, 0, ',', '.') }}</div>
                            </label>
                        </div>
                    </div>

                    <!-- Input Nomor Polisi -->
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 block mb-1">2. Nomor Polisi (Plat Nomor) *</label>
                        <input type="text" id="pos_license_plate" required placeholder="CONTOH: F 1845 EA" class="w-full py-3.5 px-4 bg-black border border-slate-700 focus:border-amber-500 rounded-2xl font-mono font-black text-base text-white tracking-widest uppercase focus:outline-none text-center">
                    </div>

                    <!-- Pilihan Pembayaran -->
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 block mb-1.5">3. Metode Pembayaran Kasir:</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="p-3 rounded-2xl border-2 border-emerald-500 bg-emerald-500/10 flex items-center gap-2.5 cursor-pointer" id="label-pay-cash" onclick="setPosPayment('cash')">
                                <input type="radio" name="pos_payment_method" value="cash" checked class="sr-only">
                                <i class="fa-solid fa-money-bill-wave text-emerald-400 text-base"></i>
                                <div>
                                    <span class="font-black text-xs text-white block">Uang Tunai</span>
                                    <span class="text-[9px] text-slate-400">Kasir terima uang fisik</span>
                                </div>
                            </label>

                            <label class="p-3 rounded-2xl border-2 border-slate-800 bg-slate-950 flex items-center gap-2.5 cursor-pointer" id="label-pay-qris" onclick="setPosPayment('qris')">
                                <input type="radio" name="pos_payment_method" value="qris" class="sr-only">
                                <i class="fa-solid fa-qrcode text-amber-400 text-base"></i>
                                <div>
                                    <span class="font-black text-xs text-white block">QRIS Digital</span>
                                    <span class="text-[9px] text-slate-400">Tampilkan QR di HP</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button type="submit" id="btnSubmitPos" class="w-full py-4 bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-600 hover:from-amber-400 hover:to-yellow-500 text-slate-950 font-black text-sm rounded-2xl shadow-xl shadow-amber-950/40 transition active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Terbitkan Karcis & Check-In (<span id="pos-fare-badge">Rp 10.000</span>)</span>
                    </button>
                </form>
            </div>

            <!-- Ringkasan Kas Tunai Terkumpul -->
            <div class="p-3.5 bg-slate-900 border border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                <span class="text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-amber-400"></i> Kas Tunai Dipegang:
                </span>
                <span class="font-mono font-black text-emerald-400 text-sm" id="pos-cash-summary">
                    Rp {{ number_format($todayCashTotal ?? 0, 0, ',', '.') }}
                </span>
            </div>

        </div>

        <!-- ================= MODE 3: REKAP KAS & LOG RIWAYAT ================= -->
        <div id="view-log" class="hidden space-y-4">
            
            <!-- Kas & Tiket Summary Cards -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kas Tunai Hari Ini</span>
                    <div class="text-lg font-black text-emerald-400 font-mono mt-1">
                        Rp {{ number_format($todayCashTotal ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="text-[10px] text-slate-500 block mt-0.5">Uang fisik di pos gerbang</span>
                </div>

                <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Karcis Diterbitkan</span>
                    <div class="text-lg font-black text-amber-300 font-mono mt-1">
                        {{ $todayTicketsCount ?? 0 }} <span class="text-xs font-normal text-slate-400">Kendaraan</span>
                    </div>
                    <span class="text-[10px] text-slate-500 block mt-0.5">Di kantong parkir ini</span>
                </div>
            </div>

            <!-- Activity Log List -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-lg space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <h4 class="font-black text-xs text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left text-rose-500"></i> Riwayat Kendaraan Terbaru
                    </h4>
                    <button type="button" onclick="window.location.reload()" class="text-[10px] text-slate-400 hover:text-white">
                        <i class="fa-solid fa-rotate mr-1"></i> Refresh
                    </button>
                </div>

                <div class="space-y-2 max-h-[360px] overflow-y-auto pr-1">
                    @forelse($recentTickets as $rt)
                        <div class="bg-slate-950/80 p-3 rounded-2xl border border-slate-800/80 flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-lg bg-black font-mono font-black text-xs text-white border border-slate-800">
                                        {{ $rt->license_plate }}
                                    </span>
                                    <span class="text-[10px] font-bold {{ $rt->is_public ? 'text-amber-400' : 'text-emerald-400' }}">
                                        {{ $rt->is_public ? 'Umum' : 'Kontingen' }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-400 truncate max-w-[180px]">
                                    {{ $rt->school_name }} &bull; {{ $rt->vehicle_type_label }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-mono text-slate-300 block font-bold">
                                    {{ $rt->check_in_at?->format('H:i') }}
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full {{ $rt->status === 'checked_out' ? 'bg-slate-800 text-slate-400' : 'bg-emerald-500/20 text-emerald-300' }}">
                                    {{ $rt->status === 'checked_out' ? 'Keluar' : 'Terparkir' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-500 text-xs">
                            Belum ada aktivitas kendaraan di kantong parkir ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </main>

    <!-- Bottom Navigation Bar (Thumb-Friendly 3 Tabs) -->
    <nav class="fixed bottom-0 inset-x-0 bg-slate-900 border-t border-slate-800 px-4 py-2 z-40 flex items-center justify-around shadow-2xl backdrop-blur-md">
        <!-- Tab 1: Scan QR -->
        <button type="button" onclick="switchTab('scan')" id="tab-btn-scan" class="flex flex-col items-center gap-1 text-rose-500 font-bold transition py-1">
            <div class="w-10 h-7 rounded-xl flex items-center justify-center text-base">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <span class="text-[10px]">Scan & In</span>
        </button>

        <!-- Tab 2: Karcis Dadakan (POS) -->
        <button type="button" onclick="switchTab('pos')" id="tab-btn-pos" class="flex flex-col items-center gap-1 text-slate-400 hover:text-amber-400 font-bold transition py-1">
            <div class="w-10 h-7 rounded-xl flex items-center justify-center text-base">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <span class="text-[10px]">Karcis Dadakan</span>
        </button>

        <!-- Tab 3: Rekap Kas & Slot -->
        <button type="button" onclick="switchTab('log')" id="tab-btn-log" class="flex flex-col items-center gap-1 text-slate-400 hover:text-blue-400 font-bold transition py-1">
            <div class="w-10 h-7 rounded-xl flex items-center justify-center text-base">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <span class="text-[10px]">Kas & Slot</span>
        </button>
    </nav>

    <!-- ================= SLIDE-UP SHEET HASIL SCAN ================= -->
    <div id="modalResult" class="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-t-3xl sm:rounded-3xl w-full max-w-md p-6 shadow-2xl animate-in slide-in-from-bottom duration-200">
            
            <!-- Result Status Header -->
            <div id="resBadgeWrapper" class="text-center pb-4 border-b border-slate-800">
                <div id="resIcon" class="w-14 h-14 mx-auto rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl mb-2">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div id="resStatusBadge" class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                    TIKET VALID ✓
                </div>
                <div id="resMessage" class="text-xs text-slate-400 mt-1">Sesuai dengan Kantong Parkir Ini</div>
            </div>

            <!-- Vehicle & School Details -->
            <div class="py-4 space-y-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Nomor Polisi:</span>
                    <span id="resPlate" class="px-3 py-1 bg-black rounded-xl font-mono font-black text-sm text-white tracking-widest border border-slate-800">
                        F 1845 EA
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Asal Kontingen / Tipe:</span>
                    <span id="resSchool" class="font-bold text-white text-right truncate max-w-[220px]">
                        SMAN 1 Bogor
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Jenis Kendaraan:</span>
                    <span id="resVType" class="font-bold text-slate-200">Roda 4 (Minibus HiAce)</span>
                </div>
                <div class="flex items-center justify-between" id="rowResFee">
                    <span class="text-slate-400">Biaya / Tarif:</span>
                    <span id="resFee" class="font-bold text-amber-300 font-mono">Gratis</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Kantong Parkir Resmi:</span>
                    <span id="resAssignedLoc" class="font-bold text-rose-400">Kantong Parkir A</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Pengemudi:</span>
                    <span id="resDriver" class="font-bold text-slate-200">Pak Hendra (081388991234)</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-800 space-y-2">
                <input type="hidden" id="currentTicketId">
                <button type="button" id="btnCheckIn" onclick="submitCheckIn()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Check-In Masuk (Parkir)
                </button>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="btnCheckOut" onclick="submitCheckOut()" class="py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-right-from-bracket"></i> Check-Out (Keluar)
                    </button>
                    <button type="button" onclick="closeResultModal()" class="py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold text-xs rounded-xl transition">
                        Tutup / Scan Lagi
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= MODAL SUKSES TIKET DADAKAN ================= -->
    <div id="modalPosSuccess" class="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-sm p-6 shadow-2xl text-center space-y-4">
            <div class="w-14 h-14 mx-auto rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 block">KARCIS BERHASIL DITERBITKAN</span>
                <h4 class="text-base font-black text-white mt-0.5" id="posSuccessPlate">F 1845 EA</h4>
                <p class="text-xs text-slate-400 mt-1" id="posSuccessMsg">Kendaraan langsung Check-In di lokasi ini.</p>
            </div>

            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 text-xs space-y-1 text-left">
                <div class="flex justify-between">
                    <span class="text-slate-400">Kode Karcis:</span>
                    <span class="font-mono font-bold text-white" id="posSuccessCode">PKR4-SBB4-0012</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Tarif Diterima:</span>
                    <span class="font-mono font-black text-emerald-400" id="posSuccessFee">Rp 10.000</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <a id="posSuccessPrintBtn" href="#" target="_blank" class="py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 border border-slate-700">
                    <i class="fa-solid fa-print"></i> Cetak Struk
                </a>
                <button type="button" onclick="closePosSuccessModal()" class="py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                    Karcis Baru &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- Script App Logic -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const activeLocationId = {{ $activeLocation?->id ?? 'null' }};
        let html5QrCode = null;
        let isProcessing = false;
        let isScanning = false;
        let isTorchOn = false;
        let currentFacingMode = "environment"; // 'environment' (belakang) atau 'user' (depan/webcam)
        let currentCameraId = null;
        let availableCameras = [];
        let selectedPosType = 'r4_car';
        let selectedPosFare = {{ $fareSetting->fare_r4_car ?? 10000 }};
        let selectedPosPayment = 'cash';

        // Web Audio API Synthesizer (Beep & Buzzer)
        function playBeep(type = 'success') {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (type === 'success') {
                    osc.frequency.setValueAtTime(800, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.15);
                    gain.gain.setValueAtTime(0.3, ctx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.01, ctx.currentTime + 0.15);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.15);
                } else {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(300, ctx.currentTime);
                    osc.frequency.linearRampToValueAtTime(200, ctx.currentTime + 0.35);
                    gain.gain.setValueAtTime(0.4, ctx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.35);
                }
            } catch (e) {
                // Ignore audio context error
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initScanner();
        });

        // Tab Navigation Switcher
        function switchTab(tab) {
            document.getElementById('view-scan').classList.add('hidden');
            document.getElementById('view-pos').classList.add('hidden');
            document.getElementById('view-log').classList.add('hidden');

            document.getElementById('tab-btn-scan').className = 'flex flex-col items-center gap-1 text-slate-400 hover:text-rose-400 font-bold transition py-1';
            document.getElementById('tab-btn-pos').className = 'flex flex-col items-center gap-1 text-slate-400 hover:text-amber-400 font-bold transition py-1';
            document.getElementById('tab-btn-log').className = 'flex flex-col items-center gap-1 text-slate-400 hover:text-blue-400 font-bold transition py-1';

            if (tab === 'scan') {
                document.getElementById('view-scan').classList.remove('hidden');
                document.getElementById('tab-btn-scan').className = 'flex flex-col items-center gap-1 text-rose-500 font-bold transition py-1';
            } else if (tab === 'pos') {
                document.getElementById('view-pos').classList.remove('hidden');
                document.getElementById('tab-btn-pos').className = 'flex flex-col items-center gap-1 text-amber-400 font-bold transition py-1';
                document.getElementById('pos_license_plate').focus();
            } else if (tab === 'log') {
                document.getElementById('view-log').classList.remove('hidden');
                document.getElementById('tab-btn-log').className = 'flex flex-col items-center gap-1 text-blue-400 font-bold transition py-1';
            }
        }

        // Inisialisasi Scanner dengan Deteksi Kamera Depan & Belakang (Smartphone & Laptop)
        async function initScanner() {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }

            // Ambil daftar kamera jika sudah diizinkan sebelumnya
            try {
                const devices = await Html5Qrcode.getCameras();
                if (devices && devices.length > 0) {
                    availableCameras = devices;
                    populateCameraSelect(devices);
                }
            } catch (e) {
                console.log("Daftar kamera belum terbaca sebelum izin aktif:", e);
            }

            // Jalankan stream kamera utama
            await startCameraScanner();

            // Refresh kembali daftar kamera setelah izin kamera disetujui pengguna
            try {
                const devices = await Html5Qrcode.getCameras();
                if (devices && devices.length > 0) {
                    availableCameras = devices;
                    populateCameraSelect(devices);
                    updateCameraUI();
                }
            } catch (e) {
                // ignore
            }
        }

        function populateCameraSelect(devices) {
            const selectEl = document.getElementById('cameraSelect');
            if (!selectEl) return;
            selectEl.innerHTML = '';

            devices.forEach((cam, idx) => {
                const opt = document.createElement('option');
                opt.value = cam.id;
                let lbl = cam.label || '';
                if (!lbl) {
                    lbl = `Kamera ${idx + 1}`;
                }
                opt.textContent = lbl;
                if (currentCameraId === cam.id) {
                    opt.selected = true;
                }
                selectEl.appendChild(opt);
            });

            if (devices.length > 1) {
                selectEl.classList.remove('hidden');
            }
        }

        async function startCameraScanner(targetCamera = null) {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }

            // Hentikan scanner aktif sebelumnya bila ada
            if (isScanning) {
                try {
                    await html5QrCode.stop();
                    isScanning = false;
                } catch (e) {
                    console.warn("Stop scanner error:", e);
                }
            }

            const config = {
                fps: 12,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    const boxSize = Math.floor(minEdge * 0.72);
                    return { width: boxSize, height: boxSize };
                },
                aspectRatio: 1.0
            };

            const onScanSuccess = (decodedText) => {
                if (!isProcessing) {
                    isProcessing = true;
                    verifyCode(decodedText);
                }
            };

            const onScanError = (errorMessage) => {
                // Frame skip
            };

            // Target kamera: prioritaskan target spesifik -> currentCameraId -> facingMode
            let target = targetCamera;
            if (!target) {
                if (currentCameraId) {
                    target = currentCameraId;
                } else {
                    target = { facingMode: currentFacingMode };
                }
            }

            try {
                await html5QrCode.start(target, config, onScanSuccess, onScanError);
                isScanning = true;
                const notice = document.getElementById('cameraNotice');
                if (notice) notice.classList.add('hidden');
                updateCameraUI();
            } catch (err) {
                console.warn("Percobaan kamera pertama gagal:", target, err);

                // Fallback 1: Jika kamera belakang gagal (Laptop hanya punya webcam depan/user)
                if (!currentCameraId && currentFacingMode === "environment") {
                    try {
                        console.log("Kamera belakang tidak ditemukan, beralih otomatis ke kamera depan/laptop (user)...");
                        currentFacingMode = "user";
                        await html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, onScanError);
                        isScanning = true;
                        const notice = document.getElementById('cameraNotice');
                        if (notice) notice.classList.add('hidden');
                        updateCameraUI();
                        return;
                    } catch (fallbackErr) {
                        console.warn("Fallback kamera depan gagal:", fallbackErr);
                    }
                }

                // Fallback 2: Jika ada camera device ID dari list, coba device pertama
                if (availableCameras.length > 0) {
                    try {
                        currentCameraId = availableCameras[0].id;
                        await html5QrCode.start(currentCameraId, config, onScanSuccess, onScanError);
                        isScanning = true;
                        const notice = document.getElementById('cameraNotice');
                        if (notice) notice.classList.add('hidden');
                        updateCameraUI();
                        return;
                    } catch (devErr) {
                        console.warn("Fallback device camera gagal:", devErr);
                    }
                }

                // Tampilkan pesan panduan bila seluruh upaya inisialisasi kamera gagal
                const notice = document.getElementById('cameraNotice');
                if (notice) notice.classList.remove('hidden');
                const noticeText = document.getElementById('cameraNoticeText');
                if (noticeText) {
                    noticeText.innerText = 'Kamera tidak dapat diakses (' + (err.name || err.message || 'Izin ditolak') + '). Pastikan izin akses kamera telah diberikan pada browser.';
                }
            }
        }

        // Beralih Kamera (Depan <-> Belakang untuk smartphone & webcam laptop)
        async function switchCameraFacing() {
            if (!html5QrCode) return;

            // Jika daftar kamera terdeteksi lebih dari 1, rotasi ke kamera berikutnya
            if (availableCameras && availableCameras.length > 1) {
                let currentIndex = availableCameras.findIndex(c => c.id === currentCameraId);
                let nextIndex = (currentIndex + 1) % availableCameras.length;
                currentCameraId = availableCameras[nextIndex].id;

                const lbl = (availableCameras[nextIndex].label || '').toLowerCase();
                if (lbl.includes('front') || lbl.includes('user') || lbl.includes('facetime') || lbl.includes('selfie') || lbl.includes('depan')) {
                    currentFacingMode = 'user';
                } else {
                    currentFacingMode = 'environment';
                }

                const selectEl = document.getElementById('cameraSelect');
                if (selectEl) selectEl.value = currentCameraId;

                await startCameraScanner(currentCameraId);
            } else {
                // Toggle mode facingMode standar: environment <-> user
                currentCameraId = null;
                currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
                await startCameraScanner({ facingMode: currentFacingMode });
            }
        }

        // Pilihan Kamera dari Dropdown Device
        async function onCameraSelectChange(cameraId) {
            currentCameraId = cameraId;
            const selected = availableCameras.find(c => c.id === cameraId);
            if (selected) {
                const lbl = (selected.label || '').toLowerCase();
                if (lbl.includes('front') || lbl.includes('user') || lbl.includes('facetime') || lbl.includes('selfie') || lbl.includes('depan')) {
                    currentFacingMode = 'user';
                } else {
                    currentFacingMode = 'environment';
                }
            }
            await startCameraScanner(cameraId);
        }

        function updateCameraUI() {
            const badge = document.getElementById('cameraLabel');
            if (!badge) return;

            let text = (currentFacingMode === 'user') ? 'Kamera Depan / Laptop' : 'Kamera Belakang';
            if (currentCameraId && availableCameras.length) {
                const found = availableCameras.find(c => c.id === currentCameraId);
                if (found && found.label) {
                    text = found.label;
                }
            }
            badge.innerText = text;
        }

        // Toggle Lampu Senter / Flashlight
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
                console.warn("Torch not supported on this device/camera", err);
                alert("Lampu senter (torch) hanya didukung pada kamera belakang smartphone.");
            }
        }

        function handleManualVerify() {
            const val = document.getElementById('manualCodeInput').value.trim();
            if (!val) {
                alert('Silakan masukkan Plat Nomor atau Kode Tiket.');
                return;
            }
            verifyCode(val);
        }

        async function verifyCode(code) {
            try {
                const res = await fetch("{{ route('parking.verify') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: code,
                        location_id: activeLocationId
                    })
                });

                const data = await res.json();
                if (!data.success) {
                    playBeep('error');
                    alert(data.message || 'Tiket tidak ditemukan!');
                    isProcessing = false;
                    return;
                }

                playBeep('success');
                showResultModal(data);
            } catch (err) {
                playBeep('error');
                alert('Gagal memverifikasi tiket: ' + err.message);
                isProcessing = false;
            }
        }

        function showResultModal(data) {
            const t = data.ticket;
            document.getElementById('currentTicketId').value = t.id;
            document.getElementById('resPlate').innerText = t.license_plate;
            document.getElementById('resSchool').innerText = t.school_name;
            document.getElementById('resVType').innerText = t.vehicle_type_label;
            document.getElementById('resAssignedLoc').innerText = t.assigned_location_name;
            document.getElementById('resDriver').innerText = `${t.driver_name} • ${t.driver_phone}`;

            // Fee row
            const feeEl = document.getElementById('resFee');
            if (t.is_public) {
                feeEl.innerText = `${t.formatted_fee} (${t.payment_status_label} - ${t.payment_method_label})`;
                feeEl.className = t.payment_status === 'paid' ? 'font-mono font-black text-emerald-400' : 'font-mono font-black text-amber-400';
            } else {
                feeEl.innerText = "Gratis (Kuota Resmi Kontingen)";
                feeEl.className = "font-bold text-emerald-400";
            }

            const badge = document.getElementById('resStatusBadge');
            const icon = document.getElementById('resIcon');
            const msg = document.getElementById('resMessage');

            if (!data.is_matching_location) {
                badge.className = "inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/40";
                badge.innerText = "PERINGATAN: SALAH KANTONG PARKIR!";
                icon.className = "w-14 h-14 mx-auto rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center text-2xl mb-2";
                icon.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
                msg.innerText = `Kendaraan ini dialokasikan ke: ${t.assigned_location_name}.`;
            } else if (t.status === 'parked') {
                badge.className = "inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-400 border border-amber-500/40";
                badge.innerText = `SUDAH CHECK-IN (JAM ${t.check_in_at || '-'})`;
                icon.className = "w-14 h-14 mx-auto rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-2xl mb-2";
                icon.innerHTML = '<i class="fa-solid fa-clock-rotate-left"></i>';
                msg.innerText = "Kendaraan tercatat sudah terparkir di lokasi ini.";
            } else {
                badge.className = "inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/40";
                badge.innerText = "TIKET VALID ✓ SESUAI KANTONG";
                icon.className = "w-14 h-14 mx-auto rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl mb-2";
                icon.innerHTML = '<i class="fa-solid fa-check"></i>';
                msg.innerText = t.is_public ? "Karcis pengunjung umum terdaftar resmi." : "Alokasi tempat parkir kontingen sudah tepat.";
            }

            document.getElementById('modalResult').classList.remove('hidden');
        }

        function closeResultModal() {
            document.getElementById('modalResult').classList.add('hidden');
            setTimeout(() => {
                isProcessing = false;
            }, 1000);
        }

        async function submitCheckIn() {
            const ticketId = document.getElementById('currentTicketId').value;
            if (!ticketId || !activeLocationId) return;

            try {
                const res = await fetch("{{ route('parking.check-in') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ticket_id: ticketId,
                        location_id: activeLocationId
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
                    alert(data.message || 'Gagal check-in');
                }
            } catch (err) {
                playBeep('error');
                alert('Gagal check-in: ' + err.message);
            }
        }

        async function submitCheckOut() {
            const ticketId = document.getElementById('currentTicketId').value;
            if (!ticketId) return;

            if (!confirm('Apakah kendaraan ini sudah keluar dari kantong parkir?')) return;

            try {
                const res = await fetch("{{ route('parking.check-out') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ticket_id: ticketId
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
                    alert(data.message || 'Gagal check-out');
                }
            } catch (err) {
                playBeep('error');
                alert('Gagal check-out: ' + err.message);
            }
        }

        // ================= POS ON-THE-SPOT FUNCTIONS =================
        function selectPosVehicle(type, fare) {
            selectedPosType = type;
            selectedPosFare = fare;

            document.querySelectorAll('.pos-vehicle-card').forEach(card => {
                card.classList.remove('border-purple-400', 'border-blue-500', 'border-amber-400', 'border-rose-400');
                card.classList.add('border-slate-800');
            });

            event.currentTarget.classList.remove('border-slate-800');
            const colorClass = type === 'r2_motor' ? 'border-purple-400' : (type === 'r4_car' ? 'border-blue-500' : (type === 'r4_elf' ? 'border-amber-400' : 'border-rose-400'));
            event.currentTarget.classList.add(colorClass);

            document.getElementById('pos-fare-badge').innerText = 'Rp ' + Number(fare).toLocaleString('id-ID');
        }

        function setPosPayment(method) {
            selectedPosPayment = method;
            const cashLabel = document.getElementById('label-pay-cash');
            const qrisLabel = document.getElementById('label-pay-qris');

            if (method === 'cash') {
                cashLabel.className = 'p-3 rounded-2xl border-2 border-emerald-500 bg-emerald-500/10 flex items-center gap-2.5 cursor-pointer';
                qrisLabel.className = 'p-3 rounded-2xl border-2 border-slate-800 bg-slate-950 flex items-center gap-2.5 cursor-pointer';
            } else {
                cashLabel.className = 'p-3 rounded-2xl border-2 border-slate-800 bg-slate-950 flex items-center gap-2.5 cursor-pointer';
                qrisLabel.className = 'p-3 rounded-2xl border-2 border-amber-500 bg-amber-500/10 flex items-center gap-2.5 cursor-pointer';
            }
        }

        async function handleOnTheSpotSubmit(e) {
            e.preventDefault();
            const plate = document.getElementById('pos_license_plate').value.trim();
            if (!plate) {
                alert('Silakan masukkan nomor polisi (plat nomor).');
                return;
            }

            const btn = document.getElementById('btnSubmitPos');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menerbitkan Karcis...';

            try {
                const res = await fetch("{{ route('parking.on-the-spot') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        parking_location_id: activeLocationId,
                        vehicle_type: selectedPosType,
                        license_plate: plate,
                        payment_method: selectedPosPayment
                    })
                });

                const data = await res.json();
                if (data.success) {
                    playBeep('success');
                    document.getElementById('posSuccessPlate').innerText = data.ticket.license_plate;
                    document.getElementById('posSuccessCode').innerText = data.ticket.ticket_code;
                    document.getElementById('posSuccessFee').innerText = data.ticket.formatted_fee;
                    document.getElementById('posSuccessPrintBtn').href = data.ticket.print_url;
                    document.getElementById('modalPosSuccess').classList.remove('hidden');

                    // Reset form
                    document.getElementById('pos_license_plate').value = '';
                } else {
                    playBeep('error');
                    alert(data.message || 'Gagal menerbitkan karcis.');
                }
            } catch (err) {
                playBeep('error');
                alert('Gagal: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-bolt"></i> <span>Terbitkan Karcis & Check-In (<span id="pos-fare-badge">Rp ${Number(selectedPosFare).toLocaleString('id-ID')}</span>)</span>`;
            }
        }

        function closePosSuccessModal() {
            document.getElementById('modalPosSuccess').classList.add('hidden');
            window.location.reload();
        }
    </script>
</body>
</html>
