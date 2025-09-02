<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OurTeam;
use Illuminate\Http\JsonResponse;

class OurTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $teamMembers = OurTeam::all();

        return response()->json([
            'success' => true,
            'data' => $teamMembers,
            'message' => 'Team members retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(OurTeam $ourTeam): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $ourTeam,
            'message' => 'Team member retrieved successfully'
        ]);
    }
}
