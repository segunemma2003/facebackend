<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeSettings;
use Illuminate\Http\JsonResponse;

class HomeSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $homeSettings = HomeSettings::first();

        return response()->json([
            'success' => true,
            'data' => $homeSettings,
            'message' => 'Home settings retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(HomeSettings $homeSettings): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $homeSettings,
            'message' => 'Home settings retrieved successfully'
        ]);
    }
}
