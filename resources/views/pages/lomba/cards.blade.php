<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta Lomba - {{ $registration->school_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .card-grid { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 flex flex-col items-center min-h-screen text-slate-800">

    <!-- Action Bar -->
    <div class="no-print max-w-5xl w-full mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div>
            <a href="{{ route('lomba.status', ['code' => $registration->registration_code]) }}" class="text-sm font-bold text-slate-600 hover:text-slate-900">
                &larr; Kembali ke Status
            </a>
            <div class="text-xs text-slate-500 mt-0.5">Kontingen: <strong>{{ $registration->school_name }}</strong> ({{ $registration->teams->count() }} Kartu Regu)</div>
        </div>
        <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Kartu Peserta
        </button>
    </div>

    <!-- Cards Container -->
    <div class="max-w-5xl w-full grid grid-cols-1 md:grid-cols-2 gap-6 card-grid">
        @foreach($registration->teams as $index => $team)
            <div class="bg-white rounded-2xl border-2 border-dashed border-slate-400 p-6 shadow-sm relative overflow-hidden flex flex-col justify-between h-[360px]">
                
                <!-- Lanyard Hole Cut Indicator -->
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-8 h-2 bg-slate-200 rounded-full border border-slate-300"></div>

                <!-- Card Header -->
                <div class="text-center pt-3 pb-3 border-b-2 border-red-600">
                    <div class="flex items-center justify-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" class="h-8" alt="Logo">
                        <div>
                            <div class="text-[11px] font-black tracking-wider text-slate-900 leading-none">SUA BHAKTI BERKARYA III 2025</div>
                            <div class="text-[9px] font-bold text-red-600 uppercase tracking-widest mt-0.5">PMR WIRA SMAN 1 CIAWI</div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="text-center my-auto py-2">
                    <div class="inline-block bg-slate-900 text-white font-extrabold text-[10px] uppercase px-3 py-0.5 rounded-full mb-1 tracking-wider">
                        KARTU PESERTA LOMBA
                    </div>
                    
                    <h2 class="text-xl font-black text-slate-900 mt-2 leading-tight uppercase">{{ $registration->school_name }}</h2>
                    <div class="text-xs font-bold text-red-600 mt-0.5">{{ $team->team_name }}</div>

                    <div class="mt-4 bg-slate-50 border border-slate-200 rounded-xl p-3 inline-block w-full text-center">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Cabang Lomba:</div>
                        <div class="text-sm font-black text-slate-800">{{ $team->category->name }}</div>
                        <div class="text-xs font-bold text-slate-600">PMR {{ $registration->level }} &bull; {{ $team->category->gender_category }}</div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500">
                    <div>
                        <div class="font-mono font-bold text-slate-700">KODE: {{ $registration->registration_code }}</div>
                        <div>Pembina: {{ $registration->advisor_name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-emerald-600"><i class="fa-solid fa-circle-check"></i> RESMI / SAH</div>
                        <div>Bogor, Okt 2026</div>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

</body>
</html>
