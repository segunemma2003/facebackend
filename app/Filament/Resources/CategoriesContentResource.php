<?php
// app/Filament/Resources/CategoriesContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriesContentResource\Pages;

class CategoriesContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Categories Page';

    protected static ?int $navigationSort = 3;

    protected static function getPageName(): string
    {
        return 'categories';
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
            'index' => Pages\ListCategoriesContents::route('/'),
            'create' => Pages\CreateCategoriesContent::route('/create'),
            'edit' => Pages\EditCategoriesContent::route('/{record}/edit'),
        ];
    }
}
