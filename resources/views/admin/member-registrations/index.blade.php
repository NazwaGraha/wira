@extends('layouts.admin')

@section('title', 'Pendaftar Calon Anggota Baru')
@section('page_title', 'Pendaftar Calon Anggota Baru')

@section('top_actions')
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('kontak') }}#daftar" target="_blank" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-3.5 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-arrow-up-right-from-square text-pmr-primary"></i>
            <span>Buka Form Publik</span>
        </a>

        <a href="{{ route('admin.members.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2 rounded-xl font-bold text-xs shadow-md shadow-red-950/20 transition flex items-center gap-1.5">
            <i class="fa-solid fa-user-plus"></i>
            <span>Input Anggota Resmi</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs animate-in fade-in">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl shrink-0"></i>
            <div class="text-sm font-semibold">{!! session('success') !!}</div>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs animate-in fade-in">
            <i class="fa-solid fa-circle-info text-blue-500 text-xl shrink-0"></i>
            <div class="text-sm font-semibold">{!! session('info') !!}</div>
        </div>
    @endif

    <!-- 1. Stats Counter Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <!-- Total -->
        <a href="{{ route('admin.member-registrations.index') }}" class="bg-white p-4 rounded-2xl border {{ $status === 'all' ? 'border-pmr-primary ring-2 ring-red-100 shadow-sm' : 'border-slate-200 hover:border-slate-300 shadow-2xs' }} transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pendaftar</span>
                <i class="fa-solid fa-users text-slate-400 text-sm"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $counts['total'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Calon Relawan</span>
        </a>

        <!-- Menunggu -->
        <a href="{{ route('admin.member-registrations.index', ['status' => 'pending']) }}" class="bg-white p-4 rounded-2xl border {{ $status === 'pending' ? 'border-amber-500 ring-2 ring-amber-100 shadow-sm' : 'border-slate-200 hover:border-slate-300 shadow-2xs' }} transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Menunggu</span>
                <i class="fa-solid fa-clock text-amber-500 text-sm"></i>
            </div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $counts['pending'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Perlu ditinjau</span>
        </a>

        <!-- Terverifikasi -->
        <a href="{{ route('admin.member-registrations.index', ['status' => 'verified']) }}" class="bg-white p-4 rounded-2xl border {{ $status === 'verified' ? 'border-blue-500 ring-2 ring-blue-100 shadow-sm' : 'border-slate-200 hover:border-slate-300 shadow-2xs' }} transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Terverifikasi</span>
                <i class="fa-solid fa-user-check text-blue-500 text-sm"></i>
            </div>
            <div class="text-2xl font-black text-blue-600 mt-2">{{ $counts['verified'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Kontak valid</span>
        </a>

        <!-- Diterima -->
        <a href="{{ route('admin.member-registrations.index', ['status' => 'accepted']) }}" class="bg-white p-4 rounded-2xl border {{ $status === 'accepted' ? 'border-emerald-500 ring-2 ring-emerald-100 shadow-sm' : 'border-slate-200 hover:border-slate-300 shadow-2xs' }} transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Diterima</span>
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
            </div>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $counts['accepted'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Lolos seleksi</span>
        </a>

        <!-- Ditolak -->
        <a href="{{ route('admin.member-registrations.index', ['status' => 'rejected']) }}" class="bg-white p-4 rounded-2xl border {{ $status === 'rejected' ? 'border-rose-500 ring-2 ring-rose-100 shadow-sm' : 'border-slate-200 hover:border-slate-300 shadow-2xs' }} transition col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Ditolak</span>
                <i class="fa-solid fa-circle-xmark text-rose-500 text-sm"></i>
            </div>
            <div class="text-2xl font-black text-rose-600 mt-2">{{ $counts['rejected'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Tidak memenuhi</span>
        </a>
    </div>

    <!-- 2. Main Table Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-6">

        <!-- Toolbar Filter & Search -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <a href="{{ route('admin.member-registrations.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Semua</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-md {{ $status === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['total'] }}</span>
                </a>
                <a href="{{ route('admin.member-registrations.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                    <span>Menunggu</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-md {{ $status === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-200 text-amber-900' }}">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('admin.member-registrations.index', array_merge(request()->except(['status', 'page']), ['status' => 'verified'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'verified' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' }}">
                    <span>Terverifikasi</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-md {{ $status === 'verified' ? 'bg-white/20 text-white' : 'bg-blue-200 text-blue-900' }}">{{ $counts['verified'] }}</span>
                </a>
                <a href="{{ route('admin.member-registrations.index', array_merge(request()->except(['status', 'page']), ['status' => 'accepted'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'accepted' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                    <span>Diterima</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-md {{ $status === 'accepted' ? 'bg-white/20 text-white' : 'bg-emerald-200 text-emerald-900' }}">{{ $counts['accepted'] }}</span>
                </a>
                <a href="{{ route('admin.member-registrations.index', array_merge(request()->except(['status', 'page']), ['status' => 'rejected'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200' }}">
                    <span>Ditolak</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-md {{ $status === 'rejected' ? 'bg-white/20 text-white' : 'bg-rose-200 text-rose-900' }}">{{ $counts['rejected'] }}</span>
                </a>
            </div>

            <!-- Search Input -->
            <form action="{{ route('admin.member-registrations.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-72">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kelas, NISN, no HP..."
                           class="w-full text-xs pl-9 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:border-pmr-primary focus:bg-white transition">
                    @if(request('search'))
                        <a href="{{ route('admin.member-registrations.index', request()->except('search')) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="bg-slate-900 hover:bg-black text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0">
                    Cari
                </button>
            </form>
        </div>

        <!-- Table Responsive -->
        <div class="overflow-x-auto touch-pan-x">
            <table class="w-full text-left text-xs min-w-[750px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider border-y border-slate-200 text-[11px]">
                        <th class="py-3.5 px-4 font-bold w-12 text-center">No</th>
                        <th class="py-3.5 px-4 font-bold">Calon Anggota</th>
                        <th class="py-3.5 px-4 font-bold">Kontak</th>
                        <th class="py-3.5 px-4 font-bold">Alasan / Motivasi</th>
                        <th class="py-3.5 px-4 font-bold">Waktu Daftar</th>
                        <th class="py-3.5 px-4 font-bold text-center">Status</th>
                        <th class="py-3.5 px-4 font-bold text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registrations as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-400">
                                {{ $registrations->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $item->full_name }}</div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                                    <span class="inline-flex items-center gap-1 font-semibold text-pmr-primary">
                                        <i class="fa-solid fa-school text-[10px]"></i> Kelas {{ $item->class_grade }}
                                    </span>
                                    @if($item->nisn)
                                        <span class="text-slate-300">&bull;</span>
                                        <span>NISN: <strong class="font-mono text-slate-700">{{ $item->nisn }}</strong></span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ $item->whatsapp_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="inline-flex items-center gap-1 font-bold font-mono text-emerald-700 hover:text-emerald-800 hover:underline"
                                       title="Chat WhatsApp Calon Anggota">
                                        <i class="fa-brands fa-whatsapp text-emerald-500 text-sm"></i>
                                        <span>{{ $item->phone }}</span>
                                    </a>
                                </div>
                                @if($item->email)
                                    <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        <i class="fa-regular fa-envelope text-[10px]"></i>
                                        <span class="truncate max-w-[150px]">{{ $item->email }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="text-slate-600 line-clamp-2 text-xs leading-relaxed max-w-xs">
                                    {{ $item->motivation ?: '-' }}
                                </p>
                                @if(strlen($item->motivation) > 80)
                                    <button type="button" onclick="showMotivationModal('{{ addslashes($item->full_name) }}', 'Kelas {{ $item->class_grade }}', '{{ addslashes($item->motivation) }}')" class="text-[10px] text-pmr-primary font-bold hover:underline mt-1">
                                        Lihat Selengkapnya &rarr;
                                    </button>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                <div class="font-semibold text-slate-700">{{ $item->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $item->created_at->format('H:i') }} WIB &bull; {{ $item->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-block relative">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $item->status_badge['bg'] }} {{ $item->status_badge['text'] }} {{ $item->status_badge['border'] }}">
                                        @if($item->status === 'accepted')
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        @elseif($item->status === 'verified')
                                            <i class="fa-solid fa-user-check text-[10px]"></i>
                                        @elseif($item->status === 'rejected')
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                        @else
                                            <i class="fa-solid fa-clock text-[10px]"></i>
                                        @endif
                                        <span>{{ $item->status_badge['label'] }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- WhatsApp Direct -->
                                    <a href="{{ $item->whatsapp_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="w-8 h-8 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white flex items-center justify-center transition shadow-2xs" 
                                       title="Hubungi via WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </a>

                                    <!-- Quick Change Status Dropdown Menu -->
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button type="button" @click="open = !open" 
                                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition shadow-2xs"
                                                title="Ubah Status Pendaftaran">
                                            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                        </button>
                                        
                                        <div x-show="open" @click.away="open = false" 
                                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 z-50 text-left focus:outline-none"
                                             x-cloak>
                                            <div class="p-1.5">
                                                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Ubah Status</div>
                                                
                                                <!-- Set Pending -->
                                                <form action="{{ route('admin.member-registrations.update-status', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-amber-700 hover:bg-amber-50 rounded-lg flex items-center gap-2">
                                                        <i class="fa-solid fa-clock w-4 text-amber-500"></i> Menunggu
                                                    </button>
                                                </form>

                                                <!-- Set Verified -->
                                                <form action="{{ route('admin.member-registrations.update-status', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="verified">
                                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-blue-700 hover:bg-blue-50 rounded-lg flex items-center gap-2">
                                                        <i class="fa-solid fa-user-check w-4 text-blue-500"></i> Verifikasi Berkas
                                                    </button>
                                                </form>

                                                <!-- Set Accepted -->
                                                <form action="{{ route('admin.member-registrations.update-status', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-emerald-700 hover:bg-emerald-50 rounded-lg flex items-center gap-2">
                                                        <i class="fa-solid fa-circle-check w-4 text-emerald-500"></i> Terima Calon
                                                    </button>
                                                </form>

                                                <!-- Set Rejected -->
                                                <form action="{{ route('admin.member-registrations.update-status', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-rose-700 hover:bg-rose-50 rounded-lg flex items-center gap-2">
                                                        <i class="fa-solid fa-circle-xmark w-4 text-rose-500"></i> Tolak Pendaftaran
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="p-1.5">
                                                <!-- Promote to Official Member -->
                                                <form action="{{ route('admin.member-registrations.promote', $item->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-pmr-primary hover:bg-red-50 rounded-lg font-bold flex items-center gap-2">
                                                        <i class="fa-solid fa-user-plus w-4"></i> Jadikan Anggota Resmi
                                                    </button>
                                                </form>

                                                <!-- Delete -->
                                                <form action="{{ route('admin.member-registrations.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data calon anggota {{ $item->full_name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 rounded-lg flex items-center gap-2">
                                                        <i class="fa-solid fa-trash w-4"></i> Hapus Data
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                                    <i class="fa-solid fa-folder-open"></i>
                                </div>
                                <div class="font-extrabold text-slate-700 text-sm">Belum Ada Data Pendaftar</div>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                                    Pendaftaran yang masuk melalui formulir rekrutmen di halaman kontak akan otomatis tertera di tabel ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($registrations->hasPages())
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                    Menampilkan <strong>{{ $registrations->firstItem() }}</strong> - <strong>{{ $registrations->lastItem() }}</strong> dari <strong>{{ $registrations->total() }}</strong> pendaftar
                </div>
                <div>
                    {{ $registrations->links() }}
                </div>
            </div>
        @endif

    </div>

</div>

<!-- Modal Motivasi / Detail Calon Anggota -->
<div id="motivation-modal" class="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 animate-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <div>
                <h3 id="modal-candidate-name" class="font-black text-slate-900 text-base">Nama Calon</h3>
                <span id="modal-candidate-class" class="text-xs text-pmr-primary font-bold">Kelas</span>
            </div>
            <button type="button" onclick="closeMotivationModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Alasan & Motivasi Bergabung:</label>
            <div id="modal-candidate-motivation" class="bg-slate-50 p-4 rounded-2xl text-xs sm:text-sm text-slate-700 leading-relaxed max-h-60 overflow-y-auto border border-slate-200/60 whitespace-pre-line font-normal">
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeMotivationModal()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function showMotivationModal(name, kelas, motivasi) {
        document.getElementById('modal-candidate-name').textContent = name;
        document.getElementById('modal-candidate-class').textContent = kelas;
        document.getElementById('modal-candidate-motivation').textContent = motivasi;
        document.getElementById('motivation-modal').classList.remove('hidden');
    }

    function closeMotivationModal() {
        document.getElementById('motivation-modal').classList.add('hidden');
    }

    document.getElementById('motivation-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMotivationModal();
        }
    });
</script>
@endsection
