<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->required()
                    ->options(\App\Enums\UserRole::asSelectArray()),
                Forms\Components\Grid::make()->columnSpan('full')->schema([
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required(static::formIsCreating())
                        ->maxLength(255)
                        ->same('password_confirmation')
                        ->dehydrated(fn ($state): bool => (bool) ($state))
                        ->dehydrateStateUsing(function ($state) {
                            return bcrypt($state);
                        }),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->password()
                        ->required(static::formIsCreating())
                        ->maxLength(255),
                ])
            ]);
    }

    public static function formIsCreating()
    {
        return function (?User $record) {
            return is_null($record);
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role')->sortable(),
                // Tables\Columns\TextColumn::make('email_verified_at')
                //     ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\MultiSelectFilter::make('role')
                    ->options(\App\Enums\UserRole::asSelectArray())
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
