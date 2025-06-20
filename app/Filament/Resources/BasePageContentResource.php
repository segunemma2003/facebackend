<?php
namespace App\Filament\Resources;

use App\Models\PageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

abstract class BasePageContentResource extends Resource
{
    protected static ?string $model = PageContent::class;

    protected static ?string $navigationGroup = 'Content Managements';

    // Each child class should define this
    abstract protected static function getPageName(): string;

    // Each child class should define this
    abstract protected static function getPageSections(): array;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('page')
                    ->default(static::getPageName()),

                Forms\Components\Section::make('Content Details')
                    ->schema([
                        Forms\Components\Select::make('section')
                            ->options(static::getPageSections())
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->helperText('Unique identifier for this content (e.g., title, subtitle, image)'),

                        Forms\Components\Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'html' => 'HTML/Rich Text',
                                'image' => 'Image',
                                'json' => 'JSON Data',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order of display (lower numbers first)'),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Content')
                    ->schema([
                        // Text content
                        Forms\Components\Textarea::make('content')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'text')
                            ->required(fn (Forms\Get $get) => $get('type') === 'text'),

                        // HTML content
                        Forms\Components\RichEditor::make('content')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'html')
                            ->required(fn (Forms\Get $get) => $get('type') === 'html'),

                        // Image upload
                        Forms\Components\FileUpload::make('content')
                            ->image()
                            ->directory('page-content/' . static::getPageName())
                            ->visibility('public')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image'),

                        // JSON content
                        Forms\Components\Textarea::make('content')
                            ->rows(8)
                            ->helperText('Enter valid JSON data')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json'),

                        // Meta data
                        Forms\Components\KeyValue::make('meta')
                            ->helperText('Additional metadata (alt text, captions, etc.)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('page', static::getPageName()))
            ->columns([
                Tables\Columns\TextColumn::make('section')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => static::getPageSections()[$state] ?? $state),

                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'html' => 'info',
                        'image' => 'success',
                        'json' => 'warning',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('content')
                    ->limit(50)
                    ->wrap()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'image') {
                            return 'Image: ' . basename($state);
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section')
                    ->options(static::getPageSections()),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text' => 'Text',
                        'html' => 'HTML',
                        'image' => 'Image',
                        'json' => 'JSON Data',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            ->defaultSort('section');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['key', 'content'];
    }
}
