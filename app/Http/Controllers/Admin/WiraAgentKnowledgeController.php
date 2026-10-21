<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WiraKnowledgeSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WiraAgentKnowledgeController extends Controller
{
    /**
     * Jalankan proses sinkronisasi seluruh konten website untuk Wira Agent.
     */
    public function sync(Request $request, WiraKnowledgeSyncService $syncService): JsonResponse|RedirectResponse
    {
        try {
            $result = $syncService->syncAll();

            $message = "Otak Wira Agent berhasil menyerap seluruh konten website! Total {$result['total_topics']} topik (termasuk {$result['articles_count']} artikel) telah tersinkronisasi.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $result,
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            $errorMsg = 'Gagal melakukan sinkronisasi pengetahuan Wira Agent: '.$e->getMessage();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 500);
            }

            return redirect()->back()->with('error', $errorMsg);
        }
    }

    /**
     * Dapatkan ringkasan status basis pengetahuan Wira Agent saat ini.
     */
    public function status(): JsonResponse
    {
        $path = storage_path('app/knowledge/knowledge.json');
        if (! File::exists($path)) {
            return response()->json([
                'synced' => false,
                'total_topics' => 0,
                'updated_at' => null,
            ]);
        }

        try {
            $data = json_decode(File::get($path), true);

            return response()->json([
                'synced' => true,
                'version' => $data['version'] ?? '2.0.0',
                'cabinet' => $data['cabinet'] ?? 'Ragana Dwi Pantara 2026/2027',
                'updated_at' => $data['updated_at'] ?? null,
                'total_topics' => count($data['topics'] ?? []),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'synced' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
