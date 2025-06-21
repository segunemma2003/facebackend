<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Page Content",
 *     description="API Endpoints for Page Content Management"
 * )
 */
class PageContentController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/content",
     *      operationId="getAllPageContent",
     *      tags={"Page Content"},
     *      summary="Get all page content",
     *      description="Returns all active page content grouped by page and section",
     *      @OA\Parameter(
     *          name="page",
     *          description="Filter by specific page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"homepage", "about", "approach", "categories", "nominees", "past_winners", "gallery", "contact", "footer", "global_settings"}
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="section",
     *          description="Filter by specific section",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="homepage",
     *                      type="object",
     *                      @OA\Property(
     *                          property="hero",
     *                          type="object",
     *                          @OA\Property(
     *                              property="main_title",
     *                              type="object",
     *                              @OA\Property(property="content", type="string", example="FACE Awards 2025"),
     *                              @OA\Property(property="type", type="string", example="text"),
     *                              @OA\Property(property="meta", type="object"),
     *                              @OA\Property(property="sort_order", type="integer", example=1)
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = PageContent::where('is_active', true);

        if ($request->has('page')) {
            $query->byPage($request->page);
        }

        if ($request->has('section')) {
            $query->bySection($request->section);
        }

        $contents = $query->orderedBySort()->get();

        // Group by page and section
        $grouped = $contents->groupBy(['page', 'section'])->map(function ($sections) {
            return $sections->map(function ($items) {
                return $items->keyBy('key')->map(function ($item) {
                    return [
                        'content' => $this->transformContent($item),
                        'type' => $item->type,
                        'meta' => $item->meta,
                        'sort_order' => $item->sort_order,
                    ];
                });
            });
        });

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/content/{page}",
     *      operationId="getPageContent",
     *      tags={"Page Content"},
     *      summary="Get content for a specific page",
     *      description="Returns all content for a specific page, organized by sections",
     *      @OA\Parameter(
     *          name="page",
     *          description="Page name",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string",
     *              enum={"homepage", "about", "approach", "categories", "nominees", "past_winners", "gallery", "contact", "footer", "global_settings"}
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="page",
     *                      type="string",
     *                      example="homepage"
     *                  ),
     *                  @OA\Property(
     *                      property="sections",
     *                      type="object",
     *                      @OA\Property(
     *                          property="hero",
     *                          type="object",
     *                          @OA\Property(
     *                              property="main_title",
     *                              type="object",
     *                              @OA\Property(property="content", type="string"),
     *                              @OA\Property(property="type", type="string"),
     *                              @OA\Property(property="meta", type="object"),
     *                              @OA\Property(property="sort_order", type="integer")
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found"
     *      )
     * )
     */
    public function show(string $page): JsonResponse
    {
        $contents = PageContent::byPage($page)
            ->activeContent()
            ->orderedBySort()
            ->get();

        if ($contents->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Page content not found'
            ], 404);
        }

        // Group by section
        $sections = $contents->groupBy('section')->map(function ($items) {
            return $items->keyBy('key')->map(function ($item) {
                return [
                    'content' => $this->transformContent($item),
                    'type' => $item->type,
                    'meta' => $item->meta,
                    'sort_order' => $item->sort_order,
                ];
            });
        });

        return response()->json([
            'success' => true,
            'data' => [
                'page' => $page,
                'sections' => $sections
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/content/{page}/{section}",
     *      operationId="getPageSectionContent",
     *      tags={"Page Content"},
     *      summary="Get content for a specific page section",
     *      description="Returns all content items for a specific page section",
     *      @OA\Parameter(
     *          name="page",
     *          description="Page name",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="section",
     *          description="Section name",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="page", type="string"),
     *                  @OA\Property(property="section", type="string"),
     *                  @OA\Property(
     *                      property="content",
     *                      type="object",
     *                      @OA\Property(
     *                          property="title",
     *                          type="object",
     *                          @OA\Property(property="content", type="string"),
     *                          @OA\Property(property="type", type="string"),
     *                          @OA\Property(property="meta", type="object")
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Section not found"
     *      )
     * )
     */
    public function section(string $page, string $section): JsonResponse
    {
        $contents = PageContent::byPage($page)
            ->bySection($section)
            ->activeContent()
            ->orderedBySort()
            ->get();

        if ($contents->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Section content not found'
            ], 404);
        }

        $content = $contents->keyBy('key')->map(function ($item) {
            return [
                'content' => $this->transformContent($item),
                'type' => $item->type,
                'meta' => $item->meta,
                'sort_order' => $item->sort_order,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'page' => $page,
                'section' => $section,
                'content' => $content
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/content/{page}/{section}/{key}",
     *      operationId="getSpecificContent",
     *      tags={"Page Content"},
     *      summary="Get a specific content item",
     *      description="Returns a specific content item by page, section, and key",
     *      @OA\Parameter(
     *          name="page",
     *          description="Page name",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="section",
     *          description="Section name",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="key",
     *          description="Content key",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="page", type="string"),
     *                  @OA\Property(property="section", type="string"),
     *                  @OA\Property(property="key", type="string"),
     *                  @OA\Property(property="content", anyOf={
     *                      @OA\Schema(type="string"),
     *                      @OA\Schema(type="object"),
     *                      @OA\Schema(type="array", @OA\Items())
     *                  }),
     *                  @OA\Property(property="type", type="string"),
     *                  @OA\Property(property="meta", type="object")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Content not found"
     *      )
     * )
     */
    public function item(string $page, string $section, string $key): JsonResponse
    {
        $content = PageContent::byPage($page)
            ->bySection($section)
            ->where('key', $key)
            ->activeContent()
            ->first();

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'page' => $content->page,
                'section' => $content->section,
                'key' => $content->key,
                'content' => $this->transformContent($content),
                'type' => $content->type,
                'meta' => $content->meta,
                'sort_order' => $content->sort_order,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/content/pages",
     *      operationId="getAvailablePages",
     *      tags={"Page Content"},
     *      summary="Get list of available pages",
     *      description="Returns list of all pages that have content",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="pages",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="page", type="string", example="homepage"),
     *                          @OA\Property(property="sections_count", type="integer", example=5),
     *                          @OA\Property(property="content_items_count", type="integer", example=15)
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="page_structure",
     *                      type="object",
     *                      description="Available pages and their sections"
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function pages(): JsonResponse
    {
        $pages = PageContent::selectRaw('page, COUNT(DISTINCT section) as sections_count, COUNT(*) as content_items_count')
            ->activeContent()
            ->groupBy('page')
            ->orderBy('page')
            ->get();

        $pageStructure = PageContent::getPageStructure();

        return response()->json([
            'success' => true,
            'data' => [
                'pages' => $pages,
                'page_structure' => $pageStructure
            ]
        ]);
    }

    /**
     * Transform content based on type with proper image URL handling
     */
    private function transformContent(PageContent $item)
    {
        $rawContent = $item->getRawOriginal('content');

        return match($item->type) {
            'json' => json_decode($rawContent, true),
            'boolean' => filter_var($rawContent, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($rawContent) ? (float) $rawContent : $rawContent,
            'image' => $this->transformImageContent($rawContent),
            'url' => $rawContent,
            default => $rawContent
        };
    }

    /**
     * Transform image content with proper URL handling
     */
    private function transformImageContent(?string $content): ?string
    {
        if (!$content) {
            return null;
        }

        // If it's already a full URL (external), return as-is
        if (str_starts_with($content, 'http://') || str_starts_with($content, 'https://')) {
            return $content;
        }

        // If it's a storage path, prepend the storage URL
        return asset('storage/' . $content);
    }
}
