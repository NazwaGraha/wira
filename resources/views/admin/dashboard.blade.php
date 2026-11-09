@extends('layouts.admin')

@section('title', 'Dasboard Wira Smanic')
@section('page_title', 'Dasboard Wira Smanic')

@section('top_actions')
    <a href="{{ route('admin.accounting.transactions') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-xs transition flex items-center gap-1.5">
        <i class="fa-solid fa-plus-circle"></i> <span class="hidden sm:inline">Catat Kas (BKM/BKK)</span><span class="sm:hidden">Kas</span>
    </a>
    <a href="{{ route('admin.articles.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-xs transition flex items-center gap-1.5">
        <i class="fa-solid fa-pen-nib"></i> <span class="hidden sm:inline">Tulis Artikel</span><span class="sm:hidden">Artikel</span>
    </a>
@endsection

@section('content')
@auth
    @php
        $isAdministrator = (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin()) 
            || (method_exists(auth()->user(), 'isSuperUser') && auth()->user()->isSuperUser()) 
            || (method_exists(auth()->user(), 'canAccessBackoffice') && auth()->user()->canAccessBackoffice())
            || in_array(auth()->user()->role ?? '', ['admin', 'superuser', 'pembina', 'bph']);
    @endphp
    @if($isAdministrator)
        @php
            $adminPhoto = auth()->user()->photo_url 
                ?? (auth()->user()->photo ? (str_starts_with(auth()->user()->photo, 'http') ? auth()->user()->photo : asset('storage/'.auth()->user()->photo)) : null)
                ?? ('https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=dc2626&color=ffffff&bold=true&size=256');
        @endphp
        <!-- 1. BANNER SAMBUTAN ADMINISTRATOR DENGAN FOTO -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-red-950 rounded-3xl p-4 sm:p-6 mb-6 text-white shadow-lg border border-red-900/30 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="relative shrink-0">
                    <img src="{{ $adminPhoto }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-red-500 shadow-md ring-4 ring-white/10 bg-white">
                    <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full bg-emerald-500 border-2 border-slate-900 shadow-xs" title="Status: Online & Aktif"></span>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-300 border border-red-500/30 mb-1">
                        <i class="fa-solid fa-shield-halved text-[9px]"></i> {{ auth()->user()->role_label }}
                    </div>
                    <h2 class="text-base sm:text-xl font-extrabold text-white">Selamat Datang, {{ auth()->user()->name }}!</h2>
                    <p class="text-xs text-slate-300">Pusat Kendali Eksekutif & Manajemen Terpadu PMR Wira SMAN 1 Ciawi</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 self-stretch sm:self-auto justify-end">
                <a href="{{ route('admin.accounting.reports') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-2 border border-white/10">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-400"></i> Laporan Keuangan
                </a>
                <a href="{{ route('admin.accounting.transactions') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-transfer"></i> Kas Masuk & Keluar
                </a>
            </div>
        </div>
    @endif
@endauth

