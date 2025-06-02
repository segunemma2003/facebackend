<?php

namespace App\Filament\Resources\GalleryEventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\FileUpload::make('gallery_image')
                    ->label('Gallery Image')
                    ->image()
                    ->imageEditor()
                    ->directory('gallery')
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(5120) // 5MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->helperText('Upload an image for the gallery')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('image_url')
                    ->label('Fallback Image URL')
                    ->url()
                    ->helperText('Only used if no image is uploaded above')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('caption')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->columns([
                 Tables\Columns\ImageColumn::make('gallery_image')
                    ->label('Image')
                    ->disk('public')
                    ->size(60)
                    ->defaultImageUrl(fn ($record) => $record->image_url),
                Tables\Columns\TextColumn::make('caption')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            ->defaultSort('sort_order');
    }
}
