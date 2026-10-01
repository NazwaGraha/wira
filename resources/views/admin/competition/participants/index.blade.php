@extends('layouts.admin')

@section('title', 'Daftar Peserta Lomba Terverifikasi')
@section('page_title', 'Daftar Regu & Peserta Lomba (Terverifikasi)')

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.competition-participants.print', request()->query()) }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-print"></i> Cetak Lembar Presensi / Roster
        </a>
        <a href="{{ route('admin.competition-registrations.index', ['status' => 'pending']) }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-clipboard-check"></i> Verifikasi Pendaftar Baru
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Statistic Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
        <!-- Total Regu -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Regu</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_teams'] }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">{{ $stats['total_schools'] }} Kontingen Sekolah</div>
        </div>

        <!-- Putra -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['gender' => 'Putra'])) }}" class="p-4 rounded-2xl border transition {{ $gender == 'Putra' ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-blue-300' }}">
            <div class="text-[11px] font-bold {{ $gender == 'Putra' ? 'text-blue-100' : 'text-blue-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-mars"></i> Regu Putra
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['putra_teams'] }}</div>
            <div class="text-[10px] {{ $gender == 'Putra' ? 'text-blue-100' : 'text-slate-500' }} mt-0.5">Kategori Laki-laki</div>
        </a>

        <!-- Putri -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['gender' => 'Putri'])) }}" class="p-4 rounded-2xl border transition {{ $gender == 'Putri' ? 'bg-rose-600 text-white border-rose-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-rose-300' }}">
            <div class="text-[11px] font-bold {{ $gender == 'Putri' ? 'text-rose-100' : 'text-rose-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-venus"></i> Regu Putri
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['putri_teams'] }}</div>
            <div class="text-[10px] {{ $gender == 'Putri' ? 'text-rose-100' : 'text-slate-500' }} mt-0.5">Kategori Perempuan</div>
        </a>

        <!-- Mula -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Mula'])) }}" class="p-4 rounded-2xl border transition {{ $level == 'Mula' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-emerald-300' }}">
            <div class="text-[11px] font-bold {{ $level == 'Mula' ? 'text-emerald-100' : 'text-emerald-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-child-reaching"></i> PMR Mula
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['mula_teams'] }}</div>
            <div class="text-[10px] {{ $level == 'Mula' ? 'text-emerald-100' : 'text-slate-500' }} mt-0.5">Jenjang SD/MI</div>
        </a>

        <!-- Madya -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Madya'])) }}" class="p-4 rounded-2xl border transition {{ $level == 'Madya' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-indigo-300' }}">
            <div class="text-[11px] font-bold {{ $level == 'Madya' ? 'text-indigo-100' : 'text-indigo-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-user-group"></i> PMR Madya
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['madya_teams'] }}</div>
            <div class="text-[10px] {{ $level == 'Madya' ? 'text-indigo-100' : 'text-slate-500' }} mt-0.5">Jenjang SMP/MTs</div>
        </a>

        <!-- Wira -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Wira'])) }}" class="p-4 rounded-2xl border transition {{ $level == 'Wira' ? 'bg-amber-600 text-white border-amber-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-amber-300' }}">
            <div class="text-[11px] font-bold {{ $level == 'Wira' ? 'text-amber-100' : 'text-amber-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-graduation-cap"></i> PMR Wira
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['wira_teams'] }}</div>
            <div class="text-[10px] {{ $level == 'Wira' ? 'text-amber-100' : 'text-slate-500' }} mt-0.5">Jenjang SMA/SMK</div>
        </a>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-4">
        <!-- Top Row Filters -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            
            <!-- Tingkat & Gender Filter Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tingkat -->
                <div class="inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->except('level'), [])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ !$level ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Semua Tingkat</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Mula'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Mula' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Mula</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Madya'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Madya' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Madya</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Wira'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Wira' ? 'bg-amber-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Wira</a>
                </div>

                <!-- Gender -->
                <div class="inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->except('gender'), [])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ !$gender ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Semua Gender</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['gender' => 'Putra'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $gender == 'Putra' ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"><i class="fa-solid fa-mars mr-1"></i> Putra</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['gender' => 'Putri'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $gender == 'Putri' ? 'bg-rose-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"><i class="fa-solid fa-venus mr-1"></i> Putri</a>
                </div>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.competition-participants.index') }}" method="GET" class="w-full lg:w-auto flex items-center gap-2">
                @if($level) <input type="hidden" name="level" value="{{ $level }}"> @endif
                @if($gender) <input type="hidden" name="gender" value="{{ $gender }}"> @endif
                @if($categoryId) <input type="hidden" name="category_id" value="{{ $categoryId }}"> @endif
                
                <div class="relative w-full lg:w-72">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari Sekolah, Regu, No Urut..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-red-500">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold transition">Cari</button>
                @if($search || $categoryId || $gender || $level)
                    <a href="{{ route('admin.competition-participants.index') }}" class="text-xs text-slate-500 hover:text-red-600 px-2 font-semibold">Reset</a>
                @endif
            </form>
        </div>

        <!-- Bottom Row: Specific Category Dropdown Filter & Batch Actions -->
        <div class="pt-3 border-t border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold text-slate-500 shrink-0">Cabang Lomba:</span>
                <select onchange="location = this.value;" class="bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 rounded-xl px-3 py-2 focus:outline-none focus:border-red-500 w-full md:w-auto">
                    <option value="{{ route('admin.competition-participants.index', array_merge(request()->except('category_id'), [])) }}">-- Seluruh Cabang Mata Lomba --</option>
                    @foreach($categories as $cat)
                        <option value="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['category_id' => $cat->id])) }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            [{{ $cat->level }}] {{ $cat->name }} ({{ $cat->gender_category }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Auto-Number & Tools -->
            <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                <form action="{{ route('admin.competition-participants.auto-assign-orders') }}" method="POST" onsubmit="return confirm('Generate nomor urut otomatis (01, 02, 03...) untuk regu yang terfilter saat ini?');">
                    @csrf
                    @if($categoryId) <input type="hidden" name="category_id" value="{{ $categoryId }}"> @endif
                    @if($level) <input type="hidden" name="level" value="{{ $level }}"> @endif
                    <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-down-1-9"></i> Auto No. Urut (01..N)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main List & Form for Bulk Updating Order Numbers -->
    <form action="{{ route('admin.competition-participants.bulk-update-orders') }}" method="POST" id="orders-form">
        @csrf

        <div class="space-y-6">
            @forelse($teamsByCategory as $catId => $categoryTeams)
                @php
                    $firstTeam = $categoryTeams->first();
                    $category = $firstTeam->category;
                    $isPa = $category?->gender_category === 'Putra';
                    $isPi = $category?->gender_category === 'Putri';
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Category Card Header -->
                    <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 {{ $category?->level == 'Mula' ? 'bg-emerald-50/50' : ($category?->level == 'Madya' ? 'bg-indigo-50/50' : 'bg-amber-50/50') }}">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-base {{ $isPa ? 'bg-blue-600' : ($isPi ? 'bg-rose-600' : 'bg-slate-800') }}">
                                @if($isPa) <i class="fa-solid fa-mars"></i>
                                @elseif($isPi) <i class="fa-solid fa-venus"></i>
                                @else <i class="fa-solid fa-users"></i> @endif
                            </span>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-black text-slate-900 text-base">
                                        {{ $category?->name ?? 'Cabang Lomba' }}
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $category?->level == 'Mula' ? 'bg-emerald-100 text-emerald-800' : ($category?->level == 'Madya' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                        PMR {{ $category?->level }}
                                    </span>
                                    @if($isPa)
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700">
                                            Kategori Putra (♂)
                                        </span>
                                    @elseif($isPi)
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700">
                                            Kategori Putri (♀)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600">
                                            Umum / Campuran
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5 font-medium">
                                    {{ $categoryTeams->count() }} Regu Terdaftar &bull; Kode: {{ $category?->code ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Action Links for this Category -->
                        <div class="flex items-center gap-2">
                            @if($category)
                                <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-calculator text-teal-600"></i> Buka Lembar Penilaian Juri
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Teams Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-slate-700 uppercase font-black border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3.5 w-28 text-center">No. Urut / Undian</th>
                                    <th class="px-5 py-3.5 min-w-[220px]">Nama Regu & Asal Sekolah</th>
                                    <th class="px-5 py-3.5 min-w-[200px]">Anggota Regu</th>
                                    <th class="px-5 py-3.5 w-48">Pembina & Kontak</th>
                                    <th class="px-5 py-3.5 w-32 text-center">Status Verifikasi</th>
                                    <th class="px-5 py-3.5 w-24 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($categoryTeams as $idx => $team)
                                    @php
                                        $reg = $team->registration;
                                        $members = is_array($team->members_list) ? $team->members_list : [];
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <!-- Order Number Input -->
                                        <td class="px-5 py-4 text-center">
                                            <input type="text" name="orders[{{ $team->id }}]" value="{{ $team->order_number ?? '' }}" placeholder="{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}" class="w-20 px-2.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-center font-black font-mono text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-red-500 shadow-2xs">
                                        </td>

                                        <!-- Team & School Name -->
                                        <td class="px-5 py-4">
                                            <div class="font-extrabold text-slate-900 text-sm">
                                                {{ $team->team_name }}
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($reg)
                                                    <a href="{{ route('admin.competition-registrations.show', $reg->id) }}" class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded border border-slate-200 transition">
                                                        {{ $reg->registration_code }}
                                                    </a>
                                                    <span class="text-[11px] text-slate-500 font-semibold">{{ $reg->school_name }}</span>
                                                @else
                                                    <span class="text-[11px] text-slate-400 italic">Peserta Langsung (Manual)</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Members List -->
                                        <td class="px-5 py-4">
                                            @if(count($members) > 0)
                                                <div class="space-y-0.5">
                                                    @foreach(array_slice($members, 0, 3) as $m)
                                                        <div class="text-[11px] text-slate-700 font-medium flex items-center gap-1.5">
                                                            <i class="fa-solid fa-user text-[9px] text-slate-400"></i>
                                                            <span>{{ is_array($m) ? ($m['name'] ?? '-') : $m }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if(count($members) > 3)
                                                        <div class="text-[10px] text-slate-400 font-bold italic">+ {{ count($members) - 3 }} anggota lainnya</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">Belum diisi nama anggota</span>
                                            @endif
                                        </td>

                                        <!-- Advisor & Contact -->
                                        <td class="px-5 py-4">
                                            @if($reg)
                                                <div class="font-bold text-slate-800 text-xs">{{ $reg->advisor_name }}</div>
                                                <div class="text-[11px] text-slate-500 mt-0.5">
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $reg->advisor_phone) }}" target="_blank" class="text-emerald-600 hover:underline inline-flex items-center gap-1 font-semibold">
                                                        <i class="fa-brands fa-whatsapp"></i> {{ $reg->advisor_phone }}
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Verification Status Badge -->
                                        <td class="px-5 py-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Terverifikasi
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-5 py-4 text-right">
                                            <button type="button" onclick="openEditModal({{ $team->id }}, '{{ addslashes($team->team_name) }}', '{{ addslashes($team->team_label ?? '') }}', '{{ $team->order_number ?? '' }}', {{ json_encode($members) }})" class="p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="Edit Detail Regu & Anggota">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-base">Belum Ada Peserta Terverifikasi yang Sesuai Filter</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Pastikan pendaftaran kontingen sekolah sudah diverifikasi di menu <a href="{{ route('admin.competition-registrations.index') }}" class="text-red-600 font-bold underline">Verifikasi Pendaftar</a>.
                    </p>
                </div>
            @endforelse
        </div>

        @if($teams->isNotEmpty())
            <!-- Sticky Bottom Submit Footer -->
            <div class="mt-8 sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-500 text-sm"></i>
                    <span>Nomor urut tampil yang diisi di atas akan menjadi nomor undian resmi pada lembar penilaian juri.</span>
                </div>
                <button type="submit" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-extrabold text-xs shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan Nomor Urut
                </button>
            </div>
        @endif
    </form>

</div>

<!-- Modal Edit Regu & Anggota -->
<div id="editTeamModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-base" id="modalTitle">Edit Detail Regu Peserta</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editTeamForm" method="POST" action="">
            @csrf
            <div class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nomor Urut / No. Undian Tampil</label>
                    <input type="text" id="modalOrderNumber" name="order_number" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-mono font-bold focus:bg-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Lengkap Regu</label>
                    <input type="text" id="modalTeamName" name="team_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold focus:bg-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Label Regu (cth: Regu A / Regu Putra 1)</label>
                    <input type="text" id="modalTeamLabel" name="team_label" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">
                        Daftar Nama Anggota Regu <span class="text-slate-400 font-normal">(1 nama per baris)</span>
                    </label>
                    <textarea id="modalMembersRaw" name="members_raw" rows="5" placeholder="1. Nama Anggota 1&#10;2. Nama Anggota 2&#10;3. Nama Anggota 3" class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-red-500 font-mono text-xs"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white text-xs font-bold shadow-md shadow-red-950/20">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditModal(teamId, teamName, teamLabel, orderNumber, members) {
        document.getElementById('modalOrderNumber').value = orderNumber || '';
        document.getElementById('modalTeamName').value = teamName || '';
        document.getElementById('modalTeamLabel').value = teamLabel || '';
        
        let membersText = '';
        if (Array.isArray(members)) {
            membersText = members.map(m => typeof m === 'object' ? (m.name || '') : m).filter(Boolean).join('\n');
        }
        document.getElementById('modalMembersRaw').value = membersText;

        const form = document.getElementById('editTeamForm');
        form.action = `/admin/competition-participants/${teamId}`;

        document.getElementById('editTeamModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editTeamModal').classList.add('hidden');
    }
</script>
@endpush
