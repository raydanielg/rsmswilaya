<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'device_id' => 'nullable|string|max:190',
            'user_id' => 'nullable|integer',
            'token' => 'required|string|max:500',
            'platform' => 'nullable|string|max:50',
        ]);

        $token = DeviceToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'device_id' => $data['device_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'platform' => $data['platform'] ?? null,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'id' => $token->id,
            'token' => $token->token,
        ]);
    }
}
