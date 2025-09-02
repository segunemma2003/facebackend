<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutSettingsResource\Pages;
use App\Models\AboutSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AboutSettingsResource extends Resource
{
    protected static ?string $model = AboutSettings::class;
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'About Settings';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('About Header')
                    ->schema([
                        Forms\Components\TextInput::make('about_title')
                            ->label('About Title')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('about_sub_title')
                            ->label('About Sub Title')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\TextInput::make('about_number_recipient')
                            ->label('Number of Recipients')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('about_number_countries')
                            ->label('Number of Countries')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('about_number_categories')
                            ->label('Number of Categories')
                            ->numeric()
                            ->minValue(0),
                    ])->columns(3),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\RichEditor::make('about_body')
                            ->label('About Body')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'h4',
                                'h5',
                                'h6',
                                'blockquote',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Mission & Vision')
                    ->schema([
                        Forms\Components\Textarea::make('mission')
                            ->label('Mission')
                            ->rows(4)
                            ->maxLength(1000),
                        Forms\Components\Textarea::make('vision')
                            ->label('Vision')
                            ->rows(4)
                            ->maxLength(1000),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('about_title')
                    ->label('About Title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('about_sub_title')
                    ->label('Sub Title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('about_number_recipient')
                    ->label('Recipients')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('about_number_countries')
                    ->label('Countries')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('about_number_categories')
                    ->label('Categories')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_statistics')
                    ->query(fn($query) => $query->whereNotNull('about_number_recipient')
                        ->orWhereNotNull('about_number_countries')
                        ->orWhereNotNull('about_number_categories')),
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
            'index' => Pages\ListAboutSettings::route('/'),
            'create' => Pages\CreateAboutSettings::route('/create'),
            'edit' => Pages\EditAboutSettings::route('/{record}/edit'),
        ];
    }
}
