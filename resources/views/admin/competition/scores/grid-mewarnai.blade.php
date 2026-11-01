@extends('layouts.admin')

@section('title', 'Grid Penilaian: ' . $category->name . ' (' . $category->level . ')')
@section('page_title', 'Grid Nilai Lomba Mewarnai Tingkat Mula')

@section('top_actions')
    <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
        <i class="fa-solid fa-table-list text-pink-500"></i> Lembar Skor Utama
    </a>
    <a href="{{ route('admin.competition-scores.export-excel', $category->id) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
        <i class="fa-solid fa-file-excel"></i> Export Excel (.xlsx)
    </a>
    <a href="{{ route('admin.competition-scores.index', ['level' => $category->level]) }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-300 transition">
        &larr; Cabang Lomba
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Banner Notifikasi Berhasil Simpan --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="text-xs bg-emerald-600 text-white font-extrabold px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition">
                Lihat di Lembar Skor Utama &rarr;
            </a>
        </div>
    @endif

    {{-- Header Banner Card --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-pink-950 p-6 rounded-3xl shadow-xl text-white flex flex-col md:flex-row justify-between md:items-center gap-4 border border-slate-700">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-pink-500 via-rose-500 to-amber-400 p-0.5 shadow-lg flex items-center justify-center">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <i class="fa-solid fa-palette text-pink-400 text-2xl"></i>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-pink-500/20 text-pink-300 border border-pink-500/30">
                        {{ $category->code ?? 'MEWARNAI-MULA' }}
                    </span>
                    <span class="text-xs text-slate-300 font-bold">PMR Tingkat {{ $category->level }} (SD/MI) &bull; Kategori {{ $category->gender_category }}</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    {{ $category->name }}
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    Grid Penilaian format kertas lembar juri &bull; Total nilai terakumulasi otomatis ke Lembar Skor Utama (Maks. 100 Poin).
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="px-4 py-2.5 rounded-2xl bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-table-list"></i> Buka Lembar Skor Utama
            </a>
        </div>
    </div>

    {{-- Form Utama Grid Mewarnai --}}
    <form action="{{ route('admin.competition-scores.save', $category->id) }}" method="POST" id="formGridMewarnai">
        @csrf
        <input type="hidden" name="round_name" value="{{ $round }}">
        <input type="hidden" name="redirect_to" id="redirectInput" value="grid">

        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 relative">
            
            {{-- Header Kertas Lembar Penilaian Juri --}}
            <div class="text-center border-b-2 border-slate-800 pb-4 mb-5">
                <div class="inline-flex items-center gap-2 text-pink-600 font-black text-xs uppercase tracking-widest mb-1">
                    <i class="fa-solid fa-award"></i> LEMBAR PENILAIAN RESMI DEWAN JURI
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    GRID NILAI LOMBA MEWARNAI TINGKAT MULA
                </h2>
                <div class="text-xs sm:text-sm font-extrabold text-slate-700 uppercase tracking-widest mt-0.5">
                    SUA BHAKTI BERKARYA IV
                </div>
            </div>

            {{-- 3 Kotak Petunjuk Bobot Kriteria --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3.5 text-center">
                    <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block">Kriteria 1</span>
                    <div class="text-xs sm:text-sm font-black text-slate-800 mt-0.5">Kerapihan dan Kebersihan</div>
                    <span class="text-[11px] font-mono font-bold text-amber-900 bg-amber-200/80 px-2.5 py-0.5 rounded-full inline-block mt-1.5 shadow-2xs">
                        Rentang Bobot: 1 - 40 Poin
                    </span>
                </div>
                <div class="bg-sky-50 border border-sky-200 rounded-2xl p-3.5 text-center">
                    <span class="text-[10px] font-bold text-sky-700 uppercase tracking-wider block">Kriteria 2</span>
                    <div class="text-xs sm:text-sm font-black text-slate-800 mt-0.5">Kombinasi Warna dan Estetika</div>
                    <span class="text-[11px] font-mono font-bold text-sky-900 bg-sky-200/80 px-2.5 py-0.5 rounded-full inline-block mt-1.5 shadow-2xs">
                        Rentang Bobot: 1 - 40 Poin
                    </span>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-3.5 text-center">
                    <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Kriteria 3</span>
                    <div class="text-xs sm:text-sm font-black text-slate-800 mt-0.5">Kreativitas</div>
                    <span class="text-[11px] font-mono font-bold text-emerald-900 bg-emerald-200/80 px-2.5 py-0.5 rounded-full inline-block mt-1.5 shadow-2xs">
                        Rentang Bobot: 1 - 20 Poin
                    </span>
                </div>
            </div>

            {{-- Tabel Grid Penilaian Mewarnai Persis Format PDF Asli --}}
            <div class="rounded-2xl border-2 border-slate-800 mb-6 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse min-w-[800px]" id="gridMewarnaiTable">
                        <thead>
                            <tr class="bg-slate-900 text-white text-xs uppercase font-black tracking-wider">
                                <th rowspan="2" class="py-3 px-3 w-20 border-r border-slate-700">No. Urut Peserta</th>
                                <th rowspan="2" class="py-3 px-4 text-left min-w-[240px] border-r border-slate-700">Nama Sekolah & Peserta</th>
                                <th colspan="3" class="py-2.5 px-3 bg-slate-800 border-r border-slate-700">KRITERIA PENILAIAN</th>
                                <th rowspan="2" class="py-3 px-4 w-32 bg-pink-600 text-white font-black border-r border-pink-700">
                                    TOTAL<br><span class="text-[10px] font-normal opacity-90">(Maks 100)</span>
                                </th>
                                <th rowspan="2" class="py-3 px-3 w-28 bg-slate-950 text-amber-300 font-black">
                                    Ranking
                                </th>
                            </tr>
                            <tr class="bg-slate-800 text-[10px] font-bold text-slate-200 uppercase tracking-wider border-t border-slate-700">
                                <th class="py-2.5 px-3 border-r border-slate-700 bg-amber-950/70 text-amber-200">
                                    KERAPIHAN & KEBERSIHAN<br><span class="text-[9px] font-mono text-amber-300">(1 - 40)</span>
                                </th>
                                <th class="py-2.5 px-3 border-r border-slate-700 bg-sky-950/70 text-sky-200">
                                    KOMBINASI WARNA & ESTETIKA<br><span class="text-[9px] font-mono text-sky-300">(1 - 40)</span>
                                </th>
                                <th class="py-2.5 px-3 border-r border-slate-700 bg-emerald-950/70 text-emerald-200">
                                    KREATIVITAS<br><span class="text-[9px] font-mono text-emerald-300">(1 - 20)</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-200 font-semibold text-slate-800">
                            @forelse($teams as $idx => $team)
                                @php
                                    $score = $scores->get($team->id);
                                    $details = is_array($score?->score_details) ? $score->score_details : (json_decode($score?->score_details ?? '', true) ?? []);
                                    $v1 = $details['criteria_1'] ?? null;
                                    $v2 = $details['criteria_2'] ?? null;
                                    $v3 = $details['criteria_3'] ?? null;
                                    $finalVal = $score?->final_score ?? null;
                                    $rank = $score?->rank ?? null;
                                    $reg = $team->registration;
                                    $school = $reg->school_name ?? ($team->team_label ?? 'Sekolah Tidak Diketahui');
                                    $participantNo = sprintf('%02d', $team->order_number ?? ($idx + 1));
                                @endphp

                                <tr class="team-row hover:bg-pink-50/30 transition {{ $idx % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }}" data-team-id="{{ $team->id }}">
                                    {{-- No Peserta --}}
                                    <td class="py-3 px-3 font-mono font-black text-slate-900 border-r border-slate-200">
                                        {{ $participantNo }}
                                    </td>

                                    {{-- Nama Sekolah & Peserta --}}
                                    <td class="py-3 px-4 text-left border-r border-slate-200">
                                        <div class="font-black text-slate-900 text-xs sm:text-sm">
                                            {{ $school }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                                            {{ $team->team_name }}
                                        </div>
                                    </td>

                                    {{-- Kriteria 1: Kerapihan dan Kebersihan (1 - 40) --}}
                                    <td class="p-1.5 border-r border-slate-200">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               max="40" 
                                               name="scores[{{ $team->id }}][criteria_1]" 
                                               value="{{ $v1 }}" 
                                               placeholder="1 - 40" 
                                               oninput="validateInput(this, 40); calculateRowScore(this)" 
                                               class="input-c1 w-full text-center py-2 px-1 rounded-xl border border-amber-300 bg-amber-50/80 font-mono font-black text-xs sm:text-sm text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-2xs">
                                    </td>

                                    {{-- Kriteria 2: Kombinasi Warna dan Estetika (1 - 40) --}}
                                    <td class="p-1.5 border-r border-slate-200">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               max="40" 
                                               name="scores[{{ $team->id }}][criteria_2]" 
                                               value="{{ $v2 }}" 
                                               placeholder="1 - 40" 
                                               oninput="validateInput(this, 40); calculateRowScore(this)" 
                                               class="input-c2 w-full text-center py-2 px-1 rounded-xl border border-sky-300 bg-sky-50/80 font-mono font-black text-xs sm:text-sm text-sky-900 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-2xs">
                                    </td>

                                    {{-- Kriteria 3: Kreativitas (1 - 20) --}}
                                    <td class="p-1.5 border-r border-slate-200">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               max="20" 
                                               name="scores[{{ $team->id }}][criteria_3]" 
                                               value="{{ $v3 }}" 
                                               placeholder="1 - 20" 
                                               oninput="validateInput(this, 20); calculateRowScore(this)" 
                                               class="input-c3 w-full text-center py-2 px-1 rounded-xl border border-emerald-300 bg-emerald-50/80 font-mono font-black text-xs sm:text-sm text-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                    </td>

                                    {{-- TOTAL (Otomatis Live Auto-Sum, Kolom Pink) --}}
                                    <td class="py-2.5 px-3 bg-pink-100/90 font-mono font-black text-sm sm:text-base text-pink-700 border-r border-pink-200 cell-total">
                                        <span class="total-text">{{ $finalVal !== null && $finalVal > 0 ? number_format($finalVal, 2, ',', '.') : '-' }}</span>
                                        <input type="hidden" name="scores[{{ $team->id }}][manual_final_score]" class="input-final" value="{{ $finalVal }}">
                                    </td>

                                    {{-- Ranking Live --}}
                                    <td class="py-2.5 px-2 text-center cell-rank">
                                        @if($rank == 1)
                                            <span class="w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-sm" title="Juara 1">1</span>
                                        @elseif($rank == 2)
                                            <span class="w-7 h-7 rounded-full bg-slate-300 text-slate-800 font-black text-xs inline-flex items-center justify-center shadow-sm" title="Juara 2">2</span>
                                        @elseif($rank == 3)
                                            <span class="w-7 h-7 rounded-full bg-amber-700 text-white font-black text-xs inline-flex items-center justify-center shadow-sm" title="Juara 3">3</span>
                                        @elseif($rank)
                                            <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-black text-xs inline-flex items-center justify-center">{{ $rank }}</span>
                                        @else
                                            <span class="text-slate-300 font-mono">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <i class="fa-solid fa-users text-4xl mb-2 text-slate-300"></i>
                                        <div class="font-bold text-sm text-slate-600">Belum ada peserta terdaftar pada Lomba Mewarnai Tingkat Mula.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer Aksi Simpan & Keterangan --}}
            <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-200">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <i class="fa-solid fa-calculator text-pink-600"></i>
                    <span>Sistem otomatis menghitung total nilai: <strong>Kerapihan (40) + Kombinasi (40) + Kreativitas (20) = Maks 100</strong></span>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" onclick="setRedirect('grid')" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-pink-600/30 flex items-center gap-2 transition">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Nilai Grid
                    </button>
                    
                    <button type="submit" onclick="setRedirect('input')" class="px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition flex items-center gap-2">
                        <i class="fa-solid fa-table-list"></i> Simpan & Ke Skor Utama
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>

{{-- Script Live Auto-Sum & Real-Time Ranking --}}
<script>
    function validateInput(input, maxVal) {
        let val = parseFloat(input.value);
        if (val > maxVal) {
            input.value = maxVal;
        } else if (val < 0) {
            input.value = 0;
        }
    }

    function calculateRowScore(inputElement) {
        const row = inputElement.closest('.team-row');
        if (!row) return;

        const c1 = parseFloat(row.querySelector('.input-c1')?.value) || 0;
        const c2 = parseFloat(row.querySelector('.input-c2')?.value) || 0;
        const c3 = parseFloat(row.querySelector('.input-c3')?.value) || 0;

        const hasValue = (row.querySelector('.input-c1')?.value !== '' || 
                          row.querySelector('.input-c2')?.value !== '' || 
                          row.querySelector('.input-c3')?.value !== '');

        const totalSpan = row.querySelector('.total-text');
        const finalInput = row.querySelector('.input-final');

        if (hasValue) {
            const total = c1 + c2 + c3;
            if (totalSpan) totalSpan.innerText = total.toFixed(2);
            if (finalInput) finalInput.value = total.toFixed(2);
        } else {
            if (totalSpan) totalSpan.innerText = '-';
            if (finalInput) finalInput.value = '';
        }

        recalculateLiveRanks();
    }

    function recalculateLiveRanks() {
        const rows = Array.from(document.querySelectorAll('.team-row'));
        const rowData = rows.map(row => {
            const finalInput = row.querySelector('.input-final');
            const val = parseFloat(finalInput?.value);
            const hasScore = !isNaN(val) && val > 0;
            return {
                row: row,
                score: hasScore ? val : -1,
                hasScore: hasScore
            };
        });

        // Urutkan nilai tertinggi ke terendah
        const scoredRows = rowData.filter(d => d.hasScore);
        scoredRows.sort((a, b) => b.score - a.score);

        // Tampilkan badge rank
        rowData.forEach(d => {
            const rankCell = d.row.querySelector('.cell-rank');
            if (!rankCell) return;

            if (d.hasScore) {
                const rank = scoredRows.indexOf(d) + 1;
                if (rank === 1) {
                    rankCell.innerHTML = `<span class="w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-sm" title="Juara 1">1</span>`;
                } else if (rank === 2) {
                    rankCell.innerHTML = `<span class="w-7 h-7 rounded-full bg-slate-300 text-slate-800 font-black text-xs inline-flex items-center justify-center shadow-sm" title="Juara 2">2</span>`;
                } else if (rank === 3) {
                    rankCell.innerHTML = `<span class="w-7 h-7 rounded-full bg-amber-700 text-white font-black text-xs inline-flex items-center justify-center shadow-sm" title="Juara 3">3</span>`;
                } else {
                    rankCell.innerHTML = `<span class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-black text-xs inline-flex items-center justify-center">${rank}</span>`;
                }
            } else {
                rankCell.innerHTML = `<span class="text-slate-300 font-mono">-</span>`;
            }
        });
    }

    function setRedirect(dest) {
        const redir = document.getElementById('redirectInput');
        if (redir) {
            redir.value = dest;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        recalculateLiveRanks();
    });
</script>
@endsection
