<?php
// app/Filament/Resources/GalleryContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryContentResource\Pages;

class GalleryContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Gallery Page';

    protected static ?int $navigationSort = 5;

    protected static function getPageName(): string
    {
        return 'gallery';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'introduction' => 'Introduction',
            'content' => 'Main Content'
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryContents::route('/'),
            'create' => Pages\CreateGalleryContent::route('/create'),
            'edit' => Pages\EditGalleryContent::route('/{record}/edit'),
        ];
    }
}
