@extends('layouts.admin')

@section('title', 'Transaksi Kas & Jurnal - Akuntansi PMR')
@section('page_title', 'Transaksi Kas & Jurnal')

@section('top_actions')
    <a href="{{ route('admin.accounting.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 border border-slate-200">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
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

    <!-- 1. FILTER TRANSAKSI -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs">
        <form action="{{ route('admin.accounting.transactions') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Transaksi:</label>
                <select name="type" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <option value="">Semua Jenis</option>
                    <option value="cash_in" {{ request('type') === 'cash_in' ? 'selected' : '' }}>Kas Masuk (BKM)</option>
                    <option value="cash_out" {{ request('type') === 'cash_out' ? 'selected' : '' }}>Kas Keluar (BKK)</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer Kas (TRF)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Proyek Kegiatan:</label>
                <select name="project_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <option value="">Semua Proyek & Kas</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->code }} - {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal:</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal:</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.accounting.transactions') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition flex items-center justify-center cursor-pointer" title="Reset Filter">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- 2. TABEL DAFTAR TRANSAKSI -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="font-black text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-amber-500"></i> Riwayat Voucher Transaksi Kas
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi jurnal penerimaan, pengeluaran, dan transfer dana internal.</p>
            </div>
            <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1.5 rounded-xl border border-slate-200">
                Menampilkan {{ $transactions->total() }} Transaksi
            </span>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No. Voucher</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Proyek Terkait</th>
                        <th class="py-3 px-4">Deskripsi & Pihak Terkait</th>
                        <th class="py-3 px-4">Detail Akun (Debit / Kredit)</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Bukti</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- No. Voucher -->
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold text-[11px] px-2 py-0.5 rounded-md border {{ $tx->type_badge_class }}">
                                    {{ $tx->voucher_number }}
                                </span>
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-600 whitespace-nowrap">
                                {{ $tx->transaction_date->format('d/m/Y') }}
                            </td>

                            <!-- Proyek -->
                            <td class="py-3 px-4">
                                @if($tx->project)
                                    <span class="font-mono text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded">
                                        {{ $tx->project->code }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Kas Rutin BPH</span>
                                @endif
                            </td>

                            <!-- Deskripsi & Pihak -->
                            <td class="py-3 px-4 max-w-xs">
                                <div class="font-bold text-slate-900 text-xs">{{ $tx->description }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                                    <i class="fa-solid fa-user-tag text-[9px]"></i> {{ $tx->recipient_or_payer ?: '-' }}
                                    @if($tx->reference_number)
                                        &bull; Ref: <span class="font-mono">{{ $tx->reference_number }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Detail Akun Jurnal -->
                            <td class="py-3 px-4 text-[11px]">
                                <div class="space-y-0.5">
                                    @foreach($tx->lines as $line)
                                        <div class="flex items-center gap-1">
                                            <span class="font-mono text-[9px] font-bold px-1 rounded {{ $line->debit > 0 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $line->debit > 0 ? 'D' : 'K' }}
                                            </span>
                                            <span class="truncate max-w-[170px] text-slate-700">
                                                {{ $line->account?->code }} - {{ $line->account?->name }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Nominal -->
                            <td class="py-3 px-4 text-right">
                                <span class="font-mono font-black text-xs {{ $tx->type === 'cash_in' ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $tx->type === 'cash_in' ? '+' : '-' }}{{ $tx->formatted_amount }}
                                </span>
                            </td>

                            <!-- Bukti Lampiran -->
                            <td class="py-3 px-4 text-center">
                                @if($tx->attachment_path)
                                    <a href="{{ asset('storage/' . $tx->attachment_path) }}" target="_blank" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition inline-flex items-center" title="Lihat Foto Bukti">
                                        <i class="fa-solid fa-paperclip"></i>
                                    </a>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('admin.accounting.transactions.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher {{ $tx->voucher_number }} ini dari jurnal?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition cursor-pointer" title="Hapus Transaksi">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-2"></i>
                                <p class="font-bold text-slate-600">Tidak ada transaksi ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<!-- INCLUDE MODALS (BKM, BKK, TRF) -->
<!-- Modal 1: Kas Masuk -->
<div id="modalCashIn" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-circle-arrow-down text-emerald-600"></i> Catat Bukti Kas Masuk (BKM)
                </h4>
            </div>
            <button type="button" onclick="closeModal('modalCashIn')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.accounting.cash-in.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Proyek Terkait</label>
                    <select name="accounting_project_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="">-- Non-Proyek (Kas BPH) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kas/Bank (Debit) <span class="text-rose-500">*</span></label>
                    <select name="debit_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        @foreach($cashBankAccounts as $cb)
                            <option value="{{ $cb->id }}">{{ $cb->code }} - {{ $cb->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Pendapatan (Kredit) <span class="text-rose-500">*</span></label>
                    <select name="credit_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        @foreach($revenueAccounts as $ra)
                            <option value="{{ $ra->id }}">{{ $ra->code }} - {{ $ra->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="1" step="1" name="amount" required placeholder="0" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Penyetor</label>
                    <input type="text" name="recipient_or_payer" placeholder="Nama penyetor..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Keterangan <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="2" required placeholder="Uraian penerimaan uang..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. Ref Eksternal</label>
                    <input type="text" name="reference_number" placeholder="No. struk/kuitansi..." class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Foto Bukti</label>
                    <input type="file" name="attachment" accept="image/*,.pdf" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-slate-600 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalCashIn')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan BKM
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Kas Keluar -->
<div id="modalCashOut" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-circle-arrow-up text-rose-600"></i> Catat Bukti Kas Keluar (BKK)
                </h4>
            </div>
            <button type="button" onclick="closeModal('modalCashOut')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.accounting.cash-out.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Proyek Terkait</label>
                    <select name="accounting_project_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="">-- Non-Proyek (Kas BPH) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Bayar Dari Kas/Bank (Kredit) <span class="text-rose-500">*</span></label>
                    <select name="credit_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        @foreach($cashBankAccounts as $cb)
                            <option value="{{ $cb->id }}">{{ $cb->code }} - {{ $cb->name }} (Saldo: {{ $cb->formatted_balance }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Akun Beban (Debit) <span class="text-rose-500">*</span></label>
                    <select name="debit_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        @foreach($expenseAccounts as $ea)
                            <option value="{{ $ea->id }}">{{ $ea->code }} - {{ $ea->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="1" step="1" name="amount" required placeholder="0" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Penerima / Vendor Toko</label>
                    <input type="text" name="recipient_or_payer" placeholder="Nama toko/penerima..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Keperluan Belanja <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="2" required placeholder="Uraian belanja barang..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. Nota / Faktur</label>
                    <input type="text" name="reference_number" placeholder="No. nota toko..." class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Unggah Foto Nota Asli</label>
                    <input type="file" name="attachment" accept="image/*,.pdf" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-slate-600 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalCashOut')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan BKK
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Transfer Kas -->
<div id="modalTransfer" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-right-left text-blue-600"></i> Transfer Dana Antar Kas & Bank
                </h4>
            </div>
            <button type="button" onclick="closeModal('modalTransfer')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.accounting.transfer.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Transfer <span class="text-rose-500">*</span></label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Dari Akun (Sumber) <span class="text-rose-500">*</span></label>
                <select name="source_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                    @foreach($cashBankAccounts as $cb)
                        <option value="{{ $cb->id }}">{{ $cb->code }} - {{ $cb->name }} (Saldo: {{ $cb->formatted_balance }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Ke Akun (Tujuan) <span class="text-rose-500">*</span></label>
                <select name="destination_account_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                    @foreach($cashBankAccounts as $cb)
                        <option value="{{ $cb->id }}" {{ $loop->iteration == 2 ? 'selected' : '' }}>{{ $cb->code }} - {{ $cb->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" min="1" step="1" name="amount" required placeholder="0" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Keterangan <span class="text-rose-500">*</span></label>
                <input type="text" name="description" required placeholder="Contoh: Tarik tunai..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
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
