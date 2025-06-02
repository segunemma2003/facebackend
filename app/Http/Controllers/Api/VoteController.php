<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nominee;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Voting",
 *     description="API Endpoints for Voting"
 * )
 */
class VoteController extends Controller
{

     /**
     * @OA\Post(
     *      path="/api/v1/votes/{nominee_id}",
     *      operationId="voteForNominee",
     *      tags={"Voting"},
     *      summary="Vote for a nominee",
     *      description="Cast a vote for a specific nominee",
     *      @OA\Parameter(
     *          name="nominee_id",
     *          description="Nominee ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="ip_address", type="string", example="192.168.1.1", description="Voter IP address")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Vote recorded successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Vote recorded successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="nominee_id", type="integer", example=1),
     *                  @OA\Property(property="new_vote_count", type="integer", example=25),
     *                  @OA\Property(property="new_percentage", type="number", format="float", example=15.5)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Voting is currently closed for this category."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="voting",
     *                      type="array",
     *                      @OA\Items(type="string", example="You have already voted for this nominee.")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Nominee not found"
     *      )
     * )
     */
    public function vote(Request $request, Nominee $nominee): JsonResponse
    {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $ipAddress = $request->ip_address ?? $request->ip();

        // Check if category voting is open
        if (!$nominee->category->is_voting_active) {
            throw ValidationException::withMessages([
                'voting' => ['Voting is currently closed for this category.']
            ]);
        }

        // Check if nominee can receive votes
        if (!$nominee->can_vote) {
            throw ValidationException::withMessages([
                'voting' => ['This nominee is not eligible for voting.']
            ]);
        }

        // Check if user has already voted for this nominee
        if ($nominee->hasUserVoted($ipAddress)) {
            throw ValidationException::withMessages([
                'voting' => ['You have already voted for this nominee.']
            ]);
        }

        // Create vote record
        Vote::create([
            'nominee_id' => $nominee->id,
            'ip_address' => $ipAddress,
            'user_agent' => $request->userAgent(),
        ]);

        // Update nominee vote count
        $nominee->increment('votes');

        // Update voting percentages for all nominees in this category
        $this->updateCategoryVotingPercentages($nominee->category_id);

        return response()->json([
            'success' => true,
            'message' => 'Vote recorded successfully',
            'data' => [
                'nominee_id' => $nominee->id,
                'new_vote_count' => $nominee->fresh()->votes,
                'new_percentage' => $nominee->fresh()->voting_percentage,
            ]
        ]);
    }

    public function hasVoted(Request $request, Nominee $nominee): JsonResponse
    {
        $ipAddress = $request->ip_address ?? $request->ip();
        $hasVoted = $nominee->hasUserVoted($ipAddress);

        return response()->json([
            'success' => true,
            'data' => [
                'has_voted' => $hasVoted,
                'nominee_id' => $nominee->id,
            ]
        ]);
    }

    public function categoryVotes(Request $request): JsonResponse
    {
        $ipAddress = $request->ip_address ?? $request->ip();

        $votedNominees = Vote::where('ip_address', $ipAddress)
            ->with('nominee.category')
            ->get()
            ->groupBy('nominee.category.id')
            ->map(function ($votes, $categoryId) {
                return [
                    'category_id' => $categoryId,
                    'category_name' => $votes->first()->nominee->category->name,
                    'voted_nominees' => $votes->pluck('nominee_id')->toArray(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $votedNominees
        ]);
    }

    private function updateCategoryVotingPercentages(int $categoryId): void
    {
        $nominees = Nominee::where('category_id', $categoryId)->get();
        $totalVotes = $nominees->sum('votes');

        if ($totalVotes > 0) {
            foreach ($nominees as $nominee) {
                $nominee->voting_percentage = ($nominee->votes / $totalVotes) * 100;
                $nominee->save();
            }
        }
    }
}
