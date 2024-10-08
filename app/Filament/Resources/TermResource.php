<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TermResource\Pages;
use App\Filament\Resources\TermResource\RelationManagers;
use App\Models\Term;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\LinkAction;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('active')->formatStateUsing(function (Term $record) {
                    return $record->is_active ? 'Active' : '-';
                }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->defaultSort('label', 'desc')
            ->filters([
                //
            ])
            ->pushActions([
                LinkAction::make('set_active')
                    ->label('Set Active')
                    ->icon('heroicon-o-check')
                    ->action(function (Term $record) {
                        Term::where('id', '!=', $record->id)->update(['is_active' => 0]);
                        $record->is_active = true;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalSubheading("Are you sure to set this term as active?"),
                LinkAction::make('lock')
                    ->label('Lock')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Term $record) {
                        $record->is_locked = true;
                        $record->save();
                    })
                    ->hidden(function (Term $record) {
                        return $record->is_locked;
                    })
                    ->requiresConfirmation()
                    ->modalSubheading("Are you sure to lock this term?"),
                LinkAction::make('unlock')
                    ->label('Unlock')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Term $record) {
                        $record->is_locked = false;
                        $record->save();
                    })
                    ->hidden(function (Term $record) {
                        return !$record->is_locked;
                    })
                    ->requiresConfirmation()
                    ->modalSubheading("Are you sure to unlock this term?"),
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
            'index' => Pages\ListTerms::route('/'),
            'create' => Pages\CreateTerm::route('/create'),
            'edit' => Pages\EditTerm::route('/{record}/edit'),
        ];
    }
}
