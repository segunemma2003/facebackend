<?php

namespace App\Filament\Resources\ProjectsContentResource\Pages;

use App\Filament\Resources\ProjectsContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectsContents extends ListRecords
{
    protected static string $resource = ProjectsContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
