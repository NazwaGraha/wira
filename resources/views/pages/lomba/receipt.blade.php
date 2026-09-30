<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pendaftaran - {{ $registration->registration_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-container { box-shadow: none !important; border: 1px solid #000 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 flex flex-col items-center justify-center min-h-screen text-slate-800">

    <!-- Action Bar -->
    <div class="no-print max-w-3xl w-full mb-4 flex justify-between items-center">
        <a href="{{ route('lomba.status', ['code' => $registration->registration_code]) }}" class="text-sm font-bold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Status
        </a>
        <button onclick="window.print()" class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Kwitansi Sheet -->
    <div class="print-container bg-white max-w-3xl w-full p-8 sm:p-12 rounded-2xl shadow-xl border border-slate-200 relative overflow-hidden">
        
        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
            <img src="{{ asset('images/logo.png') }}" class="w-96" alt="Watermark">
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between pb-6 border-b-2 border-slate-800 relative z-10">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PMR" class="h-16 w-auto">
                <div>
                    <h1 class="text-lg font-black text-slate-900 tracking-wider">PANITIA SUA BHAKTI BERKARYA III</h1>
                    <h2 class="text-xs font-bold text-red-600 tracking-widest uppercase">PMR WIRA SMAN 1 CIAWI KAB. BOGOR</h2>
                    <p class="text-[10px] text-slate-500">Jl. Veteran III No. 01 Ciawi, Bogor &bull; Email: pmrwira@sman1ciawi.sch.id</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs font-bold uppercase tracking-widest text-slate-400">Bukti Pembayaran</div>
                <div class="text-xl font-mono font-black text-slate-900">{{ $registration->registration_code }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">{{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y') : date('d F Y') }}</div>
            </div>
        </div>

        <!-- Title -->
        <div class="text-center my-6 relative z-10">
            <h3 class="text-xl font-extrabold uppercase tracking-wider text-slate-900 underline decoration-red-600 decoration-2 underline-offset-4">
                KWITANSI PENDAFTARAN RESMI
            </h3>
        </div>

        <!-- Meta Table -->
        <div class="space-y-3 text-sm relative z-10 my-6">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 font-bold text-slate-600">Telah Diterima Dari</div>
                <div class="col-span-8 font-extrabold text-slate-900">: {{ $registration->school_name }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 font-bold text-slate-600">Tingkat Kontingen</div>
                <div class="col-span-8 font-bold text-red-600">: PMR {{ $registration->level }} ({{ $registration->level == 'Mula' ? 'SD' : ($registration->level == 'Madya' ? 'SMP' : 'SMA') }})</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 font-bold text-slate-600">Nama Pembina / Pendamping</div>
                <div class="col-span-8 text-slate-800">: {{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 font-bold text-slate-600">Untuk Pembayaran</div>
                <div class="col-span-8 text-slate-800">: Registrasi Peserta Lomba {{ $registration->event->title ?? 'SUA BHAKTI BERKARYA III 2025' }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="my-6 relative z-10">
            <table class="w-full text-left text-xs border border-slate-300">
                <thead class="bg-slate-100 font-bold border-b border-slate-300 uppercase">
                    <tr>
                        <th class="p-2.5 border-r border-slate-300 w-12 text-center">No</th>
                        <th class="p-2.5 border-r border-slate-300">Cabang Mata Lomba</th>
                        <th class="p-2.5 border-r border-slate-300">Regu / Keterangan</th>
                        <th class="p-2.5 text-right w-36">Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($registration->teams as $index => $team)
                        <tr>
                            <td class="p-2.5 border-r border-slate-200 text-center">{{ $index + 1 }}</td>
                            <td class="p-2.5 border-r border-slate-200 font-semibold">{{ $team->category->name }} ({{ $team->category->gender_category }})</td>
                            <td class="p-2.5 border-r border-slate-200 text-slate-500">{{ $team->team_name }}</td>
                            <td class="p-2.5 text-right font-mono">Rp {{ number_format($registration->event->registration_fee, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-800">
                    <tr>
                        <td colspan="3" class="p-3 text-right uppercase tracking-wider font-extrabold">Total Pembayaran Lunas :</td>
                        <td class="p-3 text-right font-mono font-black text-sm text-emerald-700">Rp {{ number_format($registration->total_payment, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer & Signatures -->
        <div class="pt-8 flex justify-between items-end relative z-10 text-xs">
            <div class="border-2 border-emerald-600 text-emerald-700 font-black px-4 py-2 rounded-xl rotate-[-5deg] tracking-widest text-center">
                <i class="fa-solid fa-stamp"></i> LUNAS / VERIFIED
                <div class="text-[9px] font-normal font-sans">{{ $registration->verified_at ? $registration->verified_at->format('d/m/Y H:i') : date('d/m/Y') }}</div>
            </div>

            <div class="text-center w-52">
                <div class="text-slate-600 mb-16">Bogor, {{ date('d F Y') }}<br>Bendahara Panitia Pelaksana,</div>
                <div class="font-bold text-slate-900 border-b border-slate-400 pb-1">PANITIA SBB III 2025</div>
                <div class="text-[10px] text-slate-500">PMR Wira SMAN 1 Ciawi</div>
            </div>
        </div>

    </div>
</body>
</html>
