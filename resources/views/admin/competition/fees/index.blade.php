@extends('layouts.admin')

@section('title', 'Setup Biaya Pendaftaran Lomba')
@section('page_title', 'Setup Biaya Pendaftaran Cabang Lomba')

@section('top_actions')
    <a href="{{ route('lomba.register') }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Cek Form Pendaftaran
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Information & Bulk Setting Box -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-6 rounded-2xl shadow-sm border border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="space-y-1">
            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-slate-950">
                <i class="fa-solid fa-coins mr-1"></i> Setup Biaya Fleksibel
            </span>
            <h2 class="text-lg font-black text-white">Atur Biaya Pendaftaran Per Cabang Lomba</h2>
            <p class="text-xs text-slate-300 max-w-xl">
                Biaya yang diatur di bawah ini akan otomatis menjadi acuan tarif per regu pada <strong>Formulir Pendaftaran Peserta</strong>, kalkulasi total tagihan, e-Kwitansi resmi, dan kartu peserta.
            </p>
        </div>

        <!-- Quick Bulk Applier -->
        <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/15 w-full md:w-auto shrink-0">
            <div class="text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-2">Terapkan Cepat (Semua Input)</div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" id="bulk-fee-input" placeholder="150000" value="150000" class="w-36 pl-9 pr-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs font-bold text-white focus:outline-none focus:border-emerald-500 font-mono">
                </div>
                <button type="button" onclick="applyBulkFee()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Terapkan
                </button>
            </div>
        </div>
    </div>

    <!-- Level Filter Tabs -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.competition-fees.index') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition {{ !$level ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            Semua Tingkatan
        </a>
        <a href="{{ route('admin.competition-fees.index', ['level' => 'Mula']) }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition {{ $level == 'Mula' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-child-reaching mr-1"></i> PMR MULA (SD)
        </a>
        <a href="{{ route('admin.competition-fees.index', ['level' => 'Madya']) }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition {{ $level == 'Madya' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-user-group mr-1"></i> PMR MADYA (SMP)
        </a>
        <a href="{{ route('admin.competition-fees.index', ['level' => 'Wira']) }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition {{ $level == 'Wira' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-graduation-cap mr-1"></i> PMR WIRA (SMA)
        </a>
    </div>

    <!-- Main Setup Form -->
    <form action="{{ route('admin.competition-fees.update') }}" method="POST" id="fee-setup-form">
        @csrf

        @php
            $displayLevels = $level ? [$level] : ['Mula', 'Madya', 'Wira'];
        @endphp

        <div class="space-y-8">
            @foreach($displayLevels as $lvl)
                @php
                    $lvlCats = $categoriesByLevel->get($lvl, collect());
                @endphp

                @if($lvlCats->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <!-- Level Header -->
                        <div class="p-5 border-b border-slate-200 flex items-center justify-between {{ $lvl == 'Mula' ? 'bg-emerald-50/50' : ($lvl == 'Madya' ? 'bg-blue-50/50' : 'bg-amber-50/50') }}">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-white text-sm {{ $lvl == 'Mula' ? 'bg-emerald-600' : ($lvl == 'Madya' ? 'bg-blue-600' : 'bg-amber-600') }}">
                                    @if($lvl == 'Mula') <i class="fa-solid fa-child-reaching"></i>
                                    @elseif($lvl == 'Madya') <i class="fa-solid fa-user-group"></i>
                                    @else <i class="fa-solid fa-graduation-cap"></i> @endif
                                </span>
                                <div>
                                    <h3 class="font-black text-slate-900 text-base">Tingkat PMR {{ $lvl }} ({{ $lvl == 'Mula' ? 'SD/MI' : ($lvl == 'Madya' ? 'SMP/MTs' : 'SMA/SMK/MA') }})</h3>
                                    <div class="text-xs text-slate-500">{{ $lvlCats->count() }} Cabang Mata Lomba</div>
                                </div>
                            </div>

                            <button type="button" onclick="setGroupFee('{{ $lvl }}')" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs transition">
                                Samakan Biaya Jenjang Ini
                            </button>
                        </div>

                        <!-- Categories List Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-50 text-slate-700 uppercase font-black border-b border-slate-200">
                                    <tr>
                                        <th class="px-5 py-3.5 w-16 text-center">No</th>
                                        <th class="px-5 py-3.5 min-w-[200px]">Kode & Nama Cabang Lomba</th>
                                        <th class="px-5 py-3.5 w-40">Kategori Gender</th>
                                        <th class="px-5 py-3.5 w-48">Model Penilaian</th>
                                        <th class="px-5 py-3.5 w-60 text-right">Biaya Registrasi Per Regu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($lvlCats as $index => $cat)
                                        @php
                                            $isPa = $cat->gender_category === 'Putra';
                                            $isPi = $cat->gender_category === 'Putri';
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <!-- Number -->
                                            <td class="px-5 py-4 text-center font-bold text-slate-400">
                                                {{ $index + 1 }}
                                            </td>

                                            <!-- Code & Name -->
                                            <td class="px-5 py-4">
                                                <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                    {{ $cat->code }}
                                                </span>
                                                <div class="font-black text-slate-900 text-sm mt-1">
                                                    {{ $cat->name }}
                                                </div>
                                            </td>

                                            <!-- Gender Category Badge -->
                                            <td class="px-5 py-4">
                                                @if($isPa)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">
                                                        <i class="fa-solid fa-mars text-blue-600"></i> Putra
                                                    </span>
                                                @elseif($isPi)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-200">
                                                        <i class="fa-solid fa-venus text-rose-600"></i> Putri
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                                        <i class="fa-solid fa-users text-slate-500"></i> Umum / Campuran
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Scoring Type -->
                                            <td class="px-5 py-4 text-slate-500 font-medium">
                                                {{ ucwords(str_replace('_', ' ', $cat->scoring_type)) }}
                                            </td>

                                            <!-- Fee Input -->
                                            <td class="px-5 py-4 text-right">
                                                <div class="inline-flex items-center relative">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                    <input type="number" step="1000" min="0" name="fees[{{ $cat->id }}]" value="{{ intval($cat->registration_fee ?: 150000) }}" data-level="{{ $lvl }}" class="fee-input w-44 pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-black text-slate-900 font-mono text-right focus:bg-white focus:outline-none focus:border-red-500 shadow-2xs">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Sticky Submit Footer -->
        <div class="mt-8 sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                *Perubahan biaya akan langsung berlaku untuk seluruh pendaftaran baru berikutnya.
            </div>
            <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-extrabold text-xs shadow-lg shadow-red-900/20 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Biaya
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function applyBulkFee() {
        const val = document.getElementById('bulk-fee-input').value;
        if (!val || val < 0) {
            alert('Silakan masukkan nominal biaya yang valid.');
            return;
        }
        if (confirm(`Terapkan biaya Rp ${parseInt(val).toLocaleString('id-ID')} ke seluruh cabang lomba di layar?`)) {
            document.querySelectorAll('.fee-input').forEach(input => {
                input.value = val;
            });
        }
    }

    function setGroupFee(level) {
        const nominal = prompt(`Masukkan nominal biaya (Rp) untuk semua cabang PMR ${level}:`, "150000");
        if (nominal !== null && nominal !== '') {
            const val = parseInt(nominal);
            if (!isNaN(val) && val >= 0) {
                document.querySelectorAll(`.fee-input[data-level="${level}"]`).forEach(input => {
                    input.value = val;
                });
            } else {
                alert('Nominal tidak valid.');
            }
        }
    }
</script>
@endpush
