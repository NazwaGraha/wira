@extends('layouts.admin')

@section('title', 'Master Proyek Kegiatan (Cost Centers) - Akuntansi PMR')
@section('page_title', 'Master Proyek Kegiatan')

@section('top_actions')
    <a href="{{ route('admin.accounting.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 border border-slate-200">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <button type="button" onclick="openModal('modalCreateProject')" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-plus-circle"></i> + Buat Proyek Baru
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Box -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-diagram-project text-purple-600"></i> Manajemen Proyek Kegiatan (Cost Centers)
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola pos kegiatan kepanitiaan lomba, diklatdas, atau operasional umum lengkap dengan target dan pagu belanja.</p>
            </div>
            <button type="button" onclick="openModal('modalCreateProject')" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-2 shadow-xs shrink-0 cursor-pointer">
                <i class="fa-solid fa-plus text-rose-400"></i> Buat Proyek Baru
            </button>
        </div>
    </div>

    <!-- Grid Kartu Proyek -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $p)
            <div class="bg-white rounded-3xl p-6 border border-slate-200 hover:border-slate-300 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <!-- Badge Kategori & Status -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="font-mono font-black text-[10px] uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">
                            {{ $p->code }}
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $p->status_badge_class }}">
                            {{ $p->status_label }}
                        </span>
                    </div>

                    <h4 class="font-black text-base text-slate-900 group-hover:text-rose-600 transition">
                        {{ $p->name }}
                    </h4>

                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                        {{ $p->description ?: 'Kegiatan resmi organisasi PMR Wira SMAN 1 Ciawi.' }}
                    </p>

                    <!-- PIC & Bendahara -->
                    <div class="mt-4 p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-1 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400 text-[11px]">Ketua / PIC:</span>
                            <span class="font-bold text-slate-800">{{ $p->person_in_charge ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400 text-[11px]">Bendahara:</span>
                            <span class="font-bold text-slate-800">{{ $p->treasurer_name ?: '-' }}</span>
                        </div>
                    </div>

                    <!-- Realisasi Keuangan Proyek -->
                    <div class="mt-4 space-y-2.5">
                        <!-- Pemasukan -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-700 flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-down text-emerald-500 text-[10px]"></i> Pemasukan
                                </span>
                                <span class="font-mono font-bold text-slate-900 text-[11px]">
                                    {{ $p->formatted_revenue }} <span class="text-slate-400 font-normal">/ Rp {{ number_format($p->budget_target_revenue, 0, ',', '.') }}</span>
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-300" style="width: {{ min(100, $p->revenue_realization_percent) }}%"></div>
                            </div>
                        </div>

                        <!-- Pengeluaran -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-700 flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-up text-rose-500 text-[10px]"></i> Pengeluaran
                                </span>
                                <span class="font-mono font-bold text-rose-600 text-[11px]">
                                    {{ $p->formatted_expense }} <span class="text-slate-400 font-normal">/ Rp {{ number_format($p->budget_limit_expense, 0, ',', '.') }}</span>
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-rose-500 h-full rounded-full transition-all duration-300" style="width: {{ min(100, $p->expense_absorption_percent) }}%"></div>
                            </div>
                        </div>

                        <!-- Net Surplus Proyek -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-500">Surplus / (Defisit):</span>
                            <span class="font-mono font-black text-xs {{ $p->net_surplus >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $p->formatted_net_surplus }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Aksi -->
                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.accounting.index', ['project_id' => $p->id]) }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                        Buka Dashboard <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.accounting.projects.lpj-pdf', $p->id) }}" target="_blank" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition" title="Cetak LPJ Proyek PDF">
                            <i class="fa-solid fa-file-pdf text-rose-500"></i>
                        </a>
                        <button type="button" onclick="editProject({{ json_encode($p) }})" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition cursor-pointer" title="Edit Proyek">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="{{ route('admin.accounting.projects.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition cursor-pointer" title="Hapus Proyek">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-3xl border border-slate-200">
                <i class="fa-solid fa-diagram-project text-4xl mb-3 text-slate-300"></i>
                <p class="font-bold text-slate-600">Belum Ada Proyek Kegiatan</p>
                <p class="text-xs text-slate-400 mt-1">Klik tombol <strong>"Buat Proyek Baru"</strong> untuk menambahkan pos kegiatan anggaran.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</div>

<!-- MODAL TAMBAH PROYEK -->
<div id="modalCreateProject" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-rose-600"></i> Buat Proyek Kegiatan Baru
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan kode, nama kegiatan, dan pagu anggaran.</p>
            </div>
            <button type="button" onclick="closeModal('modalCreateProject')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.accounting.projects.store') }}" method="POST" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kode Proyek <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Contoh: PRJ-2026-DIKLAT" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kategori Kegiatan <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="competition">Perlombaan (Event SBB)</option>
                        <option value="diklat">Diklat & Pelantikan</option>
                        <option value="social_service">Aksi Sosial & Donor Darah</option>
                        <option value="routine_operational">Operasional Rutin BPH</option>
                        <option value="other">Kegiatan Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Nama Proyek Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Pendidikan & Latihan Dasar (Diklatdas) 2026" class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Target Pemasukan (Rp)</label>
                    <input type="number" min="0" step="50000" name="budget_target_revenue" value="0" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Pagu Maksimal Belanja (Rp)</label>
                    <input type="number" min="0" step="50000" name="budget_limit_expense" value="0" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Ketua Pelaksana / PIC</label>
                    <input type="text" name="person_in_charge" placeholder="Nama ketua pelaksana" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Bendahara Proyek</label>
                    <input type="text" name="treasurer_name" placeholder="Nama bendahara" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Status Proyek</label>
                    <select name="status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="planning">Perencanaan</option>
                        <option value="active" selected>Aktif / Berjalan</option>
                        <option value="completed">Selesai (LPJ Terbit)</option>
                        <option value="archived">Diarsipkan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Deskripsi Ringkas</label>
                    <textarea name="description" rows="1" placeholder="Catatan kegiatan..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalCreateProject')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Proyek
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PROYEK -->
<div id="modalEditProject" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-rose-600"></i> Edit Data Proyek Kegiatan
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui nama, status, atau pagu anggaran kegiatan.</p>
            </div>
            <button type="button" onclick="closeModal('modalEditProject')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form id="formEditProject" action="" method="POST" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kode Proyek <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="edit_prj_code" required class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kategori Kegiatan <span class="text-rose-500">*</span></label>
                    <select name="category" id="edit_prj_category" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="competition">Perlombaan (Event SBB)</option>
                        <option value="diklat">Diklat & Pelantikan</option>
                        <option value="social_service">Aksi Sosial & Donor Darah</option>
                        <option value="routine_operational">Operasional Rutin BPH</option>
                        <option value="other">Kegiatan Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Nama Proyek Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_prj_name" required class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Target Pemasukan (Rp)</label>
                    <input type="number" min="0" step="50000" name="budget_target_revenue" id="edit_prj_target" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Pagu Maksimal Belanja (Rp)</label>
                    <input type="number" min="0" step="50000" name="budget_limit_expense" id="edit_prj_limit" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="edit_prj_start" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="edit_prj_end" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Ketua Pelaksana / PIC</label>
                    <input type="text" name="person_in_charge" id="edit_prj_pic" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Bendahara Proyek</label>
                    <input type="text" name="treasurer_name" id="edit_prj_treasurer" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Status Proyek</label>
                    <select name="status" id="edit_prj_status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="planning">Perencanaan</option>
                        <option value="active">Aktif / Berjalan</option>
                        <option value="completed">Selesai (LPJ Terbit)</option>
                        <option value="archived">Diarsipkan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Deskripsi Ringkas</label>
                    <textarea name="description" id="edit_prj_description" rows="1" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalEditProject')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui Proyek
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
    function editProject(prj) {
        document.getElementById('edit_prj_code').value = prj.code || '';
        document.getElementById('edit_prj_name').value = prj.name || '';
        document.getElementById('edit_prj_category').value = prj.category || 'competition';
        document.getElementById('edit_prj_status').value = prj.status || 'active';
        document.getElementById('edit_prj_target').value = prj.budget_target_revenue || 0;
        document.getElementById('edit_prj_limit').value = prj.budget_limit_expense || 0;
        document.getElementById('edit_prj_start').value = prj.start_date ? prj.start_date.substring(0, 10) : '';
        document.getElementById('edit_prj_end').value = prj.end_date ? prj.end_date.substring(0, 10) : '';
        document.getElementById('edit_prj_pic').value = prj.person_in_charge || '';
        document.getElementById('edit_prj_treasurer').value = prj.treasurer_name || '';
        document.getElementById('edit_prj_description').value = prj.description || '';
        document.getElementById('formEditProject').action = `/admin/accounting/projects/${prj.id}`;
        openModal('modalEditProject');
    }
</script>
@endsection
