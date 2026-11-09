@extends('layouts.admin')

@section('title', 'Bagan Akun (Chart of Accounts) - Akuntansi PMR')
@section('page_title', 'Bagan Akun (Chart of Accounts)')

@section('top_actions')
    <a href="{{ route('admin.accounting.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 border border-slate-200">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <button type="button" onclick="openModal('modalCreateAccount')" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-plus-circle"></i> + Tambah Akun Baru (CoA)
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- 1. TAB FILTER GOLONGAN AKUN -->
    <div class="bg-white rounded-2xl p-2 border border-slate-200 shadow-xs flex items-center gap-1.5 overflow-x-auto">
        <a href="?type=all" class="px-4 py-2 rounded-xl text-xs font-extrabold transition whitespace-nowrap {{ $filterType === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Akun ({{ $countAll }})
        </a>
        <a href="?type=asset" class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $filterType === 'asset' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            1 - Aset / Harta ({{ $countAssets }})
        </a>
        <a href="?type=liability" class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $filterType === 'liability' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            2 - Kewajiban ({{ $countLiabilities }})
        </a>
        <a href="?type=equity" class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $filterType === 'equity' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            3 - Ekuitas ({{ $countEquity }})
        </a>
        <a href="?type=revenue" class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $filterType === 'revenue' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            4 - Pendapatan ({{ $countRevenue }})
        </a>
        <a href="?type=expense" class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $filterType === 'expense' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            5 - Beban ({{ $countExpense }})
        </a>
    </div>

    <!-- 2. TABEL BAGAN AKUN -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="font-black text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-list-ol text-rose-600"></i> Master Bagan Akun Standar Organisasi
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar akun buku besar (General Ledger Accounts) untuk klasifikasi seluruh transaksi keuangan unit.</p>
            </div>
            <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1.5 rounded-xl border border-slate-200">
                Total {{ $accounts->count() }} Akun Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Kode Akun</th>
                        <th class="py-3 px-4">Nama Akun</th>
                        <th class="py-3 px-4">Golongan / Tipe</th>
                        <th class="py-3 px-4">Kategori Akun</th>
                        <th class="py-3 px-4 text-right">Saldo Berjalan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($accounts as $acc)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Kode Akun -->
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold text-xs bg-slate-100 px-2 py-0.5 rounded border border-slate-200 text-slate-800">
                                    {{ $acc->code }}
                                </span>
                            </td>

                            <!-- Nama Akun -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $acc->name }}</div>
                                @if($acc->description)
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $acc->description }}</div>
                                @endif
                            </td>

                            <!-- Tipe Akun -->
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $acc->type_badge_class }}">
                                    {{ $acc->type_label }}
                                </span>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                {{ $acc->category_label }}
                            </td>

                            <!-- Saldo Berjalan -->
                            <td class="py-3 px-4 text-right">
                                <span class="font-mono font-black text-xs {{ $acc->current_balance < 0 ? 'text-rose-600' : 'text-slate-900' }}">
                                    {{ $acc->formatted_balance }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 text-center">
                                @if($acc->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="editAccount({{ json_encode($acc) }})" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition cursor-pointer" title="Edit Akun">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('admin.accounting.accounts.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $acc->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition cursor-pointer" title="Hapus Akun">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <i class="fa-solid fa-list-ol text-3xl text-slate-300 mb-2"></i>
                                <p class="font-bold text-slate-600">Tidak ada akun pada kategori ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH AKUN BARU -->
<div id="modalCreateAccount" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-rose-600"></i> Tambah Akun Baru (CoA)
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Buat kode akun baru sesuai hierarki akuntansi.</p>
            </div>
            <button type="button" onclick="closeModal('modalCreateAccount')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.accounting.accounts.store') }}" method="POST" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kode Akun <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Contoh: 1-1040" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tipe / Golongan <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="asset">1 - Aset / Harta</option>
                        <option value="liability">2 - Kewajiban / Utang</option>
                        <option value="equity">3 - Ekuitas / Modal</option>
                        <option value="revenue">4 - Pendapatan / Pemasukan</option>
                        <option value="expense">5 - Beban / Pengeluaran</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Nama Akun <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Kas Tabungan Kegiatan Wisnic" class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kategori Akun <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="cash_and_bank">Kas & Bank</option>
                        <option value="current_asset">Aset Lancar Lainnya</option>
                        <option value="fixed_asset">Aset Tetap (Peralatan)</option>
                        <option value="current_liability">Utang Lancar</option>
                        <option value="equity">Modal Organisasi</option>
                        <option value="operational_revenue">Pendapatan Rutin Operasional</option>
                        <option value="project_revenue">Pendapatan Proyek / Lomba</option>
                        <option value="grant_revenue">Bantuan & Sponsorship</option>
                        <option value="operational_expense">Beban Operasional Markas</option>
                        <option value="project_expense">Beban Proyek / Lomba</option>
                        <option value="admin_expense">Beban Kesekretariatan & ATK</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Akun Induk (Opsional)</label>
                    <select name="parent_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="">-- Tanpa Induk (Akun Utama) --</option>
                        @foreach($parentAccounts as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Keterangan / Deskripsi</label>
                <textarea name="description" rows="2" placeholder="Catatan peruntukan akun..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Aktifkan akun ini segera</span>
                </label>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalCreateAccount')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT AKUN -->
<div id="modalEditAccount" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-rose-600"></i> Edit Data Akun (CoA)
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui nama atau penggolongan akun.</p>
            </div>
            <button type="button" onclick="closeModal('modalEditAccount')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form id="formEditAccount" action="" method="POST" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kode Akun <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="edit_acc_code" required class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tipe / Golongan <span class="text-rose-500">*</span></label>
                    <select name="type" id="edit_acc_type" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="asset">1 - Aset / Harta</option>
                        <option value="liability">2 - Kewajiban / Utang</option>
                        <option value="equity">3 - Ekuitas / Modal</option>
                        <option value="revenue">4 - Pendapatan / Pemasukan</option>
                        <option value="expense">5 - Beban / Pengeluaran</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Nama Akun <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_acc_name" required class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kategori Akun <span class="text-rose-500">*</span></label>
                    <select name="category" id="edit_acc_category" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="cash_and_bank">Kas & Bank</option>
                        <option value="current_asset">Aset Lancar Lainnya</option>
                        <option value="fixed_asset">Aset Tetap (Peralatan)</option>
                        <option value="current_liability">Utang Lancar</option>
                        <option value="equity">Modal Organisasi</option>
                        <option value="operational_revenue">Pendapatan Rutin Operasional</option>
                        <option value="project_revenue">Pendapatan Proyek / Lomba</option>
                        <option value="grant_revenue">Bantuan & Sponsorship</option>
                        <option value="operational_expense">Beban Operasional Markas</option>
                        <option value="project_expense">Beban Proyek / Lomba</option>
                        <option value="admin_expense">Beban Kesekretariatan & ATK</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Akun Induk (Opsional)</label>
                    <select name="parent_id" id="edit_acc_parent_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="">-- Tanpa Induk (Akun Utama) --</option>
                        @foreach($parentAccounts as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Keterangan / Deskripsi</label>
                <textarea name="description" id="edit_acc_description" rows="2" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                    <input type="checkbox" name="is_active" id="edit_acc_is_active" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Akun aktif</span>
                </label>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalEditAccount')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui Akun
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
    function editAccount(acc) {
        document.getElementById('edit_acc_code').value = acc.code || '';
        document.getElementById('edit_acc_name').value = acc.name || '';
        document.getElementById('edit_acc_type').value = acc.type || 'asset';
        document.getElementById('edit_acc_category').value = acc.category || 'cash_and_bank';
        document.getElementById('edit_acc_parent_id').value = acc.parent_id || '';
        document.getElementById('edit_acc_description').value = acc.description || '';
        document.getElementById('edit_acc_is_active').checked = Boolean(acc.is_active);
        document.getElementById('formEditAccount').action = `/admin/accounting/accounts/${acc.id}`;
        openModal('modalEditAccount');
    }
</script>
@endsection
