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
            body { background: white !important; padding: 0 !important; }
            .print-container { 
                box-shadow: none !important; 
                border: 1px solid #94a3b8 !important; 
                max-width: 100% !important;
                margin: 0 !important;
                padding: 24px !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 flex flex-col items-center justify-center min-h-screen text-slate-800">

    <!-- Action Bar -->
    <div class="no-print max-w-4xl w-full mb-4 flex justify-between items-center">
        <a href="{{ route('lomba.status', ['code' => $registration->registration_code]) }}" class="text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Status
        </a>
        <button onclick="window.print()" class="bg-red-600 hover:bg-red-700 text-white font-black text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Kwitansi Sheet (A4 Style) -->
    <div class="print-container bg-white max-w-4xl w-full p-6 sm:p-12 rounded-2xl shadow-xl border border-slate-200 relative overflow-hidden">
        
        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
            <img src="{{ file_exists(public_path('images/logo_pmi_sman1ciawi.png')) ? asset('images/logo_pmi_sman1ciawi.png') : asset('images/logo.png') }}" class="w-96" alt="Watermark">
        </div>

        <!-- Header / Kop Surat Resmi -->
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-4">
                
                <!-- Kiri: Logo Resmi Gabungan (PMI | SMAN 1 Ciawi) -->
                <div class="flex-shrink-0 bg-white p-1.5 px-3 rounded-xl border border-slate-200 shadow-2xs">
                    <img src="{{ file_exists(public_path('images/logo_pmi_sman1ciawi.png')) ? asset('images/logo_pmi_sman1ciawi.png') : asset('images/logo.png') }}" 
                         alt="Logo PMI & SMAN 1 Ciawi" 
                         class="h-12 sm:h-14 w-auto object-contain">
                </div>

                <!-- Tengah: Identitas Kepanitiaan & Alamat -->
                <div class="flex-1 text-center sm:text-left px-2 sm:px-4 min-w-0">
                    <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-wide uppercase leading-snug">
                        PANITIA {{ $registration->event->title ?? 'SUA BHAKTI BERKARYA IV TAHUN 2026' }}
                    </h1>
                    <h2 class="text-xs font-black text-red-600 tracking-wider uppercase mt-0.5">
                        PMR WIRA SMAN 1 CIAWI KAB. BOGOR
                    </h2>
                    <p class="text-[10px] text-slate-500 mt-1 leading-tight">
                        Jl. Veteran III No. 01 Ciawi, Bogor &bull; Email: pmrwira@sman1ciawi.sch.id
                    </p>
                </div>

                <!-- Kanan: Kotak Kartu Bukti Pembayaran Resmi (Tidak Menabrak & Tidak Terputus) -->
                <div class="flex-shrink-0 w-full sm:w-60 bg-slate-50 border border-slate-300 rounded-2xl p-3 sm:p-3.5 text-center shadow-xs">
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-500">
                        BUKTI PEMBAYARAN
                    </div>
                    <div class="my-1.5 px-3 py-1 bg-white border border-slate-300 rounded-xl text-sm sm:text-base font-mono font-black text-slate-900 whitespace-nowrap tracking-wider shadow-2xs">
                        {{ $registration->registration_code }}
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold">
                        {{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y') : date('d F Y') }}
                    </div>
                    <div class="mt-1.5">
                        <span class="inline-flex items-center gap-1 bg-emerald-600 text-white text-[9px] font-black uppercase px-2.5 py-0.5 rounded-full shadow-2xs">
                            <i class="fa-solid fa-check"></i> LUNAS
                        </span>
                    </div>
                </div>

            </div>

            <!-- Double-Line Divider Khas Dokumen Resmi -->
            <div class="border-b-2 border-slate-900"></div>
            <div class="border-b border-slate-900 mt-0.5 mb-6"></div>
        </div>

        <!-- Title -->
        <div class="text-center my-6 relative z-10">
            <h3 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-slate-900">
                KWITANSI PENDAFTARAN RESMI
            </h3>
            <div class="w-36 h-0.5 bg-red-600 mx-auto mt-1.5"></div>

            <!-- Nomor Kode Registrasi Resmi untuk Daftar Ulang -->
            <div class="mt-4 inline-block bg-slate-900 text-white px-6 py-2.5 rounded-2xl shadow-sm border border-slate-700">
                <div class="text-[10px] uppercase font-bold tracking-widest text-slate-300">NOMOR REGISTRASI PENDAFTARAN</div>
                <div class="text-xl sm:text-2xl font-mono font-black tracking-widest text-white mt-0.5">{{ $registration->registration_code }}</div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium mt-2 max-w-md mx-auto">
                <i class="fa-solid fa-circle-info text-red-500"></i> Wajib ditunjukkan atau scan QR Code saat <strong>Daftar Ulang (Registrasi Ulang)</strong> di meja panitia lomba.
            </p>
        </div>

        <!-- Meta Table Data Kontingen -->
        <div class="space-y-2.5 text-xs sm:text-sm relative z-10 my-6 bg-slate-50/70 p-4 sm:p-5 rounded-2xl border border-slate-200">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-4 font-bold text-slate-500 uppercase text-[11px] sm:text-xs">Telah Diterima Dari</div>
                <div class="col-span-12 sm:col-span-8 font-black text-slate-900 uppercase">: {{ $registration->school_name }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-4 font-bold text-slate-500 uppercase text-[11px] sm:text-xs">Tingkat Kontingen</div>
                <div class="col-span-12 sm:col-span-8 font-extrabold text-red-600">: PMR {{ $registration->level }} ({{ $registration->level == 'Mula' ? 'SD' : ($registration->level == 'Madya' ? 'SMP' : 'SMA') }})</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-4 font-bold text-slate-500 uppercase text-[11px] sm:text-xs">Nama Pembina / Pendamping</div>
                <div class="col-span-12 sm:col-span-8 font-semibold text-slate-800">: {{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-4 font-bold text-slate-500 uppercase text-[11px] sm:text-xs">Alamat Email</div>
                <div class="col-span-12 sm:col-span-8 font-mono font-medium text-slate-800">: {{ $registration->advisor_email ?: '-' }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-4 font-bold text-slate-500 uppercase text-[11px] sm:text-xs">Untuk Pembayaran</div>
                <div class="col-span-12 sm:col-span-8 font-semibold text-slate-800">: Registrasi Peserta Lomba {{ $registration->event->title ?? 'SUA BHAKTI BERKARYA' }}</div>
            </div>
        </div>

        <!-- Items Table (Daftar Cabang Lomba) -->
        <div class="my-6 relative z-10 overflow-x-auto">
            <table class="w-full text-left text-xs border border-slate-300 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 font-black border-b border-slate-300 uppercase text-slate-700">
                    <tr>
                        <th class="p-3 border-r border-slate-300 w-12 text-center">No</th>
                        <th class="p-3 border-r border-slate-300">Cabang Mata Lomba</th>
                        <th class="p-3 border-r border-slate-300">Regu / Keterangan</th>
                        <th class="p-3 text-right w-40">Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium">
                    @foreach($registration->teams as $index => $team)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-2.5 border-r border-slate-200 text-center text-slate-400 font-bold">{{ $index + 1 }}</td>
                            <td class="p-2.5 border-r border-slate-200 font-bold text-slate-900">{{ $team->category->name }} ({{ $team->category->gender_category }})</td>
                            <td class="p-2.5 border-r border-slate-200 text-slate-600">{{ $team->team_name }}</td>
                            <td class="p-2.5 text-right font-mono font-bold text-slate-800">Rp {{ number_format($team->category->registration_fee ?: ($registration->event->registration_fee ?: 150000), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-100 font-black border-t-2 border-slate-900">
                    <tr>
                        <td colspan="3" class="p-3 text-right uppercase tracking-wider font-black text-xs text-slate-700">Total Pembayaran Lunas :</td>
                        <td class="p-3 text-right font-mono font-black text-sm sm:text-base text-emerald-700">Rp {{ number_format($registration->total_payment, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer & Signatures (Stempel Verifikasi & QR Code & Tanda Tangan) -->
        <div class="pt-6 sm:pt-8 flex flex-col sm:flex-row justify-between items-center sm:items-end gap-6 relative z-10 text-xs">
            
            <!-- Kiri: Stempel Verifikasi Tanggal & Jam + QR Code Validasi (Opsi A) -->
            <div class="flex items-center gap-4 sm:gap-6">
                
                <!-- Stempel Hijau Miring Sesuai Preferensi User -->
                <div class="border-2 border-emerald-600 text-emerald-700 bg-emerald-50/50 font-black px-4 py-2.5 rounded-2xl rotate-[-5deg] tracking-wider text-center shadow-xs">
                    <div class="flex items-center justify-center gap-1.5 text-xs sm:text-sm uppercase">
                        <i class="fa-solid fa-stamp text-emerald-600"></i> LUNAS / VERIFIED
                    </div>
                    <div class="text-[10px] font-mono font-bold mt-0.5 text-emerald-800">
                        {{ $registration->verified_at ? $registration->verified_at->format('d/m/Y H:i') : date('d/m/Y H:i') }}
                    </div>
                </div>

                <!-- QR Code Validasi Keaslian Dokumen -->
                <div class="flex flex-col items-center p-1.5 bg-white border border-slate-300 rounded-xl shadow-xs">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(route('lomba.status', ['code' => $registration->registration_code])) }}" 
                         alt="QR Code Validasi" 
                         class="w-16 h-16 sm:w-20 sm:h-20 object-contain" 
                         loading="lazy">
                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-tighter mt-1">Scan Validasi</span>
                </div>

            </div>

            <!-- Kanan: Tanda Tangan Bendahara Panitia -->
            <div class="text-center w-56 sm:w-60">
                <div class="text-slate-600 text-xs mb-14">
                    Bogor, {{ $registration->verified_at ? $registration->verified_at->translatedFormat('d F Y') : date('d F Y') }}<br>
                    Bendahara Panitia Pelaksana,
                </div>
                <div class="font-black text-slate-900 border-b-2 border-slate-800 pb-1 uppercase text-xs">
                    PANITIA {{ strtoupper($registration->event->title ?? 'SUA BHAKTI BERKARYA') }}
                </div>
                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">PMR Wira SMAN 1 Ciawi</div>
            </div>

        </div>

    </div>
</body>
</html>
