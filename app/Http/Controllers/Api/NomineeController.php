<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nominee;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NomineeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Nominee::with(['category', 'achievements', 'testimonials'])
            ->active()
            ->currentYear();

        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('category')) {
            $category = Category::where('name', $request->category)->first();
            if ($category) {
                $query->byCategory($category->id);
            }
        }

        if ($request->boolean('winners_only')) {
            $query->winners();
        }

        $orderBy = $request->get('order_by', 'votes');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $nominees = $query->get()->map(function ($nominee) {
            return [
                'id' => $nominee->id,
                'name' => $nominee->name,
                'organization' => $nominee->organization,
                'category' => $nominee->category->name,
                'description' => $nominee->description,
                'image_url' => $nominee->image_url,
                'votes' => $nominee->votes,
                'voting_percentage' => $nominee->voting_percentage,
                'can_vote' => $nominee->can_vote,
                'is_winner' => $nominee->is_winner,
                'impact_summary' => $nominee->impact_summary,
                'location' => $nominee->location,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $nominees
        ]);
    }

    public function show(Nominee $nominee): JsonResponse
    {
        $nominee->load(['category', 'achievements', 'testimonials']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $nominee->id,
                'name' => $nominee->name,
                'organization' => $nominee->organization,
                'category' => [
                    'id' => $nominee->category->id,
                    'name' => $nominee->category->name,
                    'voting_open' => $nominee->category->voting_open,
                ],
                'description' => $nominee->description,
                'long_bio' => $nominee->long_bio,
                'position' => $nominee->position,
                'location' => $nominee->location,
                'impact_summary' => $nominee->impact_summary,
                'image_url' => $nominee->image_url,
                'cover_image_url' => $nominee->cover_image_url,
                'video_url' => $nominee->video_url,
                'social_links' => $nominee->social_links,
                'votes' => $nominee->votes,
                'voting_percentage' => $nominee->voting_percentage,
                'can_vote' => $nominee->can_vote,
                'is_winner' => $nominee->is_winner,
                'achievements' => $nominee->achievements->map(function ($achievement) {
                    return [
                        'id' => $achievement->id,
                        'title' => $achievement->title,
                        'description' => $achievement->description,
                        'date' => $achievement->date,
                        'image_url' => $achievement->image_url,
                    ];
                }),
                'testimonials' => $nominee->testimonials->map(function ($testimonial) {
                    return [
                        'id' => $testimonial->id,
                        'name' => $testimonial->name,
                        'role' => $testimonial->role,
                        'organization' => $testimonial->organization,
                        'content' => $testimonial->content,
                        'image_url' => $testimonial->image_url,
                    ];
                }),
            ]
        ]);
    }

    public function trending(): JsonResponse
    {
        $trending = Nominee::with('category')
            ->active()
            ->currentYear()
            ->orderBy('votes', 'desc')
            ->take(5)
            ->get()
            ->map(function ($nominee) {
                return [
                    'id' => $nominee->id,
                    'name' => $nominee->name,
                    'category' => $nominee->category->name,
                    'votes' => $nominee->votes,
                    'voting_percentage' => $nominee->voting_percentage,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $trending
        ]);
    }
}
