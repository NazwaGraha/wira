<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentSectionImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContentSetupController extends Controller
{
    /**
     * Tampilkan formulir pengaturan konten halaman Beranda.
     */
    public function home(Request $request): View
    {
        $activeTab = $request->query('tab', 'profile');
        if (! in_array($activeTab, ['profile', 'pillars'], true)) {
            $activeTab = 'profile';
        }

        $homeProfile = ContentSectionImage::getSection('home_profile');
        $homePillars = ContentSectionImage::getSection('home_pillars');

        return view('admin.content-setup.home', compact('activeTab', 'homeProfile', 'homePillars'));
    }

    /**
     * Perbarui konten section Profil Ekstrakurikuler pada halaman Beranda.
     */
    public function updateHomeProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagline' => 'nullable|string|max:100',
            'tagline_icon' => 'nullable|string|max:100',
            'headline' => 'required|string|max:255',
            'body_text' => 'required|string|max:5000',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'badge_subtext' => 'nullable|string|max:150',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'headline.required' => 'Judul utama section (H1/H2) wajib diisi.',
            'body_text.required' => 'Deskripsi narasi profil (Text Area) wajib diisi.',
            'image.image' => 'Berkas yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran berkas gambar maksimal 5 MB.',
        ]);

        $homeProfile = ContentSectionImage::getSection('home_profile');

        // Filter checklist items agar tidak ada baris kosong
        $checklistItems = [];
        if (! empty($validated['checklist_items']) && is_array($validated['checklist_items'])) {
            $checklistItems = array_values(array_filter(
                $validated['checklist_items'],
                fn ($item) => filled(trim((string) $item))
            ));
        }

        $data = [
            'tagline' => $validated['tagline'] ?? $homeProfile->display_tagline,
            'tagline_icon' => $validated['tagline_icon'] ?? $homeProfile->display_tagline_icon,
            'headline' => $validated['headline'],
            'body_text' => $validated['body_text'],
            'checklist_items' => ! empty($checklistItems) ? $checklistItems : $homeProfile->display_checklist_items,
            'button_text' => $validated['button_text'] ?? $homeProfile->display_button_text,
            'button_url' => $validated['button_url'] ?? $homeProfile->display_button_url,
            'badge_text' => $validated['badge_text'] ?? $homeProfile->display_badge_text,
            'badge_subtext' => $validated['badge_subtext'] ?? $homeProfile->display_badge_subtext,
        ];

        // Jika mengunggah berkas gambar baru
        if ($request->hasFile('image')) {
            $this->deleteOldFile($homeProfile->image_path);
            $data['image_path'] = $this->uploadFile($request->file('image'), 'content-images');
        }

        $homeProfile->update($data);

        return redirect()->route('admin.content-setup.home', ['tab' => 'profile'])
            ->with('success', 'Konten Section Profil Ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Kembalikan konten Profil Ekstrakurikuler ke nilai bawaan.
     */
    public function resetHomeProfile(): RedirectResponse
    {
        $homeProfile = ContentSectionImage::getSection('home_profile');
        $this->deleteOldFile($homeProfile->image_path);

        $homeProfile->update([
            'tagline' => 'Profil Ekstrakurikuler',
            'tagline_icon' => 'fa-solid fa-shield-heart',
            'headline' => 'Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi',
            'body_text' => 'Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA. Kami berkomitmen menanamkan nilai-nilai Tri Bakti PMR serta 7 Prinsip Dasar Palang Merah dan Bulan Sabit Merah Internasional.',
            'checklist_items' => [
                'Meningkatkan keterampilan pertolongan pertama (P3K) dan tanggap darurat bencana.',
                'Menyelenggarakan aksi donor darah sukarela secara berkala bersama UDD PMI.',
                'Membentuk jiwa kepemimpinan, kerelawanan, dan persahabatan antar siswa.',
            ],
            'button_text' => 'Baca Profil Selengkapnya',
            'button_url' => '/tentang-kami',
            'image_path' => '/mockups/02_tentang_kami.jpg',
            'badge_text' => 'Terakreditasi A',
            'badge_subtext' => 'Unit PMR Wira Teladan Kab. Bogor',
        ]);

        return redirect()->route('admin.content-setup.home', ['tab' => 'profile'])
            ->with('success', 'Konten Section Profil Ekstrakurikuler berhasil dikembalikan ke pengaturan bawaan.');
    }

    /**
     * Perbarui konten section Pilar Aksi Kemanusiaan (Program Unggulan) pada halaman Beranda.
     */
    public function updateHomePillars(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagline' => 'nullable|string|max:100',
            'headline' => 'required|string|max:255',
            'body_text' => 'required|string|max:2000',
            'cards' => 'required|array|min:1',
            'cards.*.icon' => 'nullable|string|max:100',
            'cards.*.title' => 'required|string|max:255',
            'cards.*.description' => 'required|string|max:1000',
            'cards.*.button_text' => 'nullable|string|max:100',
            'cards.*.button_url' => 'nullable|string|max:255',
        ], [
            'headline.required' => 'Judul utama section (H1/H2) wajib diisi.',
            'body_text.required' => 'Deskripsi ringkas pilar layanan wajib diisi.',
            'cards.*.title.required' => 'Nama program pada kartu wajib diisi.',
            'cards.*.description.required' => 'Deskripsi program pada kartu wajib diisi.',
        ]);

        $homePillars = ContentSectionImage::getSection('home_pillars');

        $cleanedCards = [];
        foreach ($validated['cards'] as $card) {
            if (! empty($card['title'])) {
                $cleanedCards[] = [
                    'icon' => ! empty($card['icon']) ? $card['icon'] : 'fa-solid fa-droplet',
                    'title' => $card['title'],
                    'description' => $card['description'] ?? '',
                    'button_text' => ! empty($card['button_text']) ? $card['button_text'] : 'Lihat Selengkapnya',
                    'button_url' => ! empty($card['button_url']) ? $card['button_url'] : '#',
                ];
            }
        }

        $homePillars->update([
            'tagline' => $validated['tagline'] ?? $homePillars->display_tagline,
            'headline' => $validated['headline'],
            'body_text' => $validated['body_text'],
            'cards_data' => ! empty($cleanedCards) ? $cleanedCards : $homePillars->display_programs,
        ]);

        return redirect()->route('admin.content-setup.home', ['tab' => 'pillars'])
            ->with('success', 'Konten Section Pilar Aksi Kemanusiaan berhasil diperbarui.');
    }

    /**
     * Kembalikan konten Pilar Aksi Kemanusiaan ke nilai bawaan.
     */
    public function resetHomePillars(): RedirectResponse
    {
        $homePillars = ContentSectionImage::getSection('home_pillars');

        $homePillars->update([
            'tagline' => 'Pilar Aksi Kemanusiaan',
            'headline' => 'Program Unggulan PMR Wira',
            'body_text' => 'Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.',
            'cards_data' => [
                [
                    'icon' => 'fa-solid fa-droplet',
                    'title' => 'Donor Darah Sukarela',
                    'description' => 'Edukasi pentingnya donor darah bagi remaja usia 17 tahun ke atas serta aksi donor darah berkala bekerja sama dengan PMI Kabupaten Bogor.',
                    'button_text' => 'Cek Stok & Jadwal',
                    'button_url' => '/donor-darah',
                ],
                [
                    'icon' => 'fa-solid fa-kit-medical',
                    'title' => 'Pertolongan Pertama (PP)',
                    'description' => 'Pelatihan intensif pembalutan, pembidaian, RJP/CPR, dan evakuasi korban untuk memastikan keselamatan di setiap kegiatan sekolah.',
                    'button_text' => 'Lihat Modul & Kegiatan',
                    'button_url' => '/kegiatan',
                ],
                [
                    'icon' => 'fa-solid fa-shield-halved',
                    'title' => 'Siaga Bencana & Sosial',
                    'description' => 'Simulasi evakuasi gempa, sekolah aman bencana, serta penggalangan donasi dan bakti sosial untuk warga terdampak musibah.',
                    'button_text' => 'Panduan Mitigasi',
                    'button_url' => '/artikel?kategori=kesiapsiagaan-bencana',
                ],
            ],
        ]);

        return redirect()->route('admin.content-setup.home', ['tab' => 'pillars'])
            ->with('success', 'Konten Section Pilar Aksi Kemanusiaan berhasil dikembalikan ke pengaturan bawaan.');
    }

    /**
     * Tampilkan formulir pengaturan konten halaman Tentang Kami.
     */
    public function about(Request $request): View
    {
        $activeTab = $request->query('tab', 'intro');
        if (! in_array($activeTab, ['intro', 'tri_bakti', 'principles', 'vision_mission'], true)) {
            $activeTab = 'intro';
        }

        $aboutIntro = ContentSectionImage::getSection('about_intro');
        $aboutTriBakti = ContentSectionImage::getSection('about_tri_bakti');
        $aboutPrinciples = ContentSectionImage::getSection('about_principles');
        $aboutVisionMission = ContentSectionImage::getSection('about_vision_mission');

        return view('admin.content-setup.about', compact('activeTab', 'aboutIntro', 'aboutTriBakti', 'aboutPrinciples', 'aboutVisionMission'));
    }

    /**
     * Perbarui konten section Mengenal PMR pada halaman Tentang Kami.
     */
    public function updateAboutIntro(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagline' => 'nullable|string|max:100',
            'tagline_icon' => 'nullable|string|max:100',
            'headline' => 'required|string|max:255',
            'quote_text' => 'required|string|max:2000',
            'body_text' => 'required|string|max:3000',
            'body_text_secondary' => 'nullable|string|max:3000',
            'badge_text' => 'nullable|string|max:100',
            'badge_subtext' => 'nullable|string|max:150',
            'badge_icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'headline.required' => 'Judul utama section (H1/H2) wajib diisi.',
            'quote_text.required' => 'Kutipan definisi resmi PMR wajib diisi.',
            'body_text.required' => 'Paragraf narasi profil wajib diisi.',
            'image.image' => 'Berkas yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran berkas gambar maksimal 5 MB.',
        ]);

        $aboutIntro = ContentSectionImage::getSection('about_intro');

        $data = [
            'tagline' => $validated['tagline'] ?? $aboutIntro->display_tagline,
            'tagline_icon' => $validated['tagline_icon'] ?? $aboutIntro->display_tagline_icon,
            'headline' => $validated['headline'],
            'quote_text' => $validated['quote_text'],
            'body_text' => $validated['body_text'],
            'body_text_secondary' => $validated['body_text_secondary'] ?? $aboutIntro->display_body_text_secondary,
            'badge_text' => $validated['badge_text'] ?? $aboutIntro->display_badge_text,
            'badge_subtext' => $validated['badge_subtext'] ?? $aboutIntro->display_badge_subtext,
            'badge_icon' => $validated['badge_icon'] ?? $aboutIntro->display_badge_icon,
        ];

        if ($request->hasFile('image')) {
            $this->deleteOldFile($aboutIntro->image_path);
            $data['image_path'] = $this->uploadFile($request->file('image'), 'content-images');
        }

        $aboutIntro->update($data);

        return redirect()->route('admin.content-setup.about', ['tab' => 'intro'])
            ->with('success', 'Konten Section Mengenal PMR berhasil diperbarui.');
    }

    /**
     * Kembalikan konten Mengenal PMR ke nilai bawaan.
     */
    public function resetAboutIntro(): RedirectResponse
    {
        $aboutIntro = ContentSectionImage::getSection('about_intro');
        $this->deleteOldFile($aboutIntro->image_path);

        $aboutIntro->update([
            'tagline' => 'MENGENAL PMR',
            'tagline_icon' => 'fa-solid fa-book-open-reader',
            'headline' => 'Profil PMR Wira SMAN 1 Ciawi',
            'quote_text' => 'Palang Merah Remaja atau PMR adalah suatu organisasi binaan dari Palang Merah Indonesia yang berpusat di sekolah-sekolah ataupun kelompok-kelompok masyarakat (sanggar, kelompok belajar, dll.) yang bertujuan membangun dan mengembangkan karakter Kepalangmerahan agar siap menjadi Relawan PMI pada masa depan.',
            'body_text' => 'Di SMA Negeri 1 Ciawi, unit PMR Wira berperan aktif sebagai garda kemanusiaan di lingkungan sekolah, siaga memberikan pertolongan pertama, mempromosikan perilaku hidup bersih dan sehat, serta membina kepedulian sosial melalui aksi donor darah sukarela dan tanggap darurat bencana.',
            'body_text_secondary' => 'Masa bakti Ragana Dwi Pantara 2026/2027 berkomitmen mengamalkan seluruh nilai kepalangmerahan ini secara profesional, modern, dan penuh dedikasi bagi seluruh warga sekolah dan masyarakat sekitar.',
            'badge_text' => 'PMR Tingkat Wira',
            'badge_subtext' => 'Binaan PMI Kab. Bogor & SMAN 1 Ciawi',
            'badge_icon' => 'fa-solid fa-shield-halved',
            'image_path' => '/mockups/02_tentang_kami.jpg',
        ]);

        return redirect()->route('admin.content-setup.about', ['tab' => 'intro'])
            ->with('success', 'Konten Section Mengenal PMR berhasil dikembalikan ke pengaturan bawaan.');
    }

    /**
     * Perbarui konten section Tri Bakti PMR pada halaman Tentang Kami.
     */
    public function updateAboutTriBakti(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagline' => 'nullable|string|max:100',
            'tagline_icon' => 'nullable|string|max:100',
            'headline' => 'required|string|max:255',
            'body_text' => 'required|string|max:2000',
            'cards' => 'required|array|min:1',
            'cards.*.sub_badge' => 'nullable|string|max:50',
            'cards.*.icon' => 'nullable|string|max:100',
            'cards.*.title' => 'required|string|max:255',
            'cards.*.description' => 'required|string|max:1000',
        ], [
            'headline.required' => 'Judul utama Tri Bakti wajib diisi.',
            'body_text.required' => 'Deskripsi ringkas Tri Bakti wajib diisi.',
            'cards.*.title.required' => 'Judul pilar Tri Bakti wajib diisi.',
            'cards.*.description.required' => 'Deskripsi pilar Tri Bakti wajib diisi.',
        ]);

        $aboutTriBakti = ContentSectionImage::getSection('about_tri_bakti');

        $cleanedCards = [];
        foreach ($validated['cards'] as $idx => $card) {
            if (! empty($card['title'])) {
                $cleanedCards[] = [
                    'sub_badge' => ! empty($card['sub_badge']) ? $card['sub_badge'] : ('Tri Bakti '.($idx + 1)),
                    'icon' => ! empty($card['icon']) ? $card['icon'] : 'fa-solid fa-hand-holding-heart',
                    'title' => $card['title'],
                    'description' => $card['description'] ?? '',
                ];
            }
        }

        $aboutTriBakti->update([
            'tagline' => $validated['tagline'] ?? $aboutTriBakti->display_tagline,
            'tagline_icon' => $validated['tagline_icon'] ?? $aboutTriBakti->display_tagline_icon,
            'headline' => $validated['headline'],
            'body_text' => $validated['body_text'],
            'cards_data' => ! empty($cleanedCards) ? $cleanedCards : $aboutTriBakti->display_tri_bakti,
        ]);

        return redirect()->route('admin.content-setup.about', ['tab' => 'tri_bakti'])
            ->with('success', 'Konten Section Tri Bakti PMR berhasil diperbarui.');
    }

    /**
     * Kembalikan konten Tri Bakti PMR ke nilai bawaan.
     */
    public function resetAboutTriBakti(): RedirectResponse
    {
        $aboutTriBakti = ContentSectionImage::getSection('about_tri_bakti');

        $aboutTriBakti->update([
            'tagline' => 'Tri Bakti PMR',
            'tagline_icon' => 'fa-solid fa-hands-holding-child',
            'headline' => 'Tri Bakti Palang Merah Remaja',
            'body_text' => 'Tiga pilar kewajiban utama yang senantiasa diamalkan oleh seluruh anggota PMR Wira SMAN 1 Ciawi dalam kehidupan sehari-hari:',
            'cards_data' => [
                [
                    'sub_badge' => 'Tri Bakti 1',
                    'icon' => 'fa-solid fa-hand-holding-heart',
                    'title' => 'Berbakti kepada Masyarakat',
                    'description' => 'Turut serta dalam aksi kemanusiaan di masyarakat, seperti donor darah sukarela, bakti sosial peduli sesama, dan bantuan tanggap darurat saat bencana melanda.',
                ],
                [
                    'sub_badge' => 'Tri Bakti 2',
                    'icon' => 'fa-solid fa-kit-medical',
                    'title' => 'Mempertinggi Keterampilan dan Memelihara Kebersihan dan Kesehatan',
                    'description' => 'Mengasah keahlian Pertolongan Pertama (P3K), piket medis UKS, simulasi mitigasi bencana, serta membudayakan Pola Hidup Bersih dan Sehat (PHBS) di sekolah.',
                ],
                [
                    'sub_badge' => 'Tri Bakti 3',
                    'icon' => 'fa-solid fa-earth-asia',
                    'title' => 'Mempererat Persahabatan Nasional dan Internasional',
                    'description' => 'Menjalin persaudaraan relawan muda tanpa memandang perbedaan melalui temu akrab, JUMBARA (Jumpa Bakti Gembira), dan pertukaran informasi kepalangmerahan.',
                ],
            ],
        ]);

        return redirect()->route('admin.content-setup.about', ['tab' => 'tri_bakti'])
            ->with('success', 'Konten Section Tri Bakti PMR berhasil dikembalikan ke pengaturan bawaan.');
    }

    /**
     * Perbarui konten section 7 Dasar Prinsip Palang Merah pada halaman Tentang Kami.
     */
    public function updateAboutPrinciples(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagline' => 'nullable|string|max:100',
            'headline' => 'required|string|max:255',
            'body_text' => 'required|string|max:2000',
            'cards' => 'required|array|min:1',
            'cards.*.icon' => 'nullable|string|max:100',
            'cards.*.title' => 'required|string|max:100',
            'cards.*.subtitle' => 'nullable|string|max:100',
        ], [
            'headline.required' => 'Judul utama prinsip wajib diisi.',
            'body_text.required' => 'Deskripsi ringkas prinsip wajib diisi.',
            'cards.*.title.required' => 'Nama prinsip pada kartu wajib diisi.',
        ]);

        $aboutPrinciples = ContentSectionImage::getSection('about_principles');

        $cleanedCards = [];
        foreach ($validated['cards'] as $card) {
            if (! empty($card['title'])) {
                $cleanedCards[] = [
                    'icon' => ! empty($card['icon']) ? $card['icon'] : 'fa-solid fa-heart',
                    'title' => strtoupper($card['title']),
                    'subtitle' => $card['subtitle'] ?? '',
                ];
            }
        }

        $aboutPrinciples->update([
            'tagline' => $validated['tagline'] ?? $aboutPrinciples->display_tagline,
            'headline' => $validated['headline'],
            'body_text' => $validated['body_text'],
            'cards_data' => ! empty($cleanedCards) ? $cleanedCards : $aboutPrinciples->display_principles,
        ]);

        return redirect()->route('admin.content-setup.about', ['tab' => 'principles'])
            ->with('success', 'Konten Section 7 Dasar Prinsip Palang Merah berhasil diperbarui.');
    }

    /**
     * Kembalikan konten 7 Dasar Prinsip ke nilai bawaan.
     */
    public function resetAboutPrinciples(): RedirectResponse
    {
        $aboutPrinciples = ContentSectionImage::getSection('about_principles');

        $aboutPrinciples->update([
            'tagline' => 'Landasan Moral Universal',
            'headline' => '7 Dasar Prinsip Palang Merah',
            'body_text' => 'Tujuh prinsip fundamental yang menjadi pedoman teguh setiap langkah dan tindakan relawan PMR Wira di seluruh dunia:',
            'cards_data' => [
                ['icon' => 'fa-solid fa-hand-holding-heart', 'title' => 'KEMANUSIAAN', 'subtitle' => 'Humanity'],
                ['icon' => 'fa-solid fa-scale-balanced', 'title' => 'KESAMAAN', 'subtitle' => 'Impartiality'],
                ['icon' => 'fa-solid fa-dove', 'title' => 'KENETRALAN', 'subtitle' => 'Neutrality'],
                ['icon' => 'fa-solid fa-flag', 'title' => 'KEMANDIRIAN', 'subtitle' => 'Independence'],
                ['icon' => 'fa-solid fa-heart', 'title' => 'KESUKARELAAN', 'subtitle' => 'Voluntary Service'],
                ['icon' => 'fa-solid fa-people-group', 'title' => 'KESATUAN', 'subtitle' => 'UNITY'],
                ['icon' => 'fa-solid fa-earth-americas', 'title' => 'KESEMESTAAN', 'subtitle' => 'Universality'],
            ],
        ]);

        return redirect()->route('admin.content-setup.about', ['tab' => 'principles'])
            ->with('success', 'Konten Section 7 Dasar Prinsip berhasil dikembalikan ke pengaturan bawaan.');
    }

    /**
     * Perbarui konten section Visi & Misi Kami pada halaman Tentang Kami.
     */
    public function updateAboutVisionMission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagline' => 'nullable|string|max:100',
            'headline' => 'required|string|max:255',
            'vision_text' => 'required|string|max:2000',
            'missions' => 'required|array|min:1',
            'missions.*' => 'nullable|string|max:500',
        ], [
            'headline.required' => 'Judul utama Visi & Misi wajib diisi.',
            'vision_text.required' => 'Teks pernyataan Visi Organisasi wajib diisi.',
            'missions.required' => 'Minimal satu poin Misi Organisasi wajib diisi.',
        ]);

        $aboutVisionMission = ContentSectionImage::getSection('about_vision_mission');

        $filteredMissions = [];
        if (! empty($validated['missions']) && is_array($validated['missions'])) {
            $filteredMissions = array_values(array_filter($validated['missions'], fn ($m) => filled(trim((string) $m))));
        }

        $aboutVisionMission->update([
            'tagline' => $validated['tagline'] ?? $aboutVisionMission->display_tagline,
            'headline' => $validated['headline'],
            'vision_text' => $validated['vision_text'],
            'mission_items' => ! empty($filteredMissions) ? $filteredMissions : $aboutVisionMission->display_mission_items,
        ]);

        return redirect()->route('admin.content-setup.about', ['tab' => 'vision_mission'])
            ->with('success', 'Konten Section Visi & Misi Organisasi berhasil diperbarui.');
    }

    /**
     * Kembalikan konten Visi & Misi ke nilai bawaan.
     */
    public function resetAboutVisionMission(): RedirectResponse
    {
        $aboutVisionMission = ContentSectionImage::getSection('about_vision_mission');

        $aboutVisionMission->update([
            'tagline' => 'Arah Gerak Organisasi',
            'headline' => 'Visi & Misi Kami',
            'vision_text' => 'Terwujudnya PMR Wira SMAN 1 Ciawi sebagai unit relawan muda yang berkarakter kemanusiaan tangguh, sigap, berintegritas, mandiri, dan berprestasi berlandaskan prinsip-prinsip dasar Palang Merah Internasional.',
            'mission_items' => [
                'Menanamkan pemahaman dan pengamalan 7 Prinsip Dasar Palang Merah dan Tri Bakti PMR.',
                'Meningkatkan kapasitas anggota dalam keterampilan pertolongan pertama, sanitasi kesehatan, dan penanggulangan bencana.',
                'Aktif menyelenggarakan program donor darah sukarela dan bakti sosial bagi masyarakat.',
                'Menjalin sinergi erat dengan pembina sekolah, PMI Kabupaten Bogor, dan unit PMR lainnya.',
            ],
        ]);

        return redirect()->route('admin.content-setup.about', ['tab' => 'vision_mission'])
            ->with('success', 'Konten Section Visi & Misi Organisasi berhasil dikembalikan ke pengaturan bawaan.');
    }

    /**
     * Helper upload berkas ke storage publik.
     */
    private function uploadFile($file, string $targetFolder): string
    {
        $filename = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();

        if (! file_exists(storage_path("app/public/{$targetFolder}"))) {
            File::ensureDirectoryExists(storage_path("app/public/{$targetFolder}"), 0755, true);
        }
        if (! file_exists(public_path("storage/{$targetFolder}"))) {
            File::ensureDirectoryExists(public_path("storage/{$targetFolder}"), 0755, true);
        }

        $file->storeAs($targetFolder, $filename, 'public');

        try {
            $src = storage_path("app/public/{$targetFolder}/{$filename}");
            $dst = public_path("storage/{$targetFolder}/{$filename}");
            if (file_exists($src) && ! file_exists($dst)) {
                @copy($src, $dst);
            }
        } catch (\Throwable $e) {
            // Abaikan kesalahan penyalinan sekunder
        }

        return "{$targetFolder}/{$filename}";
    }

    /**
     * Hapus berkas lama jika tersimpan di storage lokal.
     */
    private function deleteOldFile(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/mockups')) {
            return;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $path), '/');

        try {
            $storageFile = storage_path('app/public/'.$cleanPath);
            $publicFile = public_path('storage/'.$cleanPath);

            if (file_exists($storageFile)) {
                @unlink($storageFile);
            }
            if (file_exists($publicFile)) {
                @unlink($publicFile);
            }
        } catch (\Throwable $e) {
            // Abaikan
        }
    }
}
