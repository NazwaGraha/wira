@extends('layouts.admin')

@section('title', 'Daftar Pembina')
@section('page_title', 'Data Pembina PMR Wira')

@section('top_actions')
    <a href="{{ route('admin.advisors.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md shadow-red-950/20 transition flex items-center gap-1.5">
        <i class="fa-solid fa-user-plus"></i>
        <span>Tambah Pembina</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl shrink-0"></i>
            <div class="text-sm font-semibold">{!! session('success') !!}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl shrink-0"></i>
            <div class="text-sm font-semibold">{!! session('error') !!}</div>
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-6">
        <!-- Stat Badges & Search -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-900 text-white">
                    <i class="fa-solid fa-shield-halved text-red-400 text-xs"></i>
                    <span>Total Pembina:</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-white/20">{{ $totalAdvisors }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    <span>Aktif: {{ $totalAktif }}</span>
                </span>
            </div>

            <form action="{{ route('admin.advisors.index') }}" method="GET" class="w-full md:w-auto flex flex-wrap gap-2">
                <div class="relative w-full md:w-72">
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama, NIP, atau jabatan..." class="w-full bg-slate-50 border border-slate-200 pl-10 pr-4 py-2 rounded-xl text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                </div>
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-bold transition">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('admin.advisors.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl text-sm font-bold transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-bold border-y border-slate-200">
                    <tr>
                        <th class="px-4 py-4 w-16 text-center">Urutan</th>
                        <th class="px-4 py-4 w-20">Foto</th>
                        <th class="px-6 py-4">Nama Lengkap & NIP</th>
                        <th class="px-6 py-4">Jabatan</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($advisors as $advisor)
                        @php
                            $photoUrl = $advisor->photo_url;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-4 text-center font-bold text-slate-700">
                                {{ $advisor->order_position }}
                            </td>
                            <td class="px-4 py-4">
                                <img src="{{ $photoUrl }}" 
                                     alt="{{ $advisor->name }}" 
                                     class="w-12 h-16 object-cover rounded-xl border border-slate-200 shadow-sm"
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($advisor->name) }}&background=991b1b&color=ffffff&bold=true&size=256';">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 text-base">
                                    {{ $advisor->name }}{{ $advisor->title_degree ? ', ' . $advisor->title_degree : '' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    <span class="font-bold text-slate-700">NIP:</span> {{ $advisor->nip ?: '-' }}
                                    @if($advisor->gender)
                                        &bull; {{ $advisor->gender }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-900 text-red-300">
                                    <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                    {{ $advisor->position ?: 'Pembina PMR' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="space-y-0.5">
                                    @if($advisor->phone)
                                        <div class="flex items-center gap-1.5 text-slate-700 font-semibold">
                                            <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                            <span>{{ $advisor->phone }}</span>
                                        </div>
                                    @endif
                                    @if($advisor->email)
                                        <div class="flex items-center gap-1.5 text-slate-500">
                                            <i class="fa-regular fa-envelope text-slate-400 text-xs"></i>
                                            <span>{{ $advisor->email }}</span>
                                        </div>
                                    @endif
                                    @if(!$advisor->phone && !$advisor->email)
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($advisor->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.advisors.edit', $advisor) }}" class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl transition text-xs font-bold flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </a>
                                    <form action="{{ route('admin.advisors.destroy', $advisor) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Pembina ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition text-xs font-bold flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-user-tie text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-bold text-slate-600">Belum ada data Pembina.</span>
                                <div class="text-xs text-slate-400 mt-1">Silakan klik tombol <strong>"Tambah Pembina"</strong> di atas.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($advisors->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $advisors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
