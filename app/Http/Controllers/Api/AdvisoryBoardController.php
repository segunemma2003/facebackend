<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvisoryBoard;
use Illuminate\Http\JsonResponse;

class AdvisoryBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $advisoryBoard = AdvisoryBoard::all();

        return response()->json([
            'success' => true,
            'data' => $advisoryBoard,
            'message' => 'Advisory board members retrieved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(AdvisoryBoard $advisoryBoard): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $advisoryBoard,
            'message' => 'Advisory board member retrieved successfully'
        ]);
    }
}
