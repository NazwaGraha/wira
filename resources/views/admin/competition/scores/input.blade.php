@extends('layouts.admin')

@section('title', 'Input Nilai: ' . $category->display_name)
@section('page_title', 'Lembar Penilaian Juri: ' . $category->display_name)

@section('top_actions')
    <a href="{{ route('admin.competition-scores.index', ['level' => $category->level]) }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
        &larr; Kembali ke Cabang
    </a>
    <a href="{{ route('lomba.scoreboard', ['level' => $category->level, 'category_id' => $category->id]) }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5">
        <i class="fa-solid fa-tv"></i> Papan Skor Publik
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Category Header Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-2.5 py-0.5 rounded-md">{{ $category->code ?? 'LOMBA' }}</span>
                <span class="text-xs font-bold text-slate-500">PMR {{ $category->level }} &bull; Kategori: {{ $category->gender_category }}</span>
            </div>
            <h2 class="text-2xl font-black text-slate-900 mt-1">{{ $category->name }}</h2>
            <div class="text-xs text-slate-500 mt-0.5">
                Model Penilaian: <strong>{{ ucwords(str_replace('_', ' ', $category->scoring_type)) }}</strong> &bull; Total Peserta: <strong>{{ $teams->count() }} Tim</strong>
            </div>
        </div>

        <!-- Quick Add Participant OTS / Walk-in -->
        <div>
            <button onclick="document.getElementById('quick-add-modal').classList.toggle('hidden')" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Tambah Tim Walk-in (OTS)
            </button>
        </div>
    </div>

    <!-- Quick Add Modal (Hidden by default) -->
    <div id="quick-add-modal" class="hidden bg-slate-900/40 fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-slate-200">
            <h3 class="font-black text-base text-slate-900 mb-2">Tambah Peserta Baru (OTS)</h3>
            <p class="text-xs text-slate-500 mb-4">Tambahkan peserta yang mendaftar langsung di lokasi lomba.</p>
            
            <form action="{{ route('admin.competition-scores.quick-add-team', $category->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Sekolah</label>
                    <input type="text" name="school_name" required placeholder="Contoh: SMAN 1 CIAWI" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl uppercase font-semibold">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. Urut (Opsional)</label>
                        <input type="text" name="order_number" placeholder="Contoh: 1.1 atau 7" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Label Regu</label>
                        <input type="text" name="team_label" placeholder="Contoh: (A) atau (B)" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>
                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('quick-add-modal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold text-xs rounded-xl">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Round Selector if category has multiple rounds -->
    @if($category->has_rounds)
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-3">
            <span class="text-xs font-bold text-slate-500 uppercase">Pilih Babak Lomba:</span>
            <div class="flex flex-wrap gap-2">
                @foreach(['Babak 1 - Penyisihan', 'Babak 2 - Semi Final', 'Babak 3 - Final'] as $rName)
                    <a href="{{ route('admin.competition-scores.input', ['category' => $category->id, 'round' => $rName]) }}" class="px-4 py-1.5 rounded-xl text-xs font-bold transition {{ $round == $rName ? 'bg-red-600 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $rName }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main Scoring Input Table Form -->
    <form action="{{ route('admin.competition-scores.save', $category->id) }}" method="POST">
        @csrf
        <input type="hidden" name="round_name" value="{{ $round }}">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <div class="font-extrabold text-sm text-slate-800">
                    Lembar Skor: {{ $round }}
                </div>
                <div class="text-xs text-slate-500">
                    *Sistem otomatis menghitung Nilai Akhir dan mengurutkan Peringkat Juara setelah tombol simpan ditekan.
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-100 text-slate-800 uppercase font-black border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 w-16 text-center">Rank</th>
                            <th class="px-4 py-3 w-20">No. Urut</th>
                            <th class="px-4 py-3 min-w-[200px]">Nama Sekolah / Regu</th>

                            @if($category->scoring_type == 'written_practical_time')
                                <!-- LPP Madya/Wira -->
                                <th class="px-4 py-3 w-28">Nilai Tertulis</th>
                                <th class="px-4 py-3 w-28">Waktu Tertulis</th>
                                <th class="px-4 py-3 w-28">Nilai Praktik</th>
                                <th class="px-4 py-3 w-28">Waktu Praktik</th>
                            @elseif($category->scoring_type == 'multi_criteria')
                                <!-- Mading / Mewarnai -->
                                <th class="px-4 py-3 w-28">Kriteria 1</th>
                                <th class="px-4 py-3 w-28">Kriteria 2</th>
                                @if(isset($category->criteria_schema[2]))
                                    <th class="px-4 py-3 w-28">Kriteria 3</th>
                                @endif
                            @elseif($category->scoring_type == 'social_engagement')
                                <!-- PMR Favorite -->
                                <th class="px-4 py-3 w-28">Jumlah Likes</th>
                                <th class="px-4 py-3 w-28">Jumlah Stickers</th>
                            @else
                                <!-- Standard Time / Quiz -->
                                <th class="px-4 py-3 w-32">Nilai / Poin</th>
                                <th class="px-4 py-3 w-32">Waktu Tempuh</th>
                            @endif

                            <th class="px-4 py-3 w-28 text-right">Nilai Akhir</th>
                            <th class="px-4 py-3 w-24 text-center">Diskualifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($teams as $index => $team)
                            @php 
                                $score = $scores->get($team->id);
                                $details = $score?->score_details ?? [];
                            @endphp
                            <tr class="hover:bg-slate-50 transition {{ $score?->is_disqualified ? 'bg-slate-900 text-slate-400' : ($score?->rank == 1 ? 'bg-purple-50/60 font-bold' : '') }}">
                                <!-- Rank Badge -->
                                <td class="px-4 py-3 text-center font-bold">
                                    @if($score?->is_disqualified)
                                        <span class="text-rose-500 font-black text-[10px]">DSQ</span>
                                    @elseif($score?->rank == 1)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-600 text-white font-black text-xs">1</span>
                                    @elseif($score?->rank == 2)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white font-black text-xs">2</span>
                                    @elseif($score?->rank == 3)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-400 text-white font-black text-xs">3</span>
                                    @else
                                        <span class="text-slate-400">{{ $score?->rank ?: '-' }}</span>
                                    @endif
                                </td>

                                <!-- Order Number -->
                                <td class="px-4 py-3 font-mono">
                                    {{ $team->order_number ?: ($index + 1) }}
                                </td>

                                <!-- Team Name -->
                                <td class="px-4 py-3">
                                    <div class="font-extrabold text-slate-900 text-xs uppercase">{{ $team->team_name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $team->registration?->advisor_name }}</div>
                                </td>

                                <!-- Dynamic Inputs -->
                                @if($category->scoring_type == 'written_practical_time')
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][written_score]" value="{{ $details['written_score'] ?? '' }}" placeholder="0 - 100" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="scores[{{ $team->id }}][written_time]" value="{{ $details['written_time'] ?? '' }}" placeholder="25.02" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][practical_score]" value="{{ $details['practical_score'] ?? '' }}" placeholder="0 - 1000" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="scores[{{ $team->id }}][practical_time]" value="{{ $details['practical_time'] ?? '' }}" placeholder="6.30" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                @elseif($category->scoring_type == 'multi_criteria')
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][criteria_1]" value="{{ $details['criteria_1'] ?? '' }}" placeholder="{{ $category->criteria_schema[0] ?? 'Kriteria 1' }}" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][criteria_2]" value="{{ $details['criteria_2'] ?? '' }}" placeholder="{{ $category->criteria_schema[1] ?? 'Kriteria 2' }}" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                    @if(isset($category->criteria_schema[2]))
                                        <td class="px-4 py-3">
                                            <input type="number" step="0.01" name="scores[{{ $team->id }}][criteria_3]" value="{{ $details['criteria_3'] ?? '' }}" placeholder="{{ $category->criteria_schema[2] ?? 'Kriteria 3' }}" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                        </td>
                                    @endif
                                @elseif($category->scoring_type == 'social_engagement')
                                    <td class="px-4 py-3">
                                        <input type="number" name="scores[{{ $team->id }}][likes]" value="{{ $details['likes'] ?? '' }}" placeholder="Likes" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="scores[{{ $team->id }}][stickers]" value="{{ $details['stickers'] ?? '' }}" placeholder="Stickers" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                @else
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][score]" value="{{ $details['score'] ?? '' }}" placeholder="Nilai Murni" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="scores[{{ $team->id }}][time_recorded]" value="{{ $score?->time_recorded ?? ($details['time_recorded'] ?? '') }}" placeholder="00:04:42" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                @endif

                                <!-- Manual Final Score Override (Optional) -->
                                <td class="px-4 py-3 text-right">
                                    <input type="number" step="0.01" name="scores[{{ $team->id }}][manual_final_score]" value="{{ $score?->final_score ?? '' }}" placeholder="Auto / Manual" class="w-24 p-2 bg-white border border-slate-300 font-mono font-black text-right text-xs rounded-lg text-red-600 focus:outline-none focus:border-red-500">
                                </td>

                                <!-- Disqualification Checkbox -->
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" name="scores[{{ $team->id }}][is_disqualified]" value="1" {{ $score?->is_disqualified ? 'checked' : '' }} class="w-4 h-4 text-red-600 rounded border-slate-300 focus:ring-red-500 cursor-pointer">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-10 text-slate-400">
                                    Belum ada peserta yang terdaftar di cabang lomba ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-xs text-slate-500">
                    Klik <strong>Simpan & Hitung Peringkat</strong> untuk memperbarui skor secara *real-time* ke proyektor dan halaman publik.
                </div>
                <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs px-8 py-3 rounded-xl shadow-lg shadow-red-900/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan & Hitung Peringkat Otomatis
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
