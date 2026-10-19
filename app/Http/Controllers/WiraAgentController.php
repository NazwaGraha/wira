<?php

namespace App\Http\Controllers;

use App\Services\WiraAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WiraAgentController extends Controller
{
    public function __construct(
        protected WiraAgentService $agentService
    ) {}

    /**
     * Endpoint pemrosesan pesan chat AI Wira Agent.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|min:1|max:2000',
            'history' => 'nullable|array',
            'history.*.role' => 'required_with:history|string|in:user,assistant,model',
            'history.*.content' => 'required_with:history|string|max:4000',
        ], [
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan terlalu panjang (maksimal 2000 karakter).',
        ]);

        try {
            $message = trim((string) $validated['message']);
            $history = (array) ($validated['history'] ?? []);

            $result = $this->agentService->chat($message, $history);

            return response()->json([
                'success' => true,
                'reply' => $result['reply'],
                'provider' => $result['provider'],
                'timestamp' => $result['timestamp'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Wira Agent Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'reply' => 'Mohon maaf Sahabat Kemanusiaan, sistem Wira Agent sedang mengalami sedikit kendala teknis. Silakan coba tanyakan kembali beberapa saat lagi, atau hubungi langsung narahubung kami melalui tombol WhatsApp ya! ❤️',
                'provider' => 'error_handler',
                'timestamp' => now()->format('H:i'),
            ], 500);
        }
    }
}
