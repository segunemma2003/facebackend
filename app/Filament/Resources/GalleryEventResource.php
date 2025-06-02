<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryEventResource\Pages;
use App\Filament\Resources\GalleryEventResource\RelationManagers;
use App\Models\GalleryEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryEventResource extends Resource
{
    protected static ?string $model = GalleryEvent::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('event_date')
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->required()
                    ->default(date('Y')),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('attendees')
                    ->maxLength(255),
                Forms\Components\TextInput::make('highlights')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_featured')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('images_count')
                    ->label('Images')
                    ->counts('images'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(array_combine(
                        range(date('Y'), date('Y') - 5),
                        range(date('Y'), date('Y') - 5)
                    )),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('event_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryEvents::route('/'),
            'create' => Pages\CreateGalleryEvent::route('/create'),
            'edit' => Pages\EditGalleryEvent::route('/{record}/edit'),
        ];
    }
}
