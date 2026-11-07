<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tiket Parkir Resmi Kontingen - {{ $registration->school_name }} ({{ $registration->registration_code }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .ticket-card { box-shadow: none !important; border: 1.5px solid #000 !important; page-break-inside: avoid; margin-bottom: 24px; }
        }
        .barcode-strip {
            background: repeating-linear-gradient(
                90deg,
                #000,
                #000 2px,
                #fff 2px,
                #fff 4px,
                #000 4px,
                #000 7px,
                #fff 7px,
                #fff 9px
            );
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 font-sans text-slate-800">

    <!-- Top Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-3 no-print">
        <a href="{{ route('lomba.status', ['code' => $registration->registration_code]) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Cek Status Pendaftaran
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-semibold">Total {{ $tickets->count() }} E-Tiket Parkir</span>
            <button onclick="window.print()" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak / Simpan Semua Tiket (PDF)
            </button>
        </div>
    </div>

    <!-- School Header Info Card -->
    <div class="max-w-4xl mx-auto mb-6 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between sm:items-center gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 border border-slate-300 text-slate-700">{{ $registration->registration_code }}</span>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">KONTINGEN RESMI TERVERIFIKASI</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1 uppercase">{{ $registration->school_name }}</h1>
            <p class="text-xs text-slate-500">Tingkat: PMR {{ $registration->level }} &bull; Pembina: {{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</p>
        </div>
        <div class="text-right sm:border-l sm:border-slate-200 sm:pl-5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">STATUS ALOKASI KANTONG:</span>
            <span class="text-xs font-black text-slate-800">
                {{ $registration->parking_priority_criteria['assigned_location_name'] ?? ($tickets->first()?->location?->name ?? 'Sesuai Arahan Petugas') }}
            </span>
        </div>
    </div>

    <!-- Tickets Grid -->
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($tickets as $ticket)
            <div class="ticket-card bg-white rounded-3xl shadow-md border border-slate-200 overflow-hidden flex flex-col justify-between">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-4 text-center relative">
                    <div class="flex items-center justify-center gap-2.5 mb-1">
                        <div class="bg-white p-1 rounded-lg">
                            <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" alt="Logo PMI" class="h-6 w-auto">
                        </div>
                        <div class="text-left">
                            <span class="text-[8px] font-bold tracking-widest uppercase block text-rose-200">SUA BHAKTI BERKARYA IV 2026</span>
                            <h2 class="text-sm font-black tracking-tight leading-none text-white">E-TIKET PARKIR RESMI</h2>
                        </div>
                    </div>
                    <p class="text-[9px] text-rose-100">PMR WIRA UNIT SMAN 1 CIAWI BOGOR</p>
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <!-- QR Code -->
                    <div class="text-center mb-4">
                        <div class="inline-block p-2.5 bg-white border-2 border-dashed border-slate-300 rounded-2xl shadow-inner">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('parking.public-ticket', $ticket->qr_code_token)) }}" alt="QR Code" class="w-32 h-32 mx-auto">
                        </div>
                        <div class="mt-1.5 font-mono font-black text-sm tracking-wider text-slate-900">{{ $ticket->ticket_code }}</div>
                        <span class="text-[9px] text-slate-400">Scan QR Code ini di gerbang kantong parkir</span>
                    </div>

                    <!-- Assigned Location Banner -->
                    <div class="bg-emerald-50 border border-emerald-300 rounded-xl p-2.5 text-center mb-4">
                        <span class="text-[9px] font-black uppercase tracking-wider text-emerald-700 block">LOKASI KANTONG PARKIR:</span>
                        <div class="text-xs font-black text-emerald-950 mt-0.5">
                            {{ $ticket->location?->name ?? 'BEBAS / MENUNGGU ARAHAN PETUGAS' }}
                        </div>
                        @if($ticket->location?->address)
                            <p class="text-[9px] text-emerald-700 mt-0.5">{{ $ticket->location->address }}</p>
                        @endif
                    </div>

                    <!-- Detail Spec -->
                    <div class="space-y-1.5 text-[11px] border-t border-b border-slate-100 py-2.5 mb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Nomor Polisi (Plat):</span>
                            <span class="font-mono font-black text-slate-900 px-2 py-0.5 bg-slate-100 rounded border border-slate-200">{{ $ticket->license_plate }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Jenis Kendaraan:</span>
                            <span class="font-bold text-slate-900">{{ $ticket->vehicle_type_label }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Merek & Warna:</span>
                            <span class="font-semibold text-slate-800">{{ $ticket->vehicle_brand ?: '-' }} ({{ $ticket->vehicle_color ?: '-' }})</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Pengemudi / Sopir:</span>
                            <span class="font-semibold text-slate-800">{{ $ticket->driver_name ?: '-' }} {{ $ticket->driver_phone ? "({$ticket->driver_phone})" : '' }}</span>
                        </div>
                    </div>

                    <!-- Barcode Decorative -->
                    <div class="w-full h-6 barcode-strip rounded mb-1"></div>
                    <div class="text-center font-mono text-[8px] text-slate-400 tracking-widest uppercase mb-2">
                        VALID TIKET PARKIR KONTINGEN • SBB IV 2026
                    </div>
                </div>

                <div class="bg-slate-50 px-4 py-2 border-t border-slate-100 text-[9px] text-slate-500 text-center">
                    Tunjukkan tiket ini (cetak atau dari ponsel) kepada petugas parkir di lokasi.
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white p-8 rounded-2xl text-center border border-slate-200">
                <p class="text-sm text-slate-500 font-semibold">Belum ada kendaraan yang didaftarkan untuk kontingen ini.</p>
            </div>
        @endforelse
    </div>

</body>
</html>
