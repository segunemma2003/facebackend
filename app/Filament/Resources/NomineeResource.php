<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NomineeResource\Pages;
use App\Filament\Resources\NomineeResource\RelationManagers;
use App\Models\Nominee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NomineeResource extends Resource
{
    protected static ?string $model = Nominee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListNominees::route('/'),
            'create' => Pages\CreateNominee::route('/create'),
            'view' => Pages\ViewNominee::route('/{record}'),
            'edit' => Pages\EditNominee::route('/{record}/edit'),
        ];
    }
}
