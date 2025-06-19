<?php
// app/Filament/Resources/ApproachContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ApproachContentResource\Pages;

class ApproachContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Our Approach Page';

    protected static ?int $navigationSort = 6;

    protected static function getPageName(): string
    {
        return 'approach';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'methodology' => 'Our Methodology',
            'process' => 'Our Process',
            'values' => 'Our Values'
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApproachContents::route('/'),
            'create' => Pages\CreateApproachContent::route('/create'),
            'edit' => Pages\EditApproachContent::route('/{record}/edit'),
        ];
    }
}
