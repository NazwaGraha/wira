<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Advisor;
use App\Models\Article;
use App\Models\BloodDonationEvent;
use App\Models\BloodDonorRegistration;
use App\Models\BloodStock;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\HeroSlide;
use App\Models\Member;
use App\Models\MemberRegistration;
use App\Models\OrganizationGuideline;
use App\Models\OrganizationMember;
use App\Models\OrganizationSetting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $heroSlides = HeroSlide::where('is_active', true)->orderBy('order_position')->get();

        $featuredArticle = Article::with('category')->where('status', 'published')->where('is_featured', true)->latest('published_at')->first();
        if (! $featuredArticle) {
            $featuredArticle = Article::with('category')->where('status', 'published')->latest('published_at')->first();
        }

        $latestArticles = Article::with('category')
            ->where('status', 'published')
            ->when($featuredArticle, fn ($q) => $q->where('id', '!=', $featuredArticle->id))
            ->latest('published_at')
            ->take(3)
            ->get();

        $activities = Activity::latest('event_date')->take(6)->get();
        $bloodStocks = BloodStock::all();

        return view('pages.home', compact('heroSlides', 'featuredArticle', 'latestArticles', 'activities', 'bloodStocks'));
    }

    public function tentangKami()
    {
        $organizationSetting = OrganizationSetting::firstOrCreate(
            ['id' => 1],
            [
                'badge' => 'Bagan Kepengurusan',
                'title' => 'Struktur Organisasi 2026/2027',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara — Sinergi kepemimpinan dan dedikasi relawan siswa.',
            ]
        );

        $rawMembers = OrganizationMember::with('member')
            ->where('is_active', true)
            ->orderBy('level')
            ->orderBy('order_position')
            ->get();

        // 1. Pastikan Level 1 (Pembina PMR) selalu hadir di atas Ketua Umum:
        $hasLevel1 = $rawMembers->contains(fn ($m) => (int) $m->level === 1);
        if (! $hasLevel1) {
            try {
                $activeAdvisors = Advisor::where('is_active', true)->orderBy('order_position')->get();
                if ($activeAdvisors->isEmpty()) {
                    $activeAdvisors = collect([
                        new Advisor([
                            'name' => 'Drs. H. Mulyadi, M.Pd',
                            'position' => 'Pembina PMR',
                            'title_degree' => 'M.Pd',
                            'order_position' => 1,
                            'is_active' => true,
                        ]),
                    ]);
                }

                foreach ($activeAdvisors as $idx => $adv) {
                    $orgAdv = new OrganizationMember([
                        'name' => $adv->name,
                        'position' => $adv->position ?: 'Pembina PMR',
                        'subtitle' => $adv->title_degree ? ('Pembina • '.$adv->title_degree) : 'Guru Pembina PMR',
                        'level' => 1,
                        'order_position' => $adv->order_position ?: ($idx + 1),
                        'photo' => $adv->photo,
                        'icon' => 'fa-solid fa-shield-halved',
                        'is_active' => true,
                    ]);
                    $rawMembers->push($orgAdv);

                    // Buatkan juga permanen di DB jika belum ada
                    OrganizationMember::firstOrCreate(
                        ['level' => 1, 'name' => $adv->name],
                        [
                            'position' => $adv->position ?: 'Pembina PMR',
                            'subtitle' => $adv->title_degree ? ('Pembina • '.$adv->title_degree) : 'Guru Pembina PMR',
                            'order_position' => $adv->order_position ?: ($idx + 1),
                            'photo' => $adv->photo,
                            'icon' => 'fa-solid fa-shield-halved',
                            'is_active' => true,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        // Saring pengurus di bagan: hanya tampil jika pejabat/ketua aktif (atau level 1 / Pembina)
        $organizationMembers = $rawMembers->filter(function ($m) {
            return $m->isLeaderActive();
        })->map(function ($m) {
            // Saring staf yang aktif
            $m->staff_members = $m->active_staff_members;

            return $m;
        })->groupBy('level');

        $allMembers = Member::all()->keyBy('name');
        $allAdvisors = Advisor::all()->keyBy('name');
        $guideline = OrganizationGuideline::current();

        return view('pages.tentang-kami', compact('organizationSetting', 'organizationMembers', 'allMembers', 'allAdvisors', 'guideline'));
    }

    public function pedomanOrganisasi()
    {
        $guideline = OrganizationGuideline::current();
        $strategies = $guideline->strategies()->orderBy('order_number')->get();
        $chapters = $guideline->chapters()->orderBy('order_number')->get();

        return view('pages.pedoman-organisasi', compact('guideline', 'strategies', 'chapters'));
    }

    public function kegiatan(Request $request)
    {
        $category = $request->query('kategori');
        $query = Activity::query();

        if ($category && $category !== 'Semua') {
            $query->where('category', $category);
        }

        $featuredActivity = Activity::where('is_featured', true)->latest('event_date')->first();
        $activities = $query->latest('event_date')->paginate(9);

        return view('pages.kegiatan', compact('activities', 'featuredActivity', 'category'));
    }

    public function kegiatanShow($slug)
    {
        $activity = Activity::where('slug', $slug)->firstOrFail();

        // Find other recent activities to show as related
        $relatedActivities = Activity::where('id', '!=', $activity->id)
            ->latest('event_date')
            ->take(3)
            ->get();

        return view('pages.kegiatan-show', compact('activity', 'relatedActivities'));
    }

    public function galeri()
    {
        $galleries = Gallery::with('galleryCategory')->where('is_active', true)->orderBy('created_at', 'desc')->get();
        $categories = GalleryCategory::orderBy('name')->get();

        return view('pages.galeri', compact('galleries', 'categories'));
    }

    public function donorDarah()
    {
        $bloodStocks = BloodStock::all();
        $activeEvent = BloodDonationEvent::where('is_active', true)->first();

        return view('pages.donor-darah', compact('bloodStocks', 'activeEvent'));
    }

    public function storeDonorRegistration(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'blood_type' => 'required|string|max:5',
            'rhesus' => 'nullable|string|max:2',
            'last_donation_date' => 'nullable|date',
            'blood_donation_event_id' => 'nullable|exists:blood_donation_events,id',
        ]);

        BloodDonorRegistration::create($data);

        return back()->with('success', 'Pendaftaran berhasil dikirim! Silakan datang sesuai jadwal.');
    }

    public function kontak()
    {
        return view('pages.kontak');
    }

    public function storeRegistration(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'class_grade' => 'required|string|max:50',
            'nisn' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'interest_field' => 'required|string',
            'motivation' => 'required|string',
        ]);

        MemberRegistration::create($validated);

        return redirect()->route('kontak')->with('success', 'Selamat! Formulir pendaftaran relawan PMR Wira SMAN 1 Ciawi telah berhasil dikirim. Pengurus akan menghubungi Anda via WhatsApp.');
    }
}
