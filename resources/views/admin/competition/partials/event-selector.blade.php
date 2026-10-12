<!-- Event Selector & Edition Context Bar -->
<div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <!-- Left: Event Dropdown & Badge -->
    <div class="flex flex-wrap items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg shrink-0">
            <i class="fa-solid fa-trophy"></i>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider">Edisi / Tahun Lomba:</span>
                @if(isset($event) && $event && $event->is_active)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                        <i class="fa-solid fa-circle-check text-[9px]"></i> AKTIF DI WEBSITE
                    </span>
                @elseif(isset($event) && $event)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                        <i class="fa-solid fa-box-archive text-[9px]"></i> ARSIP RIWAYAT
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                        <i class="fa-solid fa-circle-question text-[9px]"></i> BELUM ADA EDISI
                    </span>
                @endif
            </div>

            <!-- Select Event Form -->
            <form method="GET" action="" class="mt-1 flex items-center gap-2 max-w-full">
                {{-- Preserve other query params like level, status, q --}}
                @foreach(request()->except(['event_id', 'page']) as $k => $v)
                    @if(is_string($v) || is_numeric($v))
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach

                @if(isset($allEvents) && $allEvents->isNotEmpty())
                    <select name="event_id" onchange="this.form.submit()" class="w-full sm:w-auto max-w-full bg-slate-50 border border-slate-300 hover:border-red-500 rounded-xl px-3 py-1.5 text-xs sm:text-sm font-black text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-100 cursor-pointer shadow-xs truncate">
                        @foreach($allEvents as $ev)
                            <option value="{{ $ev->id }}" {{ (isset($event) && $event && $event->id == $ev->id) ? 'selected' : '' }}>
                                {{ $ev->title }} {{ $ev->start_date ? '(' . $ev->start_date->format('Y') . ')' : '' }} {{ $ev->is_active ? '⭐ [AKTIF]' : '' }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <i class="fa-solid fa-calendar-xmark text-amber-600"></i> Tidak ada edisi lomba aktif atau mendatang
                    </span>
                @endif
            </form>
        </div>
    </div>

    <!-- Right: Event Quick Metadata & Link to Manager -->
    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 border-t md:border-t-0 pt-3 md:pt-0 border-slate-100">
        @if(isset($event) && $event)
            <div class="flex items-center gap-1.5 font-medium bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                <i class="fa-solid fa-calendar-days text-rose-500"></i>
                <span class="font-bold text-slate-700">{{ $event->start_date ? $event->start_date->translatedFormat('d F Y') : '-' }}</span>
            </div>
            <div class="flex items-center gap-1.5 font-medium bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                <i class="fa-solid fa-location-dot text-emerald-500"></i>
                <span class="font-bold text-slate-700 truncate max-w-[160px]">{{ $event->location ?: '-' }}</span>
            </div>
        @endif
        <a href="{{ route('admin.competition-event.index') }}" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg text-xs transition">
            <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i>
            <span>Kelola Riwayat Edisi</span>
        </a>
    </div>
</div>
