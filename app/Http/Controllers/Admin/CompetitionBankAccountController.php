<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionEvent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CompetitionBankAccountController extends Controller
{
    /**
     * Tampilkan halaman setup tujuan transfer dana lomba per tingkat.
     */
    public function index(Request $request)
    {
        $this->ensureColumnsExist();

        $today = now()->startOfDay()->toDateString();

        // Ambil daftar event aktif atau yang belum selesai untuk selector bar
        $allEvents = CompetitionEvent::query()
            ->where(function ($query) use ($today) {
                $query->where('is_active', true)
                    ->orWhere(function ($q) use ($today) {
                        $q->whereNotNull('end_date')
                            ->where('end_date', '>=', $today);
                    });
            })
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->get();

        $event = null;
        if ($request->has('event_id')) {
            $event = $allEvents->firstWhere('id', $request->query('event_id'));
        }

        if (! $event) {
            $event = $allEvents->firstWhere('is_active', true) ?? $allEvents->first() ?? CompetitionEvent::latest()->first();
        }

        $bankAccounts = $event ? $event->getAllBankAccounts() : [];

        return view('admin.competition.bank-accounts.index', compact('event', 'allEvents', 'bankAccounts'));
    }

    /**
     * Simpan pembaruan nomor rekening tujuan transfer per tingkat.
     */
    public function update(Request $request)
    {
        $this->ensureColumnsExist();

        $validated = $request->validate([
            'event_id' => 'required|exists:competition_events,id',
            // Rekening Default / Umum (Fallback)
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:150',

            // Rekening Khusus Tingkat Mula (SD / MI) - Rekening 1
            'bank_mula_name' => 'nullable|string|max:100',
            'bank_mula_number' => 'nullable|string|max:100',
            'bank_mula_holder' => 'nullable|string|max:150',

            // Rekening Khusus Tingkat Mula (SD / MI) - Rekening 2
            'bank_mula_name_2' => 'nullable|string|max:100',
            'bank_mula_number_2' => 'nullable|string|max:100',
            'bank_mula_holder_2' => 'nullable|string|max:150',

            // Rekening Khusus Tingkat Madya (SMP / MTs) - Rekening 1
            'bank_madya_name' => 'nullable|string|max:100',
            'bank_madya_number' => 'nullable|string|max:100',
            'bank_madya_holder' => 'nullable|string|max:150',

            // Rekening Khusus Tingkat Madya (SMP / MTs) - Rekening 2
            'bank_madya_name_2' => 'nullable|string|max:100',
            'bank_madya_number_2' => 'nullable|string|max:100',
            'bank_madya_holder_2' => 'nullable|string|max:150',

            // Rekening Khusus Tingkat Wira (SMA / SMK / MA) - Rekening 1
            'bank_wira_name' => 'nullable|string|max:100',
            'bank_wira_number' => 'nullable|string|max:100',
            'bank_wira_holder' => 'nullable|string|max:150',

            // Rekening Khusus Tingkat Wira (SMA / SMK / MA) - Rekening 2
            'bank_wira_name_2' => 'nullable|string|max:100',
            'bank_wira_number_2' => 'nullable|string|max:100',
            'bank_wira_holder_2' => 'nullable|string|max:150',
        ]);

        $event = CompetitionEvent::findOrFail($validated['event_id']);
        $event->update([
            'bank_name' => $validated['bank_name'] ? trim($validated['bank_name']) : null,
            'bank_account_number' => $validated['bank_account_number'] ? trim($validated['bank_account_number']) : null,
            'bank_account_holder' => $validated['bank_account_holder'] ? trim($validated['bank_account_holder']) : null,

            'bank_mula_name' => $validated['bank_mula_name'] ? trim($validated['bank_mula_name']) : null,
            'bank_mula_number' => $validated['bank_mula_number'] ? trim($validated['bank_mula_number']) : null,
            'bank_mula_holder' => $validated['bank_mula_holder'] ? trim($validated['bank_mula_holder']) : null,
            'bank_mula_name_2' => $validated['bank_mula_name_2'] ? trim($validated['bank_mula_name_2']) : null,
            'bank_mula_number_2' => $validated['bank_mula_number_2'] ? trim($validated['bank_mula_number_2']) : null,
            'bank_mula_holder_2' => $validated['bank_mula_holder_2'] ? trim($validated['bank_mula_holder_2']) : null,

            'bank_madya_name' => $validated['bank_madya_name'] ? trim($validated['bank_madya_name']) : null,
            'bank_madya_number' => $validated['bank_madya_number'] ? trim($validated['bank_madya_number']) : null,
            'bank_madya_holder' => $validated['bank_madya_holder'] ? trim($validated['bank_madya_holder']) : null,
            'bank_madya_name_2' => $validated['bank_madya_name_2'] ? trim($validated['bank_madya_name_2']) : null,
            'bank_madya_number_2' => $validated['bank_madya_number_2'] ? trim($validated['bank_madya_number_2']) : null,
            'bank_madya_holder_2' => $validated['bank_madya_holder_2'] ? trim($validated['bank_madya_holder_2']) : null,

            'bank_wira_name' => $validated['bank_wira_name'] ? trim($validated['bank_wira_name']) : null,
            'bank_wira_number' => $validated['bank_wira_number'] ? trim($validated['bank_wira_number']) : null,
            'bank_wira_holder' => $validated['bank_wira_holder'] ? trim($validated['bank_wira_holder']) : null,
            'bank_wira_name_2' => $validated['bank_wira_name_2'] ? trim($validated['bank_wira_name_2']) : null,
            'bank_wira_number_2' => $validated['bank_wira_number_2'] ? trim($validated['bank_wira_number_2']) : null,
            'bank_wira_holder_2' => $validated['bank_wira_holder_2'] ? trim($validated['bank_wira_holder_2']) : null,
        ]);

        return redirect()->route('admin.competition-bank-accounts.index', ['event_id' => $event->id])
            ->with('success', 'Pengaturan rekening tujuan transfer dana (Mula, Madya, Wira) berhasil diperbarui!');
    }

    /**
     * Memastikan kolom-kolom rekening per tingkat ada di tabel competition_events (defensive setup).
     */
    protected function ensureColumnsExist(): void
    {
        if (Schema::hasTable('competition_events')) {
            $columns = [
                'bank_mula_name', 'bank_mula_number', 'bank_mula_holder',
                'bank_mula_name_2', 'bank_mula_number_2', 'bank_mula_holder_2',
                'bank_madya_name', 'bank_madya_number', 'bank_madya_holder',
                'bank_madya_name_2', 'bank_madya_number_2', 'bank_madya_holder_2',
                'bank_wira_name', 'bank_wira_number', 'bank_wira_holder',
                'bank_wira_name_2', 'bank_wira_number_2', 'bank_wira_holder_2',
            ];

            $missing = [];
            foreach ($columns as $col) {
                if (! Schema::hasColumn('competition_events', $col)) {
                    $missing[] = $col;
                }
            }

            if (! empty($missing)) {
                Schema::table('competition_events', function (Blueprint $table) use ($missing) {
                    if (in_array('bank_mula_name', $missing, true)) {
                        $table->string('bank_mula_name', 100)->nullable()->after('bank_account_holder');
                    }
                    if (in_array('bank_mula_number', $missing, true)) {
                        $table->string('bank_mula_number', 100)->nullable()->after('bank_mula_name');
                    }
                    if (in_array('bank_mula_holder', $missing, true)) {
                        $table->string('bank_mula_holder', 150)->nullable()->after('bank_mula_number');
                    }
                    if (in_array('bank_mula_name_2', $missing, true)) {
                        $table->string('bank_mula_name_2', 100)->nullable()->after('bank_mula_holder');
                    }
                    if (in_array('bank_mula_number_2', $missing, true)) {
                        $table->string('bank_mula_number_2', 100)->nullable()->after('bank_mula_name_2');
                    }
                    if (in_array('bank_mula_holder_2', $missing, true)) {
                        $table->string('bank_mula_holder_2', 150)->nullable()->after('bank_mula_number_2');
                    }

                    if (in_array('bank_madya_name', $missing, true)) {
                        $table->string('bank_madya_name', 100)->nullable()->after('bank_mula_holder_2');
                    }
                    if (in_array('bank_madya_number', $missing, true)) {
                        $table->string('bank_madya_number', 100)->nullable()->after('bank_madya_name');
                    }
                    if (in_array('bank_madya_holder', $missing, true)) {
                        $table->string('bank_madya_holder', 150)->nullable()->after('bank_madya_number');
                    }
                    if (in_array('bank_madya_name_2', $missing, true)) {
                        $table->string('bank_madya_name_2', 100)->nullable()->after('bank_madya_holder');
                    }
                    if (in_array('bank_madya_number_2', $missing, true)) {
                        $table->string('bank_madya_number_2', 100)->nullable()->after('bank_madya_name_2');
                    }
                    if (in_array('bank_madya_holder_2', $missing, true)) {
                        $table->string('bank_madya_holder_2', 150)->nullable()->after('bank_madya_number_2');
                    }

                    if (in_array('bank_wira_name', $missing, true)) {
                        $table->string('bank_wira_name', 100)->nullable()->after('bank_madya_holder_2');
                    }
                    if (in_array('bank_wira_number', $missing, true)) {
                        $table->string('bank_wira_number', 100)->nullable()->after('bank_wira_name');
                    }
                    if (in_array('bank_wira_holder', $missing, true)) {
                        $table->string('bank_wira_holder', 150)->nullable()->after('bank_wira_number');
                    }
                    if (in_array('bank_wira_name_2', $missing, true)) {
                        $table->string('bank_wira_name_2', 100)->nullable()->after('bank_wira_holder');
                    }
                    if (in_array('bank_wira_number_2', $missing, true)) {
                        $table->string('bank_wira_number_2', 100)->nullable()->after('bank_wira_name_2');
                    }
                    if (in_array('bank_wira_holder_2', $missing, true)) {
                        $table->string('bank_wira_holder_2', 150)->nullable()->after('bank_wira_number_2');
                    }
                });
            }
        }
    }
}
