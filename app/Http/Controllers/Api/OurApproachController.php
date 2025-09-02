<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OurApproach;
use Illuminate\Http\JsonResponse;

class OurApproachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $approaches = OurApproach::orderBy('step')->get();

        return response()->json([
            'success' => true,
            'data' => $approaches,
            'message' => 'Our approach steps retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(OurApproach $ourApproach): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $ourApproach,
            'message' => 'Approach step retrieved successfully'
        ]);
    }
}
