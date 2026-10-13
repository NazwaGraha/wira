<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $status = $request->query('status');

        $query = Article::with('category');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status && in_array($status, ['published', 'draft'])) {
            $query->where('status', $status);
        }

        $articles = $query->latest()->paginate(10)->withQueryString();

        $totalCount = Article::count();
        $publishedCount = Article::where('status', 'published')->count();
        $draftCount = Article::where('status', 'draft')->count();
        $totalViews = Article::sum('views_count');

        return view('admin.articles.index', compact('articles', 'search', 'status', 'totalCount', 'publishedCount', 'draftCount', 'totalViews'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string|max:1000',
            'body' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg,bmp,avif,heic,heif,tiff|max:15360',
            'thumbnail' => 'nullable|string|max:500',
            'author_name' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:500',
            'focus_keyword' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:1000',
            'og_image_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg,bmp,avif,heic,heif,tiff|max:15360',
            'og_image' => 'nullable|string|max:500',
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'category_id.required' => 'Kategori artikel wajib dipilih.',
            'body.required' => 'Isi artikel tidak boleh kosong.',
            'image.mimes' => 'Format file gambar harus berupa JPG, JPEG, PNG, WEBP, GIF, SVG, BMP, AVIF, atau HEIC.',
            'image.max' => 'Ukuran gambar maksimal adalah 15 MB.',
        ]);

        // Upload file gambar utama jika diunggah dari komputer/ponsel
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = time().'_'.Str::random(10).'.'.$ext;
            $destinationPath = public_path('uploads/articles');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $validated['thumbnail'] = '/uploads/articles/'.$filename;
        } elseif (empty($validated['thumbnail'])) {
            $validated['thumbnail'] = '/mockups/07_artikel.jpg';
        }

        // Upload OG image opsional jika diunggah
        if ($request->hasFile('og_image_file')) {
            $ogFile = $request->file('og_image_file');
            $ext = strtolower($ogFile->getClientOriginalExtension());
            $filename = 'og_'.time().'_'.Str::random(10).'.'.$ext;
            $destinationPath = public_path('uploads/articles');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $ogFile->move($destinationPath, $filename);
            $validated['og_image'] = '/uploads/articles/'.$filename;
        }

        // Slug generator yang unik
        $baseSlug = Str::slug($request->filled('slug') ? $request->input('slug') : $validated['title']);
        if (empty($baseSlug)) {
            $baseSlug = 'artikel-'.Str::random(6);
        }
        $slug = $baseSlug;
        $counter = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }
        $validated['slug'] = $slug;

        // Auto excerpt jika kosong
        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['body']), 180);
        }

        $validated['user_id'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured');
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        unset($validated['image'], $validated['og_image_file']);

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil disimpan dan dipublikasikan!');
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        $categories = Category::all();

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string|max:1000',
            'body' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg,bmp,avif,heic,heif,tiff|max:15360',
            'thumbnail' => 'nullable|string|max:500',
            'author_name' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:500',
            'focus_keyword' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:1000',
            'og_image_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg,bmp,avif,heic,heif,tiff|max:15360',
            'og_image' => 'nullable|string|max:500',
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'category_id.required' => 'Kategori artikel wajib dipilih.',
            'body.required' => 'Isi artikel tidak boleh kosong.',
            'image.mimes' => 'Format file gambar harus berupa JPG, JPEG, PNG, WEBP, GIF, SVG, BMP, AVIF, atau HEIC.',
            'image.max' => 'Ukuran gambar maksimal adalah 15 MB.',
        ]);

        // Upload file gambar utama jika diunggah baru
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = time().'_'.Str::random(10).'.'.$ext;
            $destinationPath = public_path('uploads/articles');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $validated['thumbnail'] = '/uploads/articles/'.$filename;
        } elseif (empty($validated['thumbnail'])) {
            $validated['thumbnail'] = $article->thumbnail ?: '/mockups/07_artikel.jpg';
        }

        // Upload OG image baru jika diunggah
        if ($request->hasFile('og_image_file')) {
            $ogFile = $request->file('og_image_file');
            $ext = strtolower($ogFile->getClientOriginalExtension());
            $filename = 'og_'.time().'_'.Str::random(10).'.'.$ext;
            $destinationPath = public_path('uploads/articles');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $ogFile->move($destinationPath, $filename);
            $validated['og_image'] = '/uploads/articles/'.$filename;
        }

        // Slug update jika diedit
        if ($request->filled('slug') && $request->input('slug') !== $article->slug) {
            $baseSlug = Str::slug($request->input('slug'));
            $slug = $baseSlug;
            $counter = 1;
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $baseSlug.'-'.$counter++;
            }
            $validated['slug'] = $slug;
        }

        // Auto excerpt jika kosong
        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['body']), 180);
        }

        $validated['is_featured'] = $request->has('is_featured');
        if ($validated['status'] === 'published' && ! $article->published_at) {
            $validated['published_at'] = now();
        }

        unset($validated['image'], $validated['og_image_file']);

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Artikel telah berhasil dihapus.');
    }
}
