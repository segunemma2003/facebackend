<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function show(string $key): JsonResponse
    {
        $value = Setting::get($key);

        if ($value === null) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $key,
                'value' => $value
            ]
        ]);
    }
}
