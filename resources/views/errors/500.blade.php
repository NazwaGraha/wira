@extends('errors.layout')

@section('title', 'Terjadi Kendala pada Sistem')
@section('code', '500 INTERNAL SERVER ERROR')

@section('badge_class', 'bg-red-500/10 text-red-400 border border-red-500/20')
@section('dot_class', 'bg-red-500 animate-pulse')
@section('glow_class', 'bg-red-500/20')
@section('icon', 'fa-solid fa-server')
@section('icon_color', 'text-red-400')

@section('message')
    Mohon maaf atas ketidaknyamanan ini. Sistem mendeteksi kendala saat memproses permintaan Anda. Tim teknis PMR Wira SMAN 1 Ciawi telah mencatat kejadian ini untuk segera diselesaikan.
@endsection

@section('helper_content')
    <div class="mt-6 p-4 rounded-2xl bg-slate-900/80 border border-slate-800 text-left text-xs">
        <div class="font-bold text-slate-200 mb-1 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-wrench text-amber-400"></i> Langkah Penyelesaian Cepat:
            </span>
            <span class="text-[10px] text-slate-500 font-mono">Ref: {{ substr(md5(request()->fullUrl() . microtime()), 0, 8) }}</span>
        </div>
        <ul class="text-slate-400 space-y-1 mt-2 list-disc list-inside">
            <li>Tunggu 1–2 menit lalu coba muat ulang halaman.</li>
            <li>Periksa koneksi internet pada perangkat Anda.</li>
            <li>Bila kendala berulang, hubungi tim pengurus melalui WhatsApp dengan menyertakan tautan yang dibuka.</li>
        </ul>
        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between">
            <button type="button" onclick="window.location.reload()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-[11px] border border-slate-700 transition flex items-center gap-1.5">
                <i class="fa-solid fa-rotate-right text-emerald-400"></i> Coba Muat Ulang
            </button>
            <a href="https://wa.me/6281283921820?text=Halo%20Admin%20PMR%20Wira%20SMAN%201%20Ciawi,%20saya%20menemukan%20kendala%20500%20pada%20halaman:%20{{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 font-bold text-[11px] border border-emerald-500/30 transition flex items-center gap-1.5">
                <i class="fa-brands fa-whatsapp text-emerald-400"></i> Lapor ke Admin
            </a>
        </div>
    </div>
@endsection
