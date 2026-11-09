@extends('layouts.admin')

@section('title', 'Laporan Peserta Lomba - Sua Bhakti Berkarya IV')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER & ACTION BUTTONS -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1">
                <span>Sua Bhakti Berkarya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-pmr-primary">Laporan & Rekapitulasi Peserta</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-file-invoice text-pmr-primary"></i>
                <span>Laporan & Rekap Peserta Lomba</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Event: <strong class="text-slate-800">{{ $event?->title ?? 'Sua Bhakti Berkarya IV 2026' }}</strong> • Rincian lengkap nama siswa, nomor urut, kontingen sekolah, dan cabang lomba.
            </p>
        </div>

        <!-- Tombol Aksi Ekspor & Cetak -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.competition-reports.export-excel', request()->query()) }}" 
               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs transition cursor-pointer">
                <i class="fa-solid fa-file-excel text-sm"></i>
                <span>Unduh Excel (.csv)</span>
            </a>
            <a href="{{ route('admin.competition-reports.print-pdf', request()->query()) }}" 
               target="_blank" 
               class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs transition cursor-pointer">
                <i class="fa-solid fa-print text-sm"></i>
                <span>Cetak Lembar Resmi (PDF)</span>
            </a>
            <a href="{{ route('admin.competition-participants.index') }}" 
               class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kelola Regu</span>
            </a>
        </div>
    </div>

    <!-- 2. RESUME KPI CARDS (4 KARTU METRIK EKSEKUTIF) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- KPI 1: Total Kontingen Sekolah -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Kontingen</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-school"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $kpi['total_schools'] }} Sekolah</div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                <span class="text-emerald-600 font-bold">{{ $kpi['total_schools_verified'] }} Terverifikasi</span>
                <span>• {{ $kpi['total_schools'] - $kpi['total_schools_verified'] }} Pending</span>
            </div>
        </div>

        <!-- KPI 2: Total Regu & Peserta -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Regu / Siswa</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $kpi['total_teams'] }} Regu</div>
            <div class="text-[11px] text-amber-700 font-bold mt-1">
                ~{{ $kpi['total_students'] }} Siswa Terdaftar
            </div>
        </div>

        <!-- KPI 3: Distribusi Tingkatan -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sebaran Jenjang</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
            </div>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-xs font-black" title="PMR Mula (SD/MI)">Mula: {{ $kpi['mula_teams'] }}</span>
                <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-xs font-black" title="PMR Madya (SMP/MTs)">Madya: {{ $kpi['madya_teams'] }}</span>
                <span class="px-2 py-0.5 rounded-md bg-red-100 text-red-800 text-xs font-black" title="PMR Wira (SMA/SMK)">Wira: {{ $kpi['wira_teams'] }}</span>
            </div>
            <div class="text-[10px] text-slate-400 mt-2 font-medium">Berdasarkan cabang lomba aktif</div>
        </div>

        <!-- KPI 4: Presensi Check-in Hari-H -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Kehadiran (Check-in)</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-qrcode"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $kpi['checked_in_teams'] }} Regu</div>
            <div class="text-[11px] text-slate-500 mt-1">
                {{ $kpi['total_teams'] > 0 ? round(($kpi['checked_in_teams'] / $kpi['total_teams']) * 100, 1) : 0 }}% Telah Hadir di Lokasi
            </div>
        </div>
    </div>

    <!-- 3. CONTAINER FILTER BERJENJANG (PILAH TINGKAT & PECAH KATEGORI) -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs space-y-4">

        <!-- TINGKAT 1: TAB FILTER TINGKAT (MULA, MADYA, WIRA) -->
        <div>
            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 mb-2">
                <i class="fa-solid fa-filter text-pmr-primary"></i> 1. Pilah Berdasarkan Jenjang Tingkatan:
            </label>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.competition-reports.participants', array_merge(request()->except(['level', 'category_id', 'page']))) }}"
                   class="px-4 py-2 rounded-xl text-xs font-extrabold transition border {{ empty($level) ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border-slate-200' }}">
                    <i class="fa-solid fa-globe mr-1.5"></i> Semua Tingkat
                </a>
                <a href="{{ route('admin.competition-reports.participants', array_merge(request()->except(['level', 'category_id', 'page']), ['level' => 'Mula'])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-extrabold transition border {{ $level === 'Mula' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-950/20' : 'bg-emerald-50/70 hover:bg-emerald-100/70 text-emerald-800 border-emerald-200' }}">
                    <i class="fa-solid fa-seedling mr-1.5"></i> PMR Mula (SD / MI)
                </a>
                <a href="{{ route('admin.competition-reports.participants', array_merge(request()->except(['level', 'category_id', 'page']), ['level' => 'Madya'])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-extrabold transition border {{ $level === 'Madya' ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-950/20' : 'bg-blue-50/70 hover:bg-blue-100/70 text-blue-800 border-blue-200' }}">
                    <i class="fa-solid fa-leaf mr-1.5"></i> PMR Madya (SMP / MTs)
                </a>
                <a href="{{ route('admin.competition-reports.participants', array_merge(request()->except(['level', 'category_id', 'page']), ['level' => 'Wira'])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-extrabold transition border {{ $level === 'Wira' ? 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-950/20' : 'bg-rose-50/70 hover:bg-rose-100/70 text-rose-800 border-rose-200' }}">
                    <i class="fa-solid fa-tree mr-1.5"></i> PMR Wira (SMA / SMK / MA)
                </a>
            </div>
        </div>

        <!-- TINGKAT 2: PILL FILTER PECAH KATEGORI LOMBA -->
        <div class="pt-3 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2">
                <label class="text-[11px] font-black uppercase tracking-wider text-slate-400">
                    <i class="fa-solid fa-cubes-stacked text-amber-500"></i> 2. Pecah per Cabang Mata Lomba:
                </label>
                @if($categoryId)
                    <a href="{{ route('admin.competition-reports.participants', array_merge(request()->except(['category_id', 'page']))) }}" 
                       class="text-[11px] text-rose-600 hover:underline font-bold">
                        Tampilkan Semua Cabang &times;
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto pr-1 custom-scrollbar">
                <a href="{{ route('admin.competition-reports.participants', array_merge(request()->except(['category_id', 'page']))) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition border {{ empty($categoryId) ? 'bg-amber-500 text-slate-950 border-amber-500 font-black shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border-slate-200' }}">
                    Semua Cabang
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('admin.competition-reports.participants', array_merge(request()->except(['category_id', 'page']), ['category_id' => $cat->id])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition border {{ $categoryId == $cat->id ? 'bg-pmr-primary text-white border-pmr-primary shadow-xs' : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-200' }}">
                        <span>{{ $cat->name }}</span>
                        @if(!$level)
                            <span class="text-[10px] opacity-75 font-normal">({{ $cat->level }})</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- TINGKAT 3: FORM PENCARIAN & FILTER STATUS -->
        <form action="{{ route('admin.competition-reports.participants') }}" method="GET" class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            @if($level) <input type="hidden" name="level" value="{{ $level }}"> @endif
            @if($categoryId) <input type="hidden" name="category_id" value="{{ $categoryId }}"> @endif

            <!-- Pencarian Kata Kunci -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Cari Siswa, Sekolah, No. Urut:</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Ketik nama siswa/sekolah/nomor..." 
                           class="w-full bg-slate-50 focus:bg-white border border-slate-200 rounded-xl px-3 py-2 pl-9 text-xs focus:outline-none focus:border-pmr-primary focus:ring-1 focus:ring-pmr-primary">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Filter Status Pendaftaran -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Verifikasi:</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-pmr-primary">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="verified" {{ $status === 'verified' ? 'selected' : '' }}>Terverifikasi Saja</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi (Pending)</option>
                </select>
            </div>

            <!-- Filter Kehadiran Check-in -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Check-in QR:</label>
                <select name="checkin" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-pmr-primary">
                    <option value="all" {{ $checkin === 'all' ? 'selected' : '' }}>Semua Status Kehadiran</option>
                    <option value="checked_in" {{ $checkin === 'checked_in' ? 'selected' : '' }}>Sudah Hadir (Check-in)</option>
                    <option value="not_checked_in" {{ $checkin === 'not_checked_in' ? 'selected' : '' }}>Belum Hadir</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
                <a href="{{ route('admin.competition-reports.participants') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition text-center" title="Reset Semua Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- 4. TABEL DATA LAPORAN RINCI PESERTA LOMBA -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Table Header Info Bar -->
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-sm text-slate-900">
                    Daftar Peserta & Anggota Regu Terindeks
                </h3>
                <p class="text-xs text-slate-500">
                    Menampilkan <strong class="text-slate-800">{{ $teams->firstItem() ?? 0 }} - {{ $teams->lastItem() ?? 0 }}</strong> dari total <strong class="text-slate-800">{{ $teams->total() }}</strong> regu sesuai filter aktif.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold bg-slate-200/70 text-slate-700 px-3 py-1 rounded-full">
                    Tingkat: {{ $level ?: 'Semua' }}
                </span>
                @if($selectedCategory)
                    <span class="text-xs font-bold bg-amber-100 text-amber-900 px-3 py-1 rounded-full">
                        {{ $selectedCategory->name }}
                    </span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100/90 text-slate-700 font-black uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <th class="py-3 px-3.5 text-center w-12">No</th>
                        <th class="py-3 px-3.5 w-28">No. Urut</th>
                        <th class="py-3 px-4">Nama Tim / Regu</th>
                        <th class="py-3 px-4">Rincian Nama Anggota Siswa</th>
                        <th class="py-3 px-4">Asal Sekolah & Tingkat</th>
                        <th class="py-3 px-4">Mata Lomba</th>
                        <th class="py-3 px-4">Pembina Pendamping</th>
                        <th class="py-3 px-3.5 text-center w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teams as $index => $team)
                        @php
                            $reg = $team->registration;
                            $cat = $team->category;
                            $members = is_array($team->members_list) ? $team->members_list : [];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Kolom 1: No -->
                            <td class="py-3.5 px-3.5 text-center font-bold text-slate-500">
                                {{ $teams->firstItem() + $index }}
                            </td>

                            <!-- Kolom 2: Nomor Urut Lomba -->
                            <td class="py-3.5 px-3.5">
                                @if($team->order_number)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono font-black text-xs bg-slate-900 text-white shadow-2xs">
                                        {{ $team->order_number }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Kolom 3: Nama Regu -->
                            <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                <div class="text-sm font-bold text-slate-900">{{ $team->team_name ?: 'Regu ' . ($team->team_label ?: 'A') }}</div>
                                @if($team->team_label)
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.2 rounded mt-0.5 inline-block">
                                        Regu {{ $team->team_label }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 4: Rincian Nama Anggota Siswa (Ketua & Anggota) -->
                            <td class="py-3.5 px-4">
                                @if(!empty($members) && count($members) > 0)
                                    <div class="space-y-1">
                                        @foreach($members as $mIdx => $m)
                                            @php
                                                $mName = is_array($m) ? ($m['name'] ?? ($m['nama'] ?? '-')) : (string) $m;
                                                $mRole = is_array($m) && !empty($m['role']) ? $m['role'] : ($mIdx === 0 ? 'Ketua' : 'Anggota');
                                                $isLeader = (stripos($mRole, 'ketua') !== false || $mIdx === 0);
                                            @endphp
                                            <div class="flex items-center gap-1.5 text-xs">
                                                <span class="w-4 text-[10px] font-mono text-slate-400 text-right">{{ $mIdx + 1 }}.</span>
                                                <span class="font-semibold {{ $isLeader ? 'text-slate-900 font-extrabold' : 'text-slate-700' }}">
                                                    {{ $mName }}
                                                </span>
                                                @if($isLeader)
                                                    <span class="text-[9px] bg-amber-100 text-amber-800 font-black px-1.5 py-0.2 rounded">Ketua</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-slate-400 italic text-[11px]">
                                        <i class="fa-solid fa-circle-info text-[10px] mr-1"></i> Data nama anggota belum diinput
                                    </div>
                                @endif

                                @if($team->members_file)
                                    <div class="mt-1.5">
                                        <a href="{{ $team->members_file_url }}" target="_blank" class="text-[10px] text-blue-600 hover:underline font-bold inline-flex items-center gap-1">
                                            <i class="fa-solid fa-paperclip"></i> Berkas Lampiran Excel
                                        </a>
                                    </div>
                                @endif
                            </td>

                            <!-- Kolom 5: Asal Sekolah & Tingkat -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-xs">
                                    {{ $reg?->school_name ?? '-' }}
                                </div>
                                @php
                                    $teamLevel = $cat?->level ?: ($reg?->level ?: '-');
                                    $levelBadgeClass = match($teamLevel) {
                                        'Mula' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'Madya' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Wira' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        default => 'bg-slate-100 text-slate-800 border-slate-200',
                                    };
                                @endphp
                                <div class="mt-1 flex items-center gap-1.5">
                                    <span class="text-[10px] font-black px-2 py-0.2 rounded border {{ $levelBadgeClass }}">
                                        PMR {{ $teamLevel }}
                                    </span>
                                    @if($reg?->registration_code)
                                        <span class="text-[10px] font-mono text-slate-400">#{{ $reg->registration_code }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom 6: Mata Lomba -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-xs">
                                    {{ $cat?->name ?? 'Cabang Lomba' }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    {{ $cat?->gender_category ?: 'Umum / Beregu' }}
                                </div>
                            </td>

                            <!-- Kolom 7: Guru Pembina & Kontak -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-xs">
                                    {{ $reg?->advisor_name ?? '-' }}
                                </div>
                                @if($reg?->advisor_phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $reg->advisor_phone) }}" target="_blank" 
                                       class="text-[11px] text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1 mt-0.5">
                                        <i class="fa-brands fa-whatsapp text-xs"></i>
                                        <span>{{ $reg->advisor_phone }}</span>
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">No HP tidak tersedia</span>
                                @endif
                            </td>

                            <!-- Kolom 8: Status Verifikasi & Check-in QR -->
                            <td class="py-3.5 px-3.5 text-center">
                                <div class="space-y-1">
                                    <!-- Status Verifikasi Registrasi -->
                                    @if($reg?->status === 'verified')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                                            <i class="fa-solid fa-clock text-[9px]"></i> Pending
                                        </span>
                                    @endif

                                    <!-- Status Kehadiran (Check-in QR) -->
                                    <div>
                                        @if($reg?->is_checked_in)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-800" title="Check-in: {{ $reg->checked_in_at?->format('H:i') ?? '-' }} WIB">
                                                <i class="fa-solid fa-qrcode text-[8px]"></i> Hadir Hari-H
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-500">
                                                Belum Hadir
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>
                                <div class="font-extrabold text-slate-700 text-sm">Tidak ada data peserta ditemukan</div>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter jenjang, kategori lomba, atau kata kunci pencarian Anda.</p>
                                <a href="{{ route('admin.competition-reports.participants') }}" class="mt-3 inline-block px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition">
                                    Reset Semua Filter
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($teams->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                {{ $teams->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
