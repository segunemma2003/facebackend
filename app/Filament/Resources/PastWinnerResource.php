<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PastWinnerResource\Pages;
use App\Models\PastWinner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PastWinnerResource extends Resource
{
    protected static ?string $model = PastWinner::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Awards Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('organization')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('achievement')
                    ->required()
                    ->columnSpanFull(),

                // Add file upload for profile image
                Forms\Components\FileUpload::make('profile_image')
                    ->label('Profile Image')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                        '4:3',
                        '16:9',
                    ])
                    ->directory('past-winners')
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(3072) // 3MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->helperText('Upload a profile photo for the past winner')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('image_url')
                    ->label('Fallback Image URL')
                    ->url()
                    ->helperText('Only used if no image is uploaded above')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->required()
                    ->default(date('Y')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_image') // Changed from 'image_url'
                    ->label('Photo')
                    ->disk('public')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl(fn ($record) => $record->image_url), // Fallback to accessor
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(array_combine(
                        range(date('Y'), date('Y') - 10),
                        range(date('Y'), date('Y') - 10)
                    )),
                Tables\Filters\SelectFilter::make('category')
                    ->options(function () {
                        return PastWinner::distinct()->pluck('category', 'category');
                    }),
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
            ->defaultSort('year', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPastWinners::route('/'),
            'create' => Pages\CreatePastWinner::route('/create'),
            'edit' => Pages\EditPastWinner::route('/{record}/edit'),
        ];
    }
}
