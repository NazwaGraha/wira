@extends('layouts.admin')

@section('title', 'Siaran Email & Informasi Lomba')
@section('page_title', 'Siaran Email & Undangan Kegiatan')

@section('top_actions')
    <a href="{{ route('admin.competition-broadcast.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-sm shadow-red-600/30">
        <i class="fa-solid fa-paper-plane"></i> Buat Siaran Email Baru
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Stat Cards Kontak Email Database -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Email Terkumpul</div>
                <div class="text-3xl font-black text-slate-900 mt-1">{{ $totalUniqueEmails }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5 font-medium">Kontak Unik Sekolah / Pembina</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Email PMR Mula (SD)</div>
                <div class="text-3xl font-black text-blue-600 mt-1">{{ $totalMulaEmails }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5 font-medium">Unit Tingkat SD / MI</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Email PMR Madya (SMP)</div>
                <div class="text-3xl font-black text-red-600 mt-1">{{ $totalMadyaEmails }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5 font-medium">Unit Tingkat SMP / MTs</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-school"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Email PMR Wira (SMA)</div>
                <div class="text-3xl font-black text-amber-600 mt-1">{{ $totalWiraEmails }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5 font-medium">Unit Tingkat SMA / SMK / MA</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-building-columns"></i>
            </div>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="bg-gradient-to-r from-red-900 to-red-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <h3 class="text-lg font-black flex items-center gap-2">
                <i class="fa-solid fa-bullhorn text-amber-300"></i> Siaran Informasi & Undangan Lomba PMR
            </h3>
            <p class="text-xs text-red-100 max-w-2xl leading-relaxed">
                Kirimkan pengumuman penting, panduan teknis (juklak/juknis), undangan lomba tahunan, maupun informasi kegiatan PMI/PMR langsung ke seluruh alamat email sekolah, pembina, dan organisasi yang pernah mendaftar.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.competition-broadcast.create') }}" class="bg-white text-red-700 hover:bg-red-50 font-extrabold px-5 py-3 rounded-xl text-xs transition inline-flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-paper-plane"></i> Mulai Tulis Pesan Siaran
            </a>
        </div>
    </div>

    <!-- Riwayat Pengiriman Siaran Email -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-black text-slate-900 text-base">Riwayat Siaran Email</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar informasi dan siaran email yang pernah dikirimkan oleh panitia</p>
            </div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-100 px-3 py-1 rounded-full">
                {{ $broadcasts->total() }} Siaran
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Waktu Kirim</th>
                        <th class="px-6 py-4">Subjek & Judul Pesan</th>
                        <th class="px-6 py-4">Target Audiens</th>
                        <th class="px-6 py-4 text-center">Penerima</th>
                        <th class="px-6 py-4">Pengirim</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-xs">
                    @forelse($broadcasts as $bc)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-500">
                                <div class="font-bold text-slate-800">{{ $bc->created_at ? $bc->created_at->translatedFormat('d M Y') : '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $bc->created_at ? $bc->created_at->format('H:i') . ' WIB' : '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $bc->subject }}</div>
                                @if($bc->headline)
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-medium">Banner: {{ $bc->headline }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $bc->target_scope == 'all' ? 'Semua Edisi Lomba' : ($bc->event->title ?? 'Edisi Tertentu') }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $bc->target_level == 'all' ? 'bg-indigo-50 text-indigo-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $bc->target_level == 'all' ? 'Semua Jenjang' : 'PMR ' . $bc->target_level }}
                                    </span>
                                    @if($bc->target_status == 'verified')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">Hanya Lunas</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <i class="fa-solid fa-users text-[10px] mr-1"></i> {{ $bc->recipient_count }} Kontak
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $bc->sent_by ?: 'Admin' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($bc->status == 'sent')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Terkirim
                                    </span>
                                @elseif($bc->status == 'failed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-100 text-rose-800 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Gagal
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ ucfirst($bc->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.competition-broadcast.show', $bc->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>
                                    <form action="{{ route('admin.competition-broadcast.destroy', $bc->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat siaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Riwayat">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                                <div class="font-bold text-slate-600">Belum ada riwayat siaran email yang dikirim.</div>
                                <div class="text-xs text-slate-400 mt-1">Gunakan tombol "Buat Siaran Email Baru" untuk mulai menyebarkan informasi kegiatan atau lomba.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($broadcasts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $broadcasts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
