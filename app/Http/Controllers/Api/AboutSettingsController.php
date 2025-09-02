<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutSettings;
use Illuminate\Http\JsonResponse;

class AboutSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $aboutSettings = AboutSettings::first();

        return response()->json([
            'success' => true,
            'data' => $aboutSettings,
            'message' => 'About settings retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(AboutSettings $aboutSettings): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $aboutSettings,
            'message' => 'About settings retrieved successfully'
        ]);
    }
}
