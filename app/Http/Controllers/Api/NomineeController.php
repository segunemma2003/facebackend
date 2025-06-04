<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nominee;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Nominees",
 *     description="API Endpoints for Nominees"
 * )
 */
class NomineeController extends Controller
{
     /**
     * @OA\Get(
     *      path="/api/v1/nominees",
     *      operationId="getNominees",
     *      tags={"Nominees"},
     *      summary="Get list of nominees",
     *      description="Returns list of nominees with filtering options",
     *      @OA\Parameter(
     *          name="category_id",
     *          description="Filter by category ID",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="category",
     *          description="Filter by category name",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="winners_only",
     *          description="Show only winners",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="boolean")
     *      ),
     *      @OA\Parameter(
     *          name="order_by",
     *          description="Order by field",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", enum={"votes", "name", "created_at"}, default="votes")
     *      ),
     *      @OA\Parameter(
     *          name="order_direction",
     *          description="Order direction",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="John Doe"),
     *                      @OA\Property(property="organization", type="string", example="Tech Corp"),
     *                      @OA\Property(property="category", type="string", example="Best Innovation Award"),
     *                      @OA\Property(property="description", type="string"),
     *                      @OA\Property(property="image_url", type="string"),
     *                      @OA\Property(property="votes", type="integer", example=25),
     *                      @OA\Property(property="voting_percentage", type="number", format="float", example=15.5),
     *                      @OA\Property(property="can_vote", type="boolean", example=true),
     *                      @OA\Property(property="is_winner", type="boolean", example=false),
     *                      @OA\Property(property="impact_summary", type="string"),
     *                      @OA\Property(property="location", type="string", example="New York, USA")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
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

        // Validate order column exists
        $allowedOrderColumns = ['votes', 'name', 'created_at', 'voting_percentage', 'organization'];
        if (!in_array($orderBy, $allowedOrderColumns)) {
            $orderBy = 'votes';
        }

        $query->orderBy($orderBy, $orderDirection);

        $nominees = $query->get()->map(function ($nominee) {
            return [
                'id' => $nominee->id,
                'name' => $nominee->name,
                'organization' => $nominee->organization,
                'category' => $nominee->category->name,
                'description' => $nominee->description,
                'image_url' => $nominee->image_url, // This uses the accessor from model
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
                'image_url' => $nominee->image_url, // Uses accessor
                'cover_image_url' => $nominee->cover_image_url, // Uses accessor
                'video_url' => $nominee->video_url,
                'social_links' => $nominee->social_links,
                'gallery_images' => $nominee->gallery_images_urls, // Uses accessor for full URLs
                'votes' => $nominee->votes,
                'voting_percentage' => $nominee->voting_percentage,
                'can_vote' => $nominee->can_vote,
                'is_winner' => $nominee->is_winner,
                'achievements' => $nominee->achievements->map(function ($achievement) {
                    return [
                        'id' => $achievement->id,
                        'title' => $achievement->title,
                        'description' => $achievement->description,
                        'date' => $achievement->date->format('Y-m-d'), // Format date properly
                        'image_url' => $achievement->image_url, // Uses accessor
                    ];
                }),
                'testimonials' => $nominee->testimonials->map(function ($testimonial) {
                    return [
                        'id' => $testimonial->id,
                        'name' => $testimonial->name,
                        'role' => $testimonial->role,
                        'organization' => $testimonial->organization,
                        'content' => $testimonial->content,
                        'image_url' => $testimonial->image_url, // Uses accessor
                    ];
                }),
            ]
        ]);
    }
     /**
     * @OA\Get(
     *      path="/api/v1/nominees/trending",
     *      operationId="getTrendingNominees",
     *      tags={"Nominees"},
     *      summary="Get trending nominees",
     *      description="Returns top 5 nominees by votes",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="John Doe"),
     *                      @OA\Property(property="category", type="string", example="Best Innovation Award"),
     *                      @OA\Property(property="votes", type="integer", example=25),
     *                      @OA\Property(property="voting_percentage", type="number", format="float", example=15.5)
     *                  )
     *              )
     *          )
     *      )
     * )
     */

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
                    'organization' => $nominee->organization,
                    'image_url' => $nominee->image_url, // Uses accessor
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
