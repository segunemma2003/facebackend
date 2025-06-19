<?php
// app/Filament/Resources/FooterContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterContentResource\Pages;

class FooterContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?string $navigationLabel = 'Footer Content';

    protected static ?int $navigationSort = 9;

    protected static function getPageName(): string
    {
        return 'footer';
    }

    protected static function getPageSections(): array
    {
        return [
            'links' => 'Footer Links',
            'social' => 'Social Media',
            'contact' => 'Contact Information',
            'copyright' => 'Copyright & Legal'
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFooterContents::route('/'),
            'create' => Pages\CreateFooterContent::route('/create'),
            'edit' => Pages\EditFooterContent::route('/{record}/edit'),
        ];
    }
}
