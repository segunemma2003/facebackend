<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuccessStories;
use Illuminate\Http\JsonResponse;

class SuccessStoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $successStories = SuccessStories::all();

        return response()->json([
            'success' => true,
            'data' => $successStories,
            'message' => 'Success stories retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SuccessStories $successStories): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $successStories,
            'message' => 'Success story retrieved successfully'
        ]);
    }
}
