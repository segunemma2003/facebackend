<?php

namespace App\Filament\Resources\SuccessStoriesResource\Pages;

use App\Filament\Resources\SuccessStoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuccessStories extends ListRecords
{
    protected static string $resource = SuccessStoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
