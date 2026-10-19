@extends('layouts.app')

@section('title', 'Tentang Kami & Profil PMR')

@push('styles')
<style>
    /* Smart Face Zoom for Member Photos (Crops wide Canva empty margins & lower body, zooms in directly to head & shoulders) */
    .member-photo-zoom {
        object-fit: cover !important;
        object-position: center 18% !important;
        transform: scale(1.65);
        transform-origin: center 20%;
        transition: transform 0.3s ease;
    }
    .group:hover .member-photo-zoom,
    .group\/photo:hover .member-photo-zoom,
    .group\/staf:hover .member-photo-zoom {
        transform: scale(1.72);
    }
</style>
@endpush

@section('content')
<!-- Page Header Banner -->
<section class="gradient-pmr text-white py-16 sm:py-20 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-1 rounded-full text-xs font-bold text-red-200 uppercase tracking-widest mb-4 border border-white/20">
            <i class="fa-solid fa-shield-heart text-red-300"></i> Profil Resmi Unit
        </div>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-4 text-shadow-sm">
            Tentang Kami & Profil PMR
        </h1>
        <p class="text-red-100 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
            Mengenal Lebih Dekat PMR Wira SMAN 1 Ciawi, Landasan Moral 7 Prinsip Dasar, Tri Bakti, dan Struktur Kepengurusan Ragana Dwi Pantara 2026/2027.
        </p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="bg-white border-b border-slate-200 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-xs text-slate-500 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-pmr-primary">Beranda</a>
        <i class="fa-solid fa-angle-right text-[10px]"></i>
        <span class="text-slate-800 font-semibold">Tentang Kami</span>
    </div>
</div>

