<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class FcmService
{
    public static function sendToUser(?int $userId, string $title, string $body, array $data = []): void
    {
        $serverKey = Config::get('services.fcm.server_key');
        if (!$serverKey) {
            return;
        }

        $query = DeviceToken::query();
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        $tokens = $query->pluck('token')->all();
        if (empty($tokens)) {
            return;
        }

        $payload = [
            'registration_ids' => array_values($tokens),
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data,
        ];

        try {
            Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);
        } catch (\Throwable $e) {
            // ignore transport errors for now
        }
    }
}
