<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Nominee;
use App\Models\Registration;
use App\Models\Vote;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Categories', Category::count())
                ->description('Active award categories')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
            Stat::make('Total Nominees', Nominee::where('year', date('Y'))->count())
                ->description('Current year nominees')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('Total Votes', Vote::count())
                ->description('All time votes')
                ->descriptionIcon('heroicon-m-hand-thumb-up')
                ->color('warning'),
            Stat::make('Event Registrations', Registration::where('status', 'confirmed')->count())
                ->description('Confirmed registrations')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
        ];
    }
}
