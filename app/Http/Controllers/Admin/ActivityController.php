<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $category = $request->query('kategori');

        $query = Activity::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category && $category !== 'Semua') {
            $query->where('category', $category);
        }

        $activities = $query->latest('event_date')->paginate(10)->withQueryString();
        $totalActivities = Activity::count();
        $featuredCount = Activity::where('is_featured', true)->count();
        $upcomingCount = Activity::where('event_date', '>=', now()->toDateString())->count();

        return view('admin.activities.index', compact(
            'activities',
            'search',
            'category',
            'totalActivities',
            'featuredCount',
            'upcomingCount'
        ));
    }

    public function create()
    {
        $categories = [
            'Pertolongan Pertama',
            'Donor Darah',
            'Kesiapsiagaan Bencana',
            'Pendidikan',
            'Bakti Sosial',
            'Latihan Rutin',
        ];

        return view('admin.activities.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_url' => 'nullable|string|max:500',
            'is_featured' => 'nullable|boolean',
        ]);

        // Generate unique slug
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Activity::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . (++$counter);
        }
        $validated['slug'] = $slug;

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/activities'), $filename);
            $validated['image'] = '/uploads/activities/' . $filename;
        } elseif (!empty($validated['image_url'])) {
            $validated['image'] = $validated['image_url'];
        } else {
            $validated['image'] = '/mockups/03_kegiatan.jpg';
        }

        $validated['is_featured'] = $request->has('is_featured');

        Activity::create($validated);

        return redirect()->route('admin.activities.index')->with('success', "Kegiatan '{$validated['title']}' berhasil disimpan ke database!");
    }

    public function edit(Activity $kegiatan)
    {
        $activity = $kegiatan;
        $categories = [
            'Pertolongan Pertama',
            'Donor Darah',
            'Kesiapsiagaan Bencana',
            'Pendidikan',
            'Bakti Sosial',
            'Latihan Rutin',
        ];

        return view('admin.activities.edit', compact('activity', 'categories'));
    }

    public function update(Request $request, Activity $kegiatan)
    {
        $activity = $kegiatan;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_url' => 'nullable|string|max:500',
            'is_featured' => 'nullable|boolean',
        ]);

        // Update slug if title changed
        if ($activity->title !== $validated['title']) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Activity::where('slug', $slug)->where('id', '!=', $activity->id)->exists()) {
                $slug = $baseSlug . '-' . (++$counter);
            }
            $validated['slug'] = $slug;
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/activities'), $filename);
            $validated['image'] = '/uploads/activities/' . $filename;
        } elseif (!empty($validated['image_url'])) {
            $validated['image'] = $validated['image_url'];
        }

        $validated['is_featured'] = $request->has('is_featured');

        $activity->update($validated);

        return redirect()->route('admin.activities.index')->with('success', "Kegiatan '{$activity->title}' berhasil diperbarui!");
    }

    public function destroy(Activity $kegiatan)
    {
        $title = $kegiatan->title;
        $kegiatan->delete();

        return redirect()->route('admin.activities.index')->with('success', "Kegiatan '{$title}' berhasil dihapus dari database.");
    }
}
