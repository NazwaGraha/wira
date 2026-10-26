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
            .card-item { page-break-inside: avoid; break-inside: avoid; margin-bottom: 20px; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-6 flex flex-col items-center min-h-screen text-slate-800">

    <!-- Action Bar -->
    <div class="no-print max-w-5xl w-full mb-6 flex flex-col sm:flex-row justify-between items-center gap-3 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div>
            <a href="{{ route('lomba.status', ['code' => $registration->registration_code]) }}" class="text-sm font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Status Pendaftaran
            </a>
            <div class="text-xs text-slate-500 mt-1">
                Kontingen: <strong>{{ $registration->school_name }}</strong> &bull; {{ $registration->teams->count() }} Kartu Regu Terdaftar
            </div>
        </div>
        <button onclick="window.print()" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow transition flex items-center justify-center gap-2 cursor-pointer">
            <i class="fa-solid fa-print"></i> Cetak Kartu Peserta (ID Card)
        </button>
    </div>

    <!-- Cards Container -->
    <div class="max-w-5xl w-full grid grid-cols-1 md:grid-cols-2 gap-6 card-grid">
        @foreach($registration->teams as $index => $team)
            @php
                $orderNum = $team->order_number ?: '-';
                $fullCode = $team->full_order_code ?: ($team->category?->code ? $team->category->code . '-' . $orderNum : $registration->registration_code);
                $members = is_array($team->members_list) ? $team->members_list : (is_string($team->members_list) ? json_decode($team->members_list, true) : []);
                if (!is_array($members)) $members = [];
                
                $isReguB = str_starts_with(strtoupper($orderNum), 'B') || str_contains(strtoupper($team->team_label ?? ''), 'REGU B');
                $isReguC = str_starts_with(strtoupper($orderNum), 'C') || str_contains(strtoupper($team->team_label ?? ''), 'REGU C');
                $sessionLabel = $isReguB ? 'Sesi 2 (Regu B)' : ($isReguC ? 'Sesi 3 (Regu C)' : 'Sesi 1 (Regu A)');

                $cardLevelBadge = match($registration->level) {
                    'Mula' => 'text-emerald-800 bg-emerald-100 border-emerald-300',
                    'Madya' => 'text-blue-800 bg-blue-100 border-blue-300',
                    default => 'text-amber-900 bg-amber-100 border-amber-300',
                };
            @endphp
            <div class="card-item bg-white rounded-3xl border-2 border-dashed border-slate-300 p-5 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[460px]">
                
                <!-- Lanyard Hole Cut Indicator -->
                <div class="w-12 h-2.5 bg-slate-200 rounded-full border border-slate-300 mx-auto mb-2"></div>

                <!-- Card Header -->
                <div class="text-center pb-2.5 border-b-2 border-red-600">
                    <div class="flex items-center justify-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" class="h-8" alt="Logo">
                        <div class="text-left">
                            <div class="text-[11px] font-black tracking-wider text-slate-900 leading-tight">{{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA') }}</div>
                            <div class="text-[9px] font-extrabold text-red-600 uppercase tracking-widest">PMR WIRA SMAN 1 CIAWI BOGOR</div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="my-auto py-2 text-center">
                    <div class="inline-block bg-slate-900 text-white font-black text-[9px] uppercase px-3 py-0.5 rounded-full mb-1 tracking-wider">
                        KARTU PESERTA RESMI
                    </div>
                    
                    <!-- Kotak Nomor Urut Tampil (Highlight Utama Skema 1) -->
                    <div class="mt-1.5 bg-gradient-to-br from-amber-50 to-orange-100 border-2 border-amber-400 rounded-2xl p-2.5 shadow-2xs">
                        <div class="flex items-center justify-between text-[9px] font-bold text-amber-800 uppercase px-1">
                            <span>Nomor Urut Tampil:</span>
                            <span class="bg-amber-200/80 text-amber-900 px-2 py-0.5 rounded-full text-[8px]">{{ $sessionLabel }}</span>
                        </div>
                        <div class="text-3xl font-black text-amber-950 font-mono tracking-tight mt-0.5">
                            {{ $orderNum }}
                        </div>
                        <div class="text-[9px] font-extrabold text-amber-700 font-mono mt-0.5">
                            KODE: {{ $fullCode }}
                        </div>
                    </div>

                    <!-- Identitas Kontingen & Regu -->
                    <h2 class="text-base font-black text-slate-900 uppercase mt-2.5 leading-snug">{{ $registration->school_name }}</h2>
                    <div class="inline-block bg-red-50 text-red-700 font-bold text-xs px-2.5 py-0.5 rounded-md border border-red-200 mt-0.5">
                        {{ $team->team_name }}
                    </div>

                    <!-- Mata Lomba Info -->
                    <div class="mt-2 bg-slate-50 border border-slate-200 rounded-xl p-2 flex items-center justify-between text-left">
                        <div>
                            <div class="text-[8px] text-slate-400 font-bold uppercase">Mata Lomba:</div>
                            <div class="text-xs font-black text-slate-900 leading-tight">{{ $team->category->name ?? 'Cabang Lomba' }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black border {{ $cardLevelBadge }}">PMR {{ $registration->level }}</span>
                            <div class="text-[10px] font-bold text-slate-600 mt-0.5">&bull; {{ $team->category->gender_category ?? 'Umum' }}</div>
                        </div>
                    </div>

                    <!-- Anggota Regu (Hasil Import Excel) -->
                    <div class="mt-2 border border-slate-200 rounded-xl overflow-hidden text-left">
                        <div class="bg-slate-100 px-2.5 py-1 text-[9px] font-black text-slate-700 flex justify-between items-center">
                            <span>ANGGOTA PESERTA (IMPORT EXCEL)</span>
                            <span class="font-mono text-slate-500">{{ count($members) }} Orang</span>
                        </div>
                        <div class="p-1.5 space-y-1 bg-white text-[10px] max-h-24 overflow-y-auto">
                            @forelse($members as $mIdx => $m)
                                @php
                                    $mName = is_array($m) ? ($m['name'] ?? ($m['nama'] ?? 'Peserta')) : $m;
                                    $mRole = is_array($m) ? ($m['role'] ?? ($m['peran'] ?? 'Anggota')) : 'Anggota';
                                    $mNis = is_array($m) ? ($m['nisn'] ?? ($m['nis'] ?? '')) : '';
                                    $mClass = is_array($m) ? ($m['class_grade'] ?? ($m['kelas'] ?? '')) : '';
                                    $isLeader = strcasecmp($mRole, 'Ketua') === 0 || strcasecmp($mRole, 'Ketua Regu') === 0;
                                @endphp
                                <div class="flex items-center justify-between border-b border-slate-50 pb-0.5 last:border-0">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span class="w-3.5 h-3.5 rounded-full {{ $isLeader ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600' }} text-[8px] font-bold flex items-center justify-center shrink-0">
                                            {{ $mIdx + 1 }}
                                        </span>
                                        <div class="truncate">
                                            <span class="font-bold text-slate-800 {{ $isLeader ? 'text-red-700' : '' }}">{{ $mName }}</span>
                                            @if($mNis || $mClass)
                                                <span class="text-[8px] text-slate-400 font-mono">({{ trim(($mNis ? 'NISN: '.$mNis : '') . ' ' . ($mClass ? 'Kls: '.$mClass : '')) }})</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-[8px] font-bold px-1.5 py-0.2 rounded {{ $isLeader ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-500' }} shrink-0">
                                        {{ $mRole }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-[9px] text-slate-400 italic text-center py-1">
                                    Daftar nama anggota terdaftar pada berkas pendaftaran panitia.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-[9px] text-slate-500">
                    <div>
                        <div class="font-mono font-bold text-slate-800">REG: {{ $registration->registration_code }}</div>
                        <div>Pembina: <span class="font-semibold text-slate-700">{{ $registration->advisor_name }}</span></div>
                        <div class="font-bold text-emerald-600 mt-0.5"><i class="fa-solid fa-circle-check"></i> RESMI / SAH</div>
                    </div>
                    <!-- QR Box Mini -->
                    <div class="w-11 h-11 border border-slate-300 rounded-lg p-0.5 flex flex-col items-center justify-center bg-slate-50 text-slate-800 shrink-0">
                        <i class="fa-solid fa-qrcode text-xl text-slate-900"></i>
                        <span class="text-[6px] font-black tracking-tighter mt-0.5">CHECK-IN</span>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

</body>
</html>
