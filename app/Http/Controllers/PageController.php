<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Activity;
use App\Models\BloodStock;
use App\Models\MemberRegistration;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $heroSlides = HeroSlide::where('is_active', true)->orderBy('order_position')->get();

        $featuredArticle = Article::with('category')->where('status', 'published')->where('is_featured', true)->latest('published_at')->first();
        if (!$featuredArticle) {
            $featuredArticle = Article::with('category')->where('status', 'published')->latest('published_at')->first();
        }

        $latestArticles = Article::with('category')
            ->where('status', 'published')
            ->when($featuredArticle, fn($q) => $q->where('id', '!=', $featuredArticle->id))
            ->latest('published_at')
            ->take(3)
            ->get();

        $activities = Activity::latest('event_date')->take(6)->get();
        $bloodStocks = BloodStock::all();

        return view('pages.home', compact('heroSlides', 'featuredArticle', 'latestArticles', 'activities', 'bloodStocks'));
    }

    public function tentangKami()
    {
        $organizationSetting = \App\Models\OrganizationSetting::firstOrCreate(
            ['id' => 1],
            [
                'badge' => 'Bagan Kepengurusan',
                'title' => 'Struktur Organisasi 2026/2027',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara — Sinergi kepemimpinan dan dedikasi relawan siswa.',
            ]
        );

        $organizationMembers = \App\Models\OrganizationMember::with('member')
            ->where('is_active', true)
            ->orderBy('level')
            ->orderBy('order_position')
            ->get()
            ->groupBy('level');

        return view('pages.tentang-kami', compact('organizationSetting', 'organizationMembers'));
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
        $galleries = \App\Models\Gallery::where('is_active', true)->orderBy('created_at', 'desc')->get();
        return view('pages.galeri', compact('galleries'));
    }

    public function donorDarah()
    {
        $bloodStocks = BloodStock::all();
        $activeEvent = \App\Models\BloodDonationEvent::where('is_active', true)->first();

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

        \App\Models\BloodDonorRegistration::create($data);

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
