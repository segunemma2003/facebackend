<?php

namespace App\Filament\Resources\ProjectsContentResource\Pages;

use App\Filament\Resources\ProjectsContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectsContent extends EditRecord
{
    protected static string $resource = ProjectsContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
