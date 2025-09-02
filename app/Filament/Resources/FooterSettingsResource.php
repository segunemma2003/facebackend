<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterSettingsResource\Pages;
use App\Models\FooterSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FooterSettingsResource extends Resource
{
    protected static ?string $model = FooterSettings::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Footer Settings';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Social Media Links')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_facebook')
                                    ->label('Enable Facebook')
                                    ->default(false)
                                    ->live(),
                                Forms\Components\TextInput::make('facebook_link')
                                    ->label('Facebook Link')
                                    ->url()
                                    ->visible(fn(Forms\Get $get) => $get('is_facebook'))
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_twitter')
                                    ->label('Enable Twitter')
                                    ->default(false)
                                    ->live(),
                                Forms\Components\TextInput::make('twitter_link')
                                    ->label('Twitter Link')
                                    ->url()
                                    ->visible(fn(Forms\Get $get) => $get('is_twitter'))
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_instagram')
                                    ->label('Enable Instagram')
                                    ->default(false)
                                    ->live(),
                                Forms\Components\TextInput::make('instagram_link')
                                    ->label('Instagram Link')
                                    ->url()
                                    ->visible(fn(Forms\Get $get) => $get('is_instagram'))
                                    ->maxLength(255),
                            ]),
                    ])->columns(1),

                Forms\Components\Section::make('Footer Content')
                    ->schema([
                        Forms\Components\Textarea::make('footer_text')
                            ->label('Footer Text')
                            ->rows(4)
                            ->placeholder('Enter footer text or copyright information'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_facebook')
                    ->label('Facebook')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_twitter')
                    ->label('Twitter')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_instagram')
                    ->label('Instagram')
                    ->boolean(),
                Tables\Columns\TextColumn::make('footer_text')
                    ->label('Footer Text')
                    ->limit(50),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_facebook')
                    ->label('Facebook'),
                Tables\Filters\TernaryFilter::make('is_twitter')
                    ->label('Twitter'),
                Tables\Filters\TernaryFilter::make('is_instagram')
                    ->label('Instagram'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListFooterSettings::route('/'),
            'create' => Pages\CreateFooterSettings::route('/create'),
            'edit' => Pages\EditFooterSettings::route('/{record}/edit'),
        ];
    }
}
