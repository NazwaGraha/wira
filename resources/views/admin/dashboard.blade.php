@extends('layouts.admin')

@section('title', 'Dashboard Backoffice')
@section('page_title', 'Ringkasan Dashboard CMS')

@section('top_actions')
    <a href="{{ route('admin.articles.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tulis Artikel Baru
    </a>
@endsection

@section('content')
<!-- KPI / Metrik Utama (Matching Mockup 08) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1 -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Artikel</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalArticles }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> Terus bertambah
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-xl">
            <i class="fa-solid fa-newspaper"></i>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Draft / Review</div>
            <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ $draftArticles }}</div>
            <div class="text-[11px] text-slate-500 font-semibold mt-1">
                Perlu konfirmasi
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pembaca</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalViews) }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                <i class="fa-solid fa-eye"></i> Impresi halaman
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-chart-line"></i>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendaftar Relawan</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalRegistrations }}</div>
            <div class="text-[11px] text-pmr-primary font-semibold mt-1">
                Masa Bakti 2026/2027
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-user-plus"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Recent Articles Table (8 Cols - Matching Mockup 08) -->
    <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-extrabold text-slate-900 text-lg">Artikel Terbaru</h3>
                <p class="text-xs text-slate-500">Daftar artikel yang baru saja diterbitkan di portal publik.</p>
            </div>
            <a href="{{ route('admin.articles.index') }}" class="text-xs text-pmr-primary font-bold hover:underline">
                Kelola Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
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
            <h4 class="font-extrabold text-slate-900 text-sm mb-4">Pendaftar Relawan Baru</h4>
            <div class="space-y-3">
                @forelse ($recentRegistrations as $reg)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-xs text-slate-800">{{ $reg->full_name }}</div>
                            <div class="text-[10px] text-slate-500">{{ $reg->class_grade }} &bull; {{ $reg->interest_field }}</div>
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
