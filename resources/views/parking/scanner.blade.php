<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Scanner Petugas Parkir Lapangan - SBB IV 2026</title>
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
            0% { top: 10%; opacity: 0.3; }
            50% { top: 90%; opacity: 1; }
            100% { top: 10%; opacity: 0.3; }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans select-none">

    <!-- Top App Bar -->
    <header class="bg-slate-900 border-b border-slate-800 px-4 py-3 shrink-0 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-rose-600 flex items-center justify-center text-white text-base shadow-md">
                <i class="fa-solid fa-square-parking"></i>
            </div>
            <div>
                <span class="text-[9px] font-bold text-rose-400 uppercase tracking-wider block">PETUGAS PARKIR LAPANGAN</span>
                <h1 class="text-xs font-black text-white truncate max-w-[200px]">
                    {{ $activeLocation?->name ?? 'Pilih Kantong Parkir' }}
                </h1>
            </div>
        </div>

        <!-- Location Dropdown Selector -->
        <select onchange="window.location.href='?location_id=' + this.value" class="text-[11px] font-bold bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-rose-500">
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ $activeLocation?->id == $loc->id ? 'selected' : '' }}>
                    {{ $loc->code ?: $loc->name }}
                </option>
            @endforeach
        </select>
    </header>

    @if($activeLocation)
        <!-- Live Occupancy Slot Counter Bar -->
        <div class="bg-slate-900/80 border-b border-slate-800/80 px-4 py-2.5 grid grid-cols-2 gap-3 shrink-0">
            <!-- Slot R4 (Mobil/Elf/Bus) -->
            <div class="bg-slate-950/70 p-2.5 rounded-2xl border border-slate-800">
                <div class="flex items-center justify-between text-[10px] mb-1">
                    <span class="font-bold text-slate-300 flex items-center gap-1">
                        <i class="fa-solid fa-car-side text-blue-400"></i> Mobil/Elf (R4)
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
            <div class="bg-slate-950/70 p-2.5 rounded-2xl border border-slate-800">
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

    <!-- Main Scanner Viewport Area -->
    <main class="flex-1 p-4 flex flex-col items-center justify-center relative overflow-hidden">
        
        <!-- Live Camera Container -->
        <div class="w-full max-w-sm aspect-square bg-black rounded-3xl overflow-hidden border-2 border-slate-800 relative shadow-2xl flex items-center justify-center">
            
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

            <!-- Hint text -->
            <div class="absolute bottom-3 left-0 right-0 text-center pointer-events-none">
                <span class="px-3 py-1 bg-black/70 backdrop-blur-xs text-slate-300 text-[10px] font-bold rounded-full border border-slate-700">
                    Arahkan kamera ke QR Code Tiket Kendaraan
                </span>
            </div>
        </div>

        <!-- Manual Input Fallback -->
        <div class="w-full max-w-sm mt-4 flex gap-2">
            <input type="text" id="manualCodeInput" placeholder="Ketik Plat No / Kode Tiket..." class="flex-1 bg-slate-900 border border-slate-800 rounded-2xl px-4 py-2.5 text-xs text-white font-mono uppercase focus:outline-none focus:ring-2 focus:ring-rose-500">
            <button type="button" onclick="handleManualVerify()" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-2xl transition shadow-xs active:scale-95">
                Cari Tiket
            </button>
        </div>

        <!-- Recent Check-Ins List -->
        @if($recentTickets->isNotEmpty())
            <div class="w-full max-w-sm mt-4">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Check-in Terakhir di Lokasi Ini:</span>
                <div class="space-y-1.5">
                    @foreach($recentTickets as $rt)
                        <div class="bg-slate-900/70 p-2 rounded-xl border border-slate-800/80 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-slate-800 font-mono font-bold text-[10px] text-white">
                                    {{ $rt->license_plate }}
                                </span>
                                <span class="text-[11px] text-slate-300 truncate max-w-[150px]">{{ $rt->school_name }}</span>
                            </div>
                            <span class="text-[10px] font-mono text-emerald-400">{{ $rt->check_in_at?->format('H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </main>

    <!-- Slide-Up Result Modal (Card Validasi Tiket) -->
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
                    <span class="text-slate-400">Asal Kontingen:</span>
                    <span id="resSchool" class="font-bold text-white text-right truncate max-w-[220px]">
                        SMAN 1 Bogor
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Jenis Kendaraan:</span>
                    <span id="resVType" class="font-bold text-slate-200">Roda 4 (Minibus HiAce)</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Merek & Warna:</span>
                    <span id="resBrand" class="font-bold text-slate-200">Toyota Putih Metalik</span>
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
                <button type="button" id="btnCheckIn" onclick="submitCheckIn()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2">
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

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const activeLocationId = {{ $activeLocation?->id ?? 'null' }};
        let html5QrCode = null;
        let isProcessing = false;

        document.addEventListener('DOMContentLoaded', () => {
            initScanner();
        });

        function initScanner() {
            html5QrCode = new Html5Qrcode("qr-reader");
            const config = { fps: 10, qrbox: { width: 220, height: 220 } };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    if (!isProcessing) {
                        isProcessing = true;
                        verifyCode(decodedText);
                    }
                },
                (errorMessage) => {
                    // Ignore frame scanning errors
                }
            ).catch(err => {
                console.warn("Camera start failed, fallback to manual input", err);
            });
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
                    alert(data.message || 'Tiket tidak ditemukan!');
                    isProcessing = false;
                    return;
                }

                showResultModal(data);
            } catch (err) {
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
            document.getElementById('resBrand').innerText = `${t.vehicle_brand} (${t.vehicle_color})`;
            document.getElementById('resAssignedLoc').innerText = t.assigned_location_name;
            document.getElementById('resDriver').innerText = `${t.driver_name} • ${t.driver_phone}`;

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
                msg.innerText = "Alokasi tempat parkir sudah tepat.";
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
                    alert(data.message);
                    closeResultModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal check-in');
                }
            } catch (err) {
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
                    alert(data.message);
                    closeResultModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal check-out');
                }
            } catch (err) {
                alert('Gagal check-out: ' + err.message);
            }
        }
    </script>
</body>
</html>
