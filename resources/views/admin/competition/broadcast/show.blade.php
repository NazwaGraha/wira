@extends('layouts.admin')

@section('title', 'Detail Siaran Email - ' . $broadcast->subject)
@section('page_title', 'Detail & Log Siaran Email')

@section('top_actions')
    <a href="{{ route('admin.competition-broadcast.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
        &larr; Kembali ke Riwayat Siaran
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Metadata Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $broadcast->status == 'sent' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $broadcast->status == 'sent' ? 'TERKIRIM' : 'GAGAL / SEBAGIAN' }}
                    </span>
                    <span class="text-xs text-slate-400 font-semibold">
                        {{ $broadcast->created_at ? $broadcast->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                    </span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 mt-2">{{ $broadcast->subject }}</h2>
                <div class="text-xs text-slate-500 font-medium mt-1">
                    Dikirim oleh: <span class="text-slate-800 font-bold">{{ $broadcast->sent_by ?: 'Administrator' }}</span>
                </div>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                <form action="{{ route('admin.competition-broadcast.destroy', $broadcast->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat siaran ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-trash-can"></i> Hapus Riwayat
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 py-6 border-b border-slate-100 text-xs">
            <div>
                <div class="font-bold text-slate-400 uppercase tracking-wider">Target Edisi Lomba</div>
                <div class="font-extrabold text-slate-800 text-sm mt-0.5">
                    {{ $broadcast->target_scope == 'all' ? 'Semua Edisi' : ($broadcast->event->title ?? 'Edisi ID ' . $broadcast->competition_event_id) }}
                </div>
            </div>
            <div>
                <div class="font-bold text-slate-400 uppercase tracking-wider">Target Jenjang PMR</div>
                <div class="font-extrabold text-slate-800 text-sm mt-0.5">
                    {{ $broadcast->target_level == 'all' ? 'Semua Jenjang' : 'PMR ' . $broadcast->target_level }}
                </div>
            </div>
            <div>
                <div class="font-bold text-slate-400 uppercase tracking-wider">Status Target</div>
                <div class="font-extrabold text-slate-800 text-sm mt-0.5">
                    {{ $broadcast->target_status == 'verified' ? 'Hanya Terverifikasi' : 'Semua Pendaftar' }}
                </div>
            </div>
            <div>
                <div class="font-bold text-slate-400 uppercase tracking-wider">Total Penerima</div>
                <div class="font-black text-indigo-700 text-base mt-0.5">
                    {{ $broadcast->recipient_count }} Kontak Email
                </div>
            </div>
        </div>
    </div>

    <!-- Content & Live Preview Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Kolom Kiri: Pratinjau Tampilan Email -->
        <div class="lg:col-span-7 space-y-4">
            <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-envelope-open text-red-600"></i> Pratinjau Tampilan Email Resmi
            </h3>

            <div class="bg-slate-100 p-4 sm:p-6 rounded-2xl border border-slate-200 flex justify-center">
                <div class="w-full max-w-lg bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-red-900 to-red-600 text-white p-6 text-center">
                        <div class="text-[11px] font-black uppercase tracking-widest text-red-200">PALANG MERAH REMAJA &bull; PMI</div>
                        <h4 class="text-xl font-black mt-1">{{ $broadcast->headline ?: 'SUA BHAKTI BERKARYA' }}</h4>
                        <span class="inline-block px-3 py-0.5 bg-white/20 rounded-full text-[10px] font-bold mt-2">Official Announcement</span>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4 text-xs leading-relaxed text-slate-800">
                        <div class="font-bold text-slate-900 text-sm">
                            Yth. Bapak/Ibu Pembina & Kontingen PMR<br>
                            <span class="text-red-600">[Nama Sekolah Penerima]</span>
                        </div>

                        <div class="prose max-w-none text-slate-700 font-normal leading-relaxed text-xs [&_img]:max-w-full [&_img]:rounded-lg [&_img]:my-2 [&_p]:mb-3">{!! $broadcast->content !!}</div>

                        @if($broadcast->button_text && $broadcast->button_url)
                            <div class="text-center pt-2 pb-2">
                                <a href="{{ $broadcast->button_url }}" target="_blank" class="inline-block bg-red-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm hover:bg-red-700">
                                    {{ $broadcast->button_text }} &rarr;
                                </a>
                            </div>
                        @endif

                        @if($broadcast->notes)
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-600">
                                <strong>Catatan Panitia:</strong><br>
                                {!! nl2br(e($broadcast->notes)) !!}
                            </div>
                        @endif

                        <div class="pt-2 text-slate-500 text-[11px]">
                            Salam Kemanusiaan,<br>
                            <strong class="text-slate-800">Panitia Pelaksana SUA BHAKTI BERKARYA</strong><br>
                            PMR WIRA
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-slate-50 p-4 border-t border-slate-200 text-center text-[11px] text-slate-400">
                        PMR WIRA &bull; SUA BHAKTI BERKARYA
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Log Penerima Email -->
        <div class="lg:col-span-5 space-y-4">
            <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-list-check text-indigo-600"></i> Log Penerima Email ({{ count($broadcast->recipients_data ?? []) }})
            </h3>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="max-h-[550px] overflow-y-auto divide-y divide-slate-100 text-xs">
                    @forelse($broadcast->recipients_data ?? [] as $r)
                        <div class="p-3.5 hover:bg-slate-50 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="font-extrabold text-slate-900 uppercase truncate">{{ $r['school_name'] ?? 'Penerima' }}</div>
                                    <div class="text-[11px] font-mono text-slate-600 truncate mt-0.5">{{ $r['email'] ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $r['advisor_name'] ?? '' }} &bull; {{ $r['level'] ?? '' }}
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black shrink-0 {{ ($r['status'] ?? '') == 'sent' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ ($r['status'] ?? '') == 'sent' ? 'Terkirim' : 'Gagal' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Tidak ada rincian data penerima yang tersimpan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
