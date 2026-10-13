<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GbhoStrategy;
use App\Models\OrganizationGuideline;
use App\Models\PpuoChapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrganizationGuidelineController extends Controller
{
    /**
     * Display the guidelines management screen.
     */
    public function index(): View
    {
        try {
            if (! Schema::hasTable('organization_guidelines')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $guideline = OrganizationGuideline::current();
        $strategies = $guideline->exists ? $guideline->strategies()->orderBy('order_number')->get() : ($guideline->strategies ?? collect());
        $chapters = $guideline->exists ? $guideline->chapters()->orderBy('order_number')->get() : ($guideline->chapters ?? collect());

        return view('admin.organization_guidelines.index', compact('guideline', 'strategies', 'chapters'));
    }

    /**
     * Update GBHO general policy & dates.
     */
    public function updateGbho(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'period' => 'required|string|max:50',
            'established_date' => 'required|string|max:100',
            'established_place' => 'required|string|max:100',
            'gbho_policy_heading' => 'required|string|max:150',
            'gbho_policy_text' => 'required|string',
        ]);

        $guideline = OrganizationGuideline::current();
        $guideline->update($validated);

        return redirect()->route('admin.organization-guidelines.index', ['tab' => 'gbho'])
            ->with('success', 'Arah kebijakan dan informasi umum GBHO berhasil diperbarui!');
    }

    /**
     * Store a new strategy item for GBHO.
     */
    public function storeStrategy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'order_number' => 'nullable|integer|min:1',
        ]);

        $guideline = OrganizationGuideline::current();
        $nextOrder = $validated['order_number'] ?? (($guideline->strategies()->max('order_number') ?? 0) + 1);

        $guideline->strategies()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'icon' => $validated['icon'] ?: 'fa-solid fa-check',
            'order_number' => $nextOrder,
        ]);

        return redirect()->route('admin.organization-guidelines.index', ['tab' => 'gbho'])
            ->with('success', 'Butir strategi GBHO berhasil ditambahkan!');
    }

    /**
     * Update an existing strategy item.
     */
    public function updateStrategy(Request $request, GbhoStrategy $strategy): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'order_number' => 'required|integer|min:1',
        ]);

        $strategy->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'icon' => $validated['icon'] ?: 'fa-solid fa-check',
            'order_number' => $validated['order_number'],
        ]);

        return redirect()->route('admin.organization-guidelines.index', ['tab' => 'gbho'])
            ->with('success', 'Butir strategi GBHO berhasil diperbarui!');
    }

    /**
     * Delete a strategy item.
     */
    public function destroyStrategy(GbhoStrategy $strategy): RedirectResponse
    {
        $strategy->delete();

        return redirect()->route('admin.organization-guidelines.index', ['tab' => 'gbho'])
            ->with('success', 'Butir strategi GBHO berhasil dihapus.');
    }

    /**
     * Store a new PPUO chapter.
     */
    public function storeChapter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'chapter_number' => 'required|string|max:30',
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'badge' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'order_number' => 'nullable|integer|min:1',
        ]);

        $guideline = OrganizationGuideline::current();
        $nextOrder = $validated['order_number'] ?? (($guideline->chapters()->max('order_number') ?? 0) + 1);

        $guideline->chapters()->create([
            'chapter_number' => $validated['chapter_number'],
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'content' => $validated['content'] ?? null,
            'order_number' => $nextOrder,
        ]);

        return redirect()->route('admin.organization-guidelines.index', ['tab' => 'ppuo'])
            ->with('success', 'Bab regulasi PPUO baru berhasil ditambahkan!');
    }

    /**
     * Update an existing PPUO chapter.
     */
    public function updateChapter(Request $request, PpuoChapter $chapter): RedirectResponse
    {
        $validated = $request->validate([
            'chapter_number' => 'required|string|max:30',
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'badge' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'order_number' => 'required|integer|min:1',
        ]);

        $chapter->update($validated);

        return redirect()->route('admin.organization-guidelines.index', ['tab' => 'ppuo'])
            ->with('success', 'Bab regulasi '.$chapter->chapter_number.' berhasil diperbarui!');
    }

    /**
     * Delete a PPUO chapter.
     */
    public function destroyChapter(PpuoChapter $chapter): RedirectResponse
    {
        $chapNum = $chapter->chapter_number;
        $chapter->delete();

        return redirect()->route('admin.organization-guidelines.index', ['tab' => 'ppuo'])
            ->with('success', 'Bab regulasi '.$chapNum.' berhasil dihapus.');
    }

    /**
     * Update official PDF files and endorsement signers.
     */
    public function updateFilesAndSigners(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'signer_presidium' => 'required|string|max:255',
            'signer_pembina' => 'required|string|max:255',
            'signer_school' => 'required|string|max:255',
            'gbho_pdf' => 'nullable|file|mimes:pdf|max:20480',
            'ppuo_pdf' => 'nullable|file|mimes:pdf|max:30720',
        ]);

        $guideline = OrganizationGuideline::current();

        $updateData = [
            'signer_presidium' => $validated['signer_presidium'],
            'signer_pembina' => $validated['signer_pembina'],
            'signer_school' => $validated['signer_school'],
        ];

        if ($request->hasFile('gbho_pdf')) {
            if ($guideline->gbho_pdf_path && Storage::disk('public')->exists($guideline->gbho_pdf_path)) {
                Storage::disk('public')->delete($guideline->gbho_pdf_path);
            }
            $updateData['gbho_pdf_path'] = $request->file('gbho_pdf')->store('documents/guidelines', 'public');
        }

        if ($request->hasFile('ppuo_pdf')) {
            if ($guideline->ppuo_pdf_path && Storage::disk('public')->exists($guideline->ppuo_pdf_path)) {
                Storage::disk('public')->delete($guideline->ppuo_pdf_path);
            }
            $updateData['ppuo_pdf_path'] = $request->file('ppuo_pdf')->store('documents/guidelines', 'public');
        }

        $guideline->update($updateData);

        return redirect()->route('admin.organization-guidelines.index', ['tab' => 'files'])
            ->with('success', 'Berkas resmi PDF dan lembar pengesahan berhasil diperbarui!');
    }
}
