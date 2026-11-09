@extends('layouts.admin')

@section('title', 'Akuntansi & Kas Organisasi (Accurate-Style)')
@section('page_title', 'Akuntansi & Kas Organisasi')

@section('top_actions')
    @if($selectedProject)
        <a href="{{ route('admin.accounting.projects.lpj-pdf', $selectedProject->id) }}" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-3.5 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
            <i class="fa-solid fa-file-pdf text-rose-400"></i> Cetak LPJ Proyek
        </a>
    @endif

    <!-- Tombol Sinkronisasi Kas Otomatis -->
    <div class="flex items-center gap-2">
        <form action="{{ route('admin.accounting.sync-competition') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer" title="Tarik data pendaftaran terverifikasi ke pembukuan">
                <i class="fa-solid fa-arrows-rotate"></i> Sinkron Pendaftaran
            </button>
        </form>

        <form action="{{ route('admin.accounting.sync-parking') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer" title="Tarik kas karcis parkir lapangan ke pembukuan">
                <i class="fa-solid fa-square-parking"></i> Sinkron Parkir
            </button>
        </form>
    </div>

    <!-- Tombol Catat Transaksi Cepat -->
    <button type="button" onclick="openModal('modalCashIn')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-plus-circle"></i> + Kas Masuk (BKM)
    </button>
    <button type="button" onclick="openModal('modalCashOut')" class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-minus-circle"></i> + Kas Keluar (BKK)
    </button>
    <button type="button" onclick="openModal('modalTransfer')" class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-right-left"></i> Transfer Kas
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- 1. SELECTOR PROYEK AKTIF (ACCURATE-STYLE MULTI-PROJECT BANNER) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-md border border-slate-700">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-300 bg-rose-950/80 border border-rose-800 px-2.5 py-0.5 rounded-full">
                        <i class="fa-solid fa-diagram-project text-rose-400"></i> PROYEK AKTIF (COST CENTER)
                    </span>
                    @if($selectedProject)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $selectedProject->status_badge_class }}">
                            {{ $selectedProject->status_label }}
                        </span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-600">
                            Konsolidasi Seluruh Kas Organisasi
                        </span>
                    @endif
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white">
                    {{ $selectedProject ? $selectedProject->name : 'Semua Proyek & Kas Operasional Konsolidasi' }}
                </h2>
                <p class="text-xs text-slate-300 leading-relaxed max-w-2xl">
                    @if($selectedProject)
                        Kode: <span class="font-mono font-bold text-rose-300">{{ $selectedProject->code }}</span> &bull; 
                        PIC: <strong class="text-white">{{ $selectedProject->person_in_charge ?: 'Ketua Pelaksana' }}</strong> &bull; 
                        Bendahara: <strong class="text-white">{{ $selectedProject->treasurer_name ?: 'Bendahara Proyek' }}</strong>
                        @if($selectedProject->start_date && $selectedProject->end_date)
                            &bull; Periode: {{ $selectedProject->start_date->format('d M Y') }} s/d {{ $selectedProject->end_date->format('d M Y') }}
                        @endif
                    @else
                        Menampilkan seluruh rekapitulasi pembukuan kas besar, kas kecil markas UKS, dan seluruh kepanitiaan lomba.
                    @endif
                </p>
            </div>

            <!-- Dropdown Pemilihan Proyek -->
            <div class="shrink-0 w-full md:w-auto">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Ganti Tampilan Proyek:</label>
                <select onchange="window.location.href = '?project_id=' + this.value" class="w-full md:w-72 bg-slate-950 border border-slate-700 text-white font-bold text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-inner">
                    <option value="" {{ empty($selectedProjectId) ? 'selected' : '' }}>-- Semua Proyek (Konsolidasi Kas) --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>
                            {{ $p->code }} - {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- 2. KARTU METRIK KPI KEUANGAN (4 KARTU METRIK UTAMA) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Total Pemasukan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-arrow-down-left"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pemasukan</span>
                    @if($selectedProject && $selectedProject->budget_target_revenue > 0)
                        <span class="text-[10px] font-black px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">
                            {{ $revenueRealization }}%
                        </span>
                    @endif
                </div>
                <div class="text-xl font-black text-slate-900 mt-0.5 truncate">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-slate-400 mt-1 truncate">
                    @if($selectedProject && $selectedProject->budget_target_revenue > 0)
                        Target: Rp {{ number_format($selectedProject->budget_target_revenue, 0, ',', '.') }}
                    @else
                        Akumulasi kas masuk
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI 2: Total Pengeluaran -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-arrow-up-right"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pengeluaran</span>
                    @if($selectedProject && $selectedProject->budget_limit_expense > 0)
                        <span class="text-[10px] font-black px-1.5 py-0.2 rounded bg-rose-100 text-rose-800">
                            {{ $expenseAbsorption }}% Serapan
                        </span>
                    @endif
                </div>
                <div class="text-xl font-black text-rose-600 mt-0.5 truncate">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-slate-400 mt-1 truncate">
                    @if($selectedProject && $selectedProject->budget_limit_expense > 0)
                        Pagu Maks: Rp {{ number_format($selectedProject->budget_limit_expense, 0, ',', '.') }}
                    @else
                        Akumulasi belanja beban
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI 3: Surplus / Defisit Bersih -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl {{ $netSurplus >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid {{ $netSurplus >= 0 ? 'fa-scale-balanced' : 'fa-circle-exclamation' }}"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Surplus / (Defisit)</span>
                <div class="text-xl font-black {{ $netSurplus >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-0.5 truncate">
                    {{ $netSurplus >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($netSurplus), 0, ',', '.') }}
                </div>
                <div class="text-[10px] {{ $netSurplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }} font-bold mt-1">
                    {{ $netSurplus >= 0 ? 'Kondisi Kas Sehat' : 'Defisit Anggaran Terlampaui' }}
                </div>
            </div>
        </div>

        <!-- KPI 4: Kas Tersedia di Tangan Panitia / Kasir -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kas & Bank Tersedia</span>
                <div class="text-xl font-black text-slate-900 mt-0.5 truncate">
                    Rp {{ number_format($availableCash, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-slate-400 mt-1 truncate">
                    Saldo likuid siap belanja
                </div>
            </div>
        </div>
    </div>

    <!-- 3. KONTEN DUA KOLOM: REALISASI ANGGARAN & HISTORI TRANSAKSI -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kolom Kiri (7 Cols): Realisasi Anggaran Laba Rugi Proyek -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-rose-600"></i> 
                        {{ $selectedProject ? 'Laporan Realisasi Anggaran: ' . $selectedProject->code : 'Ringkasan Laba Rugi Berjalan' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Rincian pendapatan dan belanja per mata anggaran akun CoA.</p>
                </div>
                <a href="{{ route('admin.accounting.reports', ['project_id' => $selectedProjectId]) }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                    Laporan Penuh <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Rincian Pemasukan -->
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs font-extrabold uppercase text-emerald-800 bg-emerald-50 px-3.5 py-2 rounded-xl border border-emerald-200">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-plus-circle"></i> 4 - Pendapatan / Pemasukan</span>
                    <span class="font-mono">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="space-y-2 pl-2">
                    @forelse($revenueBreakdown as $rb)
                        <div class="flex items-center justify-between text-xs py-1 px-2 hover:bg-slate-50 rounded-lg">
                            <span class="text-slate-700">
                                <strong class="font-mono text-slate-500 text-[11px]">{{ $rb->account?->code }}</strong> &bull; {{ $rb->account?->name }}
                            </span>
                            <span class="font-mono font-bold text-slate-800">Rp {{ number_format($rb->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 italic py-2 text-center">Belum ada pos pendapatan yang dibukukan.</div>
                    @endforelse
                </div>
            </div>

            <!-- Rincian Pengeluaran -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between text-xs font-extrabold uppercase text-rose-800 bg-rose-50 px-3.5 py-2 rounded-xl border border-rose-200">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-minus-circle"></i> 5 - Beban / Pengeluaran</span>
                    <span class="font-mono">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                </div>
                <div class="space-y-2 pl-2">
                    @forelse($expenseBreakdown as $eb)
                        <div class="flex items-center justify-between text-xs py-1 px-2 hover:bg-slate-50 rounded-lg">
                            <span class="text-slate-700">
                                <strong class="font-mono text-slate-500 text-[11px]">{{ $eb->account?->code }}</strong> &bull; {{ $eb->account?->name }}
                            </span>
                            <span class="font-mono font-bold text-rose-600">Rp {{ number_format($eb->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 italic py-2 text-center">Belum ada pos belanja yang dibukukan.</div>
                    @endforelse
                </div>
            </div>

            <!-- Ringkasan Bersih Footer -->
            <div class="pt-4 border-t-2 border-slate-200 flex items-center justify-between font-black text-sm {{ $netSurplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }} bg-slate-50 p-4 rounded-2xl">
                <span>SURPLUS / (DEFISIT) BERSIH:</span>
                <span class="font-mono text-base">
                    {{ $netSurplus >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($netSurplus), 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Kolom Kanan (5 Cols): Histori Transaksi Voucher Kas Terbaru -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-amber-500"></i> Transaksi Voucher Terbaru
                    </h3>
                    <a href="{{ route('admin.accounting.transactions', ['project_id' => $selectedProjectId]) }}" class="text-xs font-bold text-slate-600 hover:text-slate-800">
                        Lihat Semua
                    </a>
                </div>

                <div class="space-y-3 overflow-y-auto max-h-[460px] pr-1">
                    @forelse($recentTransactions as $tx)
                        <div class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-slate-300 bg-white hover:bg-slate-50/70 transition flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-[10px] px-2 py-0.5 rounded-md {{ $tx->type_badge_class }}">
                                        {{ $tx->voucher_number }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        {{ $tx->transaction_date->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="font-bold text-slate-900 text-xs mt-1 truncate">
                                    {{ $tx->description }}
                                </div>
                                <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                    <i class="fa-solid fa-user-tag text-slate-400 mr-1 text-[10px]"></i> {{ $tx->recipient_or_payer ?: '-' }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-mono font-black text-xs {{ $tx->type === 'cash_in' ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $tx->type === 'cash_in' ? '+' : '-' }}{{ $tx->formatted_amount }}
                                </div>
                                @if($tx->attachment_path)
                                    <a href="{{ asset('storage/' . $tx->attachment_path) }}" target="_blank" class="text-[10px] text-blue-600 hover:underline inline-flex items-center gap-0.5 mt-1">
                                        <i class="fa-solid fa-paperclip"></i> Bukti Nota
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-folder-open text-3xl text-slate-300 mb-2"></i>
                            <p class="font-bold text-slate-600">Belum Ada Transaksi</p>
                            <p class="text-xs text-slate-400 mt-0.5">Klik tombol di atas untuk mencatat Kas Masuk (BKM) atau Kas Keluar (BKK).</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-4 text-center">
                <span class="text-[11px] text-slate-400">
                    Sistem Pembukuan Berpasangan (Double Entry) &bull; Accurate Standard
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 1: INPUT BUKTI KAS MASUK (BKM)                   -->
<!-- ======================================================= -->
<div id="modalCashIn" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-circle-arrow-down text-emerald-600"></i> Catat Bukti Kas Masuk (BKM - Receive Money)
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Mencatat penerimaan uang tunai, pendaftaran, iuran, atau transfer masuk.</p>
            </div>
            <button type="button" onclick="closeModal('modalCashIn')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.accounting.cash-in.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Proyek Terkait</label>
                    <select name="accounting_project_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Non-Proyek (Kas Rutin BPH) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Akun Kas Masuk (Debit) & Akun Pendapatan (Kredit) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Masuk ke Kas/Bank (Debit) <span class="text-rose-500">*</span></label>
                    <select name="debit_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach($cashBankAccounts as $cb)
                            <option value="{{ $cb->id }}">{{ $cb->code }} - {{ $cb->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Sumber Pendapatan (Kredit) <span class="text-rose-500">*</span></label>
                    <select name="credit_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach($revenueAccounts as $ra)
                            <option value="{{ $ra->id }}">{{ $ra->code }} - {{ $ra->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nominal & Penyetor -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Jumlah Nominal (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-xs text-slate-400">Rp</span>
                        <input type="number" min="1" step="1000" name="amount" required placeholder="0" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Penyetor / Sumber Dana</label>
                    <input type="text" name="recipient_or_payer" placeholder="Contoh: SMAN 1 Bogor / Sponsor" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Keterangan Transaksi -->
            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Keterangan / Memo <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="2" required placeholder="Contoh: Pembayaran pendaftaran 3 regu lomba tandu & dds kontingen SMAN 1 Bogor..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <!-- No. Referensi & Upload Lampiran Bukti -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. Bukti / Ref Eksternal</label>
                    <input type="text" name="reference_number" placeholder="No. kuitansi / ref bank..." class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Lampiran Foto Bukti / Struk</label>
                    <input type="file" name="attachment" accept="image/*,.pdf" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-slate-600 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalCashIn')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan & Posting Kas Masuk
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 2: INPUT BUKTI KAS KELUAR (BKK)                  -->
<!-- ======================================================= -->
<div id="modalCashOut" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-circle-arrow-up text-rose-600"></i> Catat Bukti Kas Keluar (BKK - Pay Money)
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Mencatat pengeluaran belanja barang, piala, konsumsi, dan operasional proyek.</p>
            </div>
            <button type="button" onclick="closeModal('modalCashOut')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.accounting.cash-out.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Proyek Terkait</label>
                    <select name="accounting_project_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="">-- Non-Proyek (Operasional Umum BPH) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Akun Kas Sumber (Kredit) & Akun Beban (Debit) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Bayar Dari Kas/Bank (Kredit) <span class="text-rose-500">*</span></label>
                    <select name="credit_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        @foreach($cashBankAccounts as $cb)
                            <option value="{{ $cb->id }}">{{ $cb->code }} - {{ $cb->name }} (Saldo: {{ $cb->formatted_balance }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Alokasi Akun Beban (Debit) <span class="text-rose-500">*</span></label>
                    <select name="debit_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        @foreach($expenseAccounts as $ea)
                            <option value="{{ $ea->id }}">{{ $ea->code }} - {{ $ea->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nominal & Penerima / Vendor Toko -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Jumlah Nominal (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-xs text-slate-400">Rp</span>
                        <input type="number" min="1" step="1000" name="amount" required placeholder="0" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Penerima Dana / Vendor Toko</label>
                    <input type="text" name="recipient_or_payer" placeholder="Contoh: Toko Piala Citra Trophy Bogor" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <!-- Keterangan Transaksi Belanja -->
            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Keterangan / Keperluan Belanja <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="2" required placeholder="Contoh: Pembayaran pelunasan 15 set piala kejuaraan dan medali bergilir SBB IV..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <!-- No. Nota & Upload Foto Nota Asli -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. Faktur / Nota Toko</label>
                    <input type="text" name="reference_number" placeholder="No. nota/struk toko..." class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Unggah Foto Nota / Kuitansi</label>
                    <input type="file" name="attachment" accept="image/*,.pdf" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-slate-600 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalCashOut')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan & Posting Kas Keluar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 3: TRANSFER KAS ANTAR REKENING (TRF)             -->
<!-- ======================================================= -->
<div id="modalTransfer" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-right-left text-blue-600"></i> Transfer Dana Antar Kas & Bank
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Pemindahan kas internal (tarik tunai bank / setor kasir).</p>
            </div>
            <button type="button" onclick="closeModal('modalTransfer')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.accounting.transfer.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Transfer <span class="text-rose-500">*</span></label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Dari Akun Kas/Bank (Sumber) <span class="text-rose-500">*</span></label>
                <select name="source_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($cashBankAccounts as $cb)
                        <option value="{{ $cb->id }}">{{ $cb->code }} - {{ $cb->name }} (Saldo: {{ $cb->formatted_balance }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Ke Akun Kas/Bank (Tujuan) <span class="text-rose-500">*</span></label>
                <select name="destination_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($cashBankAccounts as $cb)
                        <option value="{{ $cb->id }}" {{ $loop->iteration == 2 ? 'selected' : '' }}>{{ $cb->code }} - {{ $cb->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Nominal Transfer (Rp) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-xs text-slate-400">Rp</span>
                    <input type="number" min="1" step="1000" name="amount" required placeholder="0" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Keterangan Transfer <span class="text-rose-500">*</span></label>
                <input type="text" name="description" required placeholder="Contoh: Tarik tunai dari Bank BJB untuk modal kas kecil panitia..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalTransfer')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Proses Transfer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
