<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralGlobalSettings;
use Illuminate\Http\JsonResponse;

class GeneralGlobalSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $globalSettings = GeneralGlobalSettings::first();

        return response()->json([
            'success' => true,
            'data' => $globalSettings,
            'message' => 'General global settings retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneralGlobalSettings $generalGlobalSettings): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $generalGlobalSettings,
            'message' => 'General global settings retrieved successfully'
        ]);
    }
}
