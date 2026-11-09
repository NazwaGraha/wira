@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Realisasi Anggaran - Akuntansi PMR')
@section('page_title', 'Laporan Keuangan & Realisasi Anggaran')

@section('top_actions')
    <a href="{{ route('admin.accounting.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 border border-slate-200">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    @if($reportType === 'project' && $selectedProject)
        <a href="{{ route('admin.accounting.projects.lpj-pdf', $selectedProject->id) }}" target="_blank" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
            <i class="fa-solid fa-file-pdf"></i> Cetak LPJ Resmi Proyek
        </a>
    @endif
    <button type="button" onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-print"></i> Cetak Layar Laporan
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- 1. FILTER LAPORAN -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs print:hidden">
        <form action="{{ route('admin.accounting.reports') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Model Laporan:</label>
                <select name="report_type" onchange="this.form.submit()" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <option value="project" {{ $reportType === 'project' ? 'selected' : '' }}>Laporan Khusus Per Proyek</option>
                    <option value="general" {{ $reportType === 'general' ? 'selected' : '' }}>Laba Rugi Konsolidasi Periode</option>
                </select>
            </div>

            @if($reportType === 'project')
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Proyek Kegiatan:</label>
                    <select name="project_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ $selectedProject?->id == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal:</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal:</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none">
                </div>
            @endif

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
                    <i class="fa-solid fa-magnifying-glass"></i> Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- 2. LEMBAR DOKUMEN LAPORAN RESMI (PRINT READY) -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm max-w-4xl mx-auto space-y-6">
        
        <!-- Kop Laporan Organisasi -->
        <div class="text-center pb-6 border-b-2 border-slate-900 space-y-1">
            <div class="flex items-center justify-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-pmr-primary text-white flex items-center justify-center text-xl font-black">
                    <i class="fa-solid fa-cross"></i>
                </div>
                <div class="text-left">
                    <h2 class="font-black text-slate-900 text-lg uppercase tracking-tight leading-none">
                        PALANG MERAH REMAJA (PMR) WIRA
                    </h2>
                    <div class="text-xs font-extrabold text-pmr-primary uppercase tracking-wider">
                        SMA NEGERI 1 CIAWI KABUPATEN BOGOR
                    </div>
                </div>
            </div>
            <div class="text-[11px] text-slate-500 pt-2">
                Sekretariat: Ruang Markas UKS SMAN 1 Ciawi, Jl. Mayor Oking No. 1 Ciawi, Bogor, Jawa Barat.
            </div>
            <div class="pt-3">
                <span class="inline-block px-3 py-1 bg-slate-100 rounded-full text-xs font-black uppercase tracking-wider text-slate-800 border border-slate-200">
                    @if($reportType === 'project' && $selectedProject)
                        LAPORAN REALISASI ANGGARAN & LPJ KEUANGAN PROYEK
                    @else
                        LAPORAN LABA RUGI / SURPLUS DEFISIT KONSOLIDASI
                    @endif
                </span>
            </div>
            @if($reportType === 'project' && $selectedProject)
                <h3 class="font-black text-slate-900 text-base pt-1">
                    {{ $selectedProject->name }} ({{ $selectedProject->code }})
                </h3>
            @else
                <div class="text-xs text-slate-600 font-medium pt-1">
                    Periode: {{ Carbon\Carbon::parse($dateFrom)->format('d F Y') }} s/d {{ Carbon\Carbon::parse($dateTo)->format('d F Y') }}
                </div>
            @endif
        </div>

        <!-- Ringkasan 3 Angka Utama -->
        <div class="grid grid-cols-3 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Pendapatan</span>
                <span class="font-mono font-black text-sm text-emerald-600">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Belanja / Beban</span>
                <span class="font-mono font-black text-sm text-rose-600">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Surplus / (Defisit)</span>
                <span class="font-mono font-black text-sm {{ $netSurplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $netSurplus >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($netSurplus), 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Tabel Pemasukan -->
        <div class="space-y-3">
            <div class="flex items-center justify-between text-xs font-black uppercase text-emerald-900 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200">
                <span>I. PENERIMAAN / PEMASUKAN</span>
                <span class="font-mono">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($revenueLines as $rl)
                    <div class="py-2 px-3 flex items-center justify-between hover:bg-slate-50">
                        <span class="text-slate-800">
                            <strong class="font-mono text-slate-500 mr-2">{{ $rl->account?->code }}</strong>
                            {{ $rl->account?->name }}
                        </span>
                        <span class="font-mono font-bold text-slate-900">
                            Rp {{ number_format($rl->total, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <div class="py-3 text-center text-slate-400 italic">Belum ada pos pendapatan dibukukan.</div>
                @endforelse
            </div>
        </div>

        <!-- Tabel Pengeluaran -->
        <div class="space-y-3 pt-3">
            <div class="flex items-center justify-between text-xs font-black uppercase text-rose-900 bg-rose-50 px-3 py-2 rounded-xl border border-rose-200">
                <span>II. BELANJA / PENGELUARAN KEGIATAN</span>
                <span class="font-mono">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($expenseLines as $el)
                    <div class="py-2 px-3 flex items-center justify-between hover:bg-slate-50">
                        <span class="text-slate-800">
                            <strong class="font-mono text-slate-500 mr-2">{{ $el->account?->code }}</strong>
                            {{ $el->account?->name }}
                        </span>
                        <span class="font-mono font-bold text-rose-600">
                            Rp {{ number_format($el->total, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <div class="py-3 text-center text-slate-400 italic">Belum ada pos belanja dibukukan.</div>
                @endforelse
            </div>
        </div>

        <!-- Total Sisa Kas Bersih -->
        <div class="p-4 rounded-2xl bg-slate-100 border border-slate-300 flex items-center justify-between font-black text-sm {{ $netSurplus >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
            <span>SISA KAS / SURPLUS (DEFISIT) BERSIH:</span>
            <span class="font-mono text-base">
                {{ $netSurplus >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($netSurplus), 0, ',', '.') }}
            </span>
        </div>

        <!-- Lembar Tanda Tangan Resmi Sesuai AD/ART -->
        <div class="pt-8 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-[11px] text-slate-600">
            <div>
                <div class="text-[10px] text-slate-400">Ketua Pelaksana,</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                    {{ $selectedProject?->person_in_charge ?: 'Sulthan Arkana P.' }}
                </div>
            </div>
            <div>
                <div class="text-[10px] text-slate-400">Bendahara Proyek,</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                    {{ $selectedProject?->treasurer_name ?: 'Bendahara Panitia' }}
                </div>
            </div>
            <div>
                <div class="text-[10px] text-slate-400">Ketua Umum PMR,</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                    Sulthan Arkana Putra R.
                </div>
            </div>
            <div>
                <div class="text-[10px] text-slate-400">Mengetahui Pembina,</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                    Pembina PMR Wira
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
