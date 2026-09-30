@extends('layouts.admin')

@section('title', 'Jadwal & Event Donor Darah')
@section('page_title', 'Jadwal & Event Donor Darah')

@section('top_actions')
<a href="{{ route('admin.blood-donation-events.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-red-950/20 transition flex items-center gap-2">
    <i class="fa-solid fa-plus"></i> Tambah Jadwal
</a>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left text-sm text-slate-600">
        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
            <tr>
                <th class="px-6 py-4">Status / Countdown</th>
                <th class="px-6 py-4">Judul Event & Waktu</th>
                <th class="px-6 py-4">Lokasi</th>
                <th class="px-6 py-4">Target (Kantong)</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($events as $ev)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        @if($ev->is_active)
                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-2 inline-flex">
                                <i class="fa-solid fa-clock animate-pulse"></i> Aktif di Web
                            </span>
                        @else
                            <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Draft / Selesai</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 text-base mb-1">{{ $ev->title }}</div>
                        <div class="text-xs flex items-center gap-2 text-slate-500">
                            <i class="fa-solid fa-calendar text-red-400"></i> {{ \Carbon\Carbon::parse($ev->event_date)->translatedFormat('l, d F Y') }}
                            &bull; <i class="fa-solid fa-clock text-amber-400"></i> {{ \Carbon\Carbon::parse($ev->time_start)->format('H:i') }} - {{ $ev->time_end ? \Carbon\Carbon::parse($ev->time_end)->format('H:i') : 'Selesai' }} WIB
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        {{ $ev->location }}
                    </td>
                    <td class="px-6 py-4 font-bold">
                        {{ $ev->target_bags ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.blood-donation-events.edit', $ev->id) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.blood-donation-events.destroy', $ev->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-3 text-slate-300 block"></i>
                        Belum ada jadwal kegiatan donor darah.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
