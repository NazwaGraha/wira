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
                <span class="font-mono text-sm sm:text-base font-black text-slate-900 bg-slate-100 border-2 border-slate-300 px-3 py-1 rounded-xl shadow-xs uppercase tracking-wider">{{ $category->code ?? 'LOMBA' }}</span>
                <span class="text-xs sm:text-sm font-bold text-slate-500">PMR {{ $category->level }}</span>
                @if($category->gender_category === 'Putra')
                    <span class="text-xs font-black text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-md inline-flex items-center gap-1">
                        <i class="fa-solid fa-mars text-blue-600"></i> Kategori Putra
                    </span>
                @elseif($category->gender_category === 'Putri')
                    <span class="text-xs font-black text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-md inline-flex items-center gap-1">
                        <i class="fa-solid fa-venus text-rose-600"></i> Kategori Putri
                    </span>
                @else
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md inline-flex items-center gap-1">
                        <i class="fa-solid fa-users text-slate-500"></i> Kategori Umum
                    </span>
                @endif
            </div>
            <h2 class="text-2xl font-black text-slate-900 mt-1">{{ $category->name }} ({{ $category->gender_category }})</h2>
            <div class="text-xs text-slate-500 mt-0.5">
                Model Penilaian: <strong>{{ ucwords(str_replace('_', ' ', $category->scoring_type)) }}</strong> &bull; Total Peserta Terverifikasi: <strong>{{ $teams->count() }} Tim</strong>
            </div>
        </div>

        <!-- Quick Actions: Reset Form, Reset DB, Add OTS -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="clearAllFormInputs()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-3.5 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-sm" title="Kosongkan seluruh isian form di layar">
                <i class="fa-solid fa-arrows-rotate text-blue-600"></i> Kosongkan Form
            </button>
            <button type="button" onclick="confirmResetDatabase()" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs px-3.5 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-sm" title="Hapus semua nilai cabang lomba ini yang tersimpan di database">
                <i class="fa-solid fa-trash-can text-rose-600"></i> Reset Database
            </button>
            <button type="button" onclick="document.getElementById('quick-add-modal').classList.remove('hidden')" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-plus"></i> Tambah Tim
            </button>
        </div>
    </div>

    <!-- Hidden Form for Resetting Database Scores -->
    <form id="reset-database-form" action="{{ route('admin.competition-scores.reset', $category->id) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="round_name" value="{{ $round }}">
    </form>

    <!-- Hidden Form for Deleting a Team from Score Sheet -->
    <form id="delete-team-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Quick Add Modal (Populated from Verified Registrations) -->
    <div id="quick-add-modal" class="hidden bg-slate-900/60 fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Modal Header -->
            <div class="p-5 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-slate-950">
                        PMR {{ $category->level }} &bull; {{ $category->gender_category }}
                    </span>
                    <h3 class="font-black text-base text-white mt-1">Tambah Tim ke Lembar Penilaian</h3>
                    <p class="text-xs text-slate-400">{{ $category->name }}</p>
                </div>
                <button type="button" onclick="document.getElementById('quick-add-modal').classList.add('hidden')" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Tab Selector -->
            <div class="flex border-b border-slate-200 bg-slate-50 px-5 pt-3 gap-3">
                <button type="button" onclick="switchAddTab('verified')" id="tab-btn-verified" class="pb-2.5 text-xs font-black border-b-2 border-red-600 text-red-600 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-clipboard-check"></i> Dari Daftar Terverifikasi
                </button>
                <button type="button" onclick="switchAddTab('ots')" id="tab-btn-ots" class="pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-user-plus"></i> Input Manual (OTS)
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto flex-grow space-y-4">
                <!-- Section 1: Verified Schools (Default) -->
                <div id="section-verified" class="space-y-4">
                    <form action="{{ route('admin.competition-scores.quick-add-team', $category->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                Pilih Sekolah / Kontingen Terverifikasi <span class="text-red-500">*</span>
                            </label>
                            <select name="registration_id" required class="w-full text-xs p-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:bg-white focus:outline-none focus:border-red-500">
                                <option value="">-- Pilih Kontingen Sekolah Terverifikasi --</option>
                                @foreach($availableRegistrations as $reg)
                                    @php
                                        $enrolledCount = $reg->teams->count();
                                    @endphp
                                    <option value="{{ $reg->id }}">
                                        {{ $reg->school_name }} ({{ $reg->registration_code }}) - {{ $enrolledCount > 0 ? "Sudah ada {$enrolledCount} regu di cabang ini" : 'Belum ada regu di cabang ini' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">
                                *Menampilkan seluruh sekolah tingkat <strong>PMR {{ $category->level }}</strong> yang telah diverifikasi oleh panitia.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Label / Nama Regu</label>
                                <input type="text" id="verified_team_label" name="team_label" placeholder="Contoh: Regu A / Regu Putra 1" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-red-500">
                                <div class="flex items-center gap-1 mt-1.5 flex-wrap">
                                    <button type="button" onclick="setLabel('Regu A')" class="text-[10px] bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded border border-slate-200 font-semibold text-slate-600">Regu A</button>
                                    <button type="button" onclick="setLabel('Regu B')" class="text-[10px] bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded border border-slate-200 font-semibold text-slate-600">Regu B</button>
                                    <button type="button" onclick="setLabel('Regu Putra 1')" class="text-[10px] bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded border border-slate-200 font-semibold text-slate-600">Putra 1</button>
                                    <button type="button" onclick="setLabel('Regu Putri 1')" class="text-[10px] bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded border border-slate-200 font-semibold text-slate-600">Putri 1</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Urut Tampil</label>
                                <input type="text" name="order_number" value="{{ $nextOrderNumber }}" placeholder="{{ $nextOrderNumber }}" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-mono font-bold focus:bg-white focus:outline-none focus:border-red-500">
                                <p class="text-[10px] text-slate-400 mt-1">Otomatis urutan berikutnya.</p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('quick-add-modal').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-red-950/20 transition flex items-center gap-1.5">
                                <i class="fa-solid fa-plus"></i> Tambahkan ke Lembar Skor
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Section 2: Manual OTS (Hidden by default) -->
                <div id="section-ots" class="hidden space-y-4">
                    <form action="{{ route('admin.competition-scores.quick-add-team', $category->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Sekolah / Kontingen (OTS) <span class="text-red-500">*</span></label>
                            <input type="text" name="school_name" required placeholder="Contoh: SMPN 2 CIAWI" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl uppercase font-semibold focus:bg-white focus:outline-none focus:border-red-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Label Regu</label>
                                <input type="text" name="team_label" placeholder="Contoh: Regu A" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Urut Tampil</label>
                                <input type="text" name="order_number" value="{{ $nextOrderNumber }}" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-mono font-bold focus:bg-white focus:outline-none focus:border-red-500">
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('quick-add-modal').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition flex items-center gap-1.5">
                                <i class="fa-solid fa-plus"></i> Tambah Tim OTS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

    @php 
        $isTandu = str_contains(strtolower($category->name), 'tandu') || str_starts_with($category->code ?? '', 'LKTR') || $category->scoring_type === 'tandu_standard_time';
    @endphp

    @if($isTandu)
        <!-- Tandu Scoring Formula Info Banner -->
        <div class="bg-gradient-to-r from-red-900 to-slate-900 text-white p-5 rounded-2xl shadow-sm border border-red-800/40 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-red-600 text-white">
                        <i class="fa-solid fa-calculator mr-1"></i> Rumus Excel Resmi
                    </span>
                    <span class="text-xs font-bold text-red-200">Sistem Penilaian Ketangkasan Tandu Reguler</span>
                </div>
                <div class="font-mono text-sm font-bold text-amber-300">
                    Total = Nilai Teknis + [300 - Penalti Keterlambatan]
                </div>
                <p class="text-xs text-slate-300">
                    Batas Waktu Standar: <strong class="text-white">06:55 (415 detik)</strong> &bull; Denda Keterlambatan: <strong class="text-amber-300">10 Poin per kelipatan 15 detik</strong> (dibulatkan ke atas).
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/10 text-xs text-slate-200 text-left md:text-right shrink-0 font-medium">
                <div>Bonus Waktu Maks: <strong class="text-emerald-400 font-bold">+300 Poin</strong></div>
                <div class="text-[11px] text-slate-400 mt-0.5">*Dihitung otomatis secara live saat Anda mengetik</div>
            </div>
        </div>
    @if($isSemiFinal)
        <!-- Semifinal Management Card (Otomatis & Fleksibel Buttons) -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-5 rounded-2xl shadow-md border border-indigo-900/50 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-500 text-white shadow-xs">
                        <i class="fa-solid fa-users-rays mr-1"></i> Babak 2 - Semi Final
                    </span>
                    <span class="text-xs font-bold text-indigo-200">Sistem 3 Termin Cepat Tepat</span>
                    <span class="text-[11px] font-semibold text-emerald-400 bg-emerald-950/60 border border-emerald-500/30 px-2 py-0.5 rounded-md">
                        3 Regu per Termin &bull; Juara 1 Lolos ke Final
                    </span>
                </div>
                <h3 class="text-lg font-black text-white">Pembagian Regu & Meja Pertandingan Semifinal</h3>
                <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                    Sembilan (9) regu terbaik dari babak penyisihan bertanding dalam 3 sesi termin. Juara 1 dari masing-masing Termin (total 3 regu) akan memperebutkan Juara Umum di <strong>Babak 3 - Final</strong>.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 shrink-0 w-full lg:w-auto">
                <button type="button" onclick="confirmAutoTermin()" class="flex-1 lg:flex-initial bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black text-xs px-4 py-3 rounded-xl shadow-lg shadow-red-950/30 transition flex items-center justify-center gap-2 cursor-pointer border border-red-400/30">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                    <span>Pembagian Termin Otomatis</span>
                </button>
                <button type="button" onclick="openFlexibleModal()" class="flex-1 lg:flex-initial bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs px-4 py-3 rounded-xl shadow transition flex items-center justify-center gap-2 cursor-pointer border border-white/20 backdrop-blur-xs">
                    <i class="fa-solid fa-sliders text-indigo-300"></i>
                    <span>Pembagian Termin Fleksibel</span>
                </button>
            </div>
        </div>

        <!-- Hidden Form for Automatic Seeding Submission -->
        <form id="auto-termin-form" action="{{ route('admin.competition-scores.assign-termins', $category->id) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="round_name" value="{{ $round }}">
            <input type="hidden" name="mode" value="automatic">
        </form>
    @endif

    <!-- Main Scoring Input Table Form -->
    <form action="{{ route('admin.competition-scores.save', $category->id) }}" method="POST" id="scoring-form">
        @csrf
        <input type="hidden" name="round_name" value="{{ $round }}">

        @if($isSemiFinal)
            @if($termin1Teams->isEmpty() && $termin2Teams->isEmpty() && $termin3Teams->isEmpty())
                <!-- Empty State: Teams not yet divided into termins -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center space-y-4">
                    <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto text-2xl border border-amber-200">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div class="max-w-md mx-auto space-y-1">
                        <h4 class="text-base font-black text-slate-800">Regu Semifinal Belum Dibagikan ke Termin</h4>
                        <p class="text-xs text-slate-500">
                            Silakan pilih salah satu opsi pembagian regu di bawah ini untuk menampilkan lembar skor Termin 1, 2, dan 3:
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <button type="button" onclick="confirmAutoTermin()" class="bg-red-600 hover:bg-red-700 text-white font-black text-xs px-5 py-3 rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i> Pembagian Termin Otomatis (Snake Seeding)
                        </button>
                        <button type="button" onclick="openFlexibleModal()" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-5 py-3 rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-sliders"></i> Pembagian Termin Fleksibel (Undian / Manual)
                        </button>
                    </div>

                    @if($qualifiedTeams->isNotEmpty())
                        <div class="mt-8 pt-6 border-t border-slate-100 max-w-2xl mx-auto text-left">
                            <h5 class="text-xs font-bold uppercase text-slate-500 tracking-wider mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-medal text-amber-500"></i> 9 Regu Terbaik Babak Penyisihan (Siap Masuk Semifinal):
                            </h5>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                @foreach($qualifiedTeams->take(9) as $qIdx => $qTeam)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs hover:border-indigo-300 transition">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-slate-900 text-xs">{{ $qIdx + 1 }}. {{ $qTeam->team_name }}</span>
                                            <span class="text-[10px] font-black text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded">R{{ $qTeam->prelim_rank ?? ($qIdx+1) }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-1 truncate font-medium">{{ $qTeam->registration?->school_name }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Nilai: <strong class="text-slate-700">{{ $qTeam->prelim_score }}</strong> &bull; Waktu: {{ $qTeam->prelim_time }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- The 3 Distinct Termin Score Sheets -->
                @php
                    $terminList = [
                        1 => [
                            'name' => 'TERMIN 1 (SESI 1)',
                            'teams' => $termin1Teams,
                            'theme' => 'emerald',
                            'header_bg' => 'from-emerald-900 to-slate-900',
                            'badge_bg' => 'bg-emerald-500',
                            'subtext' => 'Juara 1 dari Termin 1 otomatis melaju ke Babak 3 - Final',
                            'desk_letters' => ['A', 'B', 'C', 'D'],
                        ],
                        2 => [
                            'name' => 'TERMIN 2 (SESI 2)',
                            'teams' => $termin2Teams,
                            'theme' => 'blue',
                            'header_bg' => 'from-blue-900 to-slate-900',
                            'badge_bg' => 'bg-blue-500',
                            'subtext' => 'Juara 1 dari Termin 2 otomatis melaju ke Babak 3 - Final',
                            'desk_letters' => ['A', 'B', 'C', 'D'],
                        ],
                        3 => [
                            'name' => 'TERMIN 3 (SESI 3)',
                            'teams' => $termin3Teams,
                            'theme' => 'purple',
                            'header_bg' => 'from-purple-900 to-slate-900',
                            'badge_bg' => 'bg-purple-500',
                            'subtext' => 'Juara 1 dari Termin 3 otomatis melaju ke Babak 3 - Final',
                            'desk_letters' => ['A', 'B', 'C', 'D'],
                        ],
                    ];
                @endphp

                <div class="space-y-6">
                    @foreach($terminList as $tNum => $tMeta)
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden termin-section" data-termin="{{ $tNum }}">
                            <!-- Termin Header -->
                            <div class="p-4 bg-gradient-to-r {{ $tMeta['header_bg'] }} text-white flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-xl {{ $tMeta['badge_bg'] }} text-white font-black text-sm flex items-center justify-center shadow-md">
                                        {{ $tNum }}
                                    </span>
                                    <div>
                                        <div class="font-black text-sm text-white tracking-wide flex items-center gap-2">
                                            {{ $tMeta['name'] }}
                                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-white/10 text-white/90">
                                                {{ $tMeta['teams']->count() }} Regu
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-300 mt-0.5">
                                            {{ $tMeta['subtext'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="button" onclick="openFlexibleModal()" class="text-xs text-white/80 hover:text-white underline font-semibold flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-arrows-split-up-and-left text-[10px]"></i> Atur Regu Termin
                                    </button>
                                </div>
                            </div>

                            <!-- Termin Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs text-slate-700 termin-table">
                                    <thead class="bg-slate-100 text-slate-800 uppercase font-black border-b border-slate-200">
                                        <tr>
                                            <th class="px-4 py-3 w-24 text-center">Meja / Urut</th>
                                            <th class="px-4 py-3 min-w-[240px]">Nama Sekolah & Regu</th>
                                            <th class="px-4 py-3 w-40 text-center">Nilai Poin Semifinal</th>
                                            <th class="px-4 py-3 w-40">Catatan Juri</th>
                                            <th class="px-4 py-3 w-32 text-right">Nilai Akhir</th>
                                            <th class="px-4 py-3 w-52 text-center bg-slate-200/80 text-slate-900 border-l border-slate-200">Peringkat Termin {{ $tNum }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-medium">
                                        @forelse($tMeta['teams'] as $idx => $team)
                                            @php
                                                $score = $scores->get($team->id);
                                                $details = $score?->score_details ?? [];
                                                $teamScoreVal = $details['score'] ?? ($score?->final_score ?? '');
                                                $prelimScore = $prelimScores->get($team->id);
                                                $deskLetter = $tMeta['desk_letters'][$idx] ?? ($idx + 1);
                                            @endphp
                                            <tr class="team-row hover:bg-slate-50 transition {{ $score?->rank == 1 ? 'bg-amber-50/60 font-bold' : '' }}" data-team-id="{{ $team->id }}">
                                                <!-- Desk / Order -->
                                                <td class="px-4 py-3 font-mono text-center font-black text-slate-700">
                                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-300 text-slate-900 text-xs font-bold">
                                                        Meja {{ $deskLetter }}
                                                    </span>
                                                </td>

                                                <!-- Team Name & Prelim Rank Badge -->
                                                <td class="px-4 py-3">
                                                    <div class="font-extrabold text-slate-900 text-xs uppercase flex items-center gap-2 flex-wrap">
                                                        <span>{{ $team->team_name }}</span>
                                                        @if($prelimScore)
                                                            <span class="font-mono text-[10px] font-black text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded" title="Peringkat {{ $prelimScore->rank }} di Babak Penyisihan">
                                                                Rank {{ $prelimScore->rank }} Penyisihan
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                                                        <span>{{ $team->registration?->school_name }}</span>
                                                        @if($team->registration?->registration_code)
                                                            <span class="font-mono text-[10px] text-slate-400">({{ $team->registration->registration_code }})</span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Score Input -->
                                                <td class="px-4 py-3">
                                                    <input type="number" step="0.01" name="scores[{{ $team->id }}][score]" value="{{ $teamScoreVal }}" placeholder="Contoh: 2100" class="input-generic-score input-termin-score w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-900 focus:bg-white focus:outline-none focus:border-red-500 shadow-2xs">
                                                    <input type="hidden" name="scores[{{ $team->id }}][termin]" value="{{ $tNum }}">
                                                </td>

                                                <!-- Notes -->
                                                <td class="px-4 py-3">
                                                    <input type="text" name="scores[{{ $team->id }}][notes]" value="{{ $score?->notes ?? '' }}" placeholder="Catatan juri / babak rebutan" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                                </td>

                                                <!-- Final Score -->
                                                <td class="px-4 py-3 text-right">
                                                    <input type="number" step="0.01" name="scores[{{ $team->id }}][manual_final_score]" value="{{ $score?->final_score ?? '' }}" placeholder="Auto" class="input-final-score w-28 p-2 bg-white border border-slate-300 font-mono font-black text-right text-xs rounded-lg text-red-600 focus:outline-none focus:border-red-500 shadow-sm">
                                                </td>

                                                <!-- Rank in this Termin -->
                                                <td class="px-4 py-3 text-center font-bold rank-cell border-l border-slate-100 bg-slate-50/50">
                                                    @if($score?->rank == 1)
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gradient-to-r from-amber-500 to-yellow-500 text-white font-black text-xs shadow-md">
                                                            <i class="fa-solid fa-trophy text-amber-100"></i> Juara Termin {{ $tNum }} (Lolos Final)
                                                        </span>
                                                    @elseif($score?->rank == 2)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 font-bold text-xs">
                                                            <i class="fa-solid fa-medal text-slate-500"></i> Peringkat 2
                                                        </span>
                                                    @elseif($score?->rank == 3)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-bold text-xs">
                                                            <i class="fa-solid fa-medal text-amber-700"></i> Peringkat 3
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 font-mono">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-6 text-slate-400">
                                                    Belum ada regu yang dialokasikan ke Termin {{ $tNum }}.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <!-- Optional: Unassigned Teams Box -->
                    @if($unassignedTeams->isNotEmpty())
                        <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="font-extrabold text-xs text-amber-900 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation text-amber-600"></i>
                                    <span>Regu Lainnya Belum Masuk Termin ({{ $unassignedTeams->count() }} Regu):</span>
                                </div>
                                <button type="button" onclick="openFlexibleModal()" class="text-xs font-bold text-indigo-700 hover:underline">
                                    Bagi ke Termin &rarr;
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($unassignedTeams as $uTeam)
                                    <span class="px-3 py-1.5 rounded-xl bg-white border border-amber-300 text-xs font-bold text-slate-800 shadow-2xs">
                                        {{ $uTeam->team_name }} ({{ $uTeam->registration?->school_name }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Unified Semifinal Save Bar -->
                    <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm">
                        <div class="text-xs text-slate-600 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                            <span>Klik <strong>Simpan Nilai Semifinal</strong> untuk mengunci skor seluruh termin dan menetapkan 3 finalis ke babak final.</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                            <button type="button" onclick="clearAllFormInputs()" class="w-full sm:w-auto bg-slate-200 hover:bg-slate-300 text-slate-700 font-extrabold text-xs px-5 py-3 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-blue-600"></i> Kosongkan Form
                            </button>
                            <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs px-8 py-3 rounded-xl shadow-lg shadow-red-900/20 transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Nilai Semifinal (Semua Termin)
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                <div class="font-extrabold text-sm text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-red-600"></i> Lembar Skor: {{ $round }}
                </div>
                <div class="text-xs text-slate-500">
                    *Sistem otomatis menghitung Nilai Akhir dan mengurutkan Peringkat Juara setelah tombol simpan ditekan.
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-100 text-slate-800 uppercase font-black border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 w-16 text-center">No. Urut</th>
                            <th class="px-4 py-3 min-w-[220px]">Nama Sekolah / Regu</th>

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
                            @elseif($isTandu)
                                <!-- Ketangkasan Tandu Reguler (Excel Formula) -->
                                <th class="px-4 py-3 w-32">Nilai Teknis (Juri)</th>
                                <th class="px-4 py-3 w-32">Waktu Tempuh</th>
                                <th class="px-4 py-3 w-36 text-center">Poin Waktu (Maks 300)</th>
                            @else
                                <!-- Standard Time / Quiz -->
                                <th class="px-4 py-3 w-32">Nilai / Poin</th>
                                <th class="px-4 py-3 w-32">Waktu Tempuh</th>
                            @endif

                            <th class="px-4 py-3 w-32 text-right">Nilai Akhir (Total)</th>
                            <th class="px-4 py-3 w-24 text-center bg-slate-200/80 text-slate-900 border-l border-slate-200">Ranking</th>
                            <th class="px-3 py-3 w-24 text-center text-slate-600 border-l border-slate-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium" id="scoring-tbody">
                        @forelse($teams as $index => $team)
                            @php 
                                $score = $scores->get($team->id);
                                $details = $score?->score_details ?? [];
                                $teamTechScore = $details['technical_score'] ?? ($details['score'] ?? '');
                                $teamTime = $score?->time_recorded ?? ($details['time_recorded'] ?? '');
                                $teamTimeScore = $details['time_score'] ?? null;
                                $teamTimePenalty = $details['time_penalty'] ?? 0;
                            @endphp
                            <tr class="team-row hover:bg-slate-50 transition {{ $score?->rank == 1 ? 'bg-purple-50/60 font-bold' : '' }}" data-team-id="{{ $team->id }}">
                                <!-- Order Number -->
                                <td class="px-4 py-3 font-mono text-center font-bold text-slate-700">
                                    {{ $team->order_number ?: ($index + 1) }}
                                </td>

                                <!-- Team Name -->
                                <td class="px-4 py-3">
                                    <div class="font-extrabold text-slate-900 text-xs uppercase">{{ $team->team_name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                        @if($team->registration)
                                            <span class="font-mono font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded text-[10px]">{{ $team->registration->registration_code }}</span>
                                        @endif
                                        <span>{{ $team->registration?->advisor_name }}</span>
                                    </div>
                                </td>

                                <!-- Dynamic Inputs -->
                                @if($category->scoring_type == 'written_practical_time')
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][written_score]" value="{{ $details['written_score'] ?? '' }}" placeholder="0 - 100" class="input-written-score w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-semibold">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="scores[{{ $team->id }}][written_time]" value="{{ $details['written_time'] ?? '' }}" placeholder="25.02" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][practical_score]" value="{{ $details['practical_score'] ?? '' }}" placeholder="0 - 1000" class="input-practical-score w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-semibold">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="scores[{{ $team->id }}][practical_time]" value="{{ $details['practical_time'] ?? '' }}" placeholder="6.30" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                @elseif($category->scoring_type == 'multi_criteria')
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][criteria_1]" value="{{ $details['criteria_1'] ?? '' }}" placeholder="{{ $category->criteria_schema[0] ?? 'Kriteria 1' }}" class="input-crit w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-semibold">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][criteria_2]" value="{{ $details['criteria_2'] ?? '' }}" placeholder="{{ $category->criteria_schema[1] ?? 'Kriteria 2' }}" class="input-crit w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-semibold">
                                    </td>
                                    @if(isset($category->criteria_schema[2]))
                                        <td class="px-4 py-3">
                                            <input type="number" step="0.01" name="scores[{{ $team->id }}][criteria_3]" value="{{ $details['criteria_3'] ?? '' }}" placeholder="{{ $category->criteria_schema[2] ?? 'Kriteria 3' }}" class="input-crit w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-semibold">
                                        </td>
                                    @endif
                                @elseif($category->scoring_type == 'social_engagement')
                                    <td class="px-4 py-3">
                                        <input type="number" name="scores[{{ $team->id }}][likes]" value="{{ $details['likes'] ?? '' }}" placeholder="Likes" class="input-likes w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-semibold">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="scores[{{ $team->id }}][stickers]" value="{{ $details['stickers'] ?? '' }}" placeholder="Stickers" class="input-stickers w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-semibold">
                                    </td>
                                @elseif($isTandu)
                                    <!-- Ketangkasan Tandu Specific Inputs -->
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][technical_score]" value="{{ $teamTechScore }}" placeholder="Contoh: 620" class="input-tandu-tech w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-bold text-slate-800">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="scores[{{ $team->id }}][time_recorded]" value="{{ $teamTime }}" placeholder="05:44" class="input-tandu-time w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-mono font-semibold">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="tandu-time-badge inline-flex items-center justify-center font-mono">
                                            @if($teamTimeScore !== null)
                                                @if($teamTimePenalty == 0)
                                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-xs" title="Tepat Waktu (<= 06:55)">
                                                        +{{ $teamTimeScore }} <span class="text-[10px] text-emerald-600 font-normal">(0 Denda)</span>
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold text-xs" title="Denda keterlambatan: {{ $teamTimePenalty }} poin">
                                                        +{{ $teamTimeScore }} <span class="text-[10px] text-rose-600 font-normal">(-{{ $teamTimePenalty }})</span>
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-slate-300 font-mono">-</span>
                                            @endif
                                        </div>
                                    </td>
                                @else
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" name="scores[{{ $team->id }}][score]" value="{{ $details['score'] ?? '' }}" placeholder="Nilai Murni" class="input-generic-score w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500 font-semibold">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="scores[{{ $team->id }}][time_recorded]" value="{{ $score?->time_recorded ?? ($details['time_recorded'] ?? '') }}" placeholder="00:04:42" class="input-generic-time w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-red-500">
                                    </td>
                                @endif

                                <!-- Final Score Input (Auto calculated / Manual Override) -->
                                <td class="px-4 py-3 text-right">
                                    <input type="number" step="0.01" name="scores[{{ $team->id }}][manual_final_score]" value="{{ $score?->final_score ?? '' }}" placeholder="Auto" class="input-final-score w-28 p-2 bg-white border border-slate-300 font-mono font-black text-right text-xs rounded-lg text-red-600 focus:outline-none focus:border-red-500 shadow-sm">
                                </td>

                                <!-- Ranking Column -->
                                <td class="px-4 py-3 text-center font-bold rank-cell border-l border-slate-100 bg-slate-50/50">
                                    @if($score?->rank == 1)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-purple-600 text-white font-black text-xs shadow-md">1</span>
                                    @elseif($score?->rank == 2)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-500 text-white font-black text-xs shadow-md">2</span>
                                    @elseif($score?->rank == 3)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-400 text-white font-black text-xs shadow-md">3</span>
                                    @elseif($score?->rank)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-800 font-bold text-xs border border-slate-300">{{ $score->rank }}</span>
                                    @else
                                        <span class="text-slate-300 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Action: Clear Row & Remove Team -->
                                <td class="px-3 py-3 text-center border-l border-slate-100">
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button" onclick="clearRowInput(this)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-400 hover:text-amber-600 hover:border-amber-200 border border-slate-200 inline-flex items-center justify-center transition shadow-2xs" title="Kosongkan input nomor urut ini">
                                            <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                        </button>
                                        <button type="button" onclick="confirmDeleteTeam('{{ route('admin.competition-scores.remove-team', [$category->id, $team->id]) }}', '{{ addslashes($team->team_name) }}')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 hover:border-rose-200 border border-slate-200 inline-flex items-center justify-center transition shadow-2xs" title="Hapus regu ini dari lembar penilaian">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-10 text-slate-400">
                                    Belum ada peserta yang terdaftar di cabang lomba ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    <span>Klik <strong>Simpan & Hitung Peringkat</strong> untuk memperbarui skor dan peringkat juara secara permanen ke proyektor dan halaman publik.</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <button type="button" onclick="clearAllFormInputs()" class="w-full sm:w-auto bg-slate-200 hover:bg-slate-300 text-slate-700 font-extrabold text-xs px-5 py-3 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-arrows-rotate text-blue-600"></i> Kosongkan Form (Refresh)
                    </button>
                    <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs px-8 py-3 rounded-xl shadow-lg shadow-red-900/20 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan & Hitung Peringkat Otomatis
                    </button>
                </div>
            </div>
        @endif
    </form>

    @if($isSemiFinal)
        <!-- Flexible Termin Modal (Pot Drawing & Manual Assignments) -->
        <div id="flexible-termin-modal" class="hidden bg-slate-900/60 fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[92vh]">
                <!-- Modal Header -->
                <div class="p-5 bg-slate-900 text-white flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-indigo-500 text-white">
                                PMR {{ $category->level }} &bull; Semifinal
                            </span>
                            <span id="pot-draw-feedback" class="hidden text-[10px] font-bold text-amber-300 bg-amber-950/80 px-2 py-0.5 rounded border border-amber-500/40 animate-pulse"></span>
                        </div>
                        <h3 class="font-black text-base text-white mt-1">Pembagian Termin Fleksibel (Babak Semifinal)</h3>
                        <p class="text-xs text-slate-400">Atur penempatan regu ke Termin 1, 2, atau 3 secara manual atau menggunakan pengundian sistem pot.</p>
                    </div>
                    <button type="button" onclick="closeFlexibleModal()" class="text-slate-400 hover:text-white p-1 cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Quick Action Bar & Live Counters -->
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" onclick="applyModalPotDrawing()" class="flex-1 sm:flex-initial bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-slate-950 font-black text-xs px-3.5 py-2 rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-dice text-sm"></i> Acak / Undi (Sistem Pot)
                        </button>
                        <button type="button" onclick="applyModalSnakeSeeding()" class="flex-1 sm:flex-initial bg-slate-200 hover:bg-slate-300 text-slate-800 font-extrabold text-xs px-3.5 py-2 rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-wand-magic-sparkles text-indigo-600"></i> Snake Seeding
                        </button>
                    </div>

                    <!-- Live Counters -->
                    <div class="flex items-center gap-1.5 text-xs font-bold w-full sm:w-auto justify-end">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                            T1: <strong id="counter-t1" class="font-black">0</strong>/3
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 border border-blue-300 flex items-center gap-1">
                            T2: <strong id="counter-t2" class="font-black">0</strong>/3
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-800 border border-purple-300 flex items-center gap-1">
                            T3: <strong id="counter-t3" class="font-black">0</strong>/3
                        </span>
                    </div>
                </div>

                <!-- Modal Body: Table of Qualified Teams -->
                <form action="{{ route('admin.competition-scores.assign-termins', $category->id) }}" method="POST" id="flexible-termin-form" class="overflow-y-auto flex-grow p-5 space-y-3">
                    @csrf
                    <input type="hidden" name="round_name" value="{{ $round }}">
                    <input type="hidden" name="mode" value="manual">

                    <div class="space-y-2">
                        @php
                            $modalTeams = $qualifiedTeams->isNotEmpty() ? $qualifiedTeams->take(9) : $teams->take(9);
                        @endphp

                        @foreach($modalTeams as $mIdx => $mTeam)
                            @php
                                $mScore = $scores->get($mTeam->id);
                                $mDetails = $mScore?->score_details ?? [];
                                $currentTermin = $mDetails['termin'] ?? (($mIdx % 3) + 1);

                                // Pot categorization (Rank 1-3: Pot 1, 4-6: Pot 2, 7-9: Pot 3)
                                $potNum = ($mIdx < 3) ? 1 : (($mIdx < 6) ? 2 : 3);
                                $potLabel = ($potNum == 1) ? 'Pot 1 (Unggulan)' : (($potNum == 2) ? 'Pot 2 (Menengah)' : 'Pot 3 (Kuda Hitam)');
                                $potColor = ($potNum == 1) ? 'text-amber-700 bg-amber-50 border-amber-200' : (($potNum == 2) ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200');
                            @endphp

                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 modal-team-row" data-team-id="{{ $mTeam->id }}" data-pot="{{ $potNum }}" data-school="{{ strtoupper($mTeam->registration?->school_name ?? $mTeam->team_name) }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-lg bg-slate-200 text-slate-800 font-mono font-black text-xs flex items-center justify-center shrink-0">
                                        {{ $mIdx + 1 }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-xs flex items-center gap-2 flex-wrap">
                                            <span>{{ $mTeam->team_name }}</span>
                                            <span class="text-[10px] font-black px-1.5 py-0.5 rounded border {{ $potColor }}">
                                                {{ $potLabel }}
                                            </span>
                                            @if($mTeam->prelim_rank)
                                                <span class="text-[10px] font-bold text-slate-500">Rank {{ $mTeam->prelim_rank }}</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 truncate">{{ $mTeam->registration?->school_name }}</div>
                                    </div>
                                </div>

                                <!-- 3 Termin Selector Pills -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <label class="cursor-pointer select-none">
                                        <input type="radio" name="assignments[{{ $mTeam->id }}]" value="1" {{ $currentTermin == 1 ? 'checked' : '' }} onchange="updateTerminCounters()" class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-300 text-slate-600 bg-white peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 transition inline-block">
                                            Termin 1
                                        </span>
                                    </label>
                                    <label class="cursor-pointer select-none">
                                        <input type="radio" name="assignments[{{ $mTeam->id }}]" value="2" {{ $currentTermin == 2 ? 'checked' : '' }} onchange="updateTerminCounters()" class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-300 text-slate-600 bg-white peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition inline-block">
                                            Termin 2
                                        </span>
                                    </label>
                                    <label class="cursor-pointer select-none">
                                        <input type="radio" name="assignments[{{ $mTeam->id }}]" value="3" {{ $currentTermin == 3 ? 'checked' : '' }} onchange="updateTerminCounters()" class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-300 text-slate-600 bg-white peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 transition inline-block">
                                            Termin 3
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row justify-end items-center gap-2">
                        <button type="button" onclick="closeFlexibleModal()" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-check"></i> Terapkan Pembagian Termin & Tampilkan di Lembar Skor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>

<!-- Client-Side Live Formula & Dynamic Real-Time Ranking Script -->
<script>
window.switchAddTab = function(tab) {
    const secVerified = document.getElementById('section-verified');
    const secOts = document.getElementById('section-ots');
    const btnVerified = document.getElementById('tab-btn-verified');
    const btnOts = document.getElementById('tab-btn-ots');

    if (tab === 'verified') {
        secVerified.classList.remove('hidden');
        secOts.classList.add('hidden');
        btnVerified.className = 'pb-2.5 text-xs font-black border-b-2 border-red-600 text-red-600 transition flex items-center gap-1.5';
        btnOts.className = 'pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5';
    } else {
        secVerified.classList.add('hidden');
        secOts.classList.remove('hidden');
        btnOts.className = 'pb-2.5 text-xs font-black border-b-2 border-red-600 text-red-600 transition flex items-center gap-1.5';
        btnVerified.className = 'pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5';
    }
};

window.setLabel = function(val) {
    const input = document.getElementById('verified_team_label');
    if (input) input.value = val;
};

// Semifinal Termin Modal & Allocation Functions
window.confirmAutoTermin = function() {
    if (confirm("Terapkan Pembagian Termin Otomatis (Metode Snake Seeding Berimbang) untuk 9 regu terbaik?\n\n- Termin 1: Peringkat 1, 6, 7\n- Termin 2: Peringkat 2, 5, 8\n- Termin 3: Peringkat 3, 4, 9\n\n*Regu dari sekolah yang sama otomatis dipisahkan ke termin berbeda.")) {
        const form = document.getElementById('auto-termin-form');
        if (form) form.submit();
    }
};

window.openFlexibleModal = function() {
    const modal = document.getElementById('flexible-termin-modal');
    if (modal) {
        modal.classList.remove('hidden');
        window.updateTerminCounters();
    }
};

window.closeFlexibleModal = function() {
    const modal = document.getElementById('flexible-termin-modal');
    if (modal) modal.classList.add('hidden');
};

window.applyModalSnakeSeeding = function() {
    const rows = Array.from(document.querySelectorAll('.modal-team-row'));
    const pattern = [1, 2, 3, 3, 2, 1, 1, 2, 3];
    rows.forEach((row, idx) => {
        const tNum = pattern[idx] || ((idx % 3) + 1);
        const rad = row.querySelector(`input[type="radio"][value="${tNum}"]`);
        if (rad) rad.checked = true;
    });
    window.updateTerminCounters();
    const fb = document.getElementById('pot-draw-feedback');
    if (fb) {
        fb.classList.remove('hidden');
        fb.innerText = '⚡ Snake Seeding Diterapkan!';
        setTimeout(() => fb.classList.add('hidden'), 2500);
    }
};

window.applyModalPotDrawing = function() {
    const rows = Array.from(document.querySelectorAll('.modal-team-row'));
    if (rows.length === 0) return;

    const pot1 = rows.slice(0, 3);
    const pot2 = rows.slice(3, 6);
    const pot3 = rows.slice(6, 9);

    const shuffle = arr => arr.slice().sort(() => Math.random() - 0.5);

    const sPot1 = shuffle(pot1);
    const sPot2 = shuffle(pot2);
    const sPot3 = shuffle(pot3);

    [1, 2, 3].forEach((tNum, idx) => {
        if (sPot1[idx]) {
            const rad = sPot1[idx].querySelector(`input[type="radio"][value="${tNum}"]`);
            if (rad) rad.checked = true;
        }
        if (sPot2[idx]) {
            const rad = sPot2[idx].querySelector(`input[type="radio"][value="${tNum}"]`);
            if (rad) rad.checked = true;
        }
        if (sPot3[idx]) {
            const rad = sPot3[idx].querySelector(`input[type="radio"][value="${tNum}"]`);
            if (rad) rad.checked = true;
        }
    });

    window.updateTerminCounters();
    const fb = document.getElementById('pot-draw-feedback');
    if (fb) {
        fb.classList.remove('hidden');
        fb.innerText = '🎲 Undian Sistem Pot Berhasil!';
        setTimeout(() => fb.classList.add('hidden'), 2500);
    }
};

window.updateTerminCounters = function() {
    const counts = { 1: 0, 2: 0, 3: 0 };
    document.querySelectorAll('.modal-team-row input[type="radio"]:checked').forEach(rad => {
        const val = parseInt(rad.value, 10);
        if (counts[val] !== undefined) counts[val]++;
    });

    [1, 2, 3].forEach(tNum => {
        const el = document.getElementById(`counter-t${tNum}`);
        if (el) {
            el.innerText = counts[tNum];
            const parent = el.parentElement;
            if (counts[tNum] === 3) {
                parent.className = 'px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-400 font-black flex items-center gap-1';
            } else {
                parent.className = 'px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-300 font-bold flex items-center gap-1';
            }
        }
    });
};

window.recalcTerminRanks = function(terminNum) {
    const section = document.querySelector(`.termin-section[data-termin="${terminNum}"]`);
    if (!section) return;

    const rows = Array.from(section.querySelectorAll('.team-row'));
    const scoredRows = rows.map(row => {
        const scoreInput = row.querySelector('.input-termin-score') || row.querySelector('.input-generic-score');
        const finalInput = row.querySelector('.input-final-score');
        let val = parseFloat(finalInput?.value);
        if (isNaN(val) && scoreInput && scoreInput.value !== '') {
            val = parseFloat(scoreInput.value);
            if (!isNaN(val) && finalInput) finalInput.value = val;
        }
        return {
            row: row,
            score: isNaN(val) ? -999999 : val,
            hasScore: !isNaN(val) && (scoreInput?.value !== '' || finalInput?.value !== '')
        };
    });

    scoredRows.sort((a, b) => b.score - a.score);

    let rank = 1;
    scoredRows.forEach(item => {
        const cell = item.row.querySelector('.rank-cell');
        if (!cell) return;

        if (!item.hasScore) {
            cell.innerHTML = '<span class="text-slate-400 font-mono">-</span>';
            item.row.classList.remove('bg-amber-50/60');
            return;
        }

        if (rank === 1) {
            cell.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gradient-to-r from-amber-500 to-yellow-500 text-white font-black text-xs shadow-md"><i class="fa-solid fa-trophy text-amber-100"></i> Juara Termin ${terminNum} (Lolos Final)</span>`;
            item.row.classList.add('bg-amber-50/60');
        } else if (rank === 2) {
            cell.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 font-bold text-xs"><i class="fa-solid fa-medal text-slate-500"></i> Peringkat 2</span>`;
            item.row.classList.remove('bg-amber-50/60');
        } else if (rank === 3) {
            cell.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-bold text-xs"><i class="fa-solid fa-medal text-amber-700"></i> Peringkat 3</span>`;
            item.row.classList.remove('bg-amber-50/60');
        } else {
            cell.innerHTML = `<span class="font-bold text-slate-600 text-xs">Peringkat ${rank}</span>`;
            item.row.classList.remove('bg-amber-50/60');
        }
        rank++;
    });
};

window.confirmDeleteTeam = function(actionUrl, teamName) {
    if (confirm(`Hapus regu "${teamName}" dari lembar penilaian cabang ini?`)) {
        const form = document.getElementById('delete-team-form');
        form.action = actionUrl;
        form.submit();
    }
};

// Expose global helper for clearing a single row
window.clearRowInput = function(btn) {
    const row = btn.closest('.team-row');
    if (!row) return;

    row.querySelectorAll('input').forEach(input => {
        if (input.type === 'checkbox') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });

    const timeBadge = row.querySelector('.tandu-time-badge');
    if (timeBadge) {
        timeBadge.innerHTML = '<span class="text-slate-300 font-mono">-</span>';
    }

    const rankCell = row.querySelector('.rank-cell');
    if (rankCell) {
        rankCell.innerHTML = '<span class="text-slate-300 font-mono">-</span>';
    }

    row.classList.remove('bg-purple-50/40', 'bg-purple-50/60', 'font-bold');

    // Automatically recalculate remaining rows' ranks
    if (typeof window.recalculateAllRanks === 'function') {
        window.recalculateAllRanks();
    }
};

// Expose global helper for clearing all inputs
window.clearAllFormInputs = function() {
    if (!confirm('Kosongkan semua inputan nilai dan waktu pada form ini?')) {
        return;
    }

    document.querySelectorAll('#scoring-tbody input').forEach(input => {
        if (input.type === 'checkbox') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });

    document.querySelectorAll('.tandu-time-badge').forEach(badge => {
        badge.innerHTML = '<span class="text-slate-300 font-mono">-</span>';
    });

    document.querySelectorAll('.rank-cell').forEach(cell => {
        cell.innerHTML = '<span class="text-slate-300 font-mono">-</span>';
    });

    document.querySelectorAll('.team-row').forEach(row => {
        row.classList.remove('bg-purple-50/40', 'bg-purple-50/60', 'font-bold', 'bg-slate-100', 'opacity-60');
    });

    if (typeof window.recalculateAllRanks === 'function') {
        window.recalculateAllRanks();
    }
};

window.confirmResetDatabase = function() {
    if (confirm('PERINGATAN: Apakah Anda yakin ingin menghapus seluruh nilai & peringkat yang tersimpan di database untuk cabang lomba ini? Data yang dihapus tidak dapat dikembalikan.')) {
        document.getElementById('reset-database-form').submit();
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const isTanduCategory = {{ $isTandu ? 'true' : 'false' }};
    const scoringType = "{{ $category->scoring_type }}";

    function parseTimeToSeconds(timeStr) {
        if (!timeStr) return null;
        timeStr = timeStr.trim();
        if (!timeStr) return null;

        // Raw numeric seconds
        if (!isNaN(timeStr) && !timeStr.includes(':') && !timeStr.includes('.')) {
            return parseInt(timeStr, 10);
        }

        const isAmPm = /am|pm/i.test(timeStr);
        const cleanStr = timeStr.replace(/[^0-9:.]/g, '');

        if (cleanStr.includes(':')) {
            const parts = cleanStr.split(':').map(Number);
            if (parts.length === 2) {
                return (parts[0] * 60) + parts[1];
            } else if (parts.length === 3) {
                let h = parts[0];
                let m = parts[1];
                let s = parts[2];
                if (isAmPm && h === 12) {
                    h = 0;
                }
                return (h * 3600) + (m * 60) + s;
            }
        } else if (cleanStr.includes('.')) {
            const parts = cleanStr.split('.').map(Number);
            if (parts.length === 2) {
                return (parts[0] * 60) + parts[1];
            }
        }

        return null;
    }

    function calculateTandu(techVal, timeStr) {
        const tech = parseFloat(techVal) || 0;
        const seconds = parseTimeToSeconds(timeStr);

        if (seconds === null) {
            return {
                tech: tech,
                seconds: null,
                penalty: 0,
                timeScore: 0,
                finalScore: tech,
                hasTime: false
            };
        }

        const limitSeconds = 415; // 06:55
        const excess = Math.max(0, seconds - limitSeconds);
        let penalty = 0;
        if (excess > 0) {
            const intervals = Math.ceil(excess / 15);
            penalty = intervals * 10;
        }

        const timeScore = 300 - penalty;
        const finalScore = tech + timeScore;

        return {
            tech: tech,
            seconds: seconds,
            excess: excess,
            penalty: penalty,
            timeScore: timeScore,
            finalScore: finalScore,
            hasTime: true
        };
    }

    // Dynamic Rank Recalculation across all rows
    window.recalculateAllRanks = function() {
        const rows = Array.from(document.querySelectorAll('.team-row'));
        const rowData = rows.map(row => {
            const finalInput = row.querySelector('.input-final-score');
            const scoreVal = finalInput ? parseFloat(finalInput.value) : NaN;
            const timeInput = row.querySelector('.input-tandu-time') || row.querySelector('input[name*="time_recorded"]');
            const timeStr = timeInput ? timeInput.value : '';
            const seconds = parseTimeToSeconds(timeStr) ?? 999999;
            const hasScore = !isNaN(scoreVal) && finalInput.value.trim() !== '' && scoreVal > 0;

            return {
                row: row,
                score: hasScore ? scoreVal : -999999,
                seconds: seconds,
                hasScore: hasScore
            };
        });

        // Filter and sort active rows from highest score to lowest, tiebreak with lowest seconds
        const scoredRows = rowData.filter(d => d.hasScore);
        scoredRows.sort((a, b) => {
            if (b.score !== a.score) {
                return b.score - a.score;
            }
            return a.seconds - b.seconds;
        });

        // Assign rank badges
        rowData.forEach(d => {
            const rankIndex = scoredRows.indexOf(d);
            const rankCell = d.row.querySelector('.rank-cell');
            if (!rankCell) return;

            if (rankIndex !== -1) {
                const rank = rankIndex + 1;
                let badgeHtml = '';
                if (rank === 1) {
                    badgeHtml = '<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-purple-600 text-white font-black text-xs shadow-md">1</span>';
                    d.row.classList.add('bg-purple-50/40');
                } else if (rank === 2) {
                    badgeHtml = '<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-500 text-white font-black text-xs shadow-md">2</span>';
                    d.row.classList.remove('bg-purple-50/40');
                } else if (rank === 3) {
                    badgeHtml = '<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-400 text-white font-black text-xs shadow-md">3</span>';
                    d.row.classList.remove('bg-purple-50/40');
                } else {
                    badgeHtml = `<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-800 font-bold text-xs border border-slate-300">${rank}</span>`;
                    d.row.classList.remove('bg-purple-50/40');
                }
                rankCell.innerHTML = badgeHtml;
            } else {
                rankCell.innerHTML = '<span class="text-slate-300 font-mono">-</span>';
                d.row.classList.remove('bg-purple-50/40');
            }
        });
    };

    // Attach listeners to all team rows
    document.querySelectorAll('.team-row').forEach(row => {
        const finalInput = row.querySelector('.input-final-score');

        if (isTanduCategory) {
            const techInput = row.querySelector('.input-tandu-tech');
            const timeInput = row.querySelector('.input-tandu-time');
            const badgeContainer = row.querySelector('.tandu-time-badge');

            function updateTanduRow() {
                const techVal = techInput ? techInput.value : '';
                const timeVal = timeInput ? timeInput.value : '';

                if (techVal === '' && timeVal === '') {
                    if (badgeContainer) badgeContainer.innerHTML = '<span class="text-slate-300 font-mono">-</span>';
                    if (finalInput) finalInput.value = '';
                    window.recalculateAllRanks();
                    return;
                }

                const res = calculateTandu(techVal, timeVal);

                if (badgeContainer) {
                    if (!res.hasTime) {
                        badgeContainer.innerHTML = '<span class="text-slate-300 font-mono">-</span>';
                    } else if (res.penalty === 0) {
                        badgeContainer.innerHTML = `
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-xs" title="Tepat Waktu (<= 06:55)">
                                +${res.timeScore} <span class="text-[10px] text-emerald-600 font-normal">(0 Denda)</span>
                            </span>
                        `;
                    } else {
                        badgeContainer.innerHTML = `
                            <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold text-xs" title="Lewat ${res.excess}s -> Denda ${res.penalty} poin">
                                +${res.timeScore} <span class="text-[10px] text-rose-600 font-normal">(-${res.penalty})</span>
                            </span>
                        `;
                    }
                }

                if (finalInput) {
                    finalInput.value = res.finalScore;
                }

                window.recalculateAllRanks();
            }

            if (techInput) techInput.addEventListener('input', updateTanduRow);
            if (timeInput) timeInput.addEventListener('input', updateTanduRow);

        } else if (scoringType === 'written_practical_time') {
            const writtenInput = row.querySelector('.input-written-score');
            const practicalInput = row.querySelector('.input-practical-score');

            function updateLppRow() {
                const w = parseFloat(writtenInput?.value || 0);
                const p = parseFloat(practicalInput?.value || 0);
                const score = (w * 0.3) + ((p / 10) * 0.7);
                if (finalInput) finalInput.value = score.toFixed(2);
                window.recalculateAllRanks();
            }

            if (writtenInput) writtenInput.addEventListener('input', updateLppRow);
            if (practicalInput) practicalInput.addEventListener('input', updateLppRow);

        } else if (scoringType === 'multi_criteria') {
            const critInputs = row.querySelectorAll('.input-crit');

            function updateCritRow() {
                let total = 0;
                critInputs.forEach(inp => {
                    total += parseFloat(inp.value || 0);
                });
                if (finalInput) finalInput.value = total.toFixed(2);
                window.recalculateAllRanks();
            }

            critInputs.forEach(inp => inp.addEventListener('input', updateCritRow));

        } else if (scoringType === 'social_engagement') {
            const likesInput = row.querySelector('.input-likes');
            const stickersInput = row.querySelector('.input-stickers');

            function updateSocialRow() {
                const l = parseInt(likesInput?.value || 0, 10);
                const s = parseInt(stickersInput?.value || 0, 10);
                const score = l + (s * 1.5);
                if (finalInput) finalInput.value = score.toFixed(2);
                window.recalculateAllRanks();
            }

            if (likesInput) likesInput.addEventListener('input', updateSocialRow);
            if (stickersInput) stickersInput.addEventListener('input', updateSocialRow);
        } else {
            const genericScore = row.querySelector('.input-generic-score');
            function updateGenericRow() {
                if (finalInput && genericScore) finalInput.value = genericScore.value;
                window.recalculateAllRanks();
            }
            if (genericScore) genericScore.addEventListener('input', updateGenericRow);
        }

        if (finalInput) {
            finalInput.addEventListener('input', window.recalculateAllRanks);
        }
    });

    // Real-time rank calculation for Semifinal Termin sections
    document.querySelectorAll('.termin-section').forEach(sec => {
        const tNum = sec.dataset.termin;
        sec.querySelectorAll('.input-termin-score, .input-final-score').forEach(inp => {
            inp.addEventListener('input', () => window.recalcTerminRanks(tNum));
        });
        window.recalcTerminRanks(tNum);
    });

    // Run initial rank calculation on page load
    window.recalculateAllRanks();
});
</script>
@endsection
