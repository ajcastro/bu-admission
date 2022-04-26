<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Program;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\LinkAction;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('label')
                        ->label('Description')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('recommending_user_id')
                        ->label('Program Adviser')
                        ->required()
                        ->searchable()
                        ->options(User::where('role', UserRole::ProgramAdviser)->pluck('name', 'id')),
                    Forms\Components\Select::make('admitting_user_id')
                        ->label('Dean')
                        ->required()
                        ->searchable()
                        ->options(User::where('role', UserRole::Dean)->pluck('name', 'id')),
                    Forms\Components\Select::make('processing_user_id')
                        ->label('Registrar')
                        ->required()
                        ->searchable()
                        ->options(User::where('role', UserRole::Registrar)->pluck('name', 'id')),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Description')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->pushActions([
                LinkAction::make('view_subjects')
                    ->label('View Subjects')
                    ->url(function (Program $record): string {
                        $params = ['tableFilters' => ['program' => [
                            'values' => [ $record->id]
                        ]]];

                        return route('filament.resources.subjects.index', $params);
                    })
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
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