<!-- Profil Utama PMR SMAN 1 Ciawi (Definisi Resmi) -->
<section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <!-- Left: Official Definition & Story (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2 text-pmr-primary font-bold text-xs uppercase tracking-widest">
                <i class="fa-solid fa-book-open-reader"></i> Mengenal PMR
            </div>
            
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Profil PMR Wira SMAN 1 Ciawi
            </h2>

            <!-- Highlight Quote Definition (Sesuai Naskah User) -->
            <div class="relative bg-gradient-to-br from-red-50 to-white p-6 sm:p-8 rounded-3xl border-l-4 border-pmr-primary shadow-sm border border-slate-200/80">
                <i class="fa-solid fa-quote-left text-3xl text-red-200 mb-3 block"></i>
                <blockquote class="text-base sm:text-lg font-bold text-slate-800 leading-relaxed italic">
                    "Palang Merah Remaja atau PMR adalah suatu organisasi binaan dari Palang Merah Indonesia yang berpusat di sekolah-sekolah ataupun kelompok-kelompok masyarakat (sanggar, kelompok belajar, dll.) yang bertujuan membangun dan mengembangkan karakter Kepalangmerahan agar siap menjadi Relawan PMI pada masa depan."
                </blockquote>
            </div>

            <div class="prose text-slate-600 text-sm leading-relaxed space-y-3">
                <p>
                    Di <strong>SMA Negeri 1 Ciawi</strong>, unit PMR Wira berperan aktif sebagai garda kemanusiaan di lingkungan sekolah, siaga memberikan pertolongan pertama, mempromosikan perilaku hidup bersih dan sehat, serta membina kepedulian sosial melalui aksi donor darah sukarela dan tanggap darurat bencana.
                </p>
                <p>
                    Masa bakti <strong>Ragana Dwi Pantara 2026/2027</strong> berkomitmen mengamalkan seluruh nilai kepalangmerahan ini secara profesional, modern, dan penuh dedikasi bagi seluruh warga sekolah dan masyarakat sekitar.
                </p>
            </div>
        </div>

        <!-- Right: Official Image & Badge Card (5 Cols) -->
        <div class="lg:col-span-5 relative">
            <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-slate-900">
                <img src="/mockups/02_tentang_kami.jpg" alt="PMR SMAN 1 Ciawi" class="w-full h-auto object-cover">
            </div>

            <!-- Floating Info Badge -->
            <div class="absolute -bottom-6 -left-6 bg-white p-5 rounded-2xl shadow-xl border border-slate-100 hidden sm:flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <div class="font-extrabold text-slate-800 text-sm">PMR Tingkat Wira</div>
                    <div class="text-xs text-slate-500">Binaan PMI Kab. Bogor & SMAN 1 Ciawi</div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- TRI BAKTI PMR (Section Khusus Baru Sesuai Permintaan) -->
<section class="py-16 sm:py-20 bg-slate-900 text-white relative overflow-hidden">
    <!-- Background Glow -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-pmr-primary/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 bg-red-500/20 text-red-300 font-extrabold text-xs px-4 py-1.5 rounded-full uppercase tracking-widest mb-3 border border-red-500/30">
                <i class="fa-solid fa-hands-holding-child"></i> Tri Bakti PMR
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                Tri Bakti Palang Merah Remaja
            </h2>
            <p class="text-slate-400 text-sm mt-3 leading-relaxed">
                Tiga pilar kewajiban utama yang senantiasa diamalkan oleh seluruh anggota PMR Wira SMAN 1 Ciawi dalam kehidupan sehari-hari:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Tri Bakti 1 -->
            <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-8 shadow-xl flex flex-col relative group hover:border-red-500 transition duration-300">
                <div class="w-16 h-16 rounded-2xl bg-pmr-primary text-white flex items-center justify-center text-2xl mb-6 shadow-lg shadow-red-950/50 group-hover:scale-105 transition">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                </div>
                <div class="text-xs font-bold text-red-400 uppercase tracking-widest mb-2">Tri Bakti 1</div>
                <h3 class="text-xl font-extrabold text-white mb-3 leading-snug">
                    Berbakti kepada Masyarakat
                </h3>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed flex-grow">
                    Turut serta dalam aksi kemanusiaan di masyarakat, seperti donor darah sukarela, bakti sosial peduli sesama, dan bantuan tanggap darurat saat bencana melanda.
                </p>
            </div>

            <!-- Tri Bakti 2 -->
            <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-8 shadow-xl flex flex-col relative group hover:border-red-500 transition duration-300">
                <div class="w-16 h-16 rounded-2xl bg-pmr-primary text-white flex items-center justify-center text-2xl mb-6 shadow-lg shadow-red-950/50 group-hover:scale-105 transition">
                    <i class="fa-solid fa-kit-medical"></i>
                </div>
                <div class="text-xs font-bold text-red-400 uppercase tracking-widest mb-2">Tri Bakti 2</div>
                <h3 class="text-xl font-extrabold text-white mb-3 leading-snug">
                    Mempertinggi Keterampilan dan Memelihara Kebersihan dan Kesehatan
                </h3>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed flex-grow">
                    Mengasah keahlian Pertolongan Pertama (P3K), piket medis UKS, simulasi mitigasi bencana, serta membudayakan Pola Hidup Bersih dan Sehat (PHBS) di sekolah.
                </p>
            </div>

            <!-- Tri Bakti 3 -->
            <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-8 shadow-xl flex flex-col relative group hover:border-red-500 transition duration-300">
                <div class="w-16 h-16 rounded-2xl bg-pmr-primary text-white flex items-center justify-center text-2xl mb-6 shadow-lg shadow-red-950/50 group-hover:scale-105 transition">
                    <i class="fa-solid fa-earth-asia"></i>
                </div>
                <div class="text-xs font-bold text-red-400 uppercase tracking-widest mb-2">Tri Bakti 3</div>
                <h3 class="text-xl font-extrabold text-white mb-3 leading-snug">
                    Mempererat Persahabatan Nasional dan Internasional
                </h3>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed flex-grow">
                    Menjalin persaudaraan relawan muda tanpa memandang perbedaan melalui temu akrab, JUMBARA (Jumpa Bakti Gembira), dan pertukaran informasi kepalangmerahan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 7 Prinsip Dasar Palang Merah (Interactive Grid) -->
<section class="py-16 sm:py-20 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="text-pmr-primary font-bold text-xs uppercase tracking-widest mb-2">Landasan Moral Universal</div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                7 Dasar Prinsip Palang Merah
            </h2>
            <p class="text-slate-600 text-sm mt-3 leading-relaxed">
                Tujuh prinsip fundamental yang menjadi pedoman teguh setiap langkah dan tindakan relawan PMR Wira di seluruh dunia:
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">
            <!-- Prinsip 1 -->
            <div class="bg-white border border-slate-200 p-5 rounded-2xl text-center flex flex-col items-center hover:border-pmr-primary hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 rounded-full bg-red-50 text-pmr-primary flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                </div>
                <div class="font-extrabold text-sm text-slate-800">KEMANUSIAAN</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">Humanity</div>
            </div>

            <!-- Prinsip 2 -->
            <div class="bg-white border border-slate-200 p-5 rounded-2xl text-center flex flex-col items-center hover:border-pmr-primary hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 rounded-full bg-red-50 text-pmr-primary flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div class="font-extrabold text-sm text-slate-800">KESAMAAN</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">Impartiality</div>
            </div>

            <!-- Prinsip 3 -->
            <div class="bg-white border border-slate-200 p-5 rounded-2xl text-center flex flex-col items-center hover:border-pmr-primary hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 rounded-full bg-red-50 text-pmr-primary flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fa-solid fa-dove"></i>
                </div>
                <div class="font-extrabold text-sm text-slate-800">KENETRALAN</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">Neutrality</div>
            </div>

            <!-- Prinsip 4 -->
            <div class="bg-white border border-slate-200 p-5 rounded-2xl text-center flex flex-col items-center hover:border-pmr-primary hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 rounded-full bg-red-50 text-pmr-primary flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fa-solid fa-flag"></i>
                </div>
                <div class="font-extrabold text-sm text-slate-800">KEMANDIRIAN</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">Independence</div>
            </div>

            <!-- Prinsip 5 -->
            <div class="bg-white border border-slate-200 p-5 rounded-2xl text-center flex flex-col items-center hover:border-pmr-primary hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 rounded-full bg-red-50 text-pmr-primary flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div class="font-extrabold text-sm text-slate-800">KESUKARELAAN</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">Voluntary Service</div>
            </div>

            <!-- Prinsip 6 -->
            <div class="bg-white border border-slate-200 p-5 rounded-2xl text-center flex flex-col items-center hover:border-pmr-primary hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 rounded-full bg-red-50 text-pmr-primary flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fa-solid fa-people-group"></i>
                </div>
                <div class="font-extrabold text-sm text-slate-800">KESATUAN</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">UNITY</div>
            </div>

            <!-- Prinsip 7 -->
            <div class="bg-white border border-slate-200 p-5 rounded-2xl text-center flex flex-col items-center hover:border-pmr-primary hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 rounded-full bg-red-50 text-pmr-primary flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fa-solid fa-earth-americas"></i>
                </div>
                <div class="font-extrabold text-sm text-slate-800">KESEMESTAAN</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-1">Universality</div>
            </div>
        </div>
    </div>
</section>

<!-- Visi & Misi Cards -->
<section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-12">
        <div class="text-pmr-primary font-bold text-xs uppercase tracking-widest mb-1.5">Arah Gerak Organisasi</div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Visi & Misi Kami</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Visi -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-slate-200/80 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-full -z-0"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 bg-red-100 text-pmr-primary font-extrabold text-xs px-3.5 py-1.5 rounded-full uppercase tracking-wider mb-6">
                    <i class="fa-solid fa-eye"></i> Visi Organisasi
                </div>
                <blockquote class="text-lg font-bold text-slate-800 leading-relaxed italic mb-6">
                    "Terwujudnya PMR Wira SMAN 1 Ciawi sebagai unit relawan muda yang berkarakter kemanusiaan tangguh, sigap, berintegritas, mandiri, dan berprestasi berlandaskan prinsip-prinsip dasar Palang Merah Internasional."
                </blockquote>
            </div>
        </div>

        <!-- Misi -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-slate-200/80 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-full -z-0"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 bg-red-100 text-pmr-primary font-extrabold text-xs px-3.5 py-1.5 rounded-full uppercase tracking-wider mb-6">
                    <i class="fa-solid fa-bullseye"></i> Misi Organisasi
                </div>
                <ul class="space-y-3 text-sm text-slate-700 font-medium">
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 font-bold flex-shrink-0">1</span>
                        <span>Menanamkan pemahaman dan pengamalan 7 Prinsip Dasar Palang Merah dan Tri Bakti PMR.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 font-bold flex-shrink-0">2</span>
                        <span>Meningkatkan kapasitas anggota dalam keterampilan pertolongan pertama, sanitasi kesehatan, dan penanggulangan bencana.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 font-bold flex-shrink-0">3</span>
                        <span>Aktif menyelenggarakan program donor darah sukarela dan bakti sosial bagi masyarakat.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 font-bold flex-shrink-0">4</span>
                        <span>Menjalin sinergi erat dengan pembina sekolah, PMI Kabupaten Bogor, dan unit PMR lainnya.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Struktur Organisasi Dinamis dari Database / Backoffice -->
<section class="py-16 sm:py-24 bg-slate-100/90 border-t border-slate-200/80">
    <div class="max-w-[1550px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
            <div class="text-pmr-primary font-bold text-xs sm:text-sm uppercase tracking-widest mb-2">{{ $organizationSetting->badge ?? 'Bagan Kepengurusan' }}</div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">{{ $organizationSetting->title ?? 'Struktur Organisasi 2026/2027' }}</h2>
            @if(!empty($organizationSetting->subtitle))
                <p class="text-slate-600 text-sm sm:text-base mt-3 sm:mt-4 leading-relaxed max-w-2xl mx-auto">{{ $organizationSetting->subtitle }}</p>
            @endif
        </div>

        <!-- Organizational Chart Flow -->
        <div class="w-full flex flex-col items-center">
            @php
                $pembina = $organizationMembers->get(1, $organizationMembers->get('1', collect()));
                $ketua = $organizationMembers->get(2, $organizationMembers->get('2', collect()));
                $wakil = $organizationMembers->get(3, $organizationMembers->get('3', collect()));
                $bph = $organizationMembers->get(4, $organizationMembers->get('4', collect()));
                $seksi = $organizationMembers->get(5, $organizationMembers->get('5', collect()));
                $others = $organizationMembers->get(6, $organizationMembers->get('6', collect()));

                // Fallback otomatis: jika di organizationMembers belum ada level 1, ambil dari allAdvisors aktif
                if ($pembina->isEmpty()) {
                    if (isset($allAdvisors) && $allAdvisors->isNotEmpty()) {
                        $pembina = $allAdvisors->filter(fn($a) => (bool)$a->is_active);
                    }
                    if ($pembina->isEmpty()) {
                        if (\Illuminate\Support\Facades\Schema::hasTable('advisors')) {
                            try {
                                $pembina = \App\Models\Advisor::where('is_active', true)->orderBy('order_position')->get();
                            } catch (\Throwable $e) {}
                        }
                    }
                    if ($pembina->isEmpty()) {
                        // Sediakan fallback dewan pembina resmi PMR Wira SMAN 1 Ciawi
                        $pembina = collect([
                            (object)[
                                'name' => 'Drs. H. Mulyadi, M.Pd',
                                'position' => 'Pembina PMR',
                                'subtitle' => 'Guru Pembina PMR Wira SMAN 1 Ciawi',
                                'photo_url' => 'https://ui-avatars.com/api/?name=Drs+H+Mulyadi&background=991b1b&color=ffffff&bold=true&size=300',
                                'level' => 1,
                            ]
                        ]);
                    }
                }

                // Fallback kompatibilitas: jika DB lama belum digeser levelnya (level 3 masih BPH dan bukan wakil ketua)
                if ($wakil->isNotEmpty() && $bph->isEmpty() && !$wakil->contains(fn($item) => stripos($item->position, 'wakil') !== false)) {
                    $bph = $wakil;
                    $seksi = $organizationMembers->get(4, collect());
                    $others = $organizationMembers->get(5, collect());
                    $wakil = collect();
                }
            @endphp

            <!-- Level 1: Pembina PMR -->
            @if($pembina->isNotEmpty())
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($pembina as $m)
                        @php
                            $advisor = ($m instanceof \App\Models\Advisor) ? $m : (isset($allAdvisors) ? ($allAdvisors->get($m->name) ?? null) : null);
                            if (!$advisor && !($m instanceof \App\Models\Advisor)) {
                                if (\Illuminate\Support\Facades\Schema::hasTable('advisors')) {
                                    try {
                                        $advisor = \App\Models\Advisor::where('name', $m->name)->first();
                                        if (!$advisor && !empty($m->name)) {
                                            $advisor = \App\Models\Advisor::where('name', 'like', '%' . $m->name . '%')->first();
                                        }
                                    } catch (\Throwable $e) {}
                                }
                            }
                            $mem = method_exists($m, 'member') ? $m->member : null;
                            if (!$mem && isset($allMembers)) {
                                $mem = $allMembers->get($m->name) ?? null;
                            }
                            $photoUrl = (!empty($m->photo_url) ? $m->photo_url : null) ?: ($advisor?->photo_url ?: ($mem?->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=991b1b&color=ffffff&bold=true&size=300'));
                            $displayName = $m->name;
                            if ($advisor && !empty($advisor->title_degree) && !str_contains($displayName, $advisor->title_degree)) {
                                $displayName .= ', ' . $advisor->title_degree;
                            }
                            $mPos = $m->position ?: ($advisor?->position ?: 'Pembina PMR');
                            $mSub = $m->subtitle ?: ($advisor?->title_degree ? ('Pembina • ' . $advisor->title_degree) : ($advisor?->position ?: 'Guru Pembina PMR'));
                            $biodata = [
                                'name' => $displayName,
                                'position' => $mPos,
                                'subtitle' => $mSub,
                                'class_grade' => $advisor?->title_degree ? 'Gelar: ' . $advisor->title_degree : ($mSub ?: 'Guru Pembina'),
                                'nis' => $advisor?->nip ? 'NIP. ' . $advisor->nip : ($mem?->nip ? 'NIP. ' . $mem->nip : '-'),
                                'gender' => $advisor?->gender ?: ($mem?->gender ?? '-'),
                                'birth_place' => $advisor?->birth_place ?: ($mem?->birth_place ?? ''),
                                'birth_date' => ($advisor?->birth_date ? \Carbon\Carbon::parse($advisor->birth_date)->translatedFormat('d F Y') : ($mem?->birth_date ? \Carbon\Carbon::parse($mem->birth_date)->translatedFormat('d F Y') : '')),
                                'address' => $advisor?->address ?: ($mem?->address ?? ''),
                                'phone' => $advisor?->phone ?: ($mem?->phone ?? ''),
                                'email' => $advisor?->email ?: ($mem?->email ?? ''),
                                'motto' => $advisor?->motto ?: ($mem?->motto ?? ''),
                                'photo' => $photoUrl,
                            ];
                        @endphp
                        <div class="bg-stone-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row items-center gap-6 sm:gap-7 min-w-[340px] max-w-xl border-t-4 border-pmr-primary hover:shadow-2xl transition duration-300 group">
                            <div class="relative cursor-pointer group/photo flex-shrink-0" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">
                                <div class="w-32 h-42 sm:w-36 sm:h-48 rounded-3xl overflow-hidden border-4 border-red-500 shadow-xl group-hover/photo:scale-105 transition-transform duration-300 bg-stone-950">
                                    <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-full h-full object-cover object-top {{ !str_contains($photoUrl, 'ui-avatars.com') ? 'member-photo-zoom' : '' }}" style="object-position: center 18%;">
                                </div>
                                <div class="absolute inset-0 rounded-3xl bg-black/40 opacity-0 group-hover/photo:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5 backdrop-blur-xs">
                                    <i class="fa-solid fa-id-card"></i> Biodata
                                </div>
                            </div>
                            <div class="text-center sm:text-left">
                                <div class="text-xs uppercase tracking-wider text-red-400 font-extrabold flex items-center justify-center sm:justify-start gap-1.5">
                                    <i class="fa-solid fa-shield-halved text-xs"></i> {{ $m->position ?: ($advisor?->position ?: 'Pembina PMR') }}
                                </div>
                                <div class="text-xl sm:text-2xl font-black mt-1 text-white leading-snug cursor-pointer hover:text-red-300 transition" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">{{ $displayName }}</div>
                                @if($advisor?->nip)
                                    <div class="text-xs text-stone-400 font-mono mt-0.5">NIP. {{ $advisor->nip }}</div>
                                @elseif($m->subtitle)
                                    <div class="text-xs sm:text-sm text-stone-400 mt-1">{{ $m->subtitle }}</div>
                                @endif
                                <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-3.5 inline-flex items-center gap-1.5 text-xs font-bold text-red-300 hover:text-white bg-white/10 hover:bg-red-600/60 px-3.5 py-1.5 rounded-full transition">
                                    <i class="fa-solid fa-address-card text-xs"></i> Lihat Biodata
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($pembina->isNotEmpty() && ($ketua->isNotEmpty() || $wakil->isNotEmpty() || $bph->isNotEmpty() || $seksi->isNotEmpty() || $others->isNotEmpty()))
                <div class="w-0.5 h-12 bg-slate-300 my-2"></div>
            @endif

            <!-- Level 2: Ketua Umum / Pimpinan (Hero Card) -->
            @if($ketua->isNotEmpty())
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($ketua as $m)
                        @php
                            $mem = $m->member ?? ($allMembers->get($m->name) ?? null);
                            $photoUrl = $m->photo_url ?: ($mem?->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=dc2626&color=ffffff&bold=true&size=300');
                            $biodata = [
                                'name' => $m->name,
                                'position' => $m->position,
                                'subtitle' => $m->subtitle,
                                'class_grade' => $mem?->class_grade ?? ($m->subtitle ?: 'Kelas XI-MIPA 1'),
                                'nis' => $mem?->nis ?? '-',
                                'gender' => $mem?->gender ?? '-',
                                'birth_place' => $mem?->birth_place ?? '',
                                'birth_date' => $mem?->birth_date ? \Carbon\Carbon::parse($mem->birth_date)->translatedFormat('d F Y') : '',
                                'address' => $mem?->address ?? '',
                                'phone' => $mem?->phone ?? '',
                                'email' => $mem?->email ?? '',
                                'motto' => $mem?->motto ?? '',
                                'photo' => $photoUrl,
                            ];
                        @endphp
                        <div class="bg-gradient-to-br from-pmr-primary via-red-700 to-pmr-dark text-white p-7 sm:p-9 rounded-3xl shadow-2xl flex flex-col sm:flex-row items-center gap-7 sm:gap-8 min-w-[340px] max-w-2xl border border-red-400/30 hover:shadow-red-950/30 transition duration-300 group">
                            <div class="relative cursor-pointer group/photo flex-shrink-0" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">
                                <div class="w-36 h-48 sm:w-44 sm:h-56 rounded-3xl overflow-hidden border-4 border-white/95 shadow-2xl ring-4 ring-amber-400/50 group-hover/photo:scale-105 transition-transform duration-300 bg-red-950">
                                    <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-full h-full object-cover object-top {{ !str_contains($photoUrl, 'ui-avatars.com') ? 'member-photo-zoom' : '' }}" style="object-position: center 18%;">
                                </div>
                                <div class="absolute inset-0 rounded-3xl bg-black/40 opacity-0 group-hover/photo:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5 backdrop-blur-xs">
                                    <i class="fa-solid fa-id-card"></i> Biodata
                                </div>
                            </div>
                            <div class="text-center sm:text-left">
                                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-xs px-3.5 py-1 rounded-full text-xs uppercase tracking-wider text-red-100 font-black mb-1">
                                    <i class="fa-solid fa-crown text-amber-300 text-sm"></i> {{ $m->position }}
                                </div>
                                <div class="text-2xl sm:text-3xl font-black mt-1 text-white leading-tight cursor-pointer hover:text-amber-200 transition" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">{{ $m->name }}</div>
                                @if($m->subtitle)
                                    <div class="text-sm sm:text-base text-red-100/90 mt-1 font-medium">{{ $m->subtitle }}</div>
                                @endif
                                <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-3.5 inline-flex items-center gap-1.5 text-xs font-bold text-white bg-white/20 hover:bg-white hover:text-pmr-primary px-4 py-2 rounded-full transition shadow-xs">
                                    <i class="fa-solid fa-address-card text-xs"></i> Lihat Biodata Pribadi
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($ketua->isNotEmpty() && ($wakil->isNotEmpty() || $bph->isNotEmpty() || $seksi->isNotEmpty() || $others->isNotEmpty()))
                <div class="w-0.5 h-12 bg-slate-300 my-2"></div>
            @endif

            <!-- Level 3: Wakil Ketua (Dedicated Level) -->
            @if($wakil->isNotEmpty())
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($wakil as $m)
                        @php
                            $mem = $m->member ?? ($allMembers->get($m->name) ?? null);
                            $photoUrl = $m->photo_url ?: ($mem?->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=dc2626&color=ffffff&bold=true&size=300');
                            $biodata = [
                                'name' => $m->name,
                                'position' => $m->position,
                                'subtitle' => $m->subtitle,
                                'class_grade' => $mem?->class_grade ?? ($m->subtitle ?: 'Kelas XI'),
                                'nis' => $mem?->nis ?? '-',
                                'gender' => $mem?->gender ?? '-',
                                'birth_place' => $mem?->birth_place ?? '',
                                'birth_date' => $mem?->birth_date ? \Carbon\Carbon::parse($mem->birth_date)->translatedFormat('d F Y') : '',
                                'address' => $mem?->address ?? '',
                                'phone' => $mem?->phone ?? '',
                                'email' => $mem?->email ?? '',
                                'motto' => $mem?->motto ?? '',
                                'photo' => $photoUrl,
                            ];
                        @endphp
                        <div class="bg-gradient-to-br from-slate-900 via-red-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row items-center gap-6 sm:gap-7 min-w-[320px] max-w-xl border border-amber-500/30 hover:shadow-2xl transition duration-300 group">
                            <div class="relative cursor-pointer group/photo flex-shrink-0" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">
                                <div class="w-32 h-42 sm:w-36 sm:h-48 rounded-3xl overflow-hidden border-4 border-amber-400 shadow-xl ring-2 ring-amber-400/20 group-hover/photo:scale-105 transition-transform duration-300 bg-slate-950">
                                    <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-full h-full object-cover object-top {{ !str_contains($photoUrl, 'ui-avatars.com') ? 'member-photo-zoom' : '' }}" style="object-position: center 18%;">
                                </div>
                                <div class="absolute inset-0 rounded-3xl bg-black/40 opacity-0 group-hover/photo:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5 backdrop-blur-xs">
                                    <i class="fa-solid fa-id-card"></i> Biodata
                                </div>
                            </div>
                            <div class="text-center sm:text-left">
                                <div class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-300 border border-amber-500/30 backdrop-blur-xs px-3 py-1 rounded-full text-xs uppercase tracking-wider font-extrabold mb-1">
                                    <i class="fa-solid fa-user-shield text-amber-300 text-xs"></i> {{ $m->position }}
                                </div>
                                <div class="text-xl sm:text-2xl font-black mt-1 text-white leading-tight cursor-pointer hover:text-amber-200 transition" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">{{ $m->name }}</div>
                                @if($m->subtitle)
                                    <div class="text-xs sm:text-sm text-slate-300 mt-1 font-medium">{{ $m->subtitle }}</div>
                                @endif
                                <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-3.5 inline-flex items-center gap-1.5 text-xs font-bold text-amber-200 bg-white/10 hover:bg-amber-500 hover:text-slate-950 px-3.5 py-1.5 rounded-full transition shadow-xs">
                                    <i class="fa-solid fa-address-card text-xs"></i> Lihat Biodata
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($wakil->isNotEmpty() && ($bph->isNotEmpty() || $seksi->isNotEmpty() || $others->isNotEmpty()))
                <div class="w-0.5 h-12 bg-slate-300 my-2"></div>
            @endif

            <!-- Level 4: Pengurus Harian / BPH (Sekretaris & Bendahara) -->
            @if($bph->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-4xl">
                    @foreach($bph as $m)
                        @php
                            $mem = $m->member ?? ($allMembers->get($m->name) ?? null);
                            $photoUrl = $m->photo_url ?: ($mem?->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=dc2626&color=ffffff&bold=true&size=300');
                            $biodata = [
                                'name' => $m->name,
                                'position' => $m->position,
                                'subtitle' => $m->subtitle,
                                'class_grade' => $mem?->class_grade ?? ($m->subtitle ?: 'Kelas XI'),
                                'nis' => $mem?->nis ?? '-',
                                'gender' => $mem?->gender ?? '-',
                                'birth_place' => $mem?->birth_place ?? '',
                                'birth_date' => $mem?->birth_date ? \Carbon\Carbon::parse($mem->birth_date)->translatedFormat('d F Y') : '',
                                'address' => $mem?->address ?? '',
                                'phone' => $mem?->phone ?? '',
                                'email' => $mem?->email ?? '',
                                'motto' => $mem?->motto ?? '',
                                'photo' => $photoUrl,
                            ];
                        @endphp
                        <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-lg border border-slate-200/90 flex flex-col sm:flex-row items-center gap-5 sm:gap-6 hover:shadow-2xl hover:border-pmr-primary transition duration-300 group">
                            <div class="relative cursor-pointer group/photo flex-shrink-0" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">
                                <div class="w-30 h-40 sm:w-34 sm:h-46 rounded-3xl overflow-hidden border-4 border-slate-100 shadow-md ring-2 ring-red-100 group-hover/photo:scale-105 transition-transform duration-300 bg-slate-50">
                                    <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-full h-full object-cover object-top {{ !str_contains($photoUrl, 'ui-avatars.com') ? 'member-photo-zoom' : '' }}" style="object-position: center 18%;">
                                </div>
                                <div class="absolute inset-0 rounded-3xl bg-black/40 opacity-0 group-hover/photo:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5 backdrop-blur-xs">
                                    <i class="fa-solid fa-id-card"></i> Biodata
                                </div>
                            </div>
                            <div class="text-center sm:text-left">
                                <div class="text-xs font-black text-pmr-primary uppercase tracking-wide flex items-center justify-center sm:justify-start gap-1.5">
                                    <i class="{{ $m->icon ?: 'fa-solid fa-user' }} text-xs"></i> {{ $m->position }}
                                </div>
                                <div class="font-extrabold text-slate-900 text-lg sm:text-xl mt-1 leading-snug cursor-pointer hover:text-pmr-primary transition" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">{{ $m->name }}</div>
                                @if($m->subtitle)
                                    <div class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">{{ $m->subtitle }}</div>
                                @endif
                                <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-pmr-primary hover:text-white bg-red-50 hover:bg-pmr-primary px-3.5 py-1.5 rounded-full transition border border-red-100">
                                    <i class="fa-solid fa-address-card text-xs"></i> Lihat Biodata
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($bph->isNotEmpty() && ($seksi->isNotEmpty() || $others->isNotEmpty()))
                <div class="w-0.5 h-12 bg-slate-300 my-2"></div>
            @endif

            <!-- Level 5: 5 Bidang Utama & Staf Anggota (Formasi Simetris 3 + 2) -->
            @if($seksi->isNotEmpty())
                @php
                    $seksiChunks = $seksi->chunk(3);
                @endphp

                <div class="w-full space-y-6 sm:space-y-7 mt-2">
                    @foreach($seksiChunks as $chunkIndex => $chunk)
                        <div class="{{ $chunk->count() === 2 ? 'grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-7 w-full max-w-5xl mx-auto justify-center' : ($chunk->count() === 1 ? 'flex justify-center w-full max-w-md mx-auto' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 w-full max-w-7xl mx-auto') }}">
                            @foreach($chunk as $m)
                                @php
                                    $mem = $m->member ?? ($allMembers->get($m->name) ?? null);
                                    $photoUrl = $m->photo_url ?: ($mem?->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=dc2626&color=ffffff&bold=true&size=300');
                                    $staffList = is_array($m->staff_members) ? $m->staff_members : [];
                                    $workProg = trim($m->work_program ?? '');
                                    $leaderBiodata = [
                                        'name' => $m->name,
                                        'position' => $m->subtitle ?: 'Ketua ' . $m->position,
                                        'subtitle' => $m->position,
                                        'class_grade' => $mem?->class_grade ?? ($m->subtitle ?: 'Kelas XI'),
                                        'nis' => $mem?->nis ?? '-',
                                        'gender' => $mem?->gender ?? '-',
                                        'birth_place' => $mem?->birth_place ?? '',
                                        'birth_date' => $mem?->birth_date ? \Carbon\Carbon::parse($mem->birth_date)->translatedFormat('d F Y') : '',
                                        'address' => $mem?->address ?? '',
                                        'phone' => $mem?->phone ?? '',
                                        'email' => $mem?->email ?? '',
                                        'motto' => $mem?->motto ?? '',
                                        'photo' => $photoUrl,
                                    ];
                                @endphp
                                <div class="bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden flex flex-col hover:shadow-2xl hover:border-pmr-primary transition-all duration-300 group {{ $chunk->count() === 1 ? 'w-full' : '' }}">
                                    
                                    <!-- Header Bidang -->
                                    <div class="bg-gradient-to-r from-red-700 via-pmr-primary to-red-800 text-white px-5 py-3.5 border-b border-red-900/30 flex items-center justify-between shadow-xs">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-8 h-8 rounded-xl bg-white/15 backdrop-blur-xs text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                                <i class="{{ $m->icon ?: 'fa-solid fa-shapes' }}"></i>
                                            </div>
                                            <h3 class="font-black text-sm sm:text-base text-white uppercase tracking-tight truncate">
                                                {{ $m->position }}
                                            </h3>
                                        </div>
                                        <span class="text-[11px] font-extrabold bg-white/20 text-white px-2.5 py-0.5 rounded-full flex-shrink-0">
                                            {{ count($staffList) }} Staf
                                        </span>
                                    </div>

                                    <!-- Ketua Bidang (Foto Portrait Lebih Besar & Jelas) -->
                                    <div class="p-6 flex flex-col items-center text-center bg-gradient-to-b from-red-50/50 via-white to-white border-b border-slate-100">
                                        <div class="relative cursor-pointer group/photo" onclick="openBiodataModal({!! htmlspecialchars(json_encode($leaderBiodata), ENT_QUOTES, 'UTF-8') !!})">
                                            <div class="w-28 h-36 sm:w-32 sm:h-42 rounded-3xl overflow-hidden border-4 border-white ring-2 ring-red-400 shadow-xl group-hover/photo:scale-105 transition-transform duration-300 bg-red-50">
                                                <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-full h-full object-cover object-top {{ !str_contains($photoUrl, 'ui-avatars.com') ? 'member-photo-zoom' : '' }}" style="object-position: center 18%;">
                                            </div>
                                            <span class="absolute -bottom-2.5 inset-x-0 mx-auto w-max bg-pmr-primary text-white text-[10px] font-black px-3 py-0.5 rounded-full shadow-md border-2 border-white flex items-center justify-center gap-1 z-10">
                                                <i class="fa-solid fa-crown text-[8px] text-amber-300"></i> Ketua Bidang
                                            </span>
                                            <div class="absolute inset-0 rounded-3xl bg-black/40 opacity-0 group-hover/photo:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1 backdrop-blur-xs">
                                                <i class="fa-solid fa-id-card"></i> Biodata
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <div class="font-black text-slate-900 text-base sm:text-lg leading-snug cursor-pointer hover:text-pmr-primary transition" onclick="openBiodataModal({!! htmlspecialchars(json_encode($leaderBiodata), ENT_QUOTES, 'UTF-8') !!})" title="{{ $m->name }}">
                                                {{ $m->name }}
                                            </div>
                                            <div class="text-xs text-pmr-primary font-bold mt-0.5">
                                                {{ $m->subtitle ?: 'Ketua ' . $m->position }}
                                            </div>
                                            <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($leaderBiodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-2 text-xs font-bold text-slate-500 hover:text-pmr-primary inline-flex items-center gap-1 transition">
                                                <i class="fa-solid fa-address-card text-xs text-pmr-primary"></i> Biodata Ketua &rarr;
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Area Staf Anggota (Foto Besar, Jelas & Nama Terbaca Utuh) -->
                                    <div class="p-5 bg-slate-50/80 flex-grow flex flex-col justify-between">
                                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200/80">
                                            <span class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                                <i class="fa-solid fa-users text-pmr-primary text-xs"></i> Staf Anggota Bidang
                                            </span>
                                            <span class="text-[11px] font-bold text-slate-500 bg-white px-2.5 py-0.5 rounded-full border border-slate-200 shadow-2xs">
                                                {{ count($staffList) }} Anggota
                                            </span>
                                        </div>

                                        @if(!empty($staffList) && count($staffList) > 0)
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-3.5">
                                                @foreach($staffList as $st)
                                                    @php
                                                        $stName = $st['name'] ?? '';
                                                        $stMem = $allMembers->get($stName);
                                                        $stClass = $st['class_grade'] ?? ($stMem?->class_grade ?? '');
                                                        $stPhoto = $st['photo'] ?? ($stMem?->photo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($stName) . '&background=991b1b&color=ffffff&bold=true&size=300'));
                                                        $stBiodata = [
                                                            'name' => $stName,
                                                            'position' => 'Staf ' . $m->position,
                                                            'subtitle' => 'Staf Operasional Bidang',
                                                            'class_grade' => $stClass ?: 'Kelas X/XI',
                                                            'nis' => $stMem?->nis ?? '-',
                                                            'gender' => $stMem?->gender ?? '-',
                                                            'birth_place' => $stMem?->birth_place ?? '',
                                                            'birth_date' => $stMem?->birth_date ? \Carbon\Carbon::parse($stMem->birth_date)->translatedFormat('d F Y') : '',
                                                            'address' => $stMem?->address ?? '',
                                                            'phone' => $stMem?->phone ?? '',
                                                            'email' => $stMem?->email ?? '',
                                                            'motto' => $stMem?->motto ?? '',
                                                            'photo' => $stPhoto,
                                                        ];
                                                    @endphp
                                                    <div class="group/staf flex flex-col items-center text-center cursor-pointer p-2.5 rounded-2xl bg-white border border-slate-200/80 hover:border-pmr-primary hover:shadow-md transition-all duration-300"
                                                         onclick="openBiodataModal({!! htmlspecialchars(json_encode($stBiodata), ENT_QUOTES, 'UTF-8') !!})"
                                                         title="Klik untuk melihat biodata {{ $stName }}">
                                                        <!-- Foto Portrait Besar & Tajam -->
                                                        <div class="relative w-20 h-26 sm:w-22 sm:h-28 rounded-xl overflow-hidden border-2 border-white ring-2 ring-slate-200/80 shadow-xs group-hover/staf:ring-pmr-primary group-hover/staf:scale-105 transition-all duration-300 flex-shrink-0 bg-slate-100">
                                                            <img src="{{ $stPhoto }}" alt="{{ $stName }}" class="w-full h-full object-cover object-top {{ !str_contains($stPhoto, 'ui-avatars.com') ? 'member-photo-zoom' : '' }}" style="object-position: center 18%;">
                                                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover/staf:opacity-100 flex items-end justify-center pb-1 transition text-white text-[10px] font-bold gap-1 backdrop-blur-2xs">
                                                                <i class="fa-solid fa-address-card text-amber-300"></i>
                                                                <span>Biodata</span>
                                                            </div>
                                                        </div>
                                                        <!-- Nama Lengkap 2 Baris & Kelas -->
                                                        <div class="mt-2 w-full">
                                                            <div class="text-xs font-bold text-slate-800 group-hover/staf:text-pmr-primary leading-tight line-clamp-2 transition" title="{{ $stName }}">
                                                                {{ $stName }}
                                                            </div>
                                                            @if($stClass)
                                                                <div class="text-[10px] text-slate-500 font-semibold mt-1 inline-block px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200/60 leading-none">
                                                                    {{ $stClass }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-xs text-slate-400 italic py-8 text-center flex flex-col items-center justify-center gap-1.5">
                                                <i class="fa-solid fa-user-slash text-slate-300 text-lg"></i>
                                                <span>Belum ada staf operasional terdaftar</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Tombol Program Kerja -->
                                    <div class="p-4 bg-white border-t border-slate-100 mt-auto">
                                        <button type="button" 
                                            onclick="openProgramModal('{{ addslashes($m->position) }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->subtitle ?: 'Ketua ' . $m->position) }}', '{{ addslashes($photoUrl ?? '') }}', '{{ addslashes($m->icon ?: 'fa-solid fa-shapes') }}', {!! htmlspecialchars(json_encode($workProg), ENT_QUOTES, 'UTF-8') !!})"
                                            class="w-full text-center py-2.5 px-3 rounded-xl bg-red-50 hover:bg-pmr-primary text-pmr-primary hover:text-white font-extrabold text-xs transition duration-200 flex items-center justify-center gap-2 shadow-xs group/btn">
                                            <i class="fa-solid fa-clipboard-list text-xs group-hover/btn:scale-110 transition"></i>
                                            <span>Lihat Program Kerja</span>
                                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                        </button>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Level 6: Divisi / Anggota Tambahan (jika ada) -->
            @if($others->isNotEmpty())
                <div class="w-0.5 h-12 bg-slate-300 my-2"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-5xl">
                    @foreach($others as $m)
                        <div class="bg-white p-6 rounded-3xl shadow-md border border-slate-200 text-center hover:shadow-lg transition">
                            <div class="text-xs font-bold text-slate-500 uppercase">{{ $m->position }}</div>
                            <div class="font-extrabold text-slate-800 text-lg mt-1">{{ $m->name }}</div>
                            @if($m->subtitle)
                                <div class="text-xs text-slate-500 mt-0.5">{{ $m->subtitle }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if($organizationMembers->isEmpty())
                <div class="text-center p-8 bg-white rounded-3xl border border-slate-200 text-slate-500 text-sm">
                    Data kepengurusan sedang dalam proses pemutakhiran.
                </div>
            @endif
        </div>
    </div>
</section>

<!-- LANDASAN & REGULASI ORGANISASI (GBHO & PPUO 2026) -->
<section id="regulasi-organisasi" class="py-16 sm:py-24 bg-gradient-to-b from-white via-slate-50 to-slate-100/80 border-t border-slate-200 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-red-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-10 w-80 h-80 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-[1550px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-14 sm:mb-16">
            <div class="inline-flex items-center gap-2 bg-red-100 text-pmr-primary px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider mb-3">
                <i class="fa-solid fa-scale-balanced"></i> Landasan & Regulasi Organisasi
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                Pedoman Resmi Kepengurusan 2026
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-3 sm:mt-4 leading-relaxed max-w-2xl mx-auto">
                Setiap derap langkah, tata tertib, standarisasi seragam, dan arah kebijakan PMR Wira SMAN 1 Ciawi berpedoman kokoh pada GBHO dan PPUO Masa Bakti Ragana Dwi Pantara 2026/2027.
            </p>
        </div>

        <!-- 2 Main Cards Grid: GBHO & PPUO -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- CARD 1: GBHO 2026 (5 Cols on LG) -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-full -z-0 group-hover:scale-110 transition-transform"></div>
                
                <div class="relative z-10">
                    <!-- Card Top Header -->
                    <div class="flex items-center justify-between gap-3 mb-5">
                        <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-200/70 text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full">
                            <i class="fa-solid fa-compass"></i> Dokumen Strategis
                        </span>
                        <span class="text-xs font-bold text-slate-400">Periode {{ $guideline->period }}</span>
                    </div>

                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                        Garis Besar Haluan Organisasi (GBHO)
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 mb-6">
                        Arah haluan umum kebijakan dan strategi taktis pelaksanaan kepengurusan satu periode.
                    </p>

                    <!-- Arah Kebijakan Callout -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-red-50 via-rose-50/50 to-white border-l-4 border-pmr-primary border border-red-100/80 mb-6 shadow-xs">
                        <div class="text-[11px] font-black uppercase tracking-wider text-pmr-primary flex items-center gap-1.5 mb-2">
                            <i class="fa-solid fa-quote-left"></i> {{ $guideline->gbho_policy_heading ?: 'Arah Kebijakan Utama' }}
                        </div>
                        <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed italic">
                            "{{ $guideline->gbho_policy_text }}"
                        </p>
                    </div>

                    <!-- 6 Pilar Strategi (Grid 2 Kolom) -->
                    <div class="space-y-2.5 mb-6">
                        <div class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center justify-between">
                            <span>Strategi Pokok Organisasi</span>
                            <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">{{ $guideline->strategies->count() }} Pilar</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            @foreach($guideline->strategies as $st)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-2">
                                <span class="w-5 h-5 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] font-black flex-shrink-0 mt-0.5">{{ $st->order_number }}</span>
                                <span class="font-bold text-slate-800">{{ $st->title }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Action CTA GBHO -->
                <div class="relative z-10 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-2.5">
                    <a href="{{ route('pedoman-organisasi') }}#gbho" 
                       class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white font-extrabold text-xs uppercase tracking-wider text-center shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-book-open"></i> Baca Teks GBHO
                    </a>
                    <a href="{{ $guideline->gbho_pdf_url }}" download
                       class="w-full sm:w-auto py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs uppercase tracking-wider text-center transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-file-pdf text-red-600"></i> Unduh PDF
                    </a>
                </div>
            </div>

            <!-- CARD 2: PPUO 2026 (7 Cols on LG - Lebih Luas & Komprehensif) -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-48 h-48 bg-amber-50 rounded-bl-full -z-0 group-hover:scale-110 transition-transform"></div>
                
                <div class="relative z-10">
                    <!-- Card Top Header -->
                    <div class="flex items-center justify-between gap-3 mb-5">
                        <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 border border-amber-200/70 text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full">
                            <i class="fa-solid fa-book-bookmark text-amber-600"></i> Pedoman Baku 27 Halaman
                        </span>
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $guideline->chapters->count() }} Bab Regulasi</span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-5">
                        <div>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                                Pedoman Pelaksanaan Umum Organisasi (PPUO)
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                                Regulasi operasional baku: kode etik, standarisasi seragam & syal, tata tertib, hingga sanksi.
                            </p>
                        </div>
                    </div>

                    <!-- Highlight Box: Syal Kuning Standar & Atribut -->
                    <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 mb-6 flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-400 text-stone-900 flex items-center justify-center text-lg font-black flex-shrink-0 shadow-xs">
                            <i class="fa-solid fa-vest"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] font-black uppercase tracking-wider text-amber-900">
                                Standar Resmi Syal Kuning & Seragam PMR Wira
                            </div>
                            <p class="text-xs text-amber-950 mt-0.5 leading-relaxed">
                                Memuat spesifikasi baku syal kuning segitiga <strong>120 cm x 90 cm x 90 cm</strong> dengan bordir logo PMI dan lambang sekolah, aturan PDH/PDL, tata kelola Markas UKS, dan prosedur musyawarah RAT.
                            </p>
                        </div>
                    </div>

                    <!-- Directory 11 Bab (Mini Explorer Grid) -->
                    <div class="mb-6">
                        <div class="text-xs font-black uppercase tracking-wider text-slate-700 mb-3 flex items-center justify-between">
                            <span>Struktur Dokumen PPUO ({{ $guideline->chapters->count() }} Bab)</span>
                            <span class="text-[11px] text-slate-400 font-medium">Lengkap & Tersusun Rapi</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            @foreach($guideline->chapters->take(6) as $ch)
                            <a href="{{ route('pedoman-organisasi') }}#ppuo" class="p-2.5 rounded-xl bg-slate-50 hover:bg-red-50 hover:border-red-200 border border-slate-100 transition flex items-center gap-2 group/item">
                                <i class="fa-solid fa-file-lines text-pmr-primary text-[10px] w-4 text-center"></i>
                                <span class="font-extrabold text-slate-700 group-hover/item:text-pmr-primary transition truncate">{{ $ch->chapter_number }}: {{ $ch->title }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Action CTA PPUO -->
                <div class="relative z-10 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-2.5">
                    <a href="{{ route('pedoman-organisasi') }}#ppuo" 
                       class="w-full sm:flex-1 py-3 px-5 rounded-xl bg-gradient-to-r from-pmr-primary to-red-700 hover:from-pmr-dark hover:to-red-800 text-white font-extrabold text-xs uppercase tracking-wider text-center shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-book-journal-whills"></i> Buka Reader Digital Lengkap ({{ $guideline->chapters->count() }} Bab)
                    </a>
                    <a href="{{ $guideline->ppuo_pdf_url }}" download
                       class="w-full sm:w-auto py-3 px-5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-extrabold text-xs uppercase tracking-wider text-center transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-download text-amber-700"></i> Unduh Buku PDF (27 Hal)
                    </a>
                </div>
            </div>

        </div>

        <!-- Bottom Banner Callout -->
        <div class="mt-10 bg-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6 border border-slate-800">
            <div class="flex items-center gap-4 text-center sm:text-left">
                <div class="w-12 h-12 rounded-2xl bg-red-600/30 border border-red-500/40 text-red-400 flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                    <i class="fa-solid fa-stamp"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-base sm:text-lg text-white">Lembar Pengesahan & Arsip Resmi {{ $guideline->period }}</h4>
                    <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                        Dokumen GBHO & PPUO ini telah disahkan bersama {{ $guideline->signer_presidium }}, {{ $guideline->signer_pembina }}, dan {{ $guideline->signer_school }} pada {{ $guideline->established_date }}.
                    </p>
                </div>
            </div>
            <a href="{{ route('pedoman-organisasi') }}" class="px-6 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-900 font-black text-xs uppercase tracking-wider transition whitespace-nowrap shadow-md flex items-center gap-2">
                <span>Eksplorasi Halaman Pedoman</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <!-- Banner Prestasi & Jejak Juara -->
        <div class="mt-6 bg-gradient-to-r from-amber-600 via-amber-700 to-amber-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6 border border-amber-500/30">
            <div class="flex items-center gap-4 text-center sm:text-left">
                <div class="w-12 h-12 rounded-2xl bg-white/20 border border-white/30 text-amber-200 flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 bg-white/20 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1">
                        Pencapaian & Kehormatan
                    </div>
                    <h4 class="font-extrabold text-base sm:text-lg text-white">Etalase Prestasi & Galeri Juara PMR Wira</h4>
                    <p class="text-xs sm:text-sm text-amber-100/90 mt-0.5">
                        Telusuri rekam jejak trofi juara, sertifikat kejuaraan, dan dokumentasi penyerahan piala kontingen relawan.
                    </p>
                </div>
            </div>
            <a href="{{ url('/prestasi') }}" class="px-6 py-3 rounded-xl bg-white hover:bg-amber-50 text-amber-900 font-black text-xs uppercase tracking-wider transition whitespace-nowrap shadow-md flex items-center gap-2">
                <span>Lihat Koleksi Prestasi</span>
                <i class="fa-solid fa-trophy text-amber-600"></i>
            </a>
        </div>

    </div>
</section>

<!-- POPUP MODAL PROGRAM KERJA BIDANG -->
<div id="program-kerja-modal" class="fixed inset-0 z-50 bg-stone-900/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full max-h-[85vh] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="p-6 bg-gradient-to-r from-red-50 via-rose-50 to-white border-b border-red-100 flex items-start justify-between">
            <div class="flex items-center gap-3.5">
                <div id="modal-prog-icon-box" class="w-12 h-12 rounded-2xl bg-pmr-primary text-white flex items-center justify-center text-xl flex-shrink-0 shadow-md shadow-red-900/20">
                    <i id="modal-prog-icon" class="fa-solid fa-shapes"></i>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 bg-red-100 text-pmr-primary px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-clipboard-check"></i> Program Kerja Resmi
                    </div>
                    <h3 id="modal-prog-title" class="text-lg font-extrabold text-slate-900 leading-tight">Program Kerja</h3>
                    <p id="modal-prog-leader" class="text-xs text-slate-500 mt-0.5"></p>
                </div>
            </div>
            <button type="button" onclick="closeProgramModal()" class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-200/80 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Body: Work Program Items -->
        <div class="p-6 overflow-y-auto flex-grow space-y-3" id="modal-prog-body">
            <!-- Populated by JS -->
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-500">PMR Wira SMAN 1 Ciawi 2026/2027</span>
            <button type="button" onclick="closeProgramModal()" class="px-5 py-2 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white text-xs font-bold shadow transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- POPUP MODAL BIODATA ANGGOTA LENGKAP (KTA DIGITAL RELAWAN RESMI) -->
<div id="biodata-modal" class="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-4 animate-in fade-in duration-200">
    <div class="bg-white rounded-[32px] max-w-xl sm:max-w-2xl w-full max-h-[94vh] shadow-2xl flex flex-col overflow-hidden border border-slate-200/90 animate-in zoom-in-95 duration-200">
        
        <!-- Header Tier 1: Red Top Bar with Dual Logos, Status, and Close Button -->
        <div class="bg-[#8B0000] text-white px-5 sm:px-6 py-3.5 flex items-center justify-between border-b border-red-950/30">
            <!-- Logos -->
            <div class="flex items-center gap-2">
                <div class="bg-white px-2 py-1 rounded-xl shadow-xs flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo PMR Wira & SMAN 1 Ciawi" class="h-6 sm:h-7 w-auto object-contain">
                </div>
            </div>

            <!-- Status & Close Button -->
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center gap-1.5 bg-[#1b7a43] text-white px-3 sm:px-3.5 py-1 rounded-full text-[10px] sm:text-[11px] font-black uppercase tracking-wider shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#4ade80] animate-pulse"></span> STATUS: RELAWAN AKTIF
                </span>
                <button type="button" onclick="closeBiodataModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white text-white hover:text-[#8B0000] flex items-center justify-center transition shadow-xs text-sm font-bold" title="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Header Tier 2: Dark Slate KTA Ribbon Strip -->
        <div class="bg-[#0f172a] text-white px-5 sm:px-6 py-3 flex items-center gap-3 border-b border-slate-800">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-300 via-amber-400 to-amber-600 text-stone-950 flex items-center justify-center text-[11px] font-black shadow-md flex-shrink-0 border border-amber-200 ring-2 ring-amber-400/30">
                KTA
            </div>
            <div class="min-w-0">
                <div class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-amber-300 leading-tight">
                    KARTU TANDA ANGGOTA (KTA) ELEKTRONIK
                </div>
                <h3 class="text-base sm:text-lg font-black text-white leading-tight mt-0.5 truncate">
                    Biodata Resmi Relawan PMR Wira
                </h3>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-7 overflow-y-auto flex-grow space-y-5 bg-slate-50/50 custom-scrollbar">
            
            <!-- Hero Profile Card: Golden Ring Portrait + Identity + Barcode -->
            <div class="bg-white p-5 sm:p-6 rounded-[28px] border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center sm:items-start gap-6 sm:gap-7">
                
                <!-- Left: Portrait Photo with Golden Ring & Bottom Ribbon -->
                <div class="relative flex-shrink-0 flex flex-col items-center">
                    <!-- Golden Aura Outer Ring -->
                    <div class="relative w-40 h-52 sm:w-46 sm:h-60 rounded-3xl p-1 bg-gradient-to-b from-amber-200 via-amber-400 to-amber-600 shadow-xl">
                        <div class="w-full h-full rounded-[22px] overflow-hidden bg-slate-100 relative">
                            <img id="bio-photo" src="" alt="Foto Anggota" class="w-full h-full object-cover object-top member-photo-zoom" style="object-position: center 18%;">
                        </div>
                    </div>
                    <!-- Bottom Ribbon Banner across the base of the photo -->
                    <div class="relative -mt-5 z-10 w-[94%] max-w-[190px] text-center bg-gradient-to-r from-[#7f0000] via-pmr-primary to-[#7f0000] text-white py-1 px-2.5 rounded-lg shadow-lg border border-amber-300/40">
                        <div class="flex items-center justify-center gap-1.5 text-[10px] sm:text-[11px] font-black tracking-wide uppercase text-amber-200 leading-tight">
                            <i class="fa-solid fa-crown text-[9px] text-amber-300" id="bio-ribbon-icon"></i>
                            <span id="bio-ribbon-pos" class="truncate">KETUA UMUM</span>
                        </div>
                        <div class="text-[9px] font-bold text-red-200 tracking-wider mt-0.5" id="bio-ribbon-year">2026/2027</div>
                    </div>
                </div>

                <!-- Right: Identity Details & Barcode -->
                <div class="text-center sm:text-left min-w-0 flex-grow pt-0.5 w-full">
                    <!-- Category Badge -->
                    <div class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider mb-2 border border-slate-200/80">
                        <i class="fa-solid fa-shield-halved text-pmr-primary text-xs"></i>
                        <span id="bio-badge-pos">PIMPINAN UTAMA KEPENGURUSAN</span>
                    </div>

                    <!-- Member Full Name -->
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight tracking-tight" id="bio-name">
                        -
                    </h2>

                    <!-- Subtitle / Masa Bakti -->
                    <p class="text-xs sm:text-sm text-slate-500 font-bold mt-1" id="bio-subtitle">
                        Masa Bakti Ragana Dwi Pantara 2026/2027
                    </p>

                    <!-- Grade & School Pill -->
                    <div class="inline-block mt-2 px-3 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold border border-slate-200/80" id="bio-class">
                        Kelas XII • SMAN 1 Ciawi Bogor
                    </div>

                    <!-- Realistic Barcode Graphic -->
                    <div class="mt-4 p-2.5 sm:p-3 rounded-2xl bg-white border border-slate-200 shadow-2xs flex flex-col items-center justify-center max-w-sm sm:max-w-none mx-auto sm:mx-0">
                        <svg class="h-8 w-44 sm:w-52 text-slate-800" viewBox="0 0 200 40" fill="currentColor">
                            <rect x="0" y="0" width="3" height="40"/><rect x="6" y="0" width="2" height="40"/><rect x="11" y="0" width="5" height="40"/><rect x="18" y="0" width="2" height="40"/><rect x="23" y="0" width="4" height="40"/><rect x="30" y="0" width="2" height="40"/><rect x="34" y="0" width="6" height="40"/><rect x="43" y="0" width="3" height="40"/><rect x="49" y="0" width="2" height="40"/><rect x="54" y="0" width="5" height="40"/><rect x="62" y="0" width="3" height="40"/><rect x="68" y="0" width="2" height="40"/><rect x="73" y="0" width="6" height="40"/><rect x="82" y="0" width="3" height="40"/><rect x="88" y="0" width="4" height="40"/><rect x="95" y="0" width="2" height="40"/><rect x="100" y="0" width="5" height="40"/><rect x="108" y="0" width="3" height="40"/><rect x="114" y="0" width="2" height="40"/><rect x="119" y="0" width="6" height="40"/><rect x="128" y="0" width="3" height="40"/><rect x="134" y="0" width="4" height="40"/><rect x="141" y="0" width="2" height="40"/><rect x="146" y="0" width="5" height="40"/><rect x="154" y="0" width="3" height="40"/><rect x="160" y="0" width="2" height="40"/><rect x="165" y="0" width="6" height="40"/><rect x="174" y="0" width="3" height="40"/><rect x="180" y="0" width="4" height="40"/><rect x="187" y="0" width="2" height="40"/><rect x="192" y="0" width="5" height="40"/>
                        </svg>
                        <span class="text-[10px] sm:text-[11px] font-mono font-bold tracking-widest text-slate-700 mt-1" id="bio-token-id">PMR-WIRA-2026-004</span>
                    </div>
                </div>
            </div>

            <!-- Section Title -->
            <div class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-900 pt-1 px-1">
                INFORMASI DETAIL ANGGOTA
            </div>

            <!-- Bento Grid & Direct Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5">
                
                <!-- Left: 4 Data Cards (2x2 Grid) in 7-8 Cols -->
                <div class="md:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    
                    <!-- Card 1: NRI -->
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-red-300 transition">
                        <div class="flex items-center gap-2 text-slate-800 font-extrabold text-xs">
                            <i class="fa-solid fa-address-card text-pmr-primary"></i>
                            <span>Nomor Registrasi</span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-medium mt-1">Nomor Registrasi (NRI)</div>
                        <div class="text-xs sm:text-sm font-black text-slate-900 mt-0.5 truncate" id="bio-nis">-</div>
                    </div>

                    <!-- Card 2: Jenis Kelamin -->
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-blue-300 transition">
                        <div class="flex items-center gap-2 text-slate-800 font-extrabold text-xs">
                            <i class="fa-solid fa-user text-blue-600"></i>
                            <span>Jenis</span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-medium mt-1">Jenis Kelamin</div>
                        <div class="text-xs sm:text-sm font-black text-slate-900 mt-0.5 truncate" id="bio-gender">-</div>
                    </div>

                    <!-- Card 3: Tempat & Tgl Lahir -->
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-300 transition">
                        <div class="flex items-center gap-2 text-slate-800 font-extrabold text-xs">
                            <i class="fa-solid fa-cake-candles text-amber-600"></i>
                            <span>Tempat & Tgl Lahir</span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-medium mt-1">Tempat & Tgl Lahir</div>
                        <div class="text-xs sm:text-sm font-black text-slate-900 mt-0.5 truncate" id="bio-ttl">-</div>
                    </div>

                    <!-- Card 4: Alamat Domisili -->
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-emerald-300 transition">
                        <div class="flex items-center gap-2 text-slate-800 font-extrabold text-xs">
                            <i class="fa-solid fa-location-dot text-emerald-600"></i>
                            <span>Alamat Domisili</span>
                        </div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 mt-1 leading-snug line-clamp-2" id="bio-address">-</div>
                    </div>

                </div>

                <!-- Right: Action Buttons (WhatsApp & Email) in 4 Cols -->
                <div class="md:col-span-4 flex flex-col justify-center gap-3">
                    <!-- WhatsApp Button -->
                    <a id="bio-wa-link" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3.5 rounded-2xl bg-[#1e7e48] hover:bg-[#166538] text-white font-extrabold shadow-sm transition">
                        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div class="text-left min-w-0">
                            <div class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider">WhatsApp</div>
                            <div class="text-xs font-black truncate max-w-[150px]" id="bio-phone">-</div>
                        </div>
                    </a>

                    <!-- Email Button -->
                    <a id="bio-email-link" href="#" class="flex items-center gap-3 p-3.5 rounded-2xl bg-[#263147] hover:bg-[#1a2233] text-white font-extrabold shadow-sm transition">
                        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-base flex-shrink-0">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="text-left min-w-0">
                            <div class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Email</div>
                            <div class="text-xs font-black truncate max-w-[150px]" id="bio-email">-</div>
                        </div>
                    </a>
                </div>

            </div>

            <!-- Motto Box (Rose/Cream Card with Quote) -->
            <div id="bio-motto-container" class="p-4 sm:p-5 rounded-2xl bg-[#fbf2f2] border border-[#f5d0d0] shadow-2xs flex items-start gap-4">
                <span class="text-4xl leading-none text-amber-500/80 font-serif font-black select-none">“</span>
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-800 leading-relaxed">
                        <span class="font-bold text-slate-900">Motto Relawan:</span>
                        <span id="bio-motto" class="font-semibold text-slate-700"></span>
                    </p>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">Palang Merah Remaja Wira • Unit SMAN 1 Ciawi</span>
            <button type="button" onclick="closeBiodataModal()" class="px-7 py-2 rounded-full bg-[#800000] hover:bg-[#600000] text-white text-xs font-bold shadow-md transition">
                Tutup Biodata
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openBiodataModal(data) {
        if (!data) return;
        const modal = document.getElementById('biodata-modal');
        const photoEl = document.getElementById('bio-photo');
        const nameEl = document.getElementById('bio-name');
        const badgeEl = document.getElementById('bio-badge-pos');
        const subtitleEl = document.getElementById('bio-subtitle');
        const classEl = document.getElementById('bio-class');
        const nisEl = document.getElementById('bio-nis');
        const tokenEl = document.getElementById('bio-token-id');
        const genderEl = document.getElementById('bio-gender');
        const ttlEl = document.getElementById('bio-ttl');
        const addressEl = document.getElementById('bio-address');
        const phoneEl = document.getElementById('bio-phone');
        const emailEl = document.getElementById('bio-email');
        const waLink = document.getElementById('bio-wa-link');
        const emailLink = document.getElementById('bio-email-link');
        const mottoEl = document.getElementById('bio-motto');
        const mottoBox = document.getElementById('bio-motto-container');
        const ribbonPos = document.getElementById('bio-ribbon-pos');
        const ribbonYear = document.getElementById('bio-ribbon-year');
        const ribbonIcon = document.getElementById('bio-ribbon-icon');

        // Photo & Smart Zoom
        if (photoEl) {
            photoEl.src = data.photo || '';
            if (data.photo && !data.photo.includes('ui-avatars.com')) {
                photoEl.classList.add('member-photo-zoom');
                photoEl.style.objectPosition = 'center 18%';
            } else {
                photoEl.classList.remove('member-photo-zoom');
                photoEl.style.objectPosition = 'center center';
            }
        }

        if (nameEl) nameEl.textContent = data.name || '-';
        
        // Ribbon Banner over photo
        const pos = data.position || 'Anggota PMR';
        if (ribbonPos) ribbonPos.textContent = pos.toUpperCase();
        if (ribbonYear) ribbonYear.textContent = '2026/2027';
        if (ribbonIcon) {
            const pLower = pos.toLowerCase();
            if (pLower.includes('ketua')) {
                ribbonIcon.className = 'fa-solid fa-crown text-[9px] text-amber-300';
            } else if (pLower.includes('pembina')) {
                ribbonIcon.className = 'fa-solid fa-user-tie text-[9px] text-amber-300';
            } else {
                ribbonIcon.className = 'fa-solid fa-star text-[9px] text-amber-300';
            }
        }

        // Category Badge
        if (badgeEl) {
            let catText = 'STAF RELAWAN OPERASIONAL';
            const pLower = pos.toLowerCase();
            if (pLower.includes('pembina')) {
                catText = 'PEMBINA RESMI PMR';
            } else if (pLower.includes('ketua') || pLower.includes('wakil') || pLower.includes('sekretaris') || pLower.includes('bendahara')) {
                catText = 'PIMPINAN UTAMA KEPENGURUSAN';
            } else if (pLower.includes('bidang') || pLower.includes('seksi')) {
                catText = 'PENGURUS BIDANG OPERASIONAL';
            }
            badgeEl.textContent = catText;
        }

        // Subtitle & Class
        if (subtitleEl) subtitleEl.textContent = data.subtitle || 'Masa Bakti Ragana Dwi Pantara 2026/2027';
        if (classEl) {
            const cls = data.class_grade || 'Kelas XII';
            classEl.textContent = `${cls} • SMAN 1 Ciawi Bogor`;
        }

        // NRI & Barcode Token
        const nisVal = data.nis || '-';
        if (nisEl) nisEl.textContent = nisVal;
        if (tokenEl) {
            if (nisVal && nisVal !== '-' && !nisVal.startsWith('NIP')) {
                tokenEl.textContent = `PMR-WIRA-${nisVal.replace(/\s+/g, '-')}`;
            } else {
                tokenEl.textContent = 'PMR-WIRA-2026-004';
            }
        }

        // Gender
        if (genderEl) genderEl.textContent = data.gender || '-';

        // TTL
        let ttlStr = '';
        if (data.birth_place && data.birth_date) {
            ttlStr = `${data.birth_place}, ${data.birth_date}`;
        } else if (data.birth_place) {
            ttlStr = data.birth_place;
        } else if (data.birth_date) {
            ttlStr = data.birth_date;
        } else {
            ttlStr = '-';
        }
        if (ttlEl) ttlEl.textContent = ttlStr;

        // Address
        if (addressEl) addressEl.textContent = data.address || '-';

        // WhatsApp
        const phoneVal = data.phone || '-';
        if (phoneEl) phoneEl.textContent = phoneVal;
        if (waLink) {
            if (phoneVal !== '-' && phoneVal.trim() !== '') {
                const cleanPhone = phoneVal.replace(/[^0-9]/g, '');
                const waNumber = cleanPhone.startsWith('0') ? '62' + cleanPhone.substring(1) : cleanPhone;
                waLink.href = 'https://wa.me/' + waNumber;
                waLink.classList.remove('pointer-events-none', 'opacity-60');
            } else {
                waLink.href = '#';
                waLink.classList.add('pointer-events-none', 'opacity-60');
            }
        }

        // Email
        const emailVal = data.email || '-';
        if (emailEl) emailEl.textContent = emailVal;
        if (emailLink) {
            if (emailVal !== '-' && emailVal.trim() !== '') {
                emailLink.href = 'mailto:' + emailVal;
                emailLink.classList.remove('pointer-events-none', 'opacity-60');
            } else {
                emailLink.href = '#';
                emailLink.classList.add('pointer-events-none', 'opacity-60');
            }
        }

        // Motto
        if (mottoEl && mottoBox) {
            if (data.motto && data.motto.trim() !== '') {
                mottoEl.textContent = data.motto;
                mottoBox.classList.remove('hidden');
            } else {
                mottoBox.classList.add('hidden');
            }
        }

        if (modal) modal.classList.remove('hidden');
    }

    function closeBiodataModal() {
        const modal = document.getElementById('biodata-modal');
        if (modal) modal.classList.add('hidden');
    }

    function openProgramModal(position, leaderName, subtitle, photoUrl, iconClass, workProgramText) {
        const modal = document.getElementById('program-kerja-modal');
        const titleEl = document.getElementById('modal-prog-title');
        const leaderEl = document.getElementById('modal-prog-leader');
        const iconEl = document.getElementById('modal-prog-icon');
        const bodyEl = document.getElementById('modal-prog-body');

        if (titleEl) titleEl.textContent = 'Program Kerja ' + position;
        if (leaderEl) leaderEl.textContent = subtitle ? `${leaderName} (${subtitle})` : leaderName;
        if (iconEl) iconEl.className = iconClass || 'fa-solid fa-shapes';

        if (bodyEl) {
            bodyEl.innerHTML = '';

            if (!workProgramText || workProgramText.trim() === '') {
                bodyEl.innerHTML = `
                    <div class="text-center py-8 text-slate-400">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-2 text-slate-300 block"></i>
                        <p class="text-sm font-semibold text-slate-600">Belum ada program kerja yang diinput.</p>
                        <p class="text-xs text-slate-400 mt-1">Pengurus dapat menambahkan program kerja melalui panel admin.</p>
                    </div>
                `;
            } else {
                const lines = workProgramText.split('\n').map(l => l.trim()).filter(l => l.length > 0);
                
                let html = '<div class="space-y-2.5">';
                lines.forEach((line, index) => {
                    const cleanText = line.replace(/^[\d+\.\-\*\•]\s*/, '');
                    html += `
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 hover:border-pmr-primary/50 transition">
                            <div class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs font-black flex-shrink-0 mt-0.5">
                                ${index + 1}
                            </div>
                            <div class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed">
                                ${cleanText}
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                bodyEl.innerHTML = html;
            }
        }

        if (modal) modal.classList.remove('hidden');
    }

    function closeProgramModal() {
        const modal = document.getElementById('program-kerja-modal');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProgramModal();
            closeBiodataModal();
        }
    });

    document.getElementById('program-kerja-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeProgramModal();
    });

    document.getElementById('biodata-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeBiodataModal();
    });
</script>
@endpush
@endsection