<!-- 2. KARTU INFORMASI RESUME & METRIK UTAMA (4 KPI UTAMA) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6 sm:mb-8">
    <!-- Card 1: Kas & Bank Tersedia -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex items-center justify-between hover:border-slate-300 transition">
        <div>
            <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kas & Bank Tersedia</div>
            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1 font-mono">
                Rp {{ number_format($availableCash, 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-emerald-600 font-bold mt-1 flex items-center gap-1">
                <i class="fa-solid fa-building-columns"></i> BJB, QRIS & Kas Tunai
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>

    <!-- Card 2: Surplus / Defisit Berjalan -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex items-center justify-between hover:border-slate-300 transition">
        <div>
            <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Surplus / (Defisit)</div>
            <div class="text-xl sm:text-2xl font-black mt-1 font-mono {{ $netSurplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $netSurplus >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($netSurplus), 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-slate-500 font-semibold mt-1">
                Akumulasi Laba Rugi 2026
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl {{ $netSurplus >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid {{ $netSurplus >= 0 ? 'fa-chart-line' : 'fa-chart-line-down' }}"></i>
        </div>
    </div>

    <!-- Card 3: Kontingen Lomba SBB IV -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex items-center justify-between hover:border-slate-300 transition">
        <div>
            <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kontingen SBB IV</div>
            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                {{ $sbbStats['total_teams'] }} <span class="text-sm font-bold text-slate-500">Tim</span>
            </div>
            <div class="text-[10px] text-slate-500 font-semibold mt-1 flex items-center gap-1.5">
                <span class="text-emerald-600 font-bold">{{ $sbbStats['verified'] }} Lunas</span> &bull; 
                <span class="text-amber-600 font-bold">{{ $sbbStats['pending'] }} Pending</span>
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-trophy"></i>
        </div>
    </div>

    <!-- Card 4: Kantong Parkir Lapangan -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex items-center justify-between hover:border-slate-300 transition">
        <div>
            <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Parkir Lapangan</div>
            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                {{ $parkingStats['occupancy_percent'] }}% <span class="text-sm font-bold text-slate-500">Terisi</span>
            </div>
            <div class="text-[10px] text-slate-500 font-semibold mt-1">
                {{ $parkingStats['currently_parked'] }} dari {{ $parkingStats['total_capacity'] }} Kuota Kendaraan
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-square-parking"></i>
        </div>
    </div>
</div>

<!-- 3. PUSAT SHORTCUT LAPORAN EKSEKUTIF (1-KLIK DOWNLOAD PDF & EXCEL) -->
<div class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200 shadow-sm mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-5 border-b border-slate-200">
        <div>
            <h3 class="font-black text-slate-900 text-base sm:text-lg flex items-center gap-2">
                <i class="fa-solid fa-file-shield text-pmr-primary"></i>
                Pusat Shortcut Laporan & Dokumen Eksekutif
            </h3>
            <p class="text-[11px] sm:text-xs text-slate-500">
                Unduh instan laporan resmi ber-kop logo SMAN 1 Ciawi & PMI (Alamat Jl. Veteran III Banjarwaru) siap tanda tangan RAT.
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-pmr-primary border border-rose-200 shrink-0 self-start sm:self-auto">
            <i class="fa-solid fa-bolt"></i> 1-Klik Download
        </span>
    </div>

    <!-- 3 Pilar Laporan Utama -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- PILAR 1: KEUANGAN & KAS ORGANISASI -->
        <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <span class="text-xs font-black uppercase text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-coins text-amber-500"></i> Laporan Keuangan & RAT
                    </span>
                    <span class="text-[9px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full">SAK EP</span>
                </div>
                
                <div class="space-y-2.5 text-xs">
                    <!-- Item 1: Laba Rugi -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">1. Laba Rugi (Aktivitas)</span>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.accounting.reports', ['report_type' => 'income_statement']) }}" target="_blank" class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1" title="Cetak / Simpan PDF">
                                <i class="fa-solid fa-file-pdf text-rose-600"></i> PDF
                            </a>
                            <a href="{{ route('admin.accounting.reports.export-excel', ['report_type' => 'income_statement']) }}" class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1" title="Unduh Excel">
                                <i class="fa-solid fa-file-excel text-emerald-600"></i> XLS
                            </a>
                        </div>
                    </div>

                    <!-- Item 2: Neraca -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">2. Neraca Posisi Keuangan</span>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.accounting.reports', ['report_type' => 'balance_sheet']) }}" target="_blank" class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1" title="Cetak / Simpan PDF">
                                <i class="fa-solid fa-file-pdf text-rose-600"></i> PDF
                            </a>
                            <a href="{{ route('admin.accounting.reports.export-excel', ['report_type' => 'balance_sheet']) }}" class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1" title="Unduh Excel">
                                <i class="fa-solid fa-file-excel text-emerald-600"></i> XLS
                            </a>
                        </div>
                    </div>

                    <!-- Item 3: Arus Kas -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">3. Laporan Arus Kas</span>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.accounting.reports', ['report_type' => 'cash_flow']) }}" target="_blank" class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1" title="Cetak / Simpan PDF">
                                <i class="fa-solid fa-file-pdf text-rose-600"></i> PDF
                            </a>
                            <a href="{{ route('admin.accounting.reports.export-excel', ['report_type' => 'cash_flow']) }}" class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1" title="Unduh Excel">
                                <i class="fa-solid fa-file-excel text-emerald-600"></i> XLS
                            </a>
                        </div>
                    </div>

                    <!-- Item 4: Perubahan Modal -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">4. Perubahan Modal</span>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.accounting.reports', ['report_type' => 'equity_changes']) }}" target="_blank" class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1" title="Cetak / Simpan PDF">
                                <i class="fa-solid fa-file-pdf text-rose-600"></i> PDF
                            </a>
                            <a href="{{ route('admin.accounting.reports.export-excel', ['report_type' => 'equity_changes']) }}" class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1" title="Unduh Excel">
                                <i class="fa-solid fa-file-excel text-emerald-600"></i> XLS
                            </a>
                        </div>
                    </div>

                    <!-- Item 5: CALK -->
                    <div class="flex items-center justify-between py-1">
                        <span class="font-bold text-slate-700">5. Catatan Lap. Keuangan</span>
                        <a href="{{ route('admin.accounting.reports', ['report_type' => 'calk']) }}" target="_blank" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-file-pdf text-rose-600"></i> Dokumen
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.accounting.reports') }}" class="mt-3 block text-center py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] rounded-xl transition">
                Buka Panel Keuangan Penuh &rarr;
            </a>
        </div>

        <!-- PILAR 2: PERHELATAN AKBAR LOMBA SBB IV -->
        <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <span class="text-xs font-black uppercase text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-500"></i> Lomba SBB IV & Parkir
                    </span>
                    <span class="text-[9px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">SBB 2026</span>
                </div>

                <div class="space-y-2.5 text-xs">
                    <!-- Item 1: Rekap Peserta -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">1. Rekap Tim Peserta Lomba</span>
                        <a href="{{ route('admin.competition-participants.export-excel') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i> Unduh XLS
                        </a>
                    </div>

                    <!-- Item 2: Master Score Sheet -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">2. Master Score Sheet Juri</span>
                        <a href="{{ route('admin.competition-scores.master-sheet.export-excel') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i> Rekap Nilai
                        </a>
                    </div>

                    <!-- Item 3: Rekap Parkir -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">3. Rekapitulasi Parkir Lapangan</span>
                        <a href="{{ route('admin.parking.export-excel') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i> Karcis XLS
                        </a>
                    </div>

                    <!-- Item 4: LPJ Proyek -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">4. LPJ Keuangan Proyek SBB IV</span>
                        @if($sbbProject)
                            <a href="{{ route('admin.accounting.projects.lpj-pdf', $sbbProject->id) }}" target="_blank" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-[10px] font-black flex items-center gap-1">
                                <i class="fa-solid fa-file-pdf"></i> Cetak LPJ
                            </a>
                        @else
                            <span class="text-[10px] text-slate-400 italic">Belum dibuat</span>
                        @endif
                    </div>

                    <!-- Item 5: Leaderboard -->
                    <div class="flex items-center justify-between py-1">
                        <span class="font-bold text-slate-700">5. Klasemen Juara Umum</span>
                        <a href="{{ route('admin.competition-leaderboard.index') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-medal text-amber-500"></i> Klasemen
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.competition-participants.index') }}" class="mt-3 block text-center py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] rounded-xl transition">
                Buka Manajemen Lomba &rarr;
            </a>
        </div>

        <!-- PILAR 3: KEANGGOTAAN & KEMANUSIAAN -->
        <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <span class="text-xs font-black uppercase text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-users text-blue-500"></i> Keanggotaan & Relawan
                    </span>
                    <span class="text-[9px] font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Unit Wira</span>
                </div>

                <div class="space-y-2.5 text-xs">
                    <!-- Item 1: Calon Anggota -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">1. Rekap Calon Anggota (PPDB)</span>
                        <a href="{{ route('admin.member-registrations.export') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i> Unduh XLS
                        </a>
                    </div>

                    <!-- Item 2: Buku Induk Anggota -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">2. Buku Induk Anggota PMR</span>
                        <a href="{{ route('admin.members.export') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i> Database
                        </a>
                    </div>

                    <!-- Item 3: Donor Darah -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">3. Log Pelayanan Donor Darah</span>
                        <a href="{{ route('admin.blood-donor-registrations.index') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-droplet text-red-600"></i> Data Donor
                        </a>
                    </div>

                    <!-- Item 4: Data Pembina -->
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="font-bold text-slate-700">4. Direktori Pembina Sekolah</span>
                        <a href="{{ route('admin.advisors.index') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-user-shield text-amber-600"></i> SK Pembina
                        </a>
                    </div>

                    <!-- Item 5: Hotline UKS -->
                    <div class="flex items-center justify-between py-1">
                        <span class="font-bold text-slate-700">5. Log Medis & Hotline UKS</span>
                        <a href="{{ route('admin.uks-hotline.index') }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-kit-medical text-rose-500"></i> Log UKS
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.members.index') }}" class="mt-3 block text-center py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] rounded-xl transition">
                Buka Kelola Anggota &rarr;
            </a>
        </div>
    </div>
</div>

<!-- 4. PEMANTAUAN OPERASIONAL AKTIF (TABEL TRANSAKSI KAS & WIDGET SAMPING) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
    
    <!-- Kolom Kiri: Riwayat Mutasi Jurnal Kas & Transaksi Terbaru (8 Cols) -->
    <div class="lg:col-span-8 bg-white rounded-3xl p-5 sm:p-7 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-book-journal-whills text-emerald-600"></i>
                    Mutasi Jurnal Kas & Transaksi Terbaru
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500">Bukti Kas Masuk (BKM), Kas Keluar (BKK), dan transfer dana antar-rekening.</p>
            </div>
            <a href="{{ route('admin.accounting.transactions') }}" class="text-xs text-pmr-primary font-bold hover:underline shrink-0">
                Lihat Buku Besar &rarr;
            </a>
        </div>

        <div class="overflow-x-auto touch-pan-x">
            <table class="w-full text-left text-xs min-w-[550px]">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-y border-slate-200 text-[10px]">
                    <tr>
                        <th class="py-2.5 px-3">No. Voucher</th>
                        <th class="py-2.5 px-3">Tanggal</th>
                        <th class="py-2.5 px-3">Deskripsi & Pihak Terkait</th>
                        <th class="py-2.5 px-3 text-right">Nominal</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-mono font-bold text-slate-900">
                                <span class="px-2 py-0.5 rounded-lg {{ $tx->type === 'cash_in' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($tx->type === 'cash_out' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                    {{ $tx->voucher_number }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-600 whitespace-nowrap">
                                {{ $tx->transaction_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-3 text-slate-800">
                                <div class="font-bold truncate max-w-[220px]">{{ $tx->description }}</div>
                                <div class="text-[10px] text-slate-400">{{ $tx->recipient_or_payer ?: '-' }}</div>
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold {{ $tx->type === 'cash_in' ? 'text-emerald-600' : ($tx->type === 'cash_out' ? 'text-rose-600' : 'text-slate-800') }}">
                                {{ $tx->type === 'cash_in' ? '+' : ($tx->type === 'cash_out' ? '-' : '') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Posted
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-400 italic">
                                Belum ada transaksi kas dibukukan. <a href="{{ route('admin.accounting.transactions') }}" class="text-pmr-primary font-bold hover:underline">Catat sekarang &rarr;</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kolom Kanan: Widget Event & Ketersediaan Stok Darah PMI (4 Cols) -->
    <div class="lg:col-span-4 space-y-6">
        
        <!-- SUA BHAKTI BERKARYA Status Card -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-amber-950 rounded-3xl p-5 text-white shadow-lg border border-amber-900/40 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3 relative z-10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-sm font-black shadow-md shadow-amber-500/30">
                        <i class="fa-solid fa-flag"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-sm">SUA BHAKTI BERKARYA</h4>
                        <div class="text-[10px] text-amber-200">{{ $activeCompetitionEvent->title ?? 'Lomba PMR Wira SMAN 1 Ciawi' }}</div>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Aktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs border-t border-slate-700/80 pt-3 mt-3">
                <div class="bg-slate-800/60 p-2.5 rounded-xl border border-slate-700/50">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Pendaftar:</span>
                    <span class="font-extrabold text-white text-base">{{ $sbbStats['total_teams'] }} Tim</span>
                </div>
                <div class="bg-slate-800/60 p-2.5 rounded-xl border border-slate-700/50">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Juri Lapangan:</span>
                    <span class="font-extrabold text-amber-400 text-base">{{ $totalJuries }} Personel</span>
                </div>
            </div>

            <a href="{{ route('admin.competition-participants.index') }}" class="mt-3 block text-center py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-[11px] rounded-xl transition shadow-md">
                Kelola Lomba SBB IV &rarr;
            </a>
        </div>

        <!-- Stok Darah PMI (Visual Vials) -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <h4 class="font-black text-slate-900 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-droplet text-red-600"></i>
                    Ketersediaan Darah PMI
                </h4>
                <a href="{{ route('admin.blood-stocks.index') }}" class="text-[10px] text-pmr-primary font-bold hover:underline">
                    Perbarui
                </a>
            </div>

            <div class="grid grid-cols-4 gap-2 text-center">
                @php
                    $bloodMap = $bloodStocks->keyBy('blood_type');
                    $types = ['A', 'B', 'AB', 'O'];
                @endphp
                @foreach($types as $bt)
                    @php
                        $stockObj = $bloodMap->get($bt);
                        $qty = $stockObj?->stock_quantity ?? 0;
                    @endphp
                    <div class="p-2.5 rounded-2xl bg-red-50/60 border border-red-100 flex flex-col items-center">
                        <span class="w-6 h-6 rounded-full bg-red-600 text-white font-black text-xs flex items-center justify-center mb-1">
                            {{ $bt }}
                        </span>
                        <span class="font-mono font-black text-sm text-slate-900">{{ $qty }}</span>
                        <span class="text-[9px] text-slate-400 uppercase font-semibold">Ktg</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Calon Anggota Baru (PPDB PMR) -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <h4 class="font-black text-slate-900 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-emerald-600"></i>
                    Pendaftar Calon Anggota
                </h4>
                <a href="{{ route('admin.member-registrations.index') }}" class="text-[10px] text-pmr-primary font-bold hover:underline">
                    Semua ({{ $totalRegistrations }})
                </a>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentRegistrations as $reg)
                    <div class="py-2 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-slate-800">{{ $reg->full_name }}</div>
                            <div class="text-[10px] text-slate-400">Kelas {{ $reg->class_grade }} &bull; {{ $reg->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $reg->status === 'verified' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ ucfirst($reg->status) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-3 text-slate-400 italic">Belum ada pendaftar baru.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
