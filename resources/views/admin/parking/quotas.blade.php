@extends('layouts.admin')

@section('title', 'Pengaturan Kuota Parkir Sekolah & Kontingen')
@section('page_title', 'Pengaturan Kuota Parkir Sekolah')

@section('top_actions')
    <a href="{{ route('admin.parking.index') }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-xl font-bold text-xs shadow-2xs transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Dashboard & Kantong Parkir
    </a>
    <a href="{{ route('parking.index') }}" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fa-solid fa-camera-viewfinder text-rose-400"></i> Buka Web Petugas Parkir (/parkir)
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Terjadi Kesalahan:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 1. SECTION: KONFIGURASI ATURAN KUOTA GLOBAL & MATRIKS REGULER -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 bg-rose-50 px-2.5 py-1 rounded-md border border-rose-100">
                    SISTEM ATURAN KUOTA CERDAS
                </span>
                <h3 class="font-black text-lg text-slate-900 mt-1 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-rose-600"></i> Konfigurasi Rumus & Mode Alokasi Kuota
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan bagaimana kuota tiket parkir (R4 Mobil & R2 Motor) otomatis dibagikan kepada setiap sekolah peserta lomba.</p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                <form action="{{ route('admin.parking.auto-allocate-priority') }}" method="POST" onsubmit="return confirm('Jalankan Algoritma Prioritas Cerdas (SPPS)?\n\nSistem akan mengevaluasi 3 kriteria:\n1. Keikutsertaan Cabang Lomba (40%)\n2. Pemanfaatan Kuota Regu Maksimal (30%)\n3. Kecepatan Waktu Pendaftaran (30%)\n\nSekolah peringkat teratas akan otomatis dialokasikan ke Kantong Parkir Utama (Kantong A SMANIC). Lanjutkan?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 text-white font-extrabold text-xs transition flex items-center gap-2 shadow-xs">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Kalkulasi Prioritas Cerdas (SPPS)
                    </button>
                </form>
                <form action="{{ route('admin.parking.quotas.reset-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin me-reset kuota seluruh sekolah kembali ke mode otomatis berdasarkan jumlah regu?')">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5" title="Reset semua kuota custom ke mode otomatis">
                        <i class="fa-solid fa-rotate-left text-slate-500"></i> Reset Kuota
                    </button>
                </form>
                <button type="button" onclick="openModal('modalEditSetting')" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-2 shadow-xs">
                    <i class="fa-solid fa-pen-to-square text-rose-400"></i> Edit Rumus Matriks
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Panel: Mode Info -->
            <div class="lg:col-span-4 bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Mode Alokasi Aktif:</span>
                    @if($setting->mode === 'proportional_teams')
                        <div class="flex items-center gap-2.5 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shadow-sm">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-sm text-slate-900">Proporsional Jumlah Regu</h4>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    Otomatis Aktif
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed mt-2">
                            Sistem secara dinamis menghitung jatah parkir berdasarkan <strong>jumlah regu lomba terverifikasi</strong> yang didaftarkan oleh masing-masing sekolah. Semakin banyak regu, semakin besar jatah kuotanya.
                        </p>
                    @else
                        <div class="flex items-center gap-2.5 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm">
                                <i class="fa-solid fa-equals"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-sm text-slate-900">Mode Flat (Sama Rata)</h4>
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                                    Flat Aktif
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed mt-2">
                            Seluruh sekolah mendapatkan kuota standar yang sama: <strong>{{ $setting->flat_default_r4 }} Mobil (R4)</strong> dan <strong>{{ $setting->flat_default_r2 }} Motor (R2)</strong>.
                        </p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Override Kustom:</span>
                    <span class="font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                        Diperbolehkan Per Sekolah
                    </span>
                </div>
            </div>

            <!-- Right Panel: Matriks Tier Table -->
            <div class="lg:col-span-8">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Matriks Alokasi Berdasarkan Jumlah Regu:</span>
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="p-3">Tingkatan Tier</th>
                                <th class="p-3">Jumlah Regu Lomba</th>
                                <th class="p-3 text-center">Jatah Roda 4 (Mobil/Elf)</th>
                                <th class="p-3 text-center">Jatah Roda 2 (Motor)</th>
                                <th class="p-3">Keterangan / Estimasi Muatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @php
                                $rules = is_array($setting->matrix_rules) ? $setting->matrix_rules : \App\Models\ParkingQuotaSetting::defaultMatrixRules();
                            @endphp
                            @foreach($rules as $r)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-3 font-bold text-slate-900">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-900 text-white font-mono text-[11px] mr-1.5">
                                            T{{ $r['tier'] ?? $loop->iteration }}
                                        </span>
                                        {{ $r['label'] ?? "Tier {$loop->iteration}" }}
                                    </td>
                                    <td class="p-3 font-mono font-bold text-slate-800">
                                        {{ $r['min_teams'] }} - {{ $r['max_teams'] >= 999 ? '∞' : $r['max_teams'] }} Regu
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-mono font-black text-xs border border-blue-200">
                                            {{ $r['quota_r4'] }} Mobil
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 font-mono font-black text-xs border border-purple-200">
                                            {{ $r['quota_r2'] }} Motor
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-500 text-[11px]">
                                        {{ $r['desc'] ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. KPI SUMMARY METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-school"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sekolah Terverifikasi</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">
                    {{ $totalVerifiedSchools }} <span class="text-xs font-normal text-slate-500">Kontingen</span>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-car-side"></i>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Kuota Roda 4</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">
                    {{ $totalAllocatedR4 }} <span class="text-xs font-normal text-slate-500">/ {{ $totalLocationCapacityR4 }} Kapasitas Kantong</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-1 flex justify-between">
                    <span>Terbit: {{ $totalIssuedR4 }} Tiket</span>
                    <span class="font-bold text-blue-600">{{ $totalAllocatedR4 - $totalIssuedR4 }} Sisa Kuota</span>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Kuota Roda 2</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">
                    {{ $totalAllocatedR2 }} <span class="text-xs font-normal text-slate-500">/ {{ $totalLocationCapacityR2 }} Kapasitas Kantong</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-1 flex justify-between">
                    <span>Terbit: {{ $totalIssuedR2 }} Tiket</span>
                    <span class="font-bold text-purple-600">{{ $totalAllocatedR2 - $totalIssuedR2 }} Sisa Kuota</span>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-id-card-clip"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sekolah Kuota Kustom</span>
                <div class="text-xl font-black text-amber-600 mt-0.5">
                    {{ $customQuotaCount }} <span class="text-xs font-normal text-slate-500">Dispensasi Khusus</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SECTION: TABEL ALOKASI & PEMANTAUAN KUOTA PER SEKOLAH -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="font-black text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-rose-600"></i> Tabel Pemantauan Kuota Tiket per Sekolah
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pantau jatah kuota masing-masing sekolah, jumlah regu terdaftar, tiket yang sudah terbit, dan lakukan override kustom bila diperlukan.</p>
            </div>

            <!-- Filters -->
            <form action="{{ route('admin.parking.quotas') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <div class="relative flex-1 sm:w-60">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Sekolah..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <i class="fa-solid fa-search text-slate-400 absolute left-3 top-2.5 text-xs"></i>
                </div>

                <select name="level" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none">
                    <option value="">Semua Tingkat</option>
                    <option value="Mula" {{ request('level') == 'Mula' ? 'selected' : '' }}>PMR Mula (SD)</option>
                    <option value="Madya" {{ request('level') == 'Madya' ? 'selected' : '' }}>PMR Madya (SMP)</option>
                    <option value="Wira" {{ request('level') == 'Wira' ? 'selected' : '' }}>PMR Wira (SMA)</option>
                </select>

                @if(request()->hasAny(['search', 'level']))
                    <a href="{{ route('admin.parking.quotas') }}" class="px-2.5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Data Sekolah -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-700 font-black uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">Asal Sekolah & Kode</th>
                        <th class="p-3.5">Tingkat & Jumlah Regu</th>
                        <th class="p-3.5">Prioritas & Alokasi</th>
                        <th class="p-3.5 text-center">Kuota R4 (Mobil)</th>
                        <th class="p-3.5 text-center">Kuota R2 (Motor)</th>
                        <th class="p-3.5 text-center">Status Tiket Terbit</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($schools as $sch)
                        @php
                            $teamsCount = $sch->total_teams_count;
                            $r4Quota = $sch->allocated_quota_r4;
                            $r2Quota = $sch->allocated_quota_r2;
                            $r4Issued = $sch->issued_tickets_r4_count;
                            $r2Issued = $sch->issued_tickets_r2_count;
                            $isCustom = ($sch->parking_quota_mode === 'custom');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5">
                                <span class="font-black text-slate-900 block text-sm">{{ $sch->school_name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $sch->registration_code }}</span>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                        {{ $sch->level === 'Mula' ? 'bg-emerald-100 text-emerald-800' : ($sch->level === 'Madya' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $sch->level }}
                                    </span>
                                    <span class="font-bold text-slate-800">{{ $teamsCount }} Regu Terdaftar</span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                @if($sch->parking_priority_score)
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if($sch->parking_priority_criteria['is_top_priority'] ?? false)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                                    <i class="fa-solid fa-star text-amber-500"></i> Peringkat #{{ $sch->parking_priority_rank }} ({{ $sch->parking_priority_score }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    Peringkat #{{ $sch->parking_priority_rank }} ({{ $sch->parking_priority_score }})
                                                </span>
                                            @endif
                                        </div>
                                        @if(!empty($sch->parking_priority_criteria['assigned_location_name']))
                                            <span class="text-[10px] font-extrabold text-emerald-800 block truncate max-w-[160px]" title="{{ $sch->parking_priority_criteria['assigned_location_name'] }}">
                                                <i class="fa-solid fa-square-parking text-emerald-600"></i> {{ $sch->parking_priority_criteria['assigned_location_name'] }}
                                            </span>
                                        @endif
                                    </div>
                                @elseif($isCustom)
                                    <span class="text-[10px] font-black text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200 block truncate max-w-[150px]">
                                        <i class="fa-solid fa-star text-[9px]"></i> Kustom Admin
                                    </span>
                                    @if($sch->parking_notes)
                                        <span class="text-[9px] text-slate-400 italic block mt-0.5 truncate max-w-[160px]">{{ $sch->parking_notes }}</span>
                                    @endif
                                @else
                                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 block truncate max-w-[150px]">
                                        Belum Dikalkulasi
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="font-mono font-black text-xs text-slate-900">
                                    {{ $r4Issued }} / {{ $r4Quota }} Slot
                                </div>
                                <div class="w-24 mx-auto bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                    @php $pR4 = $r4Quota > 0 ? min(100, round(($r4Issued / $r4Quota) * 100)) : 0; @endphp
                                    <div class="h-full rounded-full {{ $pR4 >= 100 ? 'bg-rose-500' : ($pR4 >= 50 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ $pR4 }}%"></div>
                                </div>
                                <span class="text-[9px] text-slate-400 block mt-0.5">Sisa: {{ $sch->remaining_quota_r4 }} Slot</span>
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="font-mono font-black text-xs text-slate-900">
                                    {{ $r2Issued }} / {{ $r2Quota }} Slot
                                </div>
                                <div class="w-24 mx-auto bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                    @php $pR2 = $r2Quota > 0 ? min(100, round(($r2Issued / $r2Quota) * 100)) : 0; @endphp
                                    <div class="h-full rounded-full {{ $pR2 >= 100 ? 'bg-rose-500' : ($pR2 >= 50 ? 'bg-amber-500' : 'bg-purple-500') }}" style="width: {{ $pR2 }}%"></div>
                                </div>
                                <span class="text-[9px] text-slate-400 block mt-0.5">Sisa: {{ $sch->remaining_quota_r2 }} Slot</span>
                            </td>
                            <td class="p-3.5 text-center">
                                @if($r4Issued >= $r4Quota && $r2Issued >= $r2Quota && ($r4Quota + $r2Quota) > 0)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                        Kuota Penuh
                                    </span>
                                @elseif($r4Issued > 0 || $r2Issued > 0)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Sebagian Terbit
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Belum Ada Tiket
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="editSchoolQuota({{ json_encode($sch) }})" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-1 shadow-2xs" title="Edit / Kustom Kuota Sekolah">
                                        <i class="fa-solid fa-pen-to-square text-[10px] text-rose-400"></i> Edit Kuota
                                    </button>
                                    <a href="{{ route('admin.parking.index', ['search' => $sch->school_name]) }}" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition" title="Lihat Tiket Kendaraan Sekolah Ini">
                                        <i class="fa-solid fa-ticket"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-school-slash text-3xl mb-2 text-slate-300"></i>
                                <p class="font-bold text-slate-600">Tidak ada data sekolah terverifikasi ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $schools->links() }}
        </div>
    </div>

</div>

<!-- ================= MODAL EDIT KONFIGURASI GLOBAL & MATRIKS ================= -->
<div id="modalEditSetting" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-sliders text-rose-600"></i> Konfigurasi Aturan Kuota Parkir
            </h4>
            <button type="button" onclick="closeModal('modalEditSetting')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center">✕</button>
        </div>

        <form action="{{ route('admin.parking.quotas.settings') }}" method="POST" class="mt-4 space-y-5">
            @csrf
            
            <!-- Pilih Mode -->
            <div>
                <label class="text-xs font-bold text-slate-700 block mb-2">Pilih Mode Alokasi Kuota:</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="p-3.5 rounded-2xl border-2 border-slate-200 has-checked:border-rose-600 has-checked:bg-rose-50/50 flex items-start gap-3 cursor-pointer transition">
                        <input type="radio" name="mode" value="proportional_teams" {{ $setting->mode === 'proportional_teams' ? 'checked' : '' }} class="mt-0.5 text-rose-600 focus:ring-rose-500">
                        <div>
                            <span class="font-black text-xs text-slate-900 block">Proporsional Jumlah Regu</span>
                            <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">Otomatis dihitung dari akumulasi regu lomba yang terdaftar.</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-2xl border-2 border-slate-200 has-checked:border-blue-600 has-checked:bg-blue-50/50 flex items-start gap-3 cursor-pointer transition">
                        <input type="radio" name="mode" value="flat" {{ $setting->mode === 'flat' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="font-black text-xs text-slate-900 block">Mode Flat (Sama Rata)</span>
                            <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">Seluruh sekolah mendapatkan kuota tetap standar yang sama.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Flat Settings -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Pengaturan Default Mode Flat:</span>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Default Roda 4 (Mobil/Elf)</label>
                        <input type="number" name="flat_default_r4" min="0" value="{{ $setting->flat_default_r4 }}" class="w-full text-xs font-bold bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Default Roda 2 (Motor)</label>
                        <input type="number" name="flat_default_r2" min="0" value="{{ $setting->flat_default_r2 }}" class="w-full text-xs font-bold bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800">
                    </div>
                </div>
            </div>

            <!-- Matriks Tier Settings -->
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Edit Matriks Tier Proporsional:</span>
                <div class="space-y-3">
                    @foreach($rules as $idx => $r)
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 grid grid-cols-12 gap-2.5 items-center text-xs">
                            <input type="hidden" name="matrix_rules[{{ $idx }}][tier]" value="{{ $r['tier'] ?? ($idx + 1) }}">
                            <div class="col-span-12 sm:col-span-4">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Label Tier</label>
                                <input type="text" name="matrix_rules[{{ $idx }}][label]" value="{{ $r['label'] ?? "Tier " . ($idx + 1) }}" class="w-full text-xs font-bold bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-800">
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Min Regu</label>
                                <input type="number" name="matrix_rules[{{ $idx }}][min_teams]" value="{{ $r['min_teams'] ?? 1 }}" min="1" class="w-full text-xs font-bold bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-slate-800">
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Max Regu</label>
                                <input type="number" name="matrix_rules[{{ $idx }}][max_teams]" value="{{ $r['max_teams'] ?? 999 }}" min="1" class="w-full text-xs font-bold bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-slate-800">
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Kuota R4</label>
                                <input type="number" name="matrix_rules[{{ $idx }}][quota_r4]" value="{{ $r['quota_r4'] ?? 1 }}" min="0" class="w-full text-xs font-bold bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-blue-700">
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Kuota R2</label>
                                <input type="number" name="matrix_rules[{{ $idx }}][quota_r2]" value="{{ $r['quota_r2'] ?? 1 }}" min="0" class="w-full text-xs font-bold bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-purple-700">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditSetting')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md">Simpan Aturan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT KUOTA SEKOLAH (OVERRIDE CUSTOM) ================= -->
<div id="modalEditSchool" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">DISPENSASI / KUSTOM KUOTA</span>
                <h4 class="font-black text-slate-900 text-base" id="school_modal_name">SMAN 1 BOGOR</h4>
            </div>
            <button type="button" onclick="closeModal('modalEditSchool')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center">✕</button>
        </div>

        <form id="formEditSchoolQuota" action="" method="POST" class="mt-4 space-y-4">
            @csrf
            
            <div>
                <label class="text-xs font-bold text-slate-700 block mb-2">Pilihan Mode Kuota Sekolah:</label>
                <div class="space-y-2">
                    <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="parking_quota_mode" value="auto" id="school_mode_auto" onchange="toggleCustomInputs(false)" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="font-bold text-xs text-slate-900 block">Mode Otomatis (Standar Matriks)</span>
                            <span class="text-[10px] text-slate-500">Dihitung otomatis dari jumlah regu sekolah ini.</span>
                        </div>
                    </label>

                    <label class="p-3 rounded-xl border border-slate-200 hover:border-purple-500 flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="parking_quota_mode" value="custom" id="school_mode_custom" onchange="toggleCustomInputs(true)" class="text-purple-600 focus:ring-purple-500">
                        <div>
                            <span class="font-bold text-xs text-slate-900 block">Kustom Admin / Dispensasi Khusus</span>
                            <span class="text-[10px] text-slate-500">Tentukan angka jatah mobil dan motor secara manual.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Custom Input Fields -->
            <div id="customInputsWrapper" class="space-y-3 bg-purple-50/50 p-4 rounded-2xl border border-purple-200/60 hidden">
                <span class="text-[10px] font-black uppercase text-purple-700 tracking-wider block">Input Kuota Kustom:</span>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Kuota Roda 4 (Mobil)</label>
                        <input type="number" name="custom_quota_r4" id="input_custom_r4" min="0" value="2" class="w-full text-xs font-bold bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Kuota Roda 2 (Motor)</label>
                        <input type="number" name="custom_quota_r2" id="input_custom_r2" min="0" value="2" class="w-full text-xs font-bold bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Alasan Dispensasi / Catatan Panitia</label>
                    <textarea name="parking_notes" id="input_custom_notes" rows="2" placeholder="Contoh: Disetujui panitia membawa 1 Minibus Elf dan rombongan komite." class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditSchool')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md">Simpan Kuota Sekolah</button>
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

    function editSchoolQuota(school) {
        document.getElementById('school_modal_name').innerText = school.school_name;
        document.getElementById('formEditSchoolQuota').action = `/admin/parking/quotas/school/${school.id}`;
        
        const isCustom = (school.parking_quota_mode === 'custom');
        document.getElementById('school_mode_auto').checked = !isCustom;
        document.getElementById('school_mode_custom').checked = isCustom;

        document.getElementById('input_custom_r4').value = school.custom_quota_r4 !== null ? school.custom_quota_r4 : school.allocated_quota_r4;
        document.getElementById('input_custom_r2').value = school.custom_quota_r2 !== null ? school.custom_quota_r2 : school.allocated_quota_r2;
        document.getElementById('input_custom_notes').value = school.parking_notes || '';

        toggleCustomInputs(isCustom);
        openModal('modalEditSchool');
    }

    function toggleCustomInputs(show) {
        const wrapper = document.getElementById('customInputsWrapper');
        if (show) {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    }
</script>
@endsection
