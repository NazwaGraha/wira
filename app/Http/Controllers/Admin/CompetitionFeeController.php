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
            }

            // 2. Ensure registration_fee and wave columns exist
            if (Schema::hasTable('competition_categories')) {
                if (!Schema::hasColumn('competition_categories', 'registration_fee')) {
                    Schema::table('competition_categories', function (Blueprint $table) {
                        $table->decimal('registration_fee', 12, 2)->default(150000)->after('criteria_schema');
                    });
                }
                if (!Schema::hasColumn('competition_categories', 'registration_fee_wave1')) {
                    Schema::table('competition_categories', function (Blueprint $table) {
                        $table->decimal('registration_fee_wave1', 12, 2)->nullable()->after('registration_fee');
                        $table->decimal('registration_fee_wave2', 12, 2)->nullable()->after('registration_fee_wave1');
                    });
                }
            }

            if (Schema::hasTable('competition_events') && !Schema::hasColumn('competition_events', 'wave1_start_date')) {
                Schema::table('competition_events', function (Blueprint $table) {
                    $table->string('wave1_name')->default('Gelombang 1')->nullable()->after('registration_fee');
                    $table->dateTime('wave1_start_date')->nullable()->after('wave1_name');
                    $table->dateTime('wave1_end_date')->nullable()->after('wave1_start_date');
                    $table->string('wave2_name')->default('Gelombang 2')->nullable()->after('wave1_end_date');
                    $table->dateTime('wave2_start_date')->nullable()->after('wave2_name');
                    $table->dateTime('wave2_end_date')->nullable()->after('wave2_start_date');
                    $table->boolean('is_waves_enabled')->default(true)->after('wave2_end_date');
                });
            }

            if (Schema::hasTable('competition_registrations') && !Schema::hasColumn('competition_registrations', 'registration_wave')) {
                Schema::table('competition_registrations', function (Blueprint $table) {
                    $table->unsignedTinyInteger('registration_wave')->default(1)->after('total_payment');
                });
            }

            // Note: Never auto-seed default dummy events on request to allow permanent deletion of old editions.

            $allEvents = collect();
            $event = null;
            if (Schema::hasTable('competition_events')) {
                $today = now()->startOfDay()->toDateString();

                // Hanya ambil event yang sedang aktif ATAU belum selesai / akan diselenggarakan di masa depan
                $allEvents = CompetitionEvent::query()
                    ->where(function ($query) use ($today) {
                        $query->where('is_active', true)
                              ->orWhere(function ($q) use ($today) {
                                  $q->whereNotNull('end_date')
                                    ->where('end_date', '>=', $today);
                              })
                              ->orWhere(function ($q) use ($today) {
                                  $q->whereNull('end_date')
                                    ->whereNotNull('start_date')
                                    ->where('start_date', '>=', $today);
                              })
                              ->orWhere(function ($q) {
                                  $q->whereNull('start_date')
                                    ->whereNull('end_date');
                              });
                    })
                    ->orderByDesc('is_active')
                    ->orderBy('start_date')
                    ->orderByDesc('id')
                    ->get();

                $selectedEventId = $request->query('event_id');
                if ($selectedEventId) {
                    $event = $allEvents->firstWhere('id', $selectedEventId) ?: CompetitionEvent::find($selectedEventId);
                } else {
                    $event = $allEvents->firstWhere('is_active', true) ?: $allEvents->first();
                }
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
                } else {
                    $query->whereRaw('1 = 0');
                }

                if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
                    $query->where('level', $level);
                }

                $categories = $query->get();
            }

            $categoriesByLevel = $categories->groupBy('level');

            return view('admin.competition.fees.index', compact('event', 'allEvents', 'categories', 'categoriesByLevel', 'level'));
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
            'event_id' => 'nullable|exists:competition_events,id',
            'is_waves_enabled' => 'nullable',
            'wave1_name' => 'nullable|string|max:100',
            'wave1_start_date' => 'nullable|string',
            'wave1_end_date' => 'nullable|string',
            'wave2_name' => 'nullable|string|max:100',
            'wave2_start_date' => 'nullable|string',
            'wave2_end_date' => 'nullable|string',
            'fees_wave1' => 'nullable|array',
            'fees_wave1.*' => 'nullable|numeric|min:0',
            'fees_wave2' => 'nullable|array',
            'fees_wave2.*' => 'nullable|numeric|min:0',
            'fees' => 'nullable|array',
            'fees.*' => 'nullable|numeric|min:0',
            'event_registration_fee' => 'nullable|numeric|min:0',
        ]);

        $eventId = $request->input('event_id');
        $event = $eventId ? CompetitionEvent::find($eventId) : CompetitionEvent::where('is_active', true)->first();
        if ($event) {
            $eventData = [
                'is_waves_enabled' => $request->has('is_waves_enabled'),
                'wave1_name' => $request->input('wave1_name', 'Gelombang 1') ?: 'Gelombang 1',
                'wave1_start_date' => $request->input('wave1_start_date') ? date('Y-m-d H:i:s', strtotime($request->input('wave1_start_date'))) : null,
                'wave1_end_date' => $request->input('wave1_end_date') ? date('Y-m-d H:i:s', strtotime($request->input('wave1_end_date'))) : null,
                'wave2_name' => $request->input('wave2_name', 'Gelombang 2') ?: 'Gelombang 2',
                'wave2_start_date' => $request->input('wave2_start_date') ? date('Y-m-d H:i:s', strtotime($request->input('wave2_start_date'))) : null,
                'wave2_end_date' => $request->input('wave2_end_date') ? date('Y-m-d H:i:s', strtotime($request->input('wave2_end_date'))) : null,
            ];

            if (isset($validated['event_registration_fee'])) {
                $eventData['registration_fee'] = $validated['event_registration_fee'];
            }

            $event->update($eventData);
        }

        $updatedCount = 0;
        $feesWave1 = $request->input('fees_wave1', []);
        $feesWave2 = $request->input('fees_wave2', []);
        $legacyFees = $request->input('fees', []);

        // Ambil semua category ID dari input mana pun yang tersedia
        $allCatIds = array_unique(array_merge(array_keys($feesWave1), array_keys($feesWave2), array_keys($legacyFees)));

        foreach ($allCatIds as $catId) {
            $category = CompetitionCategory::find($catId);
            if ($category) {
                $fee1 = isset($feesWave1[$catId]) ? floatval($feesWave1[$catId]) : (isset($legacyFees[$catId]) ? floatval($legacyFees[$catId]) : $category->registration_fee);
                $fee2 = isset($feesWave2[$catId]) ? floatval($feesWave2[$catId]) : $fee1;

                $category->update([
                    'registration_fee_wave1' => $fee1,
                    'registration_fee_wave2' => $fee2,
                    'registration_fee' => $fee2, // Selaraskan legacy field agar tidak terjadi konflik
                ]);
                $updatedCount++;
            }
        }

        return redirect()->back()->with('success', "Jadwal pendaftaran 2 gelombang dan biaya untuk {$updatedCount} cabang lomba berhasil diperbarui!");
    }
}

