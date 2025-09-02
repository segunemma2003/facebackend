<?php

namespace App\Filament\Resources\SuccessStoriesResource\Pages;

use App\Filament\Resources\SuccessStoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuccessStories extends EditRecord
{
    protected static string $resource = SuccessStoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
