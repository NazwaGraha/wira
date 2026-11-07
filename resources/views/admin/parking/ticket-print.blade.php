<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Parkir Resmi - {{ $ticket->ticket_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .ticket-card { box-shadow: none !important; border: 1.5px solid #000 !important; }
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
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center justify-center font-sans">

    <!-- Top Action Bar -->
    <div class="max-w-md w-full mb-4 flex items-center justify-between no-print">
        <a href="{{ route('admin.parking.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Admin
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Tiket (PDF / Print)
        </button>
    </div>

    <!-- Official Ticket Card -->
    <div class="ticket-card max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-5 text-center relative">
            <div class="flex items-center justify-center gap-3 mb-1">
                <div class="bg-white p-1 rounded-lg">
                    <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" alt="Logo PMI SMAN 1 Ciawi" class="h-8 w-auto">
                </div>
                <div class="text-left">
                    <span class="text-[9px] font-bold tracking-widest uppercase block text-rose-200">SUA BHAKTI BERKARYA IV 2026</span>
                    <h1 class="text-base font-black tracking-tight leading-none text-white">E-TIKET PARKIR RESMI</h1>
                </div>
            </div>
            <p class="text-[10px] text-rose-100 mt-2">PMR WIRA UNIT SMAN 1 CIAWI BOGOR</p>
        </div>

        <div class="p-6">
            <!-- QR Code Section -->
            <div class="text-center mb-5">
                <div class="inline-block p-3 bg-white border-2 border-dashed border-slate-300 rounded-2xl shadow-inner">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('parking.public-ticket', $ticket->qr_code_token)) }}" alt="QR Code" class="w-40 h-40 mx-auto">
                </div>
                <div class="mt-2 font-mono font-black text-sm tracking-wider text-slate-900">{{ $ticket->ticket_code }}</div>
                <span class="text-[10px] text-slate-400">Tunjukkan QR Code ini kepada petugas parkir di lokasi</span>
            </div>

            <!-- Kantong Parkir Badge -->
            <div class="bg-emerald-50 border border-emerald-300 rounded-2xl p-3 text-center mb-5">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block">LOKASI KANTONG PARKIR DISETUJUI:</span>
                <div class="text-sm font-black text-emerald-950 mt-0.5">
                    {{ $ticket->location?->name ?? 'BEBAS / MENUNGGU ARAHAN PETUGAS' }}
                </div>
                @if($ticket->location?->address)
                    <p class="text-[10px] text-emerald-700 mt-0.5">{{ $ticket->location->address }}</p>
                @endif
            </div>

            <!-- Detail Table -->
            <div class="space-y-2 text-xs border-t border-b border-slate-100 py-3 mb-4">
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-400 font-medium">Asal Sekolah / Kontingen:</span>
                    <span class="font-bold text-slate-900 text-right">{{ $ticket->school_name }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-400 font-medium">Nomor Polisi (Plat):</span>
                    <span class="font-mono font-black text-slate-900 px-2 py-0.5 bg-slate-100 rounded border border-slate-200">{{ $ticket->license_plate }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-400 font-medium">Jenis Kendaraan:</span>
                    <span class="font-bold text-slate-900">{{ $ticket->vehicle_type_label }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-400 font-medium">Merek & Warna:</span>
                    <span class="font-bold text-slate-900">{{ $ticket->vehicle_brand ?: '-' }} ({{ $ticket->vehicle_color ?: '-' }})</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-400 font-medium">Pengemudi / Sopir:</span>
                    <span class="font-bold text-slate-900">{{ $ticket->driver_name ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-400 font-medium">Petugas Kantong:</span>
                    <span class="font-bold text-slate-900">{{ $ticket->location?->officer_name ?: '-' }}</span>
                </div>
            </div>

            <!-- Barcode Graphic Decorative -->
            <div class="w-full h-8 barcode-strip rounded-md mb-2"></div>
            <div class="text-center font-mono text-[9px] text-slate-400 tracking-widest uppercase mb-4">
                VALID • SUA BHAKTI BERKARYA IV
            </div>

            <!-- Petunjuk -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-[10px] text-slate-500 space-y-1">
                <div class="font-bold text-slate-700">Tata Tertib Parkir:</div>
                <p>1. Wajib parkir rapi sesuai arahan petugas di kantong parkir yang telah ditentukan.</p>
                <p>2. Kunci ganda kendaraan Anda. Panitia tidak bertanggung jawab atas barang berharga yang tertinggal.</p>
            </div>
        </div>
    </div>

</body>
</html>
