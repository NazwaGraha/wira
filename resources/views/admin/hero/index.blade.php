@extends('layouts.admin')

@section('title', 'Kelola Slider Hero Banner')
@section('page_title', 'Pengaturan Slider Hero Banner Beranda')

@section('top_actions')
    <a href="{{ route('admin.hero-slides.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Slide Gambar Baru
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 text-blue-900 p-5 rounded-2xl flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg flex-shrink-0">
            <i class="fa-solid fa-sliders"></i>
        </div>
        <div class="text-xs leading-relaxed">
            <div class="font-bold text-sm text-blue-950 mb-1">Mekanisme Slider Hero Beranda:</div>
            <ul class="list-disc pl-4 space-y-1 text-blue-800">
                <li>Jika terdapat <strong>1 gambar aktif</strong>: Gambar akan tampil sebagai latar statis beranda dengan gradien merah khas PMR.</li>
                <li>Jika terdapat <strong>lebih dari 1 gambar aktif</strong>: Sistem otomatis mengaktifkan mode <strong>Slider Carousel Interaktif</strong> dengan transisi crossfade halus setiap 6 detik, tombol navigasi, dan indikator titik (*dots*).</li>
                <li><strong>Efek gradien merah marun elegan</strong> tetap otomatis aktif di atas seluruh gambar slide agar teks judul dan tombol selalu tajam dan mudah dibaca.</li>
            </ul>
        </div>
    </div>

    <!-- Slides Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($slides as $slide)
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm flex flex-col group">
                <!-- Thumbnail with Gradient Preview -->
                <div class="relative h-48 sm:h-56 bg-slate-900 overflow-hidden">
                    <img src="{{ $slide->image_path }}" alt="{{ $slide->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    
                    <!-- Overlay simulasi gradien -->
                    <div class="absolute inset-0 bg-gradient-to-r from-red-950/80 via-pmr-primary/70 to-stone-950/70"></div>
                    
                    <!-- Badge Urutan & Status -->
                    <div class="absolute top-4 left-4 flex items-center gap-2">
                        <span class="bg-black/60 text-white font-extrabold text-xs px-3 py-1 rounded-full backdrop-blur-sm">
                            Urutan #{{ $slide->order_position }}
                        </span>
                        @if ($slide->is_active)
                            <span class="bg-emerald-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow">
                                Aktif di Beranda
                            </span>
                        @else
                            <span class="bg-slate-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    <!-- Slide text preview -->
                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <h4 class="font-extrabold text-base leading-tight">{{ $slide->title }}</h4>
                        @if ($slide->caption)
                            <p class="text-xs text-red-100 line-clamp-1 mt-1 opacity-90">{{ $slide->caption }}</p>
                        @endif
                    </div>
                </div>

                <!-- Footer Card Info & Actions -->
                <div class="p-5 flex items-center justify-between bg-white text-xs border-t border-slate-100 mt-auto">
                    <div class="text-slate-400 font-mono text-[11px] truncate max-w-[200px]" title="{{ $slide->image_path }}">
                        {{ $slide->image_path }}
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.hero-slides.edit', $slide->id) }}" class="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold transition">
                            Edit
                        </a>
                        <form action="{{ route('admin.hero-slides.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus slide ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-16 bg-white rounded-3xl border border-slate-200 text-slate-400">
                <i class="fa-solid fa-images text-4xl mb-3 text-slate-300"></i>
                <p class="text-sm font-semibold">Belum ada slide gambar yang ditambahkan.</p>
                <a href="{{ route('admin.hero-slides.create') }}" class="text-xs text-pmr-primary font-bold underline mt-2 inline-block">Tambah Slide Pertama Sekarang</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
