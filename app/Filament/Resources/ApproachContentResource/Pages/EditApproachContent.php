<?php

namespace App\Filament\Resources\ApproachContentResource\Pages;

use App\Filament\Resources\ApproachContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApproachContent extends EditRecord
{
    protected static string $resource = ApproachContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
