<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterSettings;
use Illuminate\Http\JsonResponse;

class FooterSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $footerSettings = FooterSettings::first();

        return response()->json([
            'success' => true,
            'data' => $footerSettings,
            'message' => 'Footer settings retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(FooterSettings $footerSettings): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $footerSettings,
            'message' => 'Footer settings retrieved successfully'
        ]);
    }
}
