@extends('layouts.admin')

@section('title', 'Daftar Anggota')
@section('page_title', 'Daftar Biodata Anggota PMR')

@section('top_actions')
    <div class="flex flex-wrap items-center gap-2">
        <!-- Download Template Excel -->
        <a href="{{ route('admin.members.template') }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-3.5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-file-excel text-emerald-600"></i>
            <span>Template Excel</span>
        </a>

        <!-- Export Excel -->
        <a href="{{ route('admin.members.export', request()->only('status')) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-emerald-950/10 transition flex items-center gap-1.5">
            <i class="fa-solid fa-file-export"></i>
            <span>Export Excel</span>
        </a>

        <!-- Tombol Buka Modal Import Excel -->
        <button type="button" onclick="openImportModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-indigo-950/10 transition flex items-center gap-1.5">
            <i class="fa-solid fa-file-import"></i>
            <span>Import Excel</span>
        </button>

        <!-- Input Data Baru -->
        <a href="{{ route('admin.members.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-red-950/20 transition flex items-center gap-1.5">
            <i class="fa-solid fa-plus"></i>
            <span>Input Baru</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl shrink-0"></i>
            <div class="text-sm font-semibold">{!! session('success') !!}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl shrink-0"></i>
            <div class="text-sm font-semibold">{!! session('error') !!}</div>
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-6">
        <!-- Stat Badges & Filter Bar -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.members.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition {{ !$status ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <span>Semua</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-white/20">{{ $totalMembers }}</span>
                </a>
                <a href="{{ route('admin.members.index', ['status' => 'Aktif']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'Aktif' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                    <span>Aktif</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-white/20">{{ $totalAktif }}</span>
                </a>
                <a href="{{ route('admin.members.index', ['status' => 'Purna']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'Purna' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200' }}">
                    <i class="fa-solid fa-graduation-cap text-[10px]"></i>
                    <span>Purna</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-white/20">{{ $totalPurna }}</span>
                </a>
            </div>

            <form action="{{ route('admin.members.index') }}" method="GET" class="w-full md:w-auto flex flex-wrap gap-2">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="relative w-full md:w-64">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari Nama, NIS, Tahun..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-pmr-primary focus:ring-1 focus:ring-pmr-primary">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-bold transition">
                    Cari
                </button>
                @if($search || $status)
                    <a href="{{ route('admin.members.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl text-sm font-bold transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-bold border-y border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Foto</th>
                        <th class="px-6 py-4">Nama Lengkap & NIS</th>
                        <th class="px-6 py-4">Kelas / Tingkat</th>
                        <th class="px-6 py-4">Status & Tahun Masuk</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($members as $member)
                        @php
                            $effStatus = $member->effective_status;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                @if($member->photo)
                                    <img src="{{ Storage::url($member->photo) }}" class="w-12 h-16 object-cover rounded-lg border border-slate-200 shadow-sm" alt="Foto">
                                @else
                                    <div class="w-12 h-16 rounded-lg border border-slate-200 bg-slate-100 flex items-center justify-center text-slate-400 text-xl">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 text-base">{{ $member->name }}</div>
                                <div class="text-[11px] font-bold text-pmr-primary uppercase tracking-wider my-0.5">{{ $member->position ?: 'Anggota' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">NIS: {{ $member->nis ?: '-' }} &bull; {{ $member->gender }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-pmr-50 text-pmr-primary px-3 py-1 rounded-full text-[11px] font-bold border border-red-100">
                                    {{ $member->class_grade ?: 'Belum diisi' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    @if($effStatus === 'Purna')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-300">
                                            <i class="fa-solid fa-graduation-cap text-[9px]"></i> Purna
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> Aktif
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 mt-1 font-mono">
                                    Masuk: <strong>{{ $member->entry_year ?: '-' }}</strong>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs"><i class="fa-solid fa-phone w-4 text-slate-400"></i> {{ $member->phone ?: '-' }}</div>
                                <div class="text-xs mt-1"><i class="fa-solid fa-envelope w-4 text-slate-400"></i> {{ $member->email ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.members.edit', $member->id) }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition tooltip" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data anggota ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-500 hover:text-white transition tooltip" title="Hapus Data">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-slate-400 mb-2"><i class="fa-solid fa-users-slash text-4xl"></i></div>
                                <div class="font-bold text-slate-600">Belum ada data anggota</div>
                                <div class="text-xs text-slate-500 mt-1">Silakan input data baru atau gunakan fitur Import Excel.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $members->links() }}
        </div>
    </div>
</div>

<!-- Modal Dialog Import Excel -->
<div id="import-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
        <!-- Header Modal -->
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-black">
                    <i class="fa-solid fa-file-import"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Import Data Anggota dari Excel</h3>
                    <p class="text-xs text-slate-500">Unggah file Excel (.xlsx atau .csv) untuk impor massal</p>
                </div>
            </div>
            <button type="button" onclick="closeImportModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form Import -->
        <form action="{{ route('admin.members.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            <!-- Box Unduh Template -->
            <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-file-excel text-emerald-600 text-2xl"></i>
                    <div>
                        <div class="text-xs font-black text-emerald-950">Belum punya format file?</div>
                        <div class="text-[11px] text-emerald-800">Unduh template resmi agar data kolom sesuai.</div>
                    </div>
                </div>
                <a href="{{ route('admin.members.template') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 shadow-xs">
                    <i class="fa-solid fa-download"></i> Unduh
                </a>
            </div>

            <!-- Upload File Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Pilih File Excel (.xlsx / .csv) <span class="text-rose-500">*</span></label>
                <div class="border-2 border-dashed border-slate-300 hover:border-indigo-500 rounded-2xl p-6 text-center cursor-pointer transition bg-slate-50/50 hover:bg-indigo-50/30 relative">
                    <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls,.csv" required onchange="displaySelectedFilename(this)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div id="file-placeholder" class="space-y-2">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400"></i>
                        <div class="text-xs font-bold text-slate-700">Klik untuk memilih file Excel</div>
                        <div class="text-[11px] text-slate-400">Mendukung format .xlsx, .xls, atau .csv (Maks. 10MB)</div>
                    </div>
                    <div id="file-selected" class="hidden space-y-1">
                        <i class="fa-solid fa-file-circle-check text-3xl text-emerald-500"></i>
                        <div id="selected-filename" class="text-xs font-black text-slate-800 truncate max-w-xs mx-auto"></div>
                        <div class="text-[10px] text-emerald-600 font-bold">File siap diunggah!</div>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Singkat -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5 text-[11px] text-slate-600 space-y-1.5">
                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-blue-500"></i> Ketentuan Import:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-slate-500 pl-1">
                    <li>Kolom <strong>Nama Lengkap</strong> wajib terisi.</li>
                    <li>Jika <strong>NIS</strong> sudah ada di database, data anggota akan <strong>diperbarui</strong>.</li>
                    <li>Status otomatis menjadi <strong>Purna</strong> jika <strong>Tahun Masuk + 3 &le; Tahun sekarang</strong>.</li>
                </ul>
            </div>

            <!-- Actions Modal -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeImportModal()" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    Batal
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-upload"></i> Unggah & Impor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('import-modal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('import-modal').classList.add('hidden');
    }

    function displaySelectedFilename(input) {
        const placeholder = document.getElementById('file-placeholder');
        const selected = document.getElementById('file-selected');
        const filenameEl = document.getElementById('selected-filename');

        if (input.files && input.files[0]) {
            filenameEl.textContent = input.files[0].name;
            placeholder.classList.add('hidden');
            selected.classList.remove('hidden');
        } else {
            placeholder.classList.remove('hidden');
            selected.classList.add('hidden');
        }
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImportModal();
        }
    });
</script>
@endpush
