<?php

namespace App\Filament\Resources\PastWinnerResource\Pages;

use App\Filament\Resources\PastWinnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPastWinner extends EditRecord
{
    protected static string $resource = PastWinnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
