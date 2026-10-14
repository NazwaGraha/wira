@extends('layouts.app')

@section('title', 'Tentang Kami & Profil PMR')

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
                $pembina = $organizationMembers->get(1, collect());
                $ketua = $organizationMembers->get(2, collect());
                $wakil = $organizationMembers->get(3, collect());
                $bph = $organizationMembers->get(4, collect());
                $seksi = $organizationMembers->get(5, collect());
                $others = $organizationMembers->get(6, collect());

                // Fallback otomatis: jika di organizationMembers belum ada level 1, ambil dari allAdvisors aktif
                if ($pembina->isEmpty()) {
                    if (isset($allAdvisors) && $allAdvisors->isNotEmpty()) {
                        $pembina = $allAdvisors->filter(fn($a) => (bool)$a->is_active);
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
                                $advisor = \App\Models\Advisor::where('name', $m->name)->first();
                            }
                            $mem = method_exists($m, 'member') ? $m->member : null;
                            if (!$mem && isset($allMembers)) {
                                $mem = $allMembers->get($m->name) ?? null;
                            }
                            $photoUrl = (!empty($m->photo_url) ? $m->photo_url : null) ?: ($advisor?->photo_url ?: ($mem?->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=991b1b&color=ffffff&bold=true&size=300'));
                            $displayName = $advisor ? ($advisor->name . ($advisor->title_degree ? ', ' . $advisor->title_degree : '')) : $m->name;
                            $mPos = $m->position ?: ($advisor?->position ?: 'Pembina PMR');
                            $mSub = $advisor?->title_degree ? ('Pembina • ' . $advisor->title_degree) : ($m->subtitle ?? ($advisor?->position ?: 'Guru Pembina PMR'));
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
                        <div class="bg-stone-900 text-white p-6 sm:p-7 rounded-3xl shadow-xl flex flex-col sm:flex-row items-center gap-6 min-w-[320px] max-w-lg border-t-4 border-pmr-primary hover:shadow-2xl transition duration-300 group">
                            <div class="relative cursor-pointer group/photo flex-shrink-0" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">
                                <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl object-cover border-4 border-red-500 shadow-xl group-hover/photo:scale-105 transition-transform duration-300">
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
                                <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-red-300 hover:text-white bg-white/10 hover:bg-red-600/60 px-3 py-1.5 rounded-full transition">
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
                        <div class="bg-gradient-to-br from-pmr-primary via-red-700 to-pmr-dark text-white p-7 sm:p-9 rounded-3xl shadow-2xl flex flex-col sm:flex-row items-center gap-7 min-w-[340px] max-w-2xl border border-red-400/30 hover:shadow-red-950/30 transition duration-300 group">
                            <div class="relative cursor-pointer group/photo flex-shrink-0" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">
                                <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl object-cover border-4 border-white/90 shadow-2xl ring-2 ring-red-400 group-hover/photo:scale-105 transition-transform duration-300">
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
                                <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-white bg-white/20 hover:bg-white hover:text-pmr-primary px-3.5 py-1.5 rounded-full transition shadow-xs">
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
                        <div class="bg-gradient-to-br from-slate-900 via-red-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row items-center gap-6 min-w-[320px] max-w-xl border border-amber-500/30 hover:shadow-2xl transition duration-300 group">
                            <div class="relative cursor-pointer group/photo flex-shrink-0" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">
                                <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl object-cover border-4 border-amber-400 shadow-xl group-hover/photo:scale-105 transition-transform duration-300">
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
                                <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-amber-200 bg-white/10 hover:bg-amber-500 hover:text-slate-950 px-3.5 py-1.5 rounded-full transition shadow-xs">
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
                        <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-lg border border-slate-200/90 flex flex-col sm:flex-row items-center gap-5 hover:shadow-2xl hover:border-pmr-primary transition duration-300 group">
                            <div class="relative cursor-pointer group/photo flex-shrink-0" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})">
                                <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl object-cover border-4 border-slate-100 shadow-md group-hover/photo:scale-105 transition-transform duration-300">
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
                                <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($biodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-2.5 inline-flex items-center gap-1.5 text-xs font-bold text-pmr-primary hover:text-white bg-red-50 hover:bg-pmr-primary px-3 py-1.5 rounded-full transition border border-red-100">
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
                        <div class="{{ $chunk->count() === 2 ? 'grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-7 w-full max-w-4xl mx-auto justify-center' : ($chunk->count() === 1 ? 'flex justify-center w-full max-w-md mx-auto' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 w-full max-w-7xl mx-auto') }}">
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

                                    <!-- Ketua Bidang -->
                                    <div class="p-5 flex flex-col items-center text-center bg-gradient-to-b from-red-50/40 via-white to-white border-b border-slate-100">
                                        <div class="relative cursor-pointer group/photo" onclick="openBiodataModal({!! htmlspecialchars(json_encode($leaderBiodata), ENT_QUOTES, 'UTF-8') !!})">
                                            <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-4 border-white ring-2 ring-red-400 shadow-md group-hover/photo:scale-105 transition-transform duration-300">
                                            <span class="absolute -bottom-1 -right-1 bg-pmr-primary text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow border-2 border-white flex items-center gap-1">
                                                <i class="fa-solid fa-crown text-[8px] text-amber-300"></i> Ketua
                                            </span>
                                            <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover/photo:opacity-100 flex items-center justify-center transition text-white text-[10px] font-bold gap-1 backdrop-blur-xs">
                                                <i class="fa-solid fa-id-card"></i> Biodata
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="font-extrabold text-slate-900 text-sm sm:text-base leading-snug cursor-pointer hover:text-pmr-primary transition" onclick="openBiodataModal({!! htmlspecialchars(json_encode($leaderBiodata), ENT_QUOTES, 'UTF-8') !!})" title="{{ $m->name }}">
                                                {{ $m->name }}
                                            </div>
                                            <div class="text-xs text-pmr-primary font-bold mt-0.5">
                                                {{ $m->subtitle ?: 'Ketua ' . $m->position }}
                                            </div>
                                            <button type="button" onclick="openBiodataModal({!! htmlspecialchars(json_encode($leaderBiodata), ENT_QUOTES, 'UTF-8') !!})" class="mt-1.5 text-[11px] font-bold text-slate-500 hover:text-pmr-primary inline-flex items-center gap-1 transition">
                                                <i class="fa-solid fa-address-card text-xs text-pmr-primary"></i> Biodata Ketua &rarr;
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Area Staf Anggota (Mini-Grid Avatars yang Rapi & Muat Banyak) -->
                                    <div class="p-4 bg-slate-50/70 flex-grow flex flex-col justify-between">
                                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200/70">
                                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                                                <i class="fa-solid fa-users text-pmr-primary text-xs"></i> Staf Anggota Bidang
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400">
                                                Klik foto untuk biodata
                                            </span>
                                        </div>

                                        @if(!empty($staffList) && count($staffList) > 0)
                                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5 sm:gap-3">
                                                @foreach($staffList as $st)
                                                    @php
                                                        $stName = $st['name'] ?? '';
                                                        $stMem = $allMembers->get($stName);
                                                        $stClass = $st['class_grade'] ?? ($stMem?->class_grade ?? '');
                                                        $stPhoto = $st['photo'] ?? ($stMem?->photo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($stName) . '&background=dc2626&color=ffffff&bold=true&size=300'));
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
                                                    <div class="group/staf flex flex-col items-center text-center cursor-pointer p-1.5 rounded-xl hover:bg-white hover:shadow-xs transition"
                                                         onclick="openBiodataModal({!! htmlspecialchars(json_encode($stBiodata), ENT_QUOTES, 'UTF-8') !!})"
                                                         title="Klik untuk melihat biodata {{ $stName }}">
                                                        <div class="relative w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0">
                                                            <img src="{{ $stPhoto }}" alt="{{ $stName }}" class="w-full h-full rounded-full object-cover border-2 border-white ring-2 ring-red-200 shadow-xs group-hover/staf:scale-105 group-hover/staf:ring-pmr-primary transition-transform duration-200">
                                                            <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover/staf:opacity-100 flex items-center justify-center transition text-white text-[9px] font-bold backdrop-blur-xs">
                                                                <i class="fa-solid fa-id-card"></i>
                                                            </div>
                                                        </div>
                                                        <div class="mt-1.5 w-full">
                                                            <div class="text-[11px] font-bold text-slate-800 group-hover/staf:text-pmr-primary truncate leading-tight transition" title="{{ $stName }}">
                                                                {{ $stName }}
                                                            </div>
                                                            @if($stClass)
                                                                <div class="text-[9px] text-slate-400 font-medium truncate mt-0.5">
                                                                    {{ $stClass }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-xs text-slate-400 italic py-6 text-center">
                                                Belum ada staf operasional terdaftar
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

<!-- POPUP MODAL BIODATA ANGGOTA LENGKAP -->
<div id="biodata-modal" class="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200 border border-red-100">
        
        <!-- Modal Top Header Banner -->
        <div class="relative bg-gradient-to-r from-pmr-primary via-red-700 to-pmr-dark text-white p-6 sm:p-7 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-xs text-white flex items-center justify-center text-lg shadow-inner">
                    <i class="fa-solid fa-id-card-clip"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-black tracking-widest text-red-200 bg-red-900/40 px-2 py-0.5 rounded-full">Biodata Anggota</span>
                    <h3 class="text-base sm:text-lg font-black leading-tight text-white mt-0.5">PMR Wira SMAN 1 Ciawi</h3>
                </div>
            </div>
            <button type="button" onclick="closeBiodataModal()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/30 text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 sm:p-7 overflow-y-auto flex-grow space-y-6">
            
            <!-- Hero Profile Card -->
            <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-6 bg-slate-50 p-5 rounded-3xl border border-slate-200/80">
                <img id="bio-photo" src="" alt="Foto Anggota" class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl object-cover border-4 border-white shadow-xl flex-shrink-0">
                <div class="text-center sm:text-left min-w-0">
                    <div class="inline-flex items-center gap-1.5 bg-red-100 text-pmr-primary px-3 py-0.5 rounded-full text-xs font-black uppercase tracking-wider mb-1.5" id="bio-badge-pos">
                        <!-- Position Badge -->
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight" id="bio-name"></h2>
                    <p class="text-xs sm:text-sm text-slate-600 font-semibold mt-1" id="bio-class"></p>
                </div>
            </div>

            <!-- Detailed Grid Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                
                <!-- NIS -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary flex items-center justify-center text-base flex-shrink-0 font-bold">
                        <i class="fa-solid fa-address-card"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">NRI (Nomor Registrasi Induk)</div>
                        <div class="text-xs sm:text-sm font-extrabold text-slate-800 truncate mt-0.5" id="bio-nis">-</div>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base flex-shrink-0 font-bold">
                        <i class="fa-solid fa-venus-mars"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</div>
                        <div class="text-xs sm:text-sm font-extrabold text-slate-800 truncate mt-0.5" id="bio-gender">-</div>
                    </div>
                </div>

                <!-- Tempat, Tanggal Lahir -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3.5 sm:col-span-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base flex-shrink-0 font-bold">
                        <i class="fa-solid fa-cake-candles"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tempat, Tanggal Lahir</div>
                        <div class="text-xs sm:text-sm font-extrabold text-slate-800 truncate mt-0.5" id="bio-ttl">-</div>
                    </div>
                </div>

                <!-- Alamat Domisili -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3.5 sm:col-span-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base flex-shrink-0 font-bold">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Alamat Domisili</div>
                        <div class="text-xs sm:text-sm font-extrabold text-slate-800 mt-0.5 leading-snug" id="bio-address">-</div>
                    </div>
                </div>

                <!-- No. WhatsApp / HP -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base flex-shrink-0 font-bold">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">No. Telepon / WA</div>
                        <div class="text-xs sm:text-sm font-extrabold text-slate-800 truncate mt-0.5" id="bio-phone">-</div>
                    </div>
                </div>

                <!-- Email -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base flex-shrink-0 font-bold">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email</div>
                        <div class="text-xs sm:text-sm font-extrabold text-slate-800 truncate mt-0.5" id="bio-email">-</div>
                    </div>
                </div>

            </div>

            <!-- Motto Box -->
            <div id="bio-motto-container" class="p-4 rounded-2xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-100 flex items-start gap-3">
                <i class="fa-solid fa-quote-left text-pmr-primary text-xl flex-shrink-0 mt-0.5"></i>
                <div>
                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-pmr-primary">Motto Hidup Relawan</div>
                    <p class="text-xs sm:text-sm font-medium italic text-slate-700 mt-1 leading-relaxed" id="bio-motto"></p>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">Masa Bakti Ragana Dwi Pantara 2026/2027</span>
            <button type="button" onclick="closeBiodataModal()" class="px-6 py-2.5 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white text-xs font-extrabold shadow-md transition">
                Tutup
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
        const classEl = document.getElementById('bio-class');
        const nisEl = document.getElementById('bio-nis');
        const genderEl = document.getElementById('bio-gender');
        const ttlEl = document.getElementById('bio-ttl');
        const addressEl = document.getElementById('bio-address');
        const phoneEl = document.getElementById('bio-phone');
        const emailEl = document.getElementById('bio-email');
        const mottoEl = document.getElementById('bio-motto');
        const mottoBox = document.getElementById('bio-motto-container');

        if (photoEl) photoEl.src = data.photo || '';
        if (nameEl) nameEl.textContent = data.name || '-';
        if (badgeEl) badgeEl.textContent = data.position || 'Anggota PMR';
        if (classEl) classEl.textContent = data.class_grade ? `Tingkat / Kelas: ${data.class_grade}` : (data.subtitle || '');
        if (nisEl) nisEl.textContent = data.nis || '-';
        if (genderEl) genderEl.textContent = data.gender || '-';
        
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

        if (addressEl) addressEl.textContent = data.address || '-';
        if (phoneEl) phoneEl.textContent = data.phone || '-';
        if (emailEl) emailEl.textContent = data.email || '-';

        if (mottoEl && mottoBox) {
            if (data.motto && data.motto.trim() !== '') {
                mottoEl.textContent = `"${data.motto}"`;
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

