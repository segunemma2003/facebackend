<?php

namespace App\Filament\Widgets;

use App\Models\Nominee;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopNominees extends BaseWidget
{
    protected static ?string $heading = 'Top Nominees by Votes';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Nominee::query()
                    ->with('category')
                    ->where('year', date('Y'))
                    ->orderBy('votes', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('profile_image') // Changed from 'image_url'
                    ->label('Photo')
                    ->disk('public')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn ($record) => $record->image_url), // Uses accessor as fallback
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organization')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge(),
                Tables\Columns\TextColumn::make('votes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('voting_percentage')
                    ->label('Percentage')
                    ->numeric()
                    ->suffix('%'),
            ]);
    }
}
