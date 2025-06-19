<?php

namespace App\Filament\Resources\PastWinnersContentResource\Pages;

use App\Filament\Resources\PastWinnersContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPastWinnersContent extends EditRecord
{
    protected static string $resource = PastWinnersContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
