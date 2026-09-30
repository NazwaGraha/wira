@extends('layouts.admin')

@section('title', 'Kelola Galeri Foto & Video')
@section('page_title', 'Kelola Galeri')

@section('top_actions')
<a href="{{ route('admin.gallery.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-red-950/20 transition flex items-center gap-2">
    <i class="fa-solid fa-plus"></i> Tambah Media
</a>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left text-sm text-slate-600">
        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
            <tr>
                <th class="px-6 py-4">Tampilan</th>
                <th class="px-6 py-4">Judul & Kategori</th>
                <th class="px-6 py-4">Tipe Media</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($galleries as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="w-24 h-16 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shadow-inner">
                            <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 text-base mb-1">{{ $item->title }}</div>
                        <div class="text-xs text-slate-500">Kategori: <span class="font-semibold">{{ $item->category ?? '-' }}</span></div>
                    </td>
                    <td class="px-6 py-4">
                        @if($item->type === 'video')
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-2 w-max">
                                <i class="fa-brands fa-youtube"></i> Video
                            </span>
                        @else
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-2 w-max">
                                <i class="fa-regular fa-image"></i> Foto
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.gallery.edit', $item->id) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.gallery.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus media ini dari galeri?');">
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
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-images text-4xl mb-3 text-slate-300 block"></i>
                        Belum ada media foto atau video dalam galeri.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($galleries->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $galleries->links() }}
        </div>
    @endif
</div>
@endsection
