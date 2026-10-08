@extends('layouts.admin')

@section('title', 'Detail & Riwayat: ' . $event->title)
@section('page_title', 'Detail Riwayat Edisi Lomba')

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.competition-event.index') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Riwayat</span>
        </a>
        <a href="{{ route('admin.competition-event.edit', $event->id) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit Edisi Ini</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Event Info -->
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    @if($event->is_active)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800">
                            <i class="fa-solid fa-circle-check text-[10px]"></i> SEDANG AKTIF DI WEBSITE
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                            <i class="fa-solid fa-box-archive text-[10px]"></i> ARSIP RIWAYAT
                        </span>
                    @endif
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight pt-1">
                    {{ $event->title }}
                </h2>
                @if($event->theme)
                    <p class="text-sm font-semibold text-red-600 italic">
                        &ldquo;{{ $event->theme }}&rdquo;
                    </p>
                @endif
            </div>

            @if(!$event->is_active)
                <div>
                    <form action="{{ route('admin.competition-event.activate', $event->id) }}" method="POST" onsubmit="return confirm('Aktifkan edisi ini sebagai event utama?')">
                        @csrf
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-play"></i>
                            <span>Jadikan Event Aktif</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Pelaksanaan</div>
                <div class="text-sm font-extrabold text-slate-800 mt-1">
                    {{ $event->start_date ? $event->start_date->translatedFormat('d M Y') : '-' }}
                </div>
                @if($event->end_date && $event->end_date != $event->start_date)
                    <div class="text-[10px] text-slate-500">s/d {{ $event->end_date->translatedFormat('d M Y') }}</div>
                @endif
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Lokasi</div>
                <div class="text-sm font-extrabold text-slate-800 mt-1 truncate" title="{{ $event->location }}">
                    {{ $event->location ?: '-' }}
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Biaya Default</div>
                <div class="text-sm font-extrabold text-slate-800 mt-1 font-mono">
                    Rp {{ number_format($event->registration_fee ?: 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Rekening Transfer</div>
                <div class="text-sm font-extrabold text-slate-800 mt-1 font-mono">
                    {{ $event->bank_name ?: '-' }} {{ $event->bank_account_number }}
                </div>
                <div class="text-[10px] text-slate-500 truncate">a.n {{ $event->bank_account_holder }}</div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-sitemap"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500 font-semibold">Cabang Lomba</div>
                <div class="text-2xl font-black text-slate-900">{{ $event->categories->count() }} Cabang</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-school"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500 font-semibold">Sekolah Terdaftar</div>
                <div class="text-2xl font-black text-slate-900">{{ $event->registrations->count() }} Pendaftar</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500 font-semibold">Total Regu Terdaftar</div>
                <div class="text-2xl font-black text-slate-900">
                    {{ $event->registrations->sum('teams_count') }} Regu
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tabs: Cabang Lomba & Pendaftar -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Cabang Lomba -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-indigo-600"></i>
                    <span>Daftar Cabang Lomba ({{ $event->categories->count() }})</span>
                </h3>
            </div>
            <div class="divide-y divide-slate-100 max-h-[460px] overflow-y-auto">
                @forelse($event->categories as $cat)
                    <div class="p-4 hover:bg-slate-50 flex items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="font-black text-slate-900">{{ $cat->name }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-slate-100 text-slate-600">
                                    {{ $cat->code }}
                                </span>
                                <span class="font-bold {{ $cat->level == 'Mula' ? 'text-blue-600' : ($cat->level == 'Madya' ? 'text-indigo-600' : 'text-amber-600') }}">
                                    PMR {{ $cat->level }}
                                </span>
                                <span class="text-slate-400">&bull;</span>
                                <span class="text-slate-500 font-medium">{{ $cat->gender_category }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-bold text-red-600 font-mono">
                                Rp {{ number_format($cat->registration_fee ?: ($event->registration_fee ?: 0), 0, ',', '.') }}
                            </span>
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">
                                {{ $cat->point_tier == 'tier_1' ? '10/8/6 Poin' : ($cat->point_tier == 'tier_2' ? '8/6/4 Poin' : '3/2/1 Poin') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada cabang lomba yang didaftarkan untuk edisi ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Sekolah Terdaftar -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-school text-emerald-600"></i>
                    <span>Riwayat Pendaftar Sekolah ({{ $event->registrations->count() }})</span>
                </h3>
            </div>
            <div class="divide-y divide-slate-100 max-h-[460px] overflow-y-auto">
                @forelse($event->registrations as $reg)
                    <div class="p-4 hover:bg-slate-50 flex items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="font-black text-slate-900">{{ $reg->school_name }}</div>
                            <div class="text-slate-500 text-[11px] mt-0.5">
                                Pembina: {{ $reg->coach_name }} ({{ $reg->coach_phone }})
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                Invoice: {{ $reg->registration_code }} &bull; {{ $reg->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>
                        <div class="text-right shrink-0 space-y-1">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $reg->status == 'verified' ? 'bg-emerald-100 text-emerald-800' : ($reg->status == 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ $reg->status }}
                            </span>
                            <div class="text-[11px] font-bold text-slate-700">
                                {{ $reg->teams_count }} Regu
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada sekolah yang terdaftar pada edisi ini.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
