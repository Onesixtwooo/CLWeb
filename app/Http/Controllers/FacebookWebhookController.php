<?php

namespace App\Http\Controllers;

use App\Services\FacebookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacebookWebhookController extends Controller
{
    /**
     * Handle the incoming Facebook Webhook.
     */
    public function handle(Request $request)
    {
        // Standalone debug logging to bypass standard channel buffers
        file_put_contents(storage_path('logs/facebook_webhook_debug.txt'),
            "[" . date('Y-m-d H:i:s') . "] Method: " . $request->method() . " | URL: " . $request->fullUrl() . "\n",
            FILE_APPEND);

      if ($request->isMethod('get')) {
    $verifyToken = env('FACEBOOK_VERIFY_TOKEN', 'clsu_secure_token');

    // ✅ Use query() instead of input()
    $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
    $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
    $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

    // 🔥 Debug (important)
    file_put_contents(storage_path('logs/facebook_webhook_debug.txt'),
        "VERIFY MODE: $mode | TOKEN: $token | CHALLENGE: $challenge\n",
        FILE_APPEND);

    if ($mode === 'subscribe' && $token === $verifyToken) {
        return response($challenge, 200);
    }

    return response('Unauthorized', 403);
}
        // 2. Handle Event Notification (POST Request from Meta)
        Log::info('Facebook Webhook Received Payload:', $request->all());

        try {
            $created = app(FacebookService::class)->processWebhookPayload($request->all());
            Log::info('Facebook webhook processed successfully.', ['created_articles' => $created]);
        } catch (\Throwable $e) {
            Log::error('Error processing Facebook webhook: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response('Event Received', 200);
    }
}
