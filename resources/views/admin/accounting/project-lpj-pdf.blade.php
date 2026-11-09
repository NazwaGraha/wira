<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ Keuangan Proyek - {{ $project->code }} - {{ $project->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8 font-sans antialiased text-xs">

    <!-- Top Action Bar for Screen View -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('admin.accounting.index', ['project_id' => $project->id]) }}" class="px-4 py-2 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition">
            &larr; Kembali ke Dashboard Akuntansi
        </a>
        <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs shadow-md transition cursor-pointer">
            🖨️ Cetak / Simpan PDF Dokumen Resmi
        </button>
    </div>

    <!-- Official Paper Document -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-lg border border-slate-200 space-y-6">
        
        <!-- Header / Kop Surat Resmi -->
        <div class="text-center pb-6 border-b-2 border-slate-900 space-y-1">
            <h1 class="text-base sm:text-lg font-black uppercase tracking-wide text-slate-900">
                PALANG MERAH REMAJA (PMR) WIRA UNIT SMAN 1 CIAWI
            </h1>
            <h2 class="text-xs font-black text-red-600 uppercase tracking-widest">
                PANITIA PELAKSANA: {{ strtoupper($project->name) }}
            </h2>
            <p class="text-[10px] text-slate-500">
                Sekretariat: Ruang Markas UKS SMA Negeri 1 Ciawi, Jl. Mayor Oking No. 1 Ciawi, Kab. Bogor 16720
            </p>
        </div>

        <div class="text-center py-2 space-y-1">
            <h3 class="text-sm font-black text-slate-900 uppercase underline decoration-2 underline-offset-4">
                LAPORAN PERTANGGUNGJAWABAN (LPJ) REALISASI KEUANGAN PROYEK
            </h3>
            <p class="text-xs font-bold font-mono text-slate-600">Nomor Registrasi Proyek: {{ $project->code }}</p>
        </div>

        <!-- Profil Proyek -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 grid grid-cols-2 gap-3 text-[11px]">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Nama Kegiatan:</span>
                <span class="font-bold text-slate-900">{{ $project->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Kategori & Status:</span>
                <span class="font-bold text-slate-900">{{ $project->category_label }} &bull; {{ $project->status_label }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Ketua Pelaksana:</span>
                <span class="font-bold text-slate-900">{{ $project->person_in_charge ?: '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Bendahara Panitia:</span>
                <span class="font-bold text-slate-900">{{ $project->treasurer_name ?: '-' }}</span>
            </div>
        </div>

        <!-- Ringkasan Anggaran vs Realisasi -->
        <div class="grid grid-cols-3 gap-3 text-center border-y border-slate-200 py-3">
            <div>
                <span class="text-[10px] text-slate-500 uppercase font-bold block">Total Penerimaan</span>
                <span class="font-mono font-black text-sm text-emerald-600">{{ $project->formatted_revenue }}</span>
                <span class="text-[9px] text-slate-400 block">Target: Rp {{ number_format($project->budget_target_revenue, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-500 uppercase font-bold block">Total Pengeluaran</span>
                <span class="font-mono font-black text-sm text-rose-600">{{ $project->formatted_expense }}</span>
                <span class="text-[9px] text-slate-400 block">Pagu: Rp {{ number_format($project->budget_limit_expense, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-500 uppercase font-bold block">Sisa Kas Bersih</span>
                <span class="font-mono font-black text-sm {{ $project->net_surplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $project->formatted_net_surplus }}
                </span>
                <span class="text-[9px] text-slate-400 block">Status: {{ $project->net_surplus >= 0 ? 'Surplus' : 'Defisit' }}</span>
            </div>
        </div>

        <!-- Bagian I: Rincian Penerimaan -->
        <div class="space-y-2">
            <h4 class="font-bold text-xs uppercase bg-slate-100 p-2 rounded text-slate-900">
                I. Rincian Penerimaan Dana / Pemasukan
            </h4>
            <table class="w-full text-left border border-slate-200">
                <thead class="bg-slate-50 text-[10px] font-bold text-slate-700 border-b border-slate-200">
                    <tr>
                        <th class="p-2 w-24">Kode Akun</th>
                        <th class="p-2">Pos Penerimaan / Sumber Dana</th>
                        <th class="p-2 text-right w-36">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[11px]">
                    @forelse($revenueLines as $rl)
                        <tr>
                            <td class="p-2 font-mono">{{ $rl->account?->code }}</td>
                            <td class="p-2">{{ $rl->account?->name }}</td>
                            <td class="p-2 text-right font-mono font-bold">Rp {{ number_format($rl->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-3 text-center text-slate-400 italic">Belum ada pos penerimaan.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-slate-300 font-bold bg-slate-50">
                    <tr>
                        <td colspan="2" class="p-2 uppercase text-right">Total Penerimaan:</td>
                        <td class="p-2 text-right font-mono font-black text-emerald-600">{{ $project->formatted_revenue }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Bagian II: Rincian Pengeluaran -->
        <div class="space-y-2 pt-2">
            <h4 class="font-bold text-xs uppercase bg-slate-100 p-2 rounded text-slate-900">
                II. Rincian Belanja / Pengeluaran
            </h4>
            <table class="w-full text-left border border-slate-200">
                <thead class="bg-slate-50 text-[10px] font-bold text-slate-700 border-b border-slate-200">
                    <tr>
                        <th class="p-2 w-24">Kode Akun</th>
                        <th class="p-2">Mata Anggaran Belanja</th>
                        <th class="p-2 text-right w-36">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[11px]">
                    @forelse($expenseLines as $el)
                        <tr>
                            <td class="p-2 font-mono">{{ $el->account?->code }}</td>
                            <td class="p-2">{{ $el->account?->name }}</td>
                            <td class="p-2 text-right font-mono font-bold text-rose-600">Rp {{ number_format($el->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-3 text-center text-slate-400 italic">Belum ada pos belanja.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-slate-300 font-bold bg-slate-50">
                    <tr>
                        <td colspan="2" class="p-2 uppercase text-right">Total Belanja:</td>
                        <td class="p-2 text-right font-mono font-black text-rose-600">{{ $project->formatted_expense }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Rekapitulasi Sisa Kas -->
        <div class="p-3.5 bg-slate-100 rounded-xl border border-slate-300 flex items-center justify-between font-black text-xs">
            <span>SISA KAS / SALDO AKHIR PROYEK (SURPLUS/DEFISIT):</span>
            <span class="font-mono text-sm {{ $project->net_surplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $project->formatted_net_surplus }}
            </span>
        </div>

        <!-- Lembar Pengesahan RAT -->
        <div class="pt-8 border-t border-slate-200 space-y-4">
            <div class="text-center text-[10px] text-slate-500">
                Laporan ini disusun dengan sebenar-benarnya berdasarkan bukti transaksi sah dan diverifikasi untuk Rapat Anggota Tahunan (RAT).
            </div>
            <div class="grid grid-cols-4 gap-4 text-center text-[10px] text-slate-600 pt-4">
                <div>
                    <div class="text-slate-400">Ketua Pelaksana,</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                        {{ $project->person_in_charge ?: 'Sulthan Arkana P.' }}
                    </div>
                </div>
                <div>
                    <div class="text-slate-400">Bendahara Proyek,</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                        {{ $project->treasurer_name ?: 'Bendahara Panitia' }}
                    </div>
                </div>
                <div>
                    <div class="text-slate-400">Ketua Umum PMR,</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                        Sulthan Arkana Putra R.
                    </div>
                </div>
                <div>
                    <div class="text-slate-400">Mengetahui Pembina,</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1">
                        Pembina PMR Wira
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
