<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints for Categories"
 * )
 */

class CategoryController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/v1/categories",
     *      operationId="getCategories",
     *      tags={"Categories"},
     *      summary="Get list of categories",
     *      description="Returns list of categories with filtering options",
     *      @OA\Parameter(
     *          name="region",
     *          description="Filter by region",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"Global", "Americas", "Europe", "Africa", "Asia-Pacific", "Middle East"}
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="voting_only",
     *          description="Show only categories with voting open",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="boolean")
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
     *                      @OA\Property(property="name", type="string", example="Best Innovation Award"),
     *                      @OA\Property(property="description", type="string", example="Award for outstanding innovation"),
     *                      @OA\Property(property="criteria", type="array", @OA\Items(type="string")),
     *                      @OA\Property(property="region", type="string", example="Global"),
     *                      @OA\Property(property="current_nominees", type="integer", example=5),
     *                      @OA\Property(property="voting_open", type="boolean", example=true),
     *                      @OA\Property(property="voting_ends_at", type="string", format="date-time"),
     *                      @OA\Property(property="days_remaining", type="integer", example=15),
     *                      @OA\Property(property="is_voting_active", type="boolean", example=true),
     *                      @OA\Property(property="color", type="string", example="#f0f9ff"),
     *                      @OA\Property(property="icon", type="string", example="trophy"),
     *                      @OA\Property(property="image_url", type="string", example="https://example.com/image.jpg"),
     *                      @OA\Property(property="total_votes", type="integer", example=150),
     *                      @OA\Property(property="nominees_count", type="integer", example=5),
     *                      @OA\Property(property="winners_count", type="integer", example=1)
     *                  )
     *              )
     *          )
     *      )
     * )
     */
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


     /**
     * @OA\Get(
     *      path="/api/v1/categories/{id}",
     *      operationId="getCategoryById",
     *      tags={"Categories"},
     *      summary="Get category information",
     *      description="Returns category data with nominees",
     *      @OA\Parameter(
     *          name="id",
     *          description="Category id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Best Innovation Award"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="criteria", type="array", @OA\Items(type="string")),
     *                  @OA\Property(property="region", type="string"),
     *                  @OA\Property(property="voting_open", type="boolean"),
     *                  @OA\Property(property="voting_starts_at", type="string", format="date-time"),
     *                  @OA\Property(property="voting_ends_at", type="string", format="date-time"),
     *                  @OA\Property(property="days_remaining", type="integer"),
     *                  @OA\Property(property="is_voting_active", type="boolean"),
     *                  @OA\Property(property="color", type="string"),
     *                  @OA\Property(property="icon", type="string"),
     *                  @OA\Property(property="image_url", type="string"),
     *                  @OA\Property(property="total_votes", type="integer"),
     *                  @OA\Property(
     *                      property="nominees",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="name", type="string"),
     *                          @OA\Property(property="organization", type="string"),
     *                          @OA\Property(property="description", type="string"),
     *                          @OA\Property(property="image_url", type="string"),
     *                          @OA\Property(property="votes", type="integer"),
     *                          @OA\Property(property="voting_percentage", type="number", format="float"),
     *                          @OA\Property(property="can_vote", type="boolean")
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found"
     *      )
     * )
     */

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


    /**
     * @OA\Get(
     *      path="/api/v1/categories/stats",
     *      operationId="getCategoryStats",
     *      tags={"Categories"},
     *      summary="Get category statistics",
     *      description="Returns statistical data about categories",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="total_categories", type="integer", example=10),
     *                  @OA\Property(property="active_voting_categories", type="integer", example=5),
     *                  @OA\Property(property="total_nominees", type="integer", example=50),
     *                  @OA\Property(property="total_votes", type="integer", example=1500),
     *                  @OA\Property(property="regions", type="array", @OA\Items(type="string")),
     *                  @OA\Property(
     *                      property="category_breakdown",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="name", type="string"),
     *                          @OA\Property(property="nominees_count", type="integer"),
     *                          @OA\Property(property="total_votes", type="integer"),
     *                          @OA\Property(property="voting_open", type="boolean")
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
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
