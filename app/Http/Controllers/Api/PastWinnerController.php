<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PastWinner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PastWinnerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PastWinner::query();

        if ($request->has('year')) {
            $query->byYear($request->year);
        }

        if ($request->has('category') && $request->category !== 'All Categories') {
            $query->byCategory($request->category);
        }

        $winners = $query->orderBy('year', 'desc')
            ->orderBy('name')
            ->get()
            ->map(function ($winner) {
                return [
                    'id' => $winner->id,
                    'name' => $winner->name,
                    'organization' => $winner->organization,
                    'category' => $winner->category,
                    'achievement' => $winner->achievement,
                    'image_url' => $winner->image_url,
                    'year' => $winner->year,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $winners
        ]);
    }

    public function years(): JsonResponse
    {
        $years = PastWinner::selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'success' => true,
            'data' => $years
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = PastWinner::selectRaw('DISTINCT category')
            ->orderBy('category')
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}

