<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuccessStoriesResource\Pages;
use App\Models\SuccessStories;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SuccessStoriesResource extends Resource
{
    protected static ?string $model = SuccessStories::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Success Stories';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Story Information')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Story Image')
                            ->image()
                            ->imageEditor()
                            ->directory('success-stories')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sub_title')
                            ->label('Sub Title')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sub_header')
                            ->label('Sub Header')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(1000),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sub_title')
                    ->label('Sub Title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('sub_header')
                    ->label('Sub Header')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(100),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_image')
                    ->query(fn($query) => $query->whereNotNull('image')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuccessStories::route('/'),
            'create' => Pages\CreateSuccessStories::route('/create'),
            'edit' => Pages\EditSuccessStories::route('/{record}/edit'),
        ];
    }
}
