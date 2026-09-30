@extends('layouts.admin')

@section('title', 'Daftar Anggota')
@section('page_title', 'Daftar Biodata Anggota PMR')

@section('top_actions')
    <a href="{{ route('admin.members.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-red-950/20 transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Input Data Baru
    </a>
@endsection

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500">Total Anggota:</span>
            <span class="bg-slate-100 text-pmr-primary font-bold px-3 py-1 rounded-lg text-xs">{{ $totalMembers }}</span>
        </div>

        <form action="{{ route('admin.members.index') }}" method="GET" class="w-full md:w-auto flex gap-2">
            <div class="relative w-full md:w-64">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari Nama / NIS..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-pmr-primary focus:ring-1 focus:ring-pmr-primary">
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-bold transition">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.members.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl text-sm font-bold transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-bold border-y border-slate-200">
                <tr>
                    <th class="px-6 py-4">Foto</th>
                    <th class="px-6 py-4">Nama Lengkap & NIS</th>
                    <th class="px-6 py-4">Kelas / Tingkat</th>
                    <th class="px-6 py-4">Kontak</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($members as $member)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            @if($member->photo)
                                <img src="{{ Storage::url($member->photo) }}" class="w-12 h-16 object-cover rounded-lg border border-slate-200 shadow-sm" alt="Foto">
                            @else
                                <div class="w-12 h-16 rounded-lg border border-slate-200 bg-slate-100 flex items-center justify-center text-slate-400 text-xl">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900 text-base">{{ $member->name }}</div>
                            <div class="text-[11px] font-bold text-pmr-primary uppercase tracking-wider my-0.5">{{ $member->position ?: 'Anggota' }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">NIS: {{ $member->nis ?: '-' }} &bull; {{ $member->gender }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-pmr-50 text-pmr-primary px-3 py-1 rounded-full text-[11px] font-bold border border-red-100">
                                {{ $member->class_grade ?: 'Belum diisi' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs"><i class="fa-solid fa-phone w-4 text-slate-400"></i> {{ $member->phone ?: '-' }}</div>
                            <div class="text-xs mt-1"><i class="fa-solid fa-envelope w-4 text-slate-400"></i> {{ $member->email ?: '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.members.edit', $member->id) }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition tooltip" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data anggota ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-500 hover:text-white transition tooltip" title="Hapus Data">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-slate-400 mb-2"><i class="fa-solid fa-users-slash text-4xl"></i></div>
                            <div class="font-bold text-slate-600">Belum ada data anggota</div>
                            <div class="text-xs text-slate-500 mt-1">Silakan input data anggota PMR baru.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $members->links() }}
    </div>
</div>
@endsection
