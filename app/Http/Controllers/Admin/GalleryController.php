<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::with('galleryCategory')->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.gallery.index', compact('galleries'));
    }

    public function create()
    {
        $categories = GalleryCategory::orderBy('name')->get();

        return view('admin.gallery.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:photo,video',
            'title' => 'required|string|max:255',
            'gallery_category_id' => 'nullable|exists:gallery_categories,id',
            'category' => 'nullable|string|max:255',
            'image_files' => 'required|array',
            'image_files.*' => 'image|max:20480', // allows up to 20MB per file, any image format
            'video_url' => 'nullable|string|max:500',
            'duration' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $categoryName = null;
        if ($request->filled('gallery_category_id')) {
            $cat = GalleryCategory::find($request->gallery_category_id);
            $categoryName = $cat?->name;
        } elseif ($request->filled('category')) {
            $categoryName = $request->category;
        }

        $files = $request->file('image_files');

        foreach ($files as $file) {
            $imagePath = $file->store('galleries', 'public');

            Gallery::create([
                'type' => $request->type,
                'title' => $request->title,
                'category' => $categoryName,
                'gallery_category_id' => $request->gallery_category_id,
                'image_path' => $imagePath,
                'video_url' => $request->video_url,
                'duration' => $request->duration,
                'is_active' => $request->has('is_active'),
            ]);
        }

        return redirect()->route('admin.gallery.index')->with('success', count($files).' Media berhasil ditambahkan ke galeri.');
    }

    public function edit(Gallery $gallery)
    {
        $categories = GalleryCategory::orderBy('name')->get();

        return view('admin.gallery.edit', compact('gallery', 'categories'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'type' => 'required|in:photo,video',
            'title' => 'required|string|max:255',
            'gallery_category_id' => 'nullable|exists:gallery_categories,id',
            'category' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|max:20480',
            'video_url' => 'nullable|string|max:500',
            'duration' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $categoryName = null;
        if ($request->filled('gallery_category_id')) {
            $cat = GalleryCategory::find($request->gallery_category_id);
            $categoryName = $cat?->name;
        } elseif ($request->filled('category')) {
            $categoryName = $request->category;
        }

        $data = [
            'type' => $request->type,
            'title' => $request->title,
            'category' => $categoryName,
            'gallery_category_id' => $request->gallery_category_id,
            'video_url' => $request->video_url,
            'duration' => $request->duration,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image_file')) {
            if (Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            $data['image_path'] = $request->file('image_file')->store('galleries', 'public');
        }

        $gallery->update($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Media galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        if (Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        $gallery->delete();

        return back()->with('success', 'Media galeri dihapus.');
    }
}
