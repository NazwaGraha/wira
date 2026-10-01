<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CompetitionFeeController extends Controller
{
    public function index(Request $request)
    {
        try {
            // 1. Ensure competition tables exist
            if (!Schema::hasTable('competition_events') || !Schema::hasTable('competition_categories')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\CompetitionSeeder',
                    '--force' => true
                ]);
            }

            // 2. Ensure registration_fee column exists in competition_categories
            if (Schema::hasTable('competition_categories') && !Schema::hasColumn('competition_categories', 'registration_fee')) {
                Schema::table('competition_categories', function (Blueprint $table) {
                    $table->decimal('registration_fee', 12, 2)->default(150000)->after('criteria_schema');
                });
            }

            // 3. Ensure default competition categories are seeded if empty
            if (Schema::hasTable('competition_categories') && CompetitionCategory::count() === 0) {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\CompetitionSeeder',
                    '--force' => true
                ]);
            }

            $event = null;
            if (Schema::hasTable('competition_events')) {
                $event = CompetitionEvent::where('is_active', true)->first() ?: CompetitionEvent::first();
            }

            $level = $request->query('level');

            $categories = collect();
            if (Schema::hasTable('competition_categories')) {
                $query = CompetitionCategory::orderBy('order_position');
                if ($event) {
                    $query->where(function($q) use ($event) {
                        $q->where('competition_event_id', $event->id)
                          ->orWhereNull('competition_event_id');
                    });
                }

                if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
                    $query->where('level', $level);
                }

                $categories = $query->get();
            }

            $categoriesByLevel = $categories->groupBy('level');

            return view('admin.competition.fees.index', compact('event', 'categories', 'categoriesByLevel', 'level'));
        } catch (\Throwable $e) {
            Log::error('CompetitionFeeController index error: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            
            return response("<div style='font-family:sans-serif;padding:40px;max-width:700px;margin:50px auto;background:#fff;border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.05);'>
                <div style='display:inline-block;padding:6px 12px;background:#fee2e2;color:#dc2626;font-size:12px;font-weight:bold;border-radius:6px;margin-bottom:12px;'>KENDALA SERVER TERDETEKSI</div>
                <h2 style='color:#0f172a;margin-top:0;'>Terjadi Kendala Sinkronisasi Database / Cache</h2>
                <p style='color:#64748b;font-size:14px;line-height:1.6;'>Pesan Sistem: <strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>
                <div style='margin-top:24px;display:flex;gap:12px;'>
                    <a href='/server-sync-update-pmr' style='display:inline-block;padding:12px 24px;background:#dc2626;color:#fff;text-decoration:none;border-radius:10px;font-weight:bold;font-size:13px;'>⚡ Bersihkan Cache & Migrasi Otomatis</a>
                    <a href='/admin/dashboard' style='display:inline-block;padding:12px 24px;background:#f1f5f9;color:#334155;text-decoration:none;border-radius:10px;font-weight:bold;font-size:13px;'>Kembali ke Dashboard</a>
                </div>
            </div>", 200);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'fees' => 'required|array',
            'fees.*' => 'required|numeric|min:0',
            'event_registration_fee' => 'nullable|numeric|min:0',
        ]);

        $event = CompetitionEvent::where('is_active', true)->first();
        if ($event && isset($validated['event_registration_fee'])) {
            $event->update([
                'registration_fee' => $validated['event_registration_fee'],
            ]);
        }

        $updatedCount = 0;
        foreach ($validated['fees'] as $catId => $fee) {
            $category = CompetitionCategory::find($catId);
            if ($category) {
                $category->update([
                    'registration_fee' => $fee,
                ]);
                $updatedCount++;
            }
        }

        return redirect()->back()->with('success', "Biaya pendaftaran untuk {$updatedCount} cabang lomba berhasil diperbarui!");
    }
}

