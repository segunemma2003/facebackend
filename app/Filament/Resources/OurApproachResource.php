<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OurApproachResource\Pages;
use App\Models\OurApproach;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OurApproachResource extends Resource
{
    protected static ?string $model = OurApproach::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Our Approach';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Approach Step Information')
                    ->schema([
                        Forms\Components\TextInput::make('step')
                            ->label('Step Number')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('title')
                            ->label('Step Title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Step Description')
                            ->rows(4)
                            ->maxLength(1000),
                        Forms\Components\FileUpload::make('image')
                            ->label('Step Image')
                            ->image()
                            ->imageEditor()
                            ->directory('our-approach')
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('step')
                    ->label('Step')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(100),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('step', 'asc')
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
            'index' => Pages\ListOurApproach::route('/'),
            'create' => Pages\CreateOurApproach::route('/create'),
            'edit' => Pages\EditOurApproach::route('/{record}/edit'),
        ];
    }
}
