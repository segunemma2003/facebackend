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

    protected static ?string $navigationGroup = 'Content Management';

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
                            ->helperText('Unique identifier for this content (e.g., title, subtitle, image)')
                            ->live(),

                        Forms\Components\Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'html' => 'HTML/Rich Text',
                                'image' => 'Image',
                                'url' => 'URL',
                                'json' => 'JSON Data',
                                'boolean' => 'Boolean',
                                'number' => 'Number',
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
                        Forms\Components\Textarea::make('text_content')
                            ->label('Text Content')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'text')
                            ->required(fn (Forms\Get $get) => $get('type') === 'text')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrated(false),

                        // HTML content
                        Forms\Components\RichEditor::make('html_content')
                            ->label('HTML Content')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'html')
                            ->required(fn (Forms\Get $get) => $get('type') === 'html')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrated(false),

                        // URL content
                        Forms\Components\TextInput::make('url_content')
                            ->label('URL')
                            ->url()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'url')
                            ->required(fn (Forms\Get $get) => $get('type') === 'url')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrated(false),

                        // Number content
                        Forms\Components\TextInput::make('number_content')
                            ->label('Number')
                            ->numeric()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'number')
                            ->required(fn (Forms\Get $get) => $get('type') === 'number')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrated(false),

                        // Boolean content
                        Forms\Components\Toggle::make('boolean_content')
                            ->label('Boolean Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'boolean')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state(filter_var($record?->getRawOriginal('content') ?? $state, FILTER_VALIDATE_BOOLEAN)))
                            ->dehydrated(false),

                        // Image upload
                        Forms\Components\FileUpload::make('image_content')
                            ->label('Image')
                            ->image()
                            ->directory('page-content/' . static::getPageName())
                            ->visibility('public')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                $content = $record?->getRawOriginal('content');
                                if ($content && !str_starts_with($content, 'http')) {
                                    $component->state($content);
                                }
                            })
                            ->dehydrated(false),

                        // JSON content
                        Forms\Components\Textarea::make('json_content')
                            ->label('JSON Content')
                            ->rows(8)
                            ->helperText('Enter valid JSON data')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                $content = $record?->getRawOriginal('content');
                                if (is_string($content)) {
                                    // Try to decode and re-encode for pretty formatting
                                    $decoded = json_decode($content, true);
                                    if ($decoded !== null) {
                                        $component->state(json_encode($decoded, JSON_PRETTY_PRINT));
                                    } else {
                                        $component->state($content);
                                    }
                                } else {
                                    $component->state($content);
                                }
                            })
                            ->dehydrated(false),

                        // Hidden field to store the actual content
                        Forms\Components\Hidden::make('content')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrateStateUsing(function ($state, Forms\Get $get) {
                                return match ($get('type')) {
                                    'text' => $get('text_content'),
                                    'html' => $get('html_content'),
                                    'url' => $get('url_content'),
                                    'number' => $get('number_content'),
                                    'boolean' => $get('boolean_content') ? 'true' : 'false',
                                    'image' => $get('image_content'),
                                    'json' => $get('json_content'),
                                    default => $state
                                };
                            }),

                        // Meta data
                        Forms\Components\KeyValue::make('meta')
                            ->helperText('Additional metadata (alt text, captions, etc.)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->byPage(static::getPageName()))
            ->columns([
                Tables\Columns\TextColumn::make('section')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => static::getPageSections()[$state] ?? $state)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'html' => 'info',
                        'image' => 'success',
                        'url' => 'warning',
                        'json' => 'danger',
                        'boolean' => 'primary',
                        'number' => 'secondary',
                        default => 'gray'
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('content_preview')
                    ->label('Content')
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->content_preview)
                    ->formatStateUsing(function ($state, $record) {
    return match ($record->type) {
        'image' => $record->getRawOriginal('content') ?
            '🖼️ ' . $this->getImageBasename($record->getRawOriginal('content')) :
            'No image',
        'json' => static::formatJsonPreview($record),
        'boolean' => $record->formatted_content ? '✅ True' : '❌ False',
        'url' => '🔗 ' . ($record->getRawOriginal('content') ?: 'No URL'),
        'html' => '📝 ' . strip_tags($record->getRawOriginal('content') ?: ''),
        default => $record->getRawOriginal('content') ?: 'No content'
    };
}),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section')
                    ->options(static::getPageSections())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text' => 'Text',
                        'html' => 'HTML',
                        'image' => 'Image',
                        'url' => 'URL',
                        'json' => 'JSON Data',
                        'boolean' => 'Boolean',
                        'number' => 'Number',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalContent(fn ($record) => view('filament.pages.content-preview', compact('record'))),

                Tables\Actions\EditAction::make(),

                Tables\Actions\ReplicateAction::make()
                    ->form([
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->helperText('New unique key for the duplicated content'),
                        Forms\Components\Select::make('section')
                            ->options(static::getPageSections())
                            ->required(),
                    ])
                    ->beforeReplicaSaved(function (array $data, $replica): void {
                        $replica->key = $data['key'];
                        $replica->section = $data['section'];
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('toggleActive')
                        ->label('Toggle Active')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => !$record->is_active]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('updateSortOrder')
                        ->label('Update Sort Order')
                        ->icon('heroicon-o-arrows-up-down')
                        ->form([
                            Forms\Components\TextInput::make('sort_order')
                                ->numeric()
                                ->required()
                                ->helperText('All selected records will be updated with this sort order'),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['sort_order' => $data['sort_order']]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('section')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s') // Auto-refresh every 30 seconds
            ->groups([
                Tables\Grouping\Group::make('section')
                    ->label('Section')
                    ->collapsible(),
                Tables\Grouping\Group::make('type')
                    ->label('Content Type')
                    ->collapsible(),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['key', 'content', 'section'];
    }


    private static function getImageBasename(string $content): string
{
    // Handle JSON object format (with UUID)
    if (str_starts_with($content, '{')) {
        $decoded = json_decode($content, true);
        if ($decoded && is_array($decoded)) {
            $filePath = array_values($decoded)[0] ?? '';
            return basename($filePath);
        }
    }

    // Handle JSON array format
    if (str_starts_with($content, '[')) {
        $decoded = json_decode($content, true);
        if ($decoded && is_array($decoded) && !empty($decoded)) {
            return basename($decoded[0]);
        }
    }

    // Handle direct file path
    return basename($content);
}
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::byPage(static::getPageName())->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    // Helper method to get section statistics
    public static function getSectionStats(): array
    {
        $stats = [];
        $sections = static::getPageSections();

        foreach ($sections as $sectionKey => $sectionLabel) {
            $count = static::getModel()::byPage(static::getPageName())
                ->bySection($sectionKey)
                ->activeContent()
                ->count();

            $stats[$sectionKey] = [
                'label' => $sectionLabel,
                'count' => $count,
                'active' => $count > 0
            ];
        }

        return $stats;
    }
}
