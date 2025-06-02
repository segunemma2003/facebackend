<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nominee;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class VoteController extends Controller
{
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
