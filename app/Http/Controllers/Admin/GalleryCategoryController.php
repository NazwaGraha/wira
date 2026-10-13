<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryCategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori galeri.
     */
    public function index()
    {
        $categories = GalleryCategory::withCount('galleries')
            ->orderBy('name')
            ->get();

        return view('admin.gallery_categories.index', compact('categories'));
    }

    /**
     * Simpan kategori galeri baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:gallery_categories,name',
            'slug' => 'nullable|string|max:100|unique:gallery_categories,slug',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama kategori galeri wajib diisi.',
            'name.unique' => 'Kategori galeri dengan nama tersebut sudah ada.',
            'slug.unique' => 'Slug kategori galeri sudah digunakan.',
        ]);

        $baseSlug = Str::slug($request->filled('slug') ? $request->input('slug') : $validated['name']);
        if (empty($baseSlug)) {
            $baseSlug = 'kategori-'.Str::random(5);
        }

        $slug = $baseSlug;
        $counter = 1;
        while (GalleryCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }
        $validated['slug'] = $slug;

        GalleryCategory::create($validated);

        return redirect()->route('admin.gallery-categories.index')->with('success', "Kategori galeri '{$validated['name']}' berhasil ditambahkan ke database!");
    }

    /**
     * Perbarui data kategori galeri.
     */
    public function update(Request $request, GalleryCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:gallery_categories,name,'.$category->id,
            'slug' => 'nullable|string|max:100|unique:gallery_categories,slug,'.$category->id,
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama kategori galeri wajib diisi.',
            'name.unique' => 'Kategori galeri dengan nama tersebut sudah ada.',
            'slug.unique' => 'Slug kategori galeri sudah digunakan.',
        ]);

        if ($request->filled('slug') && $request->input('slug') !== $category->slug) {
            $baseSlug = Str::slug($request->input('slug'));
            $slug = $baseSlug;
            $counter = 1;
            while (GalleryCategory::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $baseSlug.'-'.$counter++;
            }
            $validated['slug'] = $slug;
        }

        $category->update($validated);

        return redirect()->route('admin.gallery-categories.index')->with('success', "Kategori galeri '{$category->name}' berhasil diperbarui!");
    }

    /**
     * Hapus kategori galeri dari database.
     */
    public function destroy(GalleryCategory $category)
    {
        $name = $category->name;
        $mediaCount = $category->galleries()->count();

        $category->delete();

        $msg = "Kategori galeri '{$name}' telah berhasil dihapus.";
        if ($mediaCount > 0) {
            $msg .= " ({$mediaCount} media galeri terkait otomatis diatur tanpa kategori).";
        }

        return redirect()->route('admin.gallery-categories.index')->with('success', $msg);
    }

    /**
     * AJAX endpoint untuk tambah cepat kategori langsung dari modal form upload/edit media galeri.
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:gallery_categories,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama kategori galeri wajib diisi.',
            'name.unique' => 'Kategori galeri dengan nama ini sudah terdaftar.',
        ]);

        $baseSlug = Str::slug($validated['name']);
        if (empty($baseSlug)) {
            $baseSlug = 'kategori-'.Str::random(5);
        }

        $slug = $baseSlug;
        $counter = 1;
        while (GalleryCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }
        $validated['slug'] = $slug;

        $category = GalleryCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Kategori galeri '{$category->name}' berhasil ditambahkan!",
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
        ]);
    }
}
