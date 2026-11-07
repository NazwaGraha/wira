<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>E-Tiket Parkir - {{ $ticket->license_plate }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-900 min-h-screen py-6 px-4 flex flex-col items-center justify-center font-sans">

    <!-- Mobile E-Ticket Container -->
    <div class="max-w-md w-full bg-slate-950 rounded-3xl overflow-hidden shadow-2xl border border-slate-800 text-white flex flex-col">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 via-rose-600 to-red-700 p-6 text-center relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="bg-white p-1.5 rounded-xl shadow-md">
                    <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" alt="Logo PMI SMAN 1 Ciawi" class="h-8 w-auto">
                </div>
                <div class="text-left">
                    <span class="text-[9px] font-black tracking-widest uppercase text-rose-200 block">SUA BHAKTI BERKARYA IV 2026</span>
                    <h1 class="text-lg font-black tracking-tight leading-none text-white">E-TIKET PARKIR RESMI</h1>
                </div>
            </div>
            <p class="text-[10px] text-rose-100 font-medium">PMR Wira SMAN 1 Ciawi Bogor</p>
        </div>

        <div class="p-6 space-y-5">
            
            <!-- QR Code Section -->
            <div class="text-center">
                <div class="inline-block p-4 bg-white rounded-3xl shadow-xl relative border-2 border-slate-700">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('parking.public-ticket', $ticket->qr_code_token)) }}" alt="QR Code" class="w-44 h-44 mx-auto">
                </div>
                <div class="mt-3 font-mono font-black text-sm tracking-widest text-slate-200">{{ $ticket->ticket_code }}</div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Tunjukkan QR Code ini kepada petugas parkir di gerbang</span>
            </div>

            <!-- Kantong Parkir Approved Badge -->
            <div class="bg-emerald-950/60 border border-emerald-500/40 rounded-2xl p-4 text-center shadow-inner">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 block">LOKASI PARKIR DISETUJUI:</span>
                <div class="text-base font-black text-white mt-1">
                    {{ $ticket->location?->name ?? 'BEBAS / MENUNGGU ARAHAN PETUGAS' }}
                </div>
                @if($ticket->location?->address)
                    <p class="text-[11px] text-slate-400 mt-1">{{ $ticket->location->address }}</p>
                @endif
                @if($ticket->location?->officer_name)
                    <div class="mt-2 text-[10px] text-emerald-300 font-bold">
                        Petugas Jaga: {{ $ticket->location->officer_name }}
                    </div>
                @endif
            </div>

            <!-- Vehicle & School Details Grid -->
            <div class="bg-slate-900/80 rounded-2xl p-4 border border-slate-800 space-y-2.5 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <span class="text-slate-400">Asal Kontingen:</span>
                    <span class="font-bold text-white text-right truncate max-w-[200px]">{{ $ticket->school_name }}</span>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <span class="text-slate-400">Nomor Polisi (Plat):</span>
                    <span class="px-2.5 py-1 bg-black rounded-lg font-mono font-black text-xs text-white border border-slate-700 tracking-wider">
                        {{ $ticket->license_plate }}
                    </span>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <span class="text-slate-400">Jenis Kendaraan:</span>
                    <span class="font-bold text-slate-200">{{ $ticket->vehicle_type_label }}</span>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <span class="text-slate-400">Merek & Warna:</span>
                    <span class="font-bold text-slate-200">{{ $ticket->vehicle_brand ?: '-' }} ({{ $ticket->vehicle_color ?: '-' }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Pengemudi / Sopir:</span>
                    <span class="font-bold text-slate-200">{{ $ticket->driver_name ?: '-' }}</span>
                </div>
            </div>

            <!-- Navigation & Contact Action Buttons -->
            <div class="space-y-2.5 pt-1">
                @if($ticket->location?->gmaps_url)
                    <a href="{{ $ticket->location->gmaps_url }}" target="_blank" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-2xl shadow-lg transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-map-location-dot"></i> Buka Petunjuk Arah Google Maps
                    </a>
                @endif

                @if($ticket->location?->officer_phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->location->officer_phone) }}?text=Halo%20Petugas%20{{ urlencode($ticket->location->name) }},%20saya%20kontingen%20{{ urlencode($ticket->school_name) }}%20(Plat%20{{ urlencode($ticket->license_plate) }}),%20sedang%20menuju%20lokasi." target="_blank" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-2xl shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-sm"></i> Hubungi Petugas Kantong Parkir
                    </a>
                @endif
            </div>

            <!-- Status Notice -->
            <div class="text-center pt-2">
                @if($ticket->status === 'parked')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        <i class="fa-solid fa-check"></i> Sudah Terparkir (Check-In)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        <i class="fa-solid fa-clock"></i> Belum Check-In di Lokasi
                    </span>
                @endif
            </div>

        </div>

        <!-- Footer -->
        <div class="bg-slate-900 border-t border-slate-800 p-4 text-center">
            <p class="text-[10px] text-slate-500">Simpan tiket digital ini atau tangkap layar (screenshot) untuk ditunjukkan saat tiba di kampus SMAN 1 Ciawi.</p>
        </div>

    </div>

</body>
</html>
