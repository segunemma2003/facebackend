<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeSettingsResource\Pages;
use App\Models\HomeSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeSettingsResource extends Resource
{
    protected static ?string $model = HomeSettings::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Home Settings';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hero Section')
                    ->schema([
                        Forms\Components\TextInput::make('hero_title')
                            ->label('Hero Title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('hero_description')
                            ->label('Hero Description')
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('Current Program')
                    ->schema([
                        Forms\Components\TextInput::make('current_program_title')
                            ->label('Current Program Title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('current_program_description')
                            ->label('Current Program Description')
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('Coming Soon & Timer')
                    ->schema([
                        Forms\Components\Toggle::make('coming_soon')
                            ->label('Coming Soon')
                            ->default(false),
                        Forms\Components\Toggle::make('timer')
                            ->label('Show Timer')
                            ->default(false)
                            ->live(),
                        Forms\Components\DateTimePicker::make('event_date')
                            ->label('Event Date')
                            ->visible(fn(Get $get) => $get('timer')),
                    ])->columns(3),

                Forms\Components\Section::make('Button Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_button')
                            ->label('Show Button')
                            ->default(false)
                            ->live(),
                        Forms\Components\TextInput::make('button_text')
                            ->label('Button Text')
                            ->visible(fn(Get $get) => $get('is_button'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('button_link')
                            ->label('Button Link')
                            ->visible(fn(Get $get) => $get('is_button'))
                            ->url()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('About Section')
                    ->schema([
                        Forms\Components\TextInput::make('about_title')
                            ->label('About Title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('about_description')
                            ->label('About Description')
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('Face Sections')
                    ->schema([
                        Forms\Components\RichEditor::make('section_face_1')
                            ->label('Section Face 1')
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
                            ]),
                        Forms\Components\RichEditor::make('section_face_2')
                            ->label('Section Face 2')
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
                            ]),
                        Forms\Components\FileUpload::make('section_pics')
                            ->label('Section Pictures')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->directory('home-settings/section-pics'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hero_title')
                    ->label('Hero Title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('coming_soon')
                    ->label('Coming Soon')
                    ->boolean(),
                Tables\Columns\IconColumn::make('timer')
                    ->label('Timer')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_button')
                    ->label('Button')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('coming_soon')
                    ->label('Coming Soon'),
                Tables\Filters\TernaryFilter::make('timer')
                    ->label('Timer'),
                Tables\Filters\TernaryFilter::make('is_button')
                    ->label('Button'),
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
            'index' => Pages\ListHomeSettings::route('/'),
            'create' => Pages\CreateHomeSettings::route('/create'),
            'edit' => Pages\EditHomeSettings::route('/{record}/edit'),
        ];
    }
}
