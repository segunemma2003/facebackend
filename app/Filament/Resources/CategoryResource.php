<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Awards Management';
    protected static ?int $navigationSort = 1;




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Category Information')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('region')
                                    ->options([
                                        'Global' => 'Global',
                                        'Americas' => 'Americas',
                                        'Europe' => 'Europe',
                                        'Africa' => 'Africa',
                                        'Asia-Pacific' => 'Asia-Pacific',
                                        'Middle East' => 'Middle East',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('icon')
                                    ->options([
                                        'trophy' => 'Trophy',
                                        'award' => 'Award',
                                        'star' => 'Star',
                                        'globe' => 'Globe',
                                        'users' => 'Users',
                                        'heart' => 'Heart',
                                        'lightbulb' => 'Lightbulb',
                                        'academic-cap' => 'Academic Cap',
                                    ])
                                    ->required(),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('Background Color')
                                    ->default('#f0f9ff'),

                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ]),
                             Tabs\Tab::make('Media')
                            ->schema([
                                Forms\Components\FileUpload::make('featured_image')
                                    ->label('Featured Image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->directory('categories')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->maxSize(5120) // 5MB
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->helperText('Upload a featured image for this category (max 5MB, recommended: 1200x675px)')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('image_url')
                                    ->label('Fallback Image URL')
                                    ->url()
                                    ->helperText('Only used if no image is uploaded above')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Voting Configuration')
                            ->schema([
                                Forms\Components\Toggle::make('voting_open')
                                    ->label('Voting Open')
                                    ->live(),
                                Forms\Components\DateTimePicker::make('voting_starts_at')
                                    ->label('Voting Starts')
                                    ->visible(fn (callable $get) => $get('voting_open')),
                                Forms\Components\DateTimePicker::make('voting_ends_at')
                                    ->label('Voting Ends')
                                    ->visible(fn (callable $get) => $get('voting_open')),
                            ]),
                        Tabs\Tab::make('Criteria')
                            ->schema([
                                Forms\Components\Repeater::make('criteria')
                                    ->schema([
                                        Forms\Components\Textarea::make('criterion')
                                            ->label('Criterion')
                                            ->required()
                                            ->rows(2),
                                    ])
                                    ->addActionLabel('Add Criterion')
                                    ->defaultItems(1)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\ImageColumn::make('featured_image')
                    ->disk('public')
                    ->size(60)
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->image_url),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('region')
                    ->badge(),
                Tables\Columns\TextColumn::make('nominees_count')
                    ->label('Nominees')
                    ->counts('nominees'),
                Tables\Columns\IconColumn::make('voting_open')
                    ->label('Voting')
                    ->boolean(),
                Tables\Columns\TextColumn::make('voting_ends_at')
                    ->label('Voting Ends')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region')
                    ->options([
                        'Global' => 'Global',
                        'Americas' => 'Americas',
                        'Europe' => 'Europe',
                        'Africa' => 'Africa',
                        'Asia-Pacific' => 'Asia-Pacific',
                        'Middle East' => 'Middle East',
                    ]),
                Tables\Filters\TernaryFilter::make('voting_open'),
                Tables\Filters\TernaryFilter::make('is_active'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
