@extends('layouts.admin')

@section('title', 'Laporan Keuangan Resmi - Akuntansi PMR Wira SMAN 1 Ciawi')
@section('page_title', 'Laporan Keuangan Standar Akuntansi')

@section('top_actions')
    <a href="{{ route('admin.accounting.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 border border-slate-200">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <a href="{{ route('admin.accounting.reports.export-excel', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fa-solid fa-file-excel text-sm"></i> Export ke Excel
    </a>
    <button type="button" onclick="window.print()" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-file-pdf text-sm"></i> Print ke PDF
    </button>
    @if($reportType === 'project' && $selectedProject)
        <a href="{{ route('admin.accounting.projects.lpj-pdf', $selectedProject->id) }}" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
            <i class="fa-solid fa-stamp text-sm"></i> Lembar LPJ Resmi
        </a>
    @endif
@endsection

@section('content')
<div class="space-y-6">

    <!-- 1. NAVIGATION TAB PILLS (5 LAPORAN STANDAR + LPJ PROYEK) -->
    <div class="bg-white rounded-3xl p-3 border border-slate-200 shadow-xs print:hidden">
        <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
            <a href="{{ route('admin.accounting.reports', ['report_type' => 'income_statement', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'project_id' => request('project_id')]) }}"
               class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $reportType === 'income_statement' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>1. Laba Rugi</span>
            </a>

            <a href="{{ route('admin.accounting.reports', ['report_type' => 'balance_sheet', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'project_id' => request('project_id')]) }}"
               class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $reportType === 'balance_sheet' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' }}">
                <i class="fa-solid fa-scale-balanced"></i>
                <span>2. Neraca (Posisi Keuangan)</span>
            </a>

            <a href="{{ route('admin.accounting.reports', ['report_type' => 'cash_flow', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'project_id' => request('project_id')]) }}"
               class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $reportType === 'cash_flow' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' }}">
                <i class="fa-solid fa-money-bill-transfer"></i>
                <span>3. Arus Kas</span>
            </a>

            <a href="{{ route('admin.accounting.reports', ['report_type' => 'equity_changes', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'project_id' => request('project_id')]) }}"
               class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $reportType === 'equity_changes' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' }}">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>4. Perubahan Modal</span>
            </a>

            <a href="{{ route('admin.accounting.reports', ['report_type' => 'calk', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'project_id' => request('project_id')]) }}"
               class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $reportType === 'calk' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' }}">
                <i class="fa-solid fa-book-bookmark"></i>
                <span>5. CALK (Catatan Lap. Keuangan)</span>
            </a>

            <a href="{{ route('admin.accounting.reports', ['report_type' => 'project', 'project_id' => $selectedProject?->id ?: ($projects->first()?->id)]) }}"
               class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $reportType === 'project' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' }}">
                <i class="fa-solid fa-trophy"></i>
                <span>LPJ Proyek (SBB IV)</span>
            </a>
        </div>
    </div>

    <!-- 2. FILTER PERIODE & PROYEK -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs print:hidden">
        <form action="{{ route('admin.accounting.reports') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <input type="hidden" name="report_type" value="{{ $reportType }}">

            @if($reportType === 'project')
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Proyek Kegiatan:</label>
                    <select name="project_id" onchange="this.form.submit()" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ $selectedProject?->id == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        {{ $reportType === 'balance_sheet' ? 'Periode Posisi (Hingga):' : 'Dari Tanggal:' }}
                    </label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal (Cut-off):</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none">
                </div>
            @endif

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
                    <i class="fa-solid fa-filter"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- 3. LEMBAR DOKUMEN LAPORAN RESMI (PRINT READY) -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm max-w-4xl mx-auto space-y-6 print:shadow-none print:border-none print:p-0">
        
        <!-- Kop Surat Laporan Organisasi (Logo Resmi Dual Logo PMI + SMAN 1 Ciawi) -->
        <div class="text-center pb-5 border-b-2 border-slate-900 space-y-2">
            <div class="flex items-center justify-center">
                <img src="{{ asset('images/logo_kop_laporan_pmr.png') }}" alt="Logo Palang Merah Indonesia & SMA Negeri 1 Ciawi" class="h-16 sm:h-20 w-auto object-contain mx-auto">
            </div>
            <div>
                <h2 class="font-black text-slate-900 text-base sm:text-lg uppercase tracking-tight leading-tight">
                    PALANG MERAH REMAJA (PMR) WIRA UNIT SMAN 1 CIAWI
                </h2>
                <div class="text-xs font-bold text-rose-600 uppercase tracking-wider">
                    KABUPATEN BOGOR &bull; PROVINSI JAWA BARAT
                </div>
                <div class="text-[11px] text-slate-500 pt-0.5">
                    Sekretariat: Ruang Markas UKS SMAN 1 Ciawi, Jl. Veteran III Banjarwaru, Ciawi, Bogor, Jawa Barat 16720
                </div>
            </div>

            <!-- Judul Laporan Dinamis -->
            <div class="pt-3">
                <span class="inline-block px-4 py-1.5 bg-slate-100 rounded-full text-xs font-black uppercase tracking-wider text-slate-800 border border-slate-300">
                    @if($reportType === 'income_statement')
                        LAPORAN LABA RUGI / AKTIVITAS KEUANGAN (INCOME STATEMENT)
                    @elseif($reportType === 'balance_sheet')
                        LAPORAN POSISI KEUANGAN (NERACA / BALANCE SHEET)
                    @elseif($reportType === 'cash_flow')
                        LAPORAN ARUS KAS (STATEMENT OF CASH FLOWS)
                    @elseif($reportType === 'equity_changes')
                        LAPORAN PERUBAHAN MODAL / EKUITAS (STATEMENT OF CHANGES IN EQUITY)
                    @elseif($reportType === 'calk')
                        CATATAN ATAS LAPORAN KEUANGAN (CALK) & PENGESAHAN RAT
                    @elseif($reportType === 'project')
                        LAPORAN REALISASI ANGGARAN & LPJ KEUANGAN PROYEK
                    @endif
                </span>
            </div>

            <div class="text-xs text-slate-600 font-medium pt-1">
                @if($reportType === 'project' && $selectedProject)
                    <strong class="text-slate-900">{{ $selectedProject->name }} ({{ $selectedProject->code }})</strong>
                @elseif($reportType === 'balance_sheet')
                    Per Posisi Tanggal: <strong class="text-slate-900">{{ Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}</strong>
                @else
                    Periode: <strong class="text-slate-900">{{ Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}</strong> s/d <strong class="text-slate-900">{{ Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}</strong>
                @endif
                <span class="text-slate-400 block text-[10px] pt-0.5">(Standar Akuntansi Nonlaba SAK EP / ISAK 35 & AD/ART PMR)</span>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 1: LAPORAN LABA RUGI (INCOME STATEMENT)                               --}}
        {{-- ========================================================================= --}}
        @if($reportType === 'income_statement')
            <!-- Ringkasan KPI Laba Rugi -->
            <div class="grid grid-cols-3 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Pendapatan</span>
                    <span class="font-mono font-black text-sm text-emerald-600">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Beban / Belanja</span>
                    <span class="font-mono font-black text-sm text-rose-600">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Surplus / (Defisit) Bersih</span>
                    <span class="font-mono font-black text-sm {{ $netSurplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $netSurplus >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($netSurplus), 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Tabel Pendapatan -->
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs font-black uppercase text-emerald-900 bg-emerald-50 px-3 py-2.5 rounded-xl border border-emerald-200">
                    <span>I. PENDAPATAN / PENERIMAAN DANA</span>
                    <span class="font-mono">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($revenueLines as $rl)
                        <div class="py-2.5 px-3 flex items-center justify-between hover:bg-slate-50">
                            <span class="text-slate-800">
                                <strong class="font-mono text-slate-500 mr-2">{{ $rl->account?->code }}</strong>
                                {{ $rl->account?->name }}
                            </span>
                            <span class="font-mono font-bold text-slate-900">
                                Rp {{ number_format($rl->total, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="py-3 text-center text-slate-400 italic">Belum ada pendapatan dibukukan pada periode ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Tabel Beban -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between text-xs font-black uppercase text-rose-900 bg-rose-50 px-3 py-2.5 rounded-xl border border-rose-200">
                    <span>II. BEBAN / PENGELUARAN KEGIATAN & OPERASIONAL</span>
                    <span class="font-mono">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($expenseLines as $el)
                        <div class="py-2.5 px-3 flex items-center justify-between hover:bg-slate-50">
                            <span class="text-slate-800">
                                <strong class="font-mono text-slate-500 mr-2">{{ $el->account?->code }}</strong>
                                {{ $el->account?->name }}
                            </span>
                            <span class="font-mono font-bold text-rose-600">
                                Rp {{ number_format($el->total, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="py-3 text-center text-slate-400 italic">Belum ada pos beban dibukukan pada periode ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Hasil Bersih -->
            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-300 flex items-center justify-between font-black text-sm {{ $netSurplus >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                <span>SURPLUS / (DEFISIT) BERSIH PERIODE BERJALAN:</span>
                <span class="font-mono text-base">
                    {{ $netSurplus >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($netSurplus), 0, ',', '.') }}
                </span>
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB 2: NERACA / LAPORAN POSISI KEUANGAN (BALANCE SHEET)                   --}}
        {{-- ========================================================================= --}}
        @elseif($reportType === 'balance_sheet')
            <!-- Status Keseimbangan Neraca -->
            <div class="p-3.5 rounded-2xl {{ $isBalanced ? 'bg-emerald-50 border border-emerald-300 text-emerald-800' : 'bg-rose-50 border border-rose-300 text-rose-800' }} flex items-center justify-between text-xs font-bold">
                <div class="flex items-center gap-2">
                    <i class="fa-solid {{ $isBalanced ? 'fa-circle-check text-emerald-600' : 'fa-triangle-exclamation text-rose-600' }} text-base"></i>
                    <span>
                        {{ $isBalanced ? 'NERACA SEIMBANG (BALANCED): Total Aset sama dengan Total Kewajiban & Ekuitas.' : 'PERHATIAN: Neraca belum seimbang (Ada selisih debit/kredit pada jurnal).' }}
                    </span>
                </div>
                <div class="font-mono text-xs">
                    Total: Rp {{ number_format($totalAssets, 0, ',', '.') }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SISI KIRI: ASET / AKTIVA -->
                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 px-3.5 py-2 rounded-xl text-xs font-black text-blue-900 uppercase flex justify-between">
                        <span>ASET (AKTIVA)</span>
                        <span class="font-mono">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
                    </div>

                    <!-- 1. Kas & Bank -->
                    <div class="space-y-2">
                        <div class="text-[11px] font-black uppercase text-slate-500 border-b pb-1">
                            A. Kas & Setara Kas (Kas & Bank)
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            @forelse($cashBankLines as $cb)
                                <div class="py-2 px-2 flex justify-between">
                                    <span class="text-slate-700">
                                        <strong class="font-mono text-slate-400 mr-1.5">{{ $cb['account']->code }}</strong>
                                        {{ $cb['account']->name }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-900">
                                        Rp {{ number_format($cb['balance'], 0, ',', '.') }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-slate-400 italic py-2">Tidak ada saldo kas.</div>
                            @endforelse
                        </div>
                        <div class="flex justify-between text-xs font-bold pt-1 border-t border-slate-200 text-slate-800">
                            <span>Subtotal Kas & Bank:</span>
                            <span class="font-mono">Rp {{ number_format($totalCashBank, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- 2. Aset Lancar Lainnya & Aset Tetap -->
                    @if($otherCurrentAssetLines->isNotEmpty())
                        <div class="space-y-2 pt-2">
                            <div class="text-[11px] font-black uppercase text-slate-500 border-b pb-1">
                                B. Piutang & Perlengkapan Operasional
                            </div>
                            <div class="divide-y divide-slate-100 text-xs">
                                @foreach($otherCurrentAssetLines as $oa)
                                    <div class="py-2 px-2 flex justify-between">
                                        <span class="text-slate-700">
                                            <strong class="font-mono text-slate-400 mr-1.5">{{ $oa['account']->code }}</strong>
                                            {{ $oa['account']->name }}
                                        </span>
                                        <span class="font-mono font-bold text-slate-900">
                                            Rp {{ number_format($oa['balance'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($fixedAssetLines->isNotEmpty())
                        <div class="space-y-2 pt-2">
                            <div class="text-[11px] font-black uppercase text-slate-500 border-b pb-1">
                                C. Aset Tetap (Peralatan Medis & Tandu)
                            </div>
                            <div class="divide-y divide-slate-100 text-xs">
                                @foreach($fixedAssetLines as $fa)
                                    <div class="py-2 px-2 flex justify-between">
                                        <span class="text-slate-700">
                                            <strong class="font-mono text-slate-400 mr-1.5">{{ $fa['account']->code }}</strong>
                                            {{ $fa['account']->name }}
                                        </span>
                                        <span class="font-mono font-bold text-slate-900">
                                            Rp {{ number_format($fa['balance'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between text-xs font-bold pt-1 border-t border-slate-200 text-slate-800">
                                <span>Subtotal Aset Tetap:</span>
                                <span class="font-mono">Rp {{ number_format($totalFixedAssets, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Total Aset -->
                    <div class="p-3 bg-blue-100 rounded-xl border border-blue-200 flex justify-between text-xs font-black text-blue-900">
                        <span>TOTAL ASET (AKTIVA):</span>
                        <span class="font-mono text-sm">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- SISI KANAN: KEWAJIBAN & EKUITAS (PASIVA) -->
                <div class="space-y-4">
                    <div class="bg-indigo-50 border border-indigo-200 px-3.5 py-2 rounded-xl text-xs font-black text-indigo-900 uppercase flex justify-between">
                        <span>KEWAJIBAN & EKUITAS (PASIVA)</span>
                        <span class="font-mono">Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</span>
                    </div>

                    <!-- 1. Kewajiban -->
                    <div class="space-y-2">
                        <div class="text-[11px] font-black uppercase text-slate-500 border-b pb-1">
                            A. Kewajiban / Utang Jangka Pendek
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            @forelse($liabilityLines as $lb)
                                <div class="py-2 px-2 flex justify-between">
                                    <span class="text-slate-700">
                                        <strong class="font-mono text-slate-400 mr-1.5">{{ $lb['account']->code }}</strong>
                                        {{ $lb['account']->name }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-900">
                                        Rp {{ number_format($lb['balance'], 0, ',', '.') }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-slate-400 italic py-2">Tidak ada kewajiban / utang tercatat.</div>
                            @endforelse
                        </div>
                        <div class="flex justify-between text-xs font-bold pt-1 border-t border-slate-200 text-slate-800">
                            <span>Total Kewajiban:</span>
                            <span class="font-mono">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- 2. Ekuitas / Modal Organisasi -->
                    <div class="space-y-2 pt-2">
                        <div class="text-[11px] font-black uppercase text-slate-500 border-b pb-1">
                            B. Ekuitas / Aset Bersih Organisasi
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            @forelse($equityLines as $eq)
                                <div class="py-2 px-2 flex justify-between">
                                    <span class="text-slate-700">
                                        <strong class="font-mono text-slate-400 mr-1.5">{{ $eq['account']->code }}</strong>
                                        {{ $eq['account']->name }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-900">
                                        Rp {{ number_format($eq['balance'], 0, ',', '.') }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-slate-400 italic py-2">Saldo modal awal tidak ditemukan.</div>
                            @endforelse
                            <div class="py-2 px-2 flex justify-between bg-amber-50 rounded-lg">
                                <span class="text-amber-900 font-bold">
                                    <i class="fa-solid fa-calculator mr-1"></i> Akumulasi Surplus Berjalan
                                </span>
                                <span class="font-mono font-black text-amber-900">
                                    Rp {{ number_format($cumulativeSurplus, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs font-bold pt-1 border-t border-slate-200 text-slate-800">
                            <span>Total Ekuitas / Aset Bersih:</span>
                            <span class="font-mono">Rp {{ number_format($totalEquity, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Total Pasiva -->
                    <div class="p-3 bg-indigo-100 rounded-xl border border-indigo-200 flex justify-between text-xs font-black text-indigo-900">
                        <span>TOTAL KEWAJIBAN & EKUITAS:</span>
                        <span class="font-mono text-sm">Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB 3: LAPORAN ARUS KAS (CASH FLOW STATEMENT)                             --}}
        {{-- ========================================================================= --}}
        @elseif($reportType === 'cash_flow')
            <div class="space-y-4">
                <!-- Saldo Awal Kas -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex justify-between items-center text-xs font-bold">
                    <span class="text-slate-600">SALDO AWAL KAS & BANK (Sebelum {{ Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}):</span>
                    <span class="font-mono text-sm font-black text-slate-900">Rp {{ number_format($openingCash, 0, ',', '.') }}</span>
                </div>

                <!-- I. Arus Kas Operasi -->
                <div class="space-y-3">
                    <div class="bg-emerald-50 border border-emerald-200 px-3.5 py-2.5 rounded-xl text-xs font-black text-emerald-900 uppercase flex justify-between">
                        <span>I. ARUS KAS DARI AKTIVITAS OPERASIONAL & PROYEK</span>
                        <span class="font-mono">Rp {{ number_format($netOperatingCash, 0, ',', '.') }}</span>
                    </div>

                    <div class="pl-3 space-y-3 text-xs">
                        <!-- Penerimaan -->
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase block mb-1">A. Penerimaan Kas:</span>
                            <div class="divide-y divide-slate-100 pl-2">
                                @forelse($cashInflowSummary as $desc => $val)
                                    <div class="py-1.5 flex justify-between">
                                        <span class="text-slate-700">{{ $desc }}</span>
                                        <span class="font-mono font-bold text-emerald-600">+Rp {{ number_format($val, 0, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <div class="text-slate-400 italic py-1">Tidak ada penerimaan kas periode ini.</div>
                                @endforelse
                                <div class="flex justify-between font-bold pt-1.5 text-slate-900">
                                    <span>Subtotal Penerimaan Kas:</span>
                                    <span class="font-mono text-emerald-700">+Rp {{ number_format($totalOperatingIn, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Pengeluaran -->
                        <div class="pt-2">
                            <span class="text-[11px] font-bold text-slate-500 uppercase block mb-1">B. Pengeluaran Kas:</span>
                            <div class="divide-y divide-slate-100 pl-2">
                                @forelse($cashOutflowSummary as $desc => $val)
                                    <div class="py-1.5 flex justify-between">
                                        <span class="text-slate-700">{{ $desc }}</span>
                                        <span class="font-mono font-bold text-rose-600">-Rp {{ number_format($val, 0, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <div class="text-slate-400 italic py-1">Tidak ada pengeluaran kas periode ini.</div>
                                @endforelse
                                <div class="flex justify-between font-bold pt-1.5 text-slate-900">
                                    <span>Subtotal Pengeluaran Kas:</span>
                                    <span class="font-mono text-rose-700">-Rp {{ number_format($totalOperatingOut, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- II. Investasi & Pendanaan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-700 block">II. Arus Kas Investasi:</span>
                        <span class="text-[11px] text-slate-500">Pengadaan peralatan tandu & medis:</span>
                        <span class="font-mono font-bold block pt-1 text-slate-900">Rp {{ number_format($netInvestingCash, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-700 block">III. Arus Kas Pendanaan:</span>
                        <span class="text-[11px] text-slate-500">Penyertaan dana awal & hibah eksternal:</span>
                        <span class="font-mono font-bold block pt-1 text-slate-900">Rp {{ number_format($netFinancingCash, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Kenaikan Bersih -->
                <div class="p-3.5 bg-slate-100 rounded-xl border border-slate-300 flex justify-between items-center text-xs font-black">
                    <span class="text-slate-800">KENAIKAN / (PENURUNAN) BERSIH KAS & BANK:</span>
                    <span class="font-mono text-sm {{ $netCashChange >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $netCashChange >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($netCashChange), 0, ',', '.') }}
                    </span>
                </div>

                <!-- Saldo Akhir Kas -->
                <div class="p-4 bg-emerald-100 rounded-2xl border border-emerald-300 flex justify-between items-center text-xs font-black text-emerald-950">
                    <div>
                        <span>SALDO AKHIR KAS & BANK PER {{ Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}:</span>
                        <span class="block text-[10px] font-normal text-emerald-800">Telah terverifikasi sesuai total Kas & Bank pada Neraca</span>
                    </div>
                    <span class="font-mono text-base font-black">Rp {{ number_format($closingCash, 0, ',', '.') }}</span>
                </div>
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB 4: LAPORAN PERUBAHAN MODAL / EKUITAS (STATEMENT OF CHANGES IN EQUITY)   --}}
        {{-- ========================================================================= --}}
        @elseif($reportType === 'equity_changes')
            <div class="space-y-4">
                <div class="divide-y divide-slate-200 border border-slate-200 rounded-2xl p-4 bg-slate-50 text-xs">
                    <div class="py-3 flex justify-between items-center">
                        <span class="text-slate-700 font-bold">1. Saldo Modal / Ekuitas Awal (Sebelum {{ Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}):</span>
                        <span class="font-mono font-bold text-slate-900">Rp {{ number_format($openingEquity, 0, ',', '.') }}</span>
                    </div>

                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <span class="text-slate-700 font-bold">2. Surplus / (Defisit) Bersih Periode Berjalan:</span>
                            <span class="text-[10px] text-slate-400 block">Berdasarkan hasil perhitungan Laporan Laba Rugi periode ini</span>
                        </div>
                        <span class="font-mono font-bold {{ $periodSurplus >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $periodSurplus >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($periodSurplus), 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <span class="text-slate-700 font-bold">3. Penambahan Langsung ke Ekuitas:</span>
                            <span class="text-[10px] text-slate-400 block">Sumbangan modal awal baru atau titipan dana khusus organisasi</span>
                        </div>
                        <span class="font-mono font-bold text-slate-900">Rp {{ number_format($periodDirectEquity, 0, ',', '.') }}</span>
                    </div>

                    <div class="py-4 flex justify-between items-center font-black bg-rose-50 -mx-4 px-4 rounded-b-xl border-t-2 border-rose-200">
                        <div>
                            <span class="text-rose-950 uppercase text-xs">SALDO AKHIR MODAL / EKUITAS ORGANISASI (Per {{ Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}):</span>
                            <span class="text-[10px] font-normal text-rose-800 block">Sesuai dengan pos Total Ekuitas pada Laporan Posisi Keuangan (Neraca)</span>
                        </div>
                        <span class="font-mono text-base text-rose-900">Rp {{ number_format($closingEquity, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB 5: CATATAN ATAS LAPORAN KEUANGAN (CALK)                               --}}
        {{-- ========================================================================= --}}
        @elseif($reportType === 'calk')
            <div class="space-y-6 text-xs text-slate-800 leading-relaxed">
                <!-- Catatan 1: Gambaran Umum -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <h4 class="font-black text-slate-900 uppercase text-xs flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">1</span>
                        Gambaran Umum Organisasi
                    </h4>
                    <p class="text-slate-600 text-[11px]">
                        Palang Merah Remaja (PMR) Wira Unit SMA Negeri 1 Ciawi Kabupaten Bogor merupakan wadah pembinaan kepalangmerahan dan relawan muda di bawah naungan Palang Merah Indonesia (PMI) Cabang Kabupaten Bogor dan SMA Negeri 1 Ciawi. Tata kelola keuangan organisasi berpedoman pada Anggaran Dasar dan Anggaran Rumah Tangga (AD/ART) PMI serta program kerja Rapat Anggota Tahunan (RAT).
                    </p>
                </div>

                <!-- Catatan 2: Kebijakan Akuntansi -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <h4 class="font-black text-slate-900 uppercase text-xs flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">2</span>
                        Kebijakan Akuntansi Signifikan
                    </h4>
                    <p class="text-slate-600 text-[11px]">
                        Laporan keuangan disusun menggunakan Standar Akuntansi Keuangan Entitas Privat (SAK EP) dan Interpretasi Standar Akuntansi Keuangan (ISAK 35) untuk Entitas Berorientasi Nonlaba. Pengakuan transaksi menggunakan prinsip pembukuan berpasangan (double-entry bookkeeping) dengan bagan akun standar multi-proyek mirip sistem Accurate Accounting.
                    </p>
                </div>

                <!-- Catatan 3: Rincian Kas & Bank -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <h4 class="font-black text-slate-900 uppercase text-xs flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">3</span>
                        Rincian Kas & Setara Kas
                    </h4>
                    <table class="w-full text-left border border-slate-200 rounded-xl overflow-hidden">
                        <thead class="bg-slate-100 text-[10px] font-bold text-slate-700">
                            <tr>
                                <th class="p-2 border-b">Kode Akun</th>
                                <th class="p-2 border-b">Nama Rekening / Pos Kas</th>
                                <th class="p-2 border-b">Institusi / Pengelola</th>
                                <th class="p-2 border-b text-right">Saldo per {{ Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[11px]">
                            @foreach($cashBankAccountsList as $cba)
                                <tr>
                                    <td class="p-2 font-mono font-bold text-slate-600">{{ $cba->code }}</td>
                                    <td class="p-2 font-bold text-slate-900">{{ $cba->name }}</td>
                                    <td class="p-2 text-slate-500">{{ $cba->description ?: 'Kas Resmi PMR' }}</td>
                                    <td class="p-2 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($cba->current_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-black text-slate-900 border-t border-slate-300">
                            <tr>
                                <td colspan="3" class="p-2 text-right">TOTAL KAS & SETARA KAS:</td>
                                <td class="p-2 text-right font-mono text-emerald-700">
                                    Rp {{ number_format($cashBankAccountsList->sum('current_balance'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Catatan 4: Rincian Proyek Lomba SBB IV -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <h4 class="font-black text-slate-900 uppercase text-xs flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">4</span>
                        Rincian Proyek: Lomba Akbar Sua Bhakti Berkarya (SBB) IV 2026
                    </h4>
                    <p class="text-slate-600 text-[11px]">
                        Proyek kompetisi kepalangmerahan tingkat Madya, Wira, dan Mula se-Jawa Barat, DKI Jakarta, dan Banten dibukukan secara terpisah sebagai pusat biaya (Cost Center) dengan kode registrasi <strong class="text-slate-900">PRJ-2026-SBB4</strong>. Seluruh pendapatan pendaftaran kontingen, retribusi parkir hari-H, dan sponsorship dikelola transparan dan dipertanggungjawabkan pada RAT.
                    </p>
                </div>
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB 6: LAPORAN KHUSUS PER PROYEK (LPJ PROYEK SBB IV)                      --}}
        {{-- ========================================================================= --}}
        @elseif($reportType === 'project' && $selectedProject)
            <!-- Profil Singkat Proyek -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Kode Proyek:</span>
                    <span class="font-bold text-slate-900 font-mono">{{ $selectedProject->code }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Kategori:</span>
                    <span class="font-bold text-slate-900">{{ $selectedProject->category_label }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Target Penerimaan:</span>
                    <span class="font-bold text-slate-900 font-mono">Rp {{ number_format($selectedProject->budget_target_revenue, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Pagu Belanja:</span>
                    <span class="font-bold text-slate-900 font-mono">Rp {{ number_format($selectedProject->budget_limit_expense, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Ringkasan Realisasi Proyek -->
            <div class="grid grid-cols-3 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Penerimaan</span>
                    <span class="font-mono font-black text-sm text-emerald-600">
                        {{ $selectedProject->formatted_revenue }}
                    </span>
                    <span class="text-[10px] text-slate-400 block pt-0.5">Capai: {{ $selectedProject->revenue_realization_percent }}%</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Belanja</span>
                    <span class="font-mono font-black text-sm text-rose-600">
                        {{ $selectedProject->formatted_expense }}
                    </span>
                    <span class="text-[10px] text-slate-400 block pt-0.5">Serap: {{ $selectedProject->expense_absorption_percent }}%</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sisa Kas / Surplus</span>
                    <span class="font-mono font-black text-sm {{ $selectedProject->net_surplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $selectedProject->formatted_net_surplus }}
                    </span>
                    <span class="text-[10px] text-slate-400 block pt-0.5">Status: {{ $selectedProject->net_surplus >= 0 ? 'Surplus' : 'Defisit' }}</span>
                </div>
            </div>

            <!-- Rincian Pos Penerimaan Proyek -->
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs font-black uppercase text-emerald-900 bg-emerald-50 px-3 py-2.5 rounded-xl border border-emerald-200">
                    <span>I. PENERIMAAN / SUMBER DANA PROYEK</span>
                    <span class="font-mono">{{ $selectedProject->formatted_revenue }}</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($revenueLines as $rl)
                        <div class="py-2.5 px-3 flex items-center justify-between hover:bg-slate-50">
                            <span class="text-slate-800">
                                <strong class="font-mono text-slate-500 mr-2">{{ $rl->account?->code }}</strong>
                                {{ $rl->account?->name }}
                            </span>
                            <span class="font-mono font-bold text-slate-900">
                                Rp {{ number_format($rl->total, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="py-3 text-center text-slate-400 italic">Belum ada pendapatan dibukukan untuk proyek ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Rincian Pos Belanja Proyek -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between text-xs font-black uppercase text-rose-900 bg-rose-50 px-3 py-2.5 rounded-xl border border-rose-200">
                    <span>II. REALISASI BELANJA / PENGELUARAN PROYEK</span>
                    <span class="font-mono">{{ $selectedProject->formatted_expense }}</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($expenseLines as $el)
                        <div class="py-2.5 px-3 flex items-center justify-between hover:bg-slate-50">
                            <span class="text-slate-800">
                                <strong class="font-mono text-slate-500 mr-2">{{ $el->account?->code }}</strong>
                                {{ $el->account?->name }}
                            </span>
                            <span class="font-mono font-bold text-rose-600">
                                Rp {{ number_format($el->total, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="py-3 text-center text-slate-400 italic">Belum ada pos belanja dibukukan untuk proyek ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Sisa Kas Proyek -->
            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-300 flex items-center justify-between font-black text-sm {{ $selectedProject->net_surplus >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                <span>SISA KAS / SURPLUS (DEFISIT) BERSIH PROYEK:</span>
                <span class="font-mono text-base">
                    {{ $selectedProject->formatted_net_surplus }}
                </span>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- LEMBAR TANDA TANGAN PENGESAHAN RESMI (SESUAI AD/ART & STANDAR RAT)        --}}
        {{-- ========================================================================= --}}
        <div class="pt-8 border-t border-slate-200">
            <div class="text-center pb-4 text-xs font-bold text-slate-700">
                Bogor, {{ Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}
                <div class="text-[11px] text-slate-400 font-normal">Disahkan dalam Rapat Anggota Tahunan (RAT) PMR Wira SMAN 1 Ciawi</div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-[11px] text-slate-600">
                <div>
                    <div class="text-[10px] text-slate-400">Bendahara Umum / Pelaksana,</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                        {{ $selectedProject?->treasurer_name ?: 'Siti Nurhaliza' }}
                    </div>
                    <div class="text-[9px] text-slate-400">NIA. PMR-WIRA-2025-014</div>
                </div>

                <div>
                    <div class="text-[10px] text-slate-400">Ketua Umum PMR Wira,</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                        Sulthan Arkana Putra R.
                    </div>
                    <div class="text-[9px] text-slate-400">NIA. PMR-WIRA-2024-001</div>
                </div>

                <div>
                    <div class="text-[10px] text-slate-400">Pembina PMR Wira,</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                        Drs. H. Mulyadi, M.Pd.
                    </div>
                    <div class="text-[9px] text-slate-400">NIP. 19740512 200003 1 004</div>
                </div>

                <div>
                    <div class="text-[10px] text-slate-400">Mengetahui Kepala Sekolah,</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                        Dr. Hj. Ai Nurjanah, M.Pd.
                    </div>
                    <div class="text-[9px] text-slate-400">NIP. 19681120 199303 2 003</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
