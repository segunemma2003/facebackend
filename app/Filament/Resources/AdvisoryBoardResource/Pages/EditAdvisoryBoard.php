<?php

namespace App\Filament\Resources\AdvisoryBoardResource\Pages;

use App\Filament\Resources\AdvisoryBoardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvisoryBoard extends EditRecord
{
    protected static string $resource = AdvisoryBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
