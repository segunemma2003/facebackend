<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Category::with(['activeNominees', 'winners'])
            ->active()
            ->orderBy('sort_order');

        if ($request->has('region') && $request->region !== 'all') {
            $query->where('region', $request->region);
        }

        if ($request->boolean('voting_only')) {
            $query->withVotingOpen();
        }

        $categories = $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'criteria' => $category->criteria,
                'region' => $category->region,
                'current_nominees' => $category->activeNominees->count(),
                'voting_open' => $category->voting_open,
                'voting_ends_at' => $category->voting_ends_at,
                'days_remaining' => $category->days_remaining,
                'is_voting_active' => $category->is_voting_active,
                'color' => $category->color,
                'icon' => $category->icon,
                'image_url' => $category->image_url,
                'total_votes' => $category->total_votes,
                'nominees_count' => $category->activeNominees->count(),
                'winners_count' => $category->winners->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['activeNominees.achievements', 'activeNominees.testimonials']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'criteria' => $category->criteria,
                'region' => $category->region,
                'voting_open' => $category->voting_open,
                'voting_starts_at' => $category->voting_starts_at,
                'voting_ends_at' => $category->voting_ends_at,
                'days_remaining' => $category->days_remaining,
                'is_voting_active' => $category->is_voting_active,
                'color' => $category->color,
                'icon' => $category->icon,
                'image_url' => $category->image_url,
                'total_votes' => $category->total_votes,
                'nominees' => $category->activeNominees->map(function ($nominee) {
                    return [
                        'id' => $nominee->id,
                        'name' => $nominee->name,
                        'organization' => $nominee->organization,
                        'description' => $nominee->description,
                        'image_url' => $nominee->image_url,
                        'votes' => $nominee->votes,
                        'voting_percentage' => $nominee->voting_percentage,
                        'can_vote' => $nominee->can_vote,
                    ];
                }),
            ]
        ]);
    }

    public function stats(): JsonResponse
    {
        $categories = Category::active()->with('activeNominees')->get();

        $stats = [
            'total_categories' => $categories->count(),
            'active_voting_categories' => $categories->where('is_voting_active', true)->count(),
            'total_nominees' => $categories->sum(fn($cat) => $cat->activeNominees->count()),
            'total_votes' => $categories->sum('total_votes'),
            'regions' => $categories->pluck('region')->unique()->values(),
            'category_breakdown' => $categories->map(function ($category) {
                return [
                    'name' => $category->name,
                    'nominees_count' => $category->activeNominees->count(),
                    'total_votes' => $category->total_votes,
                    'voting_open' => $category->voting_open,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
