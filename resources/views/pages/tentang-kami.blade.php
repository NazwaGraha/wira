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
    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8">
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
                $bph = $organizationMembers->get(3, collect());
                $seksi = $organizationMembers->get(4, collect());
                $others = $organizationMembers->get(5, collect());
            @endphp

            <!-- Level 1: Pembina PMR -->
            @if($pembina->isNotEmpty())
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($pembina as $m)
                        @php
                            $photoUrl = $m->photo_url ?: ('https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=dc2626&color=ffffff&bold=true');
                            $initials = strtoupper(substr($m->name, 0, 2));
                        @endphp
                        <div class="bg-stone-900 text-white p-6 sm:p-7 rounded-3xl shadow-xl flex flex-col sm:flex-row items-center gap-6 min-w-[320px] max-w-lg border-t-4 border-pmr-primary hover:shadow-2xl hover:scale-[1.01] transition duration-300">
                            <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-4 border-red-500 shadow-xl flex-shrink-0 cursor-pointer hover:opacity-90 transition" onclick="previewImage('{{ $photoUrl }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->position) }}')">
                            <div class="text-center sm:text-left">
                                <div class="text-xs uppercase tracking-wider text-red-400 font-extrabold flex items-center justify-center sm:justify-start gap-1.5">
                                    <i class="fa-solid fa-shield-halved text-xs"></i> {{ $m->position }}
                                </div>
                                <div class="text-xl sm:text-2xl font-black mt-1 text-white leading-snug">{{ $m->name }}</div>
                                @if($m->subtitle)
                                    <div class="text-xs sm:text-sm text-stone-400 mt-1">{{ $m->subtitle }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($pembina->isNotEmpty() && ($ketua->isNotEmpty() || $bph->isNotEmpty() || $seksi->isNotEmpty()))
                <div class="w-0.5 h-12 bg-slate-300 my-2"></div>
            @endif

            <!-- Level 2: Ketua Umum / Pimpinan (Hero Card) -->
            @if($ketua->isNotEmpty())
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($ketua as $m)
                        @php
                            $photoUrl = $m->photo_url ?: ('https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=dc2626&color=ffffff&bold=true');
                            $initials = strtoupper(substr($m->name, 0, 2));
                        @endphp
                        <div class="bg-gradient-to-br from-pmr-primary via-red-700 to-pmr-dark text-white p-7 sm:p-9 rounded-3xl shadow-2xl flex flex-col sm:flex-row items-center gap-7 min-w-[340px] max-w-2xl border border-red-400/30 hover:shadow-red-950/30 hover:scale-[1.01] transition duration-300">
                            <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-28 h-28 sm:w-36 sm:h-36 rounded-3xl object-cover border-4 border-white/90 shadow-2xl flex-shrink-0 cursor-pointer hover:scale-105 transition duration-300" onclick="previewImage('{{ $photoUrl }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->position) }}')">
                            <div class="text-center sm:text-left">
                                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-xs px-3.5 py-1 rounded-full text-xs uppercase tracking-wider text-red-100 font-black mb-1">
                                    <i class="fa-solid fa-crown text-amber-300 text-sm"></i> {{ $m->position }}
                                </div>
                                <div class="text-2xl sm:text-3xl font-black mt-1 text-white leading-tight">{{ $m->name }}</div>
                                @if($m->subtitle)
                                    <div class="text-sm sm:text-base text-red-100/90 mt-1 font-medium">{{ $m->subtitle }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($ketua->isNotEmpty() && ($bph->isNotEmpty() || $seksi->isNotEmpty()))
                <div class="w-0.5 h-12 bg-slate-300 my-2"></div>
            @endif

            <!-- Level 3: Pengurus Harian / BPH (Sekretaris & Bendahara) -->
            @if($bph->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-4xl">
                    @foreach($bph as $m)
                        @php
                            $photoUrl = $m->photo_url ?: ('https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=dc2626&color=ffffff&bold=true');
                            $initials = strtoupper(substr($m->name, 0, 2));
                        @endphp
                        <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-lg border border-slate-200/90 flex flex-col sm:flex-row items-center gap-5 hover:shadow-2xl hover:border-pmr-primary transition duration-300">
                            <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-4 border-slate-100 shadow-md flex-shrink-0 cursor-pointer hover:opacity-90 transition" onclick="previewImage('{{ $photoUrl }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->position) }}')">
                            <div class="text-center sm:text-left">
                                <div class="text-xs font-black text-pmr-primary uppercase tracking-wide flex items-center justify-center sm:justify-start gap-1.5">
                                    <i class="{{ $m->icon ?: 'fa-solid fa-user' }} text-xs"></i> {{ $m->position }}
                                </div>
                                <div class="font-extrabold text-slate-900 text-lg sm:text-xl mt-1 leading-snug">{{ $m->name }}</div>
                                @if($m->subtitle)
                                    <div class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">{{ $m->subtitle }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($bph->isNotEmpty() && ($seksi->isNotEmpty() || $others->isNotEmpty()))
                <div class="w-0.5 h-12 bg-slate-300 my-2"></div>
            @endif

            <!-- Level 4: 5 Bidang Utama (Markas, Pelayanan, Diklat, Humas, Kreasi) -->
            @if($seksi->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 sm:gap-7 w-full mt-2">
                    @foreach($seksi as $m)
                        @php
                            $photoUrl = $m->photo_url ?: ('https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=dc2626&color=ffffff&bold=true');
                            $initials = strtoupper(substr($m->name, 0, 2));
                            $staffList = is_array($m->staff_members) ? $m->staff_members : [];
                            $workProg = trim($m->work_program ?? '');
                        @endphp
                        <div class="bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden flex flex-col hover:shadow-2xl hover:border-pmr-primary transition-all duration-300 group">
                            
                            <!-- Header Bidang -->
                            <div class="bg-gradient-to-r from-red-50 via-rose-50 to-white px-5 py-4 border-b border-red-100 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-pmr-primary text-white flex items-center justify-center text-base font-bold flex-shrink-0 shadow-md">
                                    <i class="{{ $m->icon ?: 'fa-solid fa-shapes' }}"></i>
                                </div>
                                <h3 class="font-black text-sm sm:text-base text-slate-900 uppercase tracking-tight group-hover:text-pmr-primary transition">
                                    {{ $m->position }}
                                </h3>
                            </div>

                            <!-- Ketua Bidang (Foto Besar & Jelas) -->
                            <div class="p-6 flex flex-col items-center text-center border-b border-slate-100 bg-white">
                                <div class="relative group/avatar">
                                    <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl object-cover border-4 border-red-100 ring-2 ring-pmr-primary/40 shadow-lg flex-shrink-0 cursor-pointer hover:scale-105 transition duration-300" onclick="previewImage('{{ $photoUrl }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->position) }}')">
                                    <span class="absolute -bottom-2 -right-2 bg-pmr-primary text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow border-2 border-white">Ketua</span>
                                </div>
                                <div class="mt-4">
                                    <div class="font-extrabold text-slate-900 text-base sm:text-lg leading-snug" title="{{ $m->name }}">{{ $m->name }}</div>
                                    <div class="text-xs text-pmr-primary font-bold mt-1 inline-block bg-red-50 px-2.5 py-0.5 rounded-full border border-red-100">
                                        {{ $m->subtitle ?: 'Ketua ' . $m->position }}
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian Staf (FOTO STAF BESAR & SANGAT JELAS) -->
                            <div class="p-5 bg-slate-50/80 flex-grow space-y-4">
                                <div class="flex items-center justify-between pb-1 border-b border-slate-200/60">
                                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-users text-slate-400"></i> Staf Bidang
                                    </span>
                                    <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700">{{ count($staffList) }} Orang</span>
                                </div>

                                @if(!empty($staffList) && count($staffList) > 0)
                                    <div class="space-y-3">
                                        @foreach($staffList as $st)
                                            @php
                                                $stName = $st['name'] ?? '';
                                                $stClass = $st['class_grade'] ?? '';
                                                $stPhoto = $st['photo'] ?? ('https://ui-avatars.com/api/?name=' . urlencode($stName) . '&background=dc2626&color=ffffff&bold=true');
                                            @endphp
                                            <div class="bg-white p-3 sm:p-3.5 rounded-2xl border border-slate-200/90 shadow-sm flex items-center gap-3.5 hover:border-pmr-primary hover:shadow-md transition">
                                                <img src="{{ $stPhoto }}" alt="{{ $stName }}" class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl object-cover border-2 border-slate-200 shadow-sm flex-shrink-0 cursor-pointer hover:scale-105 transition duration-200" onclick="previewImage('{{ $stPhoto }}', '{{ addslashes($stName) }}', 'Staf {{ addslashes($m->position) }}')">
                                                <div class="min-w-0 text-left">
                                                    <div class="text-sm font-extrabold text-slate-900 leading-snug" title="{{ $stName }}">{{ $stName }}</div>
                                                    <div class="text-xs text-pmr-primary font-bold mt-0.5">Staf</div>
                                                    @if($stClass)
                                                        <div class="text-xs text-slate-500 font-medium truncate mt-0.5">{{ $stClass }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-slate-400 italic py-4 text-center">Staf operasional bidang</div>
                                @endif
                            </div>

                            <!-- Tombol Klik Disini Untuk Melihat Program Kerja -->
                            <div class="p-4 bg-white border-t border-slate-100 mt-auto">
                                <button type="button" 
                                    onclick="openProgramModal('{{ addslashes($m->position) }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->subtitle ?: 'Ketua ' . $m->position) }}', '{{ addslashes($photoUrl ?? '') }}', '{{ addslashes($m->icon ?: 'fa-solid fa-shapes') }}', {!! htmlspecialchars(json_encode($workProg), ENT_QUOTES, 'UTF-8') !!})"
                                    class="w-full text-center py-3.5 px-3 rounded-2xl bg-red-50 hover:bg-pmr-primary text-pmr-primary hover:text-white font-black text-xs sm:text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-xs group/btn">
                                    <i class="fa-solid fa-clipboard-list text-sm group-hover/btn:scale-110 transition"></i>
                                    <span>Program Kerja</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </button>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Level 5: Divisi / Anggota Tambahan (jika ada) -->
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

@push('scripts')
<script>
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
                // Split by newlines
                const lines = workProgramText.split('\n').map(l => l.trim()).filter(l => l.length > 0);
                
                let html = '<div class="space-y-2.5">';
                lines.forEach((line, index) => {
                    // Clean leading number or bullet
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

        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeProgramModal() {
        const modal = document.getElementById('program-kerja-modal');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProgramModal();
        }
    });

    document.getElementById('program-kerja-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeProgramModal();
        }
    });

    function previewImage(url, name, role) {
        const modal = document.getElementById('image-preview-modal');
        const img = document.getElementById('image-preview-img');
        const title = document.getElementById('image-preview-title');
        const subtitle = document.getElementById('image-preview-subtitle');
        if (img) img.src = url;
        if (title) title.textContent = name || '';
        if (subtitle) subtitle.textContent = role || '';
        if (modal) modal.classList.remove('hidden');
    }

    function closeImageModal() {
        const modal = document.getElementById('image-preview-modal');
        if (modal) modal.classList.add('hidden');
    }

    document.getElementById('image-preview-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
</script>
@endpush

<!-- LIGHTBOX MODAL PREVIEW FOTO ANGGOTA -->
<div id="image-preview-modal" class="fixed inset-0 z-50 bg-stone-950/85 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in-95 duration-200 text-center border border-white/20">
        <button type="button" onclick="closeImageModal()" class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-stone-900/60 hover:bg-stone-900 text-white flex items-center justify-center backdrop-blur-xs transition">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
        <div class="p-3 bg-stone-100 flex items-center justify-center">
            <img id="image-preview-img" src="" alt="Preview Foto" class="max-h-[70vh] w-auto max-w-full rounded-2xl object-cover shadow-md">
        </div>
        <div class="p-5 bg-white border-t border-slate-100">
            <h4 id="image-preview-title" class="font-extrabold text-slate-900 text-lg sm:text-xl"></h4>
            <p id="image-preview-subtitle" class="text-xs sm:text-sm font-semibold text-pmr-primary mt-0.5"></p>
        </div>
    </div>
</div>
@endsection

