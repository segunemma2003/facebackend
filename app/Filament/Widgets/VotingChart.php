<?php

namespace App\Filament\Widgets;

use App\Models\Vote;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VotingChart extends ChartWidget
{
    protected static ?string $heading = 'Voting Activity (Last 7 Days)';

    protected function getData(): array
    {
        $data = Vote::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as votes')
        )
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Votes per day',
                    'data' => $data->pluck('votes')->toArray(),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $data->pluck('date')->map(fn($date) =>
                \Carbon\Carbon::parse($date)->format('M d')
            )->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
