@extends('layouts.admin')

@section('title', 'Dashboard Backoffice')
@section('page_title', 'Ringkasan Dashboard CMS')

@section('top_actions')
    <a href="{{ route('admin.articles.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> <span class="hidden sm:inline">Tulis Artikel Baru</span><span class="sm:hidden">Tulis</span>
    </a>
@endsection

@section('content')
<!-- KPI / Metrik Utama (Matching Mockup 08) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
    <!-- Card 1 -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Artikel</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $totalArticles }}</div>
            <div class="text-[10px] sm:text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> <span class="truncate">Bertambah</span>
            </div>
        </div>
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg sm:text-xl shrink-0">
            <i class="fa-solid fa-newspaper"></i>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Draft / Review</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $draftArticles }}</div>
            <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-1 truncate">
                Perlu konfirmasi
            </div>
        </div>
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pembaca</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalViews) }}</div>
            <div class="text-[10px] sm:text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                <i class="fa-solid fa-eye"></i> <span class="truncate">Impresi</span>
            </div>
        </div>
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
            <i class="fa-solid fa-chart-line"></i>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Pendaftar Calon Anggota</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $totalRegistrations }}</div>
            <div class="text-[10px] sm:text-[11px] text-pmr-primary font-semibold mt-1 truncate">
                2026/2027
            </div>
        </div>
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
            <i class="fa-solid fa-user-plus"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
    <!-- Recent Articles Table (8 Cols - Matching Mockup 08) -->
    <div class="lg:col-span-8 bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base sm:text-lg">Artikel Terbaru</h3>
                <p class="text-[11px] sm:text-xs text-slate-500">Daftar artikel yang baru saja diterbitkan di portal publik.</p>
            </div>
            <a href="{{ route('admin.articles.index') }}" class="text-xs text-pmr-primary font-bold hover:underline shrink-0">
                Kelola Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto touch-pan-x">
            <table class="w-full text-left text-xs min-w-[500px]">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-y border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Judul Artikel</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Views</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentArticles as $art)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                <a href="{{ route('artikel.show', $art->slug) }}" target="_blank" class="hover:text-pmr-primary">
                                    {{ Str::limit($art->title, 40) }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <span class="bg-slate-100 px-2 py-0.5 rounded text-[11px] font-semibold">{{ $art->category->name ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if ($art->status === 'published')
                                    <span class="bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">Published</span>
                                @else
                                    <span class="bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">Draft</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-600">
                                {{ $art->views_count }}
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="{{ route('admin.articles.edit', $art->id) }}" class="text-blue-600 hover:text-blue-800 font-bold">Edit</a>
                                <a href="{{ route('artikel.show', $art->slug) }}" target="_blank" class="text-slate-400 hover:text-slate-600">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-400">Belum ada artikel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Side: Live Blood Stock & Recent Candidate Registrations (4 Cols) -->
    <div class="lg:col-span-4 space-y-6">
        <!-- SUA BHAKTI BERKARYA Management Widget -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-amber-950 rounded-3xl p-6 text-white shadow-lg border border-amber-900/40 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-center justify-between mb-3 relative z-10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-sm font-black shadow-md shadow-amber-500/30">
                        <i class="fa-solid fa-flag"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-sm">SUA BHAKTI BERKARYA</h4>
                        <div class="text-[10px] text-amber-200">{{ $activeCompetitionEvent->title ?? 'Lomba PMR Wira SMAN 1 Ciawi' }}</div>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> {{ $totalJuries }} Juri
                </span>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed mb-4 relative z-10">
                Akses cepat manajemen registrasi akun juri lapangan, input penilaian peserta lomba, dan portal juri mobile.
            </p>

            <div class="grid grid-cols-2 gap-2 relative z-10">
                <a href="{{ route('admin.competition-juries.index') }}" class="p-3 bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/30 rounded-2xl transition flex flex-col justify-between group">
                    <div class="w-7 h-7 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center text-xs font-bold mb-2 group-hover:scale-110 transition">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div>
                        <div class="font-black text-xs text-amber-300 leading-snug">Input & Registrasi Juri</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Kelola akun & mata lomba</div>
                    </div>
                </a>

                <a href="{{ route('admin.competition-scores.index') }}" class="p-3 bg-white/10 hover:bg-white/15 border border-white/10 rounded-2xl transition flex flex-col justify-between group">
                    <div class="w-7 h-7 rounded-lg bg-teal-500 text-white flex items-center justify-center text-xs font-bold mb-2 group-hover:scale-110 transition">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <div class="font-black text-xs text-white leading-snug">Input Nilai Lomba</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Lembar nilai admin</div>
                    </div>
                </a>
            </div>

            <div class="mt-2.5 pt-2.5 border-t border-white/10 flex items-center justify-between text-[11px] relative z-10">
                <a href="{{ route('jury.login') }}" target="_blank" class="text-sky-300 hover:text-sky-200 font-bold flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-mobile-screen-button text-xs"></i> Portal Juri Mobile &rarr;
                </a>
                <a href="{{ route('admin.competition-registrations.index') }}" class="text-slate-400 hover:text-white transition">
                    Verifikasi Pendaftar &rarr;
                </a>
            </div>
        </div>

        <!-- Wira Agent AI Knowledge Hub Widget -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-red-950 rounded-3xl p-6 text-white shadow-lg border border-red-900/40 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-red-600/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-center justify-between mb-3 relative z-10">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center text-xs shadow-md shadow-red-600/40">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-sm">Wira Agent AI</h4>
                        <div class="text-[10px] text-red-200">Otak & Pengetahuan Web</div>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Siap
                </span>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed mb-4 relative z-10">
                Sinkronkan seluruh konten website (artikel, kegiatan, prestasi, struktur organisasi, dan pedoman) agar Wira Agent selalu menguasai informasi terkini.
            </p>

            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/10 mb-4 text-xs space-y-1 relative z-10">
                <div class="flex justify-between items-center text-slate-300 text-[11px]">
                    <span>Topik Terindeks:</span>
                    <span class="font-bold text-white">{{ $wiraKnowledgeStats['total_topics'] ?? 0 }} Topik</span>
                </div>
                <div class="flex justify-between items-center text-slate-300 text-[11px]">
                    <span>Terakhir Diserap:</span>
                    <span class="font-bold text-amber-300">{{ $wiraKnowledgeStats['updated_at'] ?? 'Belum disinkronkan' }}</span>
                </div>
            </div>

            <form action="{{ route('admin.wira-agent.sync-knowledge') }}" method="POST" class="relative z-10">
                @csrf
                <button type="submit" onclick="this.disabled=true; this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin mr-1.5\'></i> Menyerap Seluruh Web...'; this.form.submit();" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow-md shadow-red-900/50 transition flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Sinkronkan Pengetahuan Sekarang</span>
                </button>
            </form>
        </div>

        <!-- Bagan Kepengurusan Widget -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-extrabold text-slate-900 text-sm">Bagan Kepengurusan</h4>
                <a href="{{ route('admin.organization.index') }}" class="text-[11px] text-pmr-primary font-bold hover:underline">Kelola &rarr;</a>
            </div>
            <div class="p-3.5 bg-red-50/60 rounded-2xl border border-red-100 flex items-center justify-between">
                <div>
                    <div class="font-bold text-xs text-slate-900">{{ $orgSetting->title ?? 'Struktur Organisasi' }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">{{ $totalOrgMembers }} Pejabat / Pengurus Terdaftar</div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-pmr-primary text-white flex items-center justify-center text-xs shadow-sm">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
            </div>
        </div>

        <!-- Kelola Kegiatan Widget -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-extrabold text-slate-900 text-sm">Agenda & Kegiatan</h4>
                <a href="{{ route('admin.activities.index') }}" class="text-[11px] text-pmr-primary font-bold hover:underline">Kelola &rarr;</a>
            </div>
            <div class="p-3.5 bg-blue-50/60 rounded-2xl border border-blue-100 flex items-center justify-between">
                <div>
                    <div class="font-bold text-xs text-slate-900">{{ $totalActivities }} Kegiatan Terdaftar</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">Tersimpan di database & web publik</div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs shadow-sm">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <!-- Live Blood Stock Status Widget -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-extrabold text-slate-900 text-sm">Status Ketersediaan Darah</h4>
                <a href="{{ route('donor-darah') }}" target="_blank" class="text-[11px] text-pmr-primary font-bold hover:underline">Portal Donor &rarr;</a>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                @foreach ($bloodStocks as $bs)
                    <div class="border rounded-xl p-3 flex items-center justify-between {{ $bs->status === 'aman' ? 'bg-emerald-50/50 border-emerald-200' : ($bs->status === 'menipis' ? 'bg-amber-50/50 border-amber-200' : 'bg-rose-50/50 border-rose-200') }}">
                        <div class="font-extrabold text-lg text-pmr-primary">{{ $bs->blood_type }}</div>
                        <div class="text-right">
                            <div class="text-[10px] font-bold uppercase {{ $bs->status === 'aman' ? 'text-emerald-700' : ($bs->status === 'menipis' ? 'text-amber-700' : 'text-rose-700') }}">
                                {{ $bs->status }}
                            </div>
                            <div class="text-[10px] text-slate-500">{{ $bs->bags_count }} ktg</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Registered Candidates -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-extrabold text-slate-900 text-sm">Pendaftar Calon Anggota Baru</h4>
                <a href="{{ route('admin.member-registrations.index') }}" class="text-[11px] text-pmr-primary font-bold hover:underline">
                    Kelola Semua &rarr;
                </a>
            </div>
            <div class="space-y-3">
                @forelse ($recentRegistrations as $reg)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-xs text-slate-800">{{ $reg->full_name }}</div>
                            <div class="text-[10px] text-slate-500">{{ $reg->class_grade }}{{ $reg->interest_field ? ' • '.$reg->interest_field : '' }}</div>
                        </div>
                        <span class="text-[10px] bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-full">
                            {{ $reg->status }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-slate-400">Belum ada pendaftaran masuk.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
