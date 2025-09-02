<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvisoryBoardResource\Pages;
use App\Models\AdvisoryBoard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvisoryBoardResource extends Resource
{
    protected static ?string $model = AdvisoryBoard::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Advisory Board';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Advisory Board Member Information')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Profile Image')
                            ->image()
                            ->imageEditor()
                            ->directory('advisory-board')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('title')
                            ->label('Title/Position')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('region')
                            ->label('Region')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('expertise')
                            ->label('Area of Expertise')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('region')
                    ->label('Region')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expertise')
                    ->label('Expertise')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_image')
                    ->query(fn($query) => $query->whereNotNull('image')),
                Tables\Filters\SelectFilter::make('region')
                    ->options(fn() => AdvisoryBoard::distinct()->pluck('region', 'region')->toArray()),
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
            'index' => Pages\ListAdvisoryBoard::route('/'),
            'create' => Pages\CreateAdvisoryBoard::route('/create'),
            'edit' => Pages\EditAdvisoryBoard::route('/{record}/edit'),
        ];
    }
}
