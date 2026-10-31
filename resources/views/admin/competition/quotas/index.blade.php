@extends('layouts.admin')

@section('title', 'Pengaturan Kuota & Batas Regu Lomba - Sua Bhakti Berkarya IV')
@section('page_title', 'Pengaturan Kuota & Batas Regu Lomba')

@section('top_actions')
    <form action="{{ route('admin.competition-quotas.reset-defaults') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin me-reset kuota seluruh cabang lomba ke nilai standar (50 Regu, Maks. 2 Regu per Sekolah)?');">
        @csrf
        <button type="submit" class="bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-xl font-bold text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-rotate text-slate-500"></i> Reset Default
        </button>
    </form>
    <button type="button" onclick="document.getElementById('quota-form').submit();" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-extrabold text-xs transition flex items-center gap-2 shadow-md shadow-red-950/20 cursor-pointer">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan
    </button>
@endsection

@section('content')
<div class="space-y-6 pb-20">

    <!-- Flash message -->
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-400 text-emerald-900 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-2xl text-emerald-600 shrink-0"></i>
            <div class="font-bold text-sm">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Main Container Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200 space-y-6">

        <!-- Top Header & Context Description -->
        <div class="border-b border-slate-200 pb-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <i class="fa-solid fa-layer-group text-red-600"></i> Kontrol Alokasi Kuota Regu Lomba
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 max-w-3xl leading-relaxed">
                        Tentukan kapasitas maksimal total regu di setiap cabang lomba dan tetapkan batas pengiriman regu Putra (PA) & Putri (PI) untuk masing-masing sekolah. Sistem secara adaptif akan mengunci pendaftaran jika kuota habis atau membatasi pilihan jika sisa kuota tinggal 1 regu.
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="bg-slate-100 text-slate-700 text-xs px-3.5 py-2 rounded-xl font-bold border border-slate-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-check text-amber-500"></i> {{ $event?->name ?? 'Sua Bhakti Berkarya IV' }}
                    </span>
                    <span class="bg-emerald-50 text-emerald-800 border border-emerald-300 text-xs px-3.5 py-2 rounded-xl font-black flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Validasi Otomatis Aktif
                    </span>
                </div>
            </div>

            <!-- Global KPI Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-slate-50 p-4.5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Kapasitas Lomba</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 font-mono">{{ number_format($totalCapacity) }} <span class="text-xs font-bold text-slate-500 font-sans">Regu</span></div>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-cubes text-blue-500"></i> {{ $categories->count() }} Kategori Aktif
                    </div>
                </div>
                <div class="bg-slate-50 p-4.5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Regu Terdaftar</div>
                    <div class="text-2xl font-black text-emerald-600 mt-0.5 font-mono">{{ number_format($totalRegistered) }} <span class="text-xs font-bold text-slate-500 font-sans">Regu ({{ $percentageUsed }}%)</span></div>
                    <div class="text-[11px] text-emerald-600 mt-1 flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-arrow-trend-up"></i> Terus Bertambah
                    </div>
                </div>
                <div class="bg-slate-50 p-4.5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Sisa Kuota Tersedia</div>
                    <div class="text-2xl font-black text-amber-600 mt-0.5 font-mono">{{ number_format($totalRemaining) }} <span class="text-xs font-bold text-slate-500 font-sans">Slot</span></div>
                    <div class="text-[11px] text-amber-700 mt-1 flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-clock-rotate-left"></i> Real-time Live
                    </div>
                </div>
                <div class="bg-rose-50/60 p-4.5 rounded-2xl border border-rose-200 shadow-2xs">
                    <div class="text-[10px] font-black uppercase text-rose-500 tracking-wider">Cabang Penuh / Kritis</div>
                    <div class="text-2xl font-black text-rose-600 mt-0.5 font-mono">{{ $criticalOrFullCount }} <span class="text-xs font-bold text-rose-700 font-sans">Lomba</span></div>
                    <div class="text-[11px] text-rose-700 mt-1 flex items-center gap-1 font-bold">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $fullCount }} Penuh, {{ $criticalCount }} Sisa 1 Slot
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tingkat (PMR Mula, Madya, Wira) & Bulk Actions -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-slate-500">Filter Tingkat:</span>
                <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200">
                    <a href="{{ route('admin.competition-quotas.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-black transition {{ !$level ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Semua Tingkat
                    </a>
                    <a href="{{ route('admin.competition-quotas.index', ['level' => 'Mula']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Mula' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900' }}">
                        Mula (SD)
                    </a>
                    <a href="{{ route('admin.competition-quotas.index', ['level' => 'Madya']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Madya' ? 'bg-blue-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900' }}">
                        Madya (SMP)
                    </a>
                    <a href="{{ route('admin.competition-quotas.index', ['level' => 'Wira']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Wira' ? 'bg-amber-500 text-slate-950 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900' }}">
                        Wira (SMA)
                    </a>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-500 font-bold">Aksi Cepat:</span>
                <button type="button" onclick="bulkSetQuota(50)" class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-xl hover:bg-blue-100 transition shadow-2xs">
                    Set Default 50 Regu
                </button>
                <button type="button" onclick="bulkSetSchoolLimit(2)" class="text-xs font-bold text-slate-700 bg-white border border-slate-300 px-3 py-1.5 rounded-xl hover:bg-slate-100 transition shadow-2xs">
                    Set Maks 2 Regu
                </button>
            </div>
        </div>

        <!-- Main Form for Quota Settings -->
        <form id="quota-form" action="{{ route('admin.competition-quotas.update') }}" method="POST">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                            <tr>
                                <th class="py-3.5 px-4 w-12 text-center">NO</th>
                                <th class="py-3.5 px-4 min-w-[220px]">CABANG MATA LOMBA</th>
                                <th class="py-3.5 px-3 text-center w-24">TINGKAT</th>
                                <th class="py-3.5 px-3 text-center w-28">GENDER</th>
                                <th class="py-3.5 px-4 text-center w-36 bg-slate-800">
                                    KUOTA TOTAL<br>
                                    <span class="text-[9px] font-normal text-slate-300 tracking-normal">(Maksimal Regu)</span>
                                </th>
                                <th class="py-3.5 px-4 text-center min-w-[220px] bg-indigo-950">
                                    BATASAN PER SEKOLAH<br>
                                    <span class="text-[9px] font-normal text-indigo-300 tracking-normal">(Maks Regu per Pangkalan)</span>
                                </th>
                                <th class="py-3.5 px-4 text-center w-40">
                                    KETERISIAN SAAT INI<br>
                                    <span class="text-[9px] font-normal text-slate-400 tracking-normal">(Terdaftar / Kapasitas)</span>
                                </th>
                                <th class="py-3.5 px-3 text-center w-32">STATUS KUOTA</th>
                                <th class="py-3.5 px-3 text-center w-20">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">

                            @forelse($categories as $idx => $cat)
                                @php
                                    $maxQuota = $cat->getMaxQuota();
                                    $registered = $cat->teams_count ?? 0;
                                    $remaining = max(0, $maxQuota - $registered);
                                    $percentage = $maxQuota > 0 ? min(100, round(($registered / $maxQuota) * 100)) : 0;

                                    $isFull = $remaining <= 0;
                                    $isCritical = $remaining === 1;

                                    $rowBgClass = '';
                                    if ($isFull) {
                                        $rowBgClass = 'bg-rose-50/40 hover:bg-rose-50/60 border-l-4 border-l-rose-500';
                                    } elseif ($isCritical) {
                                        $rowBgClass = 'bg-amber-50/40 hover:bg-amber-50/60 border-l-4 border-l-amber-500';
                                    } else {
                                        $rowBgClass = 'hover:bg-slate-50/80';
                                    }

                                    $isPa = $cat->gender_category === 'Putra';
                                    $isPi = $cat->gender_category === 'Putri';
                                    $isMix = !$isPa && !$isPi;

                                    $valPa = $cat->max_teams_per_school_pa ?? 2;
                                    $valPi = $cat->max_teams_per_school_pi ?? 2;
                                    $valMix = $cat->max_teams_per_school_mix ?? 2;
                                @endphp
                                <tr class="{{ $rowBgClass }} transition" id="row-cat-{{ $cat->id }}">
                                    <td class="py-3 px-4 text-center font-mono font-bold text-slate-400">{{ $idx + 1 }}</td>
                                    
                                    <!-- Cabang Mata Lomba -->
                                    <td class="py-3 px-4">
                                        <div class="font-extrabold text-slate-900 flex items-center gap-2">
                                            @if($isFull)
                                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            @elseif($isCritical)
                                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            @endif
                                            <span>{{ $cat->name }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">KODE: {{ $cat->code ?? '-' }}</div>
                                    </td>

                                    <!-- Tingkat -->
                                    <td class="py-3 px-3 text-center">
                                        @if($cat->level === 'Mula')
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">Mula</span>
                                        @elseif($cat->level === 'Madya')
                                            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 font-extrabold text-[10px]">Madya</span>
                                        @elseif($cat->level === 'Wira')
                                            <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-extrabold text-[10px]">Wira</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">{{ $cat->level }}</span>
                                        @endif
                                    </td>

                                    <!-- Gender -->
                                    <td class="py-3 px-3 text-center">
                                        @if($isPa)
                                            <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 font-bold text-[10px]">
                                                <i class="fa-solid fa-mars mr-0.5"></i> Putra
                                            </span>
                                        @elseif($isPi)
                                            <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[10px]">
                                                <i class="fa-solid fa-venus mr-0.5"></i> Putri
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-bold text-[10px]">
                                                <i class="fa-solid fa-users mr-0.5"></i> Campuran
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Kuota Total (Maksimal Regu) -->
                                    <td class="py-3 px-4 text-center {{ $isFull ? 'bg-rose-50/60' : ($isCritical ? 'bg-amber-50/60' : 'bg-slate-50') }}">
                                        <div class="inline-flex items-center gap-1.5">
                                            <input type="number" name="quotas[{{ $cat->id }}][max_quota]" value="{{ $maxQuota }}" min="1" max="500" class="input-max-quota w-16 px-2 py-1 text-center font-mono font-black text-sm bg-white border {{ $isFull ? 'border-rose-400 focus:border-rose-500' : ($isCritical ? 'border-amber-400 focus:border-amber-500' : 'border-slate-300 focus:border-red-500') }} rounded-lg focus:outline-none text-slate-900 shadow-2xs">
                                            <span class="text-[11px] text-slate-500 font-bold">Regu</span>
                                        </div>
                                    </td>

                                    <!-- Batasan Per Sekolah (Maks Regu per Pangkalan) -->
                                    <td class="py-3 px-4 bg-indigo-50/40">
                                        @if($isPa)
                                            <div class="flex items-center justify-center gap-3">
                                                <div class="text-center">
                                                    <label class="text-[9px] uppercase font-bold text-slate-500 block mb-0.5">Maks Putra</label>
                                                    <select name="quotas[{{ $cat->id }}][max_teams_per_school_pa]" class="select-school-limit px-2 py-1 bg-white border border-slate-300 rounded-lg font-black text-xs text-blue-700 shadow-2xs cursor-pointer focus:outline-none focus:border-indigo-500">
                                                        @foreach([1, 2, 3, 4, 5] as $opt)
                                                            <option value="{{ $opt }}" {{ $valPa == $opt ? 'selected' : '' }}>{{ $opt }} Regu</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="text-slate-300 font-mono">&bull;</span>
                                                <div class="text-center">
                                                    <label class="text-[9px] uppercase font-bold text-slate-400 block mb-0.5">Maks Putri</label>
                                                    <span class="px-2 py-1 bg-slate-100 text-slate-400 rounded-lg text-xs font-bold block border border-slate-200 select-none">-</span>
                                                    <input type="hidden" name="quotas[{{ $cat->id }}][max_teams_per_school_pi]" value="0">
                                                </div>
                                            </div>
                                        @elseif($isPi)
                                            <div class="flex items-center justify-center gap-3">
                                                <div class="text-center">
                                                    <label class="text-[9px] uppercase font-bold text-slate-400 block mb-0.5">Maks Putra</label>
                                                    <span class="px-2 py-1 bg-slate-100 text-slate-400 rounded-lg text-xs font-bold block border border-slate-200 select-none">-</span>
                                                    <input type="hidden" name="quotas[{{ $cat->id }}][max_teams_per_school_pa]" value="0">
                                                </div>
                                                <span class="text-slate-300 font-mono">&bull;</span>
                                                <div class="text-center">
                                                    <label class="text-[9px] uppercase font-bold text-slate-500 block mb-0.5">Maks Putri</label>
                                                    <select name="quotas[{{ $cat->id }}][max_teams_per_school_pi]" class="select-school-limit px-2 py-1 bg-white border border-slate-300 rounded-lg font-black text-xs text-rose-700 shadow-2xs cursor-pointer focus:outline-none focus:border-indigo-500">
                                                        @foreach([1, 2, 3, 4, 5] as $opt)
                                                            <option value="{{ $opt }}" {{ $valPi == $opt ? 'selected' : '' }}>{{ $opt }} Regu</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="text-center">
                                                    <label class="text-[9px] uppercase font-bold text-slate-500 block mb-0.5">Maks per Sekolah</label>
                                                    <select name="quotas[{{ $cat->id }}][max_teams_per_school_mix]" class="select-school-limit px-2.5 py-1 bg-white border border-slate-300 rounded-lg font-black text-xs text-slate-800 shadow-2xs cursor-pointer focus:outline-none focus:border-indigo-500">
                                                        @foreach([1, 2, 3, 4, 5] as $opt)
                                                            <option value="{{ $opt }}" {{ $valMix == $opt ? 'selected' : '' }}>{{ $opt }} Regu</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Keterisian Saat Ini -->
                                    <td class="py-3 px-4 text-center">
                                        <div class="font-mono font-black text-xs {{ $isFull ? 'text-rose-700' : ($isCritical ? 'text-amber-700' : 'text-slate-900') }}">
                                            {{ $registered }} / {{ $maxQuota }} <span class="text-slate-400 font-normal">Regu</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full mt-1.5 overflow-hidden">
                                            @if($isFull)
                                                <div class="bg-rose-600 h-full rounded-full" style="width: 100%"></div>
                                            @elseif($isCritical)
                                                <div class="bg-amber-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                            @else
                                                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                            @endif
                                        </div>
                                        <div class="text-[10px] {{ $isFull ? 'text-rose-600 font-black' : ($isCritical ? 'text-amber-800 font-bold' : 'text-slate-400') }} mt-1">
                                            {{ $percentage }}% Terisi
                                        </div>
                                    </td>

                                    <!-- Status Kuota -->
                                    <td class="py-3 px-3 text-center">
                                        @if($isFull)
                                            <span class="px-2.5 py-1 rounded-full bg-rose-600 text-white font-black text-[10px] shadow-2xs inline-flex items-center gap-1">
                                                <i class="fa-solid fa-lock text-[9px]"></i> PENUH
                                            </span>
                                        @elseif($isCritical)
                                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 font-black text-[10px] border border-amber-400 inline-flex items-center gap-1 shadow-2xs animate-pulse">
                                                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Sisa 1 Regu!
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] border border-emerald-300 inline-flex items-center gap-1 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                                Sisa {{ $remaining }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi Simpan Baris Ini -->
                                    <td class="py-3 px-3 text-center">
                                        <button type="button" onclick="quickSaveRow({{ $cat->id }})" class="btn-quick-save w-8 h-8 rounded-lg {{ $isFull ? 'bg-rose-100 hover:bg-rose-200 text-rose-700' : ($isCritical ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700') }} flex items-center justify-center mx-auto transition shadow-2xs cursor-pointer" title="Simpan baris ini">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                                        Tidak ada cabang lomba yang ditemukan untuk tingkatan ini.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <!-- Table Footer Summary -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-700">Total {{ $categories->count() }} Cabang Lomba</span>
                        <span>&bull;</span>
                        <span>Menampilkan konfigurasi aktif periode pendaftaran Sua Bhakti Berkarya IV</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan
                        </button>
                    </div>
                </div>
            </div>

        </form>

        <!-- System Intelligence & Business Logic Architecture Callout -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl space-y-1.5 shadow-2xs">
                <div class="flex items-center gap-2 text-emerald-800 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    1. Kuota Tersedia (Normal)
                </div>
                <p class="text-[11px] text-emerald-900 leading-relaxed">
                    Jika kuota global masih tersedia &ge; 2 regu, pangkalan dapat mendaftarkan regu secara leluasa hingga batas maksimal regu per sekolah (misal: 2 regu Putra dan 2 regu Putri).
                </p>
            </div>

            <div class="bg-amber-50 border-2 border-amber-300 p-4 rounded-2xl space-y-1.5 shadow-2xs">
                <div class="flex items-center gap-2 text-amber-900 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
                    2. Kuota Kritis (Sisa 1 Slot)
                </div>
                <p class="text-[11px] text-amber-950 leading-relaxed">
                    Jika kuota global tersisa tinggal 1 regu, sistem secara adaptif <strong>hanya mengizinkan sekolah mendaftarkan 1 perwakilan regu saja</strong>, mencegah kelebihan kapasitas (overbooking).
                </p>
            </div>

            <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl space-y-1.5 shadow-2xs">
                <div class="flex items-center gap-2 text-rose-900 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-lock text-rose-600 text-sm"></i>
                    3. Kuota Habis (Auto-Lock)
                </div>
                <p class="text-[11px] text-rose-950 leading-relaxed">
                    Saat kapasitas total cabang lomba tercapai (misal: 50/50), cabang lomba otomatis terkunci (*disabled*) di formulir pendaftaran pangkalan dengan label "KUOTA PENUH / PENDAFTARAN DITUTUP".
                </p>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Bulk fill all quota inputs with a specific number
    function bulkSetQuota(val) {
        if (!confirm(`Terapkan kuota ${val} regu ke seluruh cabang lomba yang tampil di tabel ini?`)) return;
        document.querySelectorAll('.input-max-quota').forEach(inp => {
            inp.value = val;
        });
    }

    // Bulk set school limit
    function bulkSetSchoolLimit(val) {
        if (!confirm(`Terapkan batas maksimal ${val} regu per sekolah ke seluruh cabang lomba?`)) return;
        document.querySelectorAll('.select-school-limit').forEach(sel => {
            sel.value = val;
        });
    }

    // Quick save individual row via AJAX
    function quickSaveRow(catId) {
        const row = document.getElementById(`row-cat-${catId}`);
        if (!row) return;

        const maxQuota = row.querySelector(`input[name="quotas[${catId}][max_quota]"]`)?.value || 50;
        const paSel = row.querySelector(`select[name="quotas[${catId}][max_teams_per_school_pa]"]`);
        const piSel = row.querySelector(`select[name="quotas[${catId}][max_teams_per_school_pi]"]`);
        const mixSel = row.querySelector(`select[name="quotas[${catId}][max_teams_per_school_mix]"]`);

        const data = {
            _token: '{{ csrf_token() }}',
            max_quota: parseInt(maxQuota) || 50,
            max_teams_per_school_pa: paSel ? parseInt(paSel.value) : null,
            max_teams_per_school_pi: piSel ? parseInt(piSel.value) : null,
            max_teams_per_school_mix: mixSel ? parseInt(mixSel.value) : null,
        };

        const btn = row.querySelector('.btn-quick-save');
        const origIcon = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';
        btn.disabled = true;

        fetch(`/admin/competition-quotas/quick-update/${catId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            btn.innerHTML = '<i class="fa-solid fa-check-double text-xs text-emerald-600"></i>';
            btn.classList.add('bg-emerald-100', 'border-emerald-300');
            setTimeout(() => {
                btn.innerHTML = origIcon;
                btn.disabled = false;
                btn.classList.remove('bg-emerald-100', 'border-emerald-300');
            }, 1800);
        })
        .catch(err => {
            alert('Gagal menyimpan baris: ' + err.message);
            btn.innerHTML = origIcon;
            btn.disabled = false;
        });
    }
</script>
@endpush
