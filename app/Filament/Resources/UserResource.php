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
use Filament\Tables\Actions\LinkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 6;

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
                    ->options(\App\Enums\UserRole::asSelectOptions()),
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
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->sortable(),
                // Tables\Columns\TextColumn::make('email_verified_at')
                //     ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\MultiSelectFilter::make('role')
                    ->options(\App\Enums\UserRole::asSelectArray())
            ])
            ->prependActions([
                LinkAction::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (User $record) {
                        $record->email_verified_at = now();
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalSubheading("Are you sure to verify this user?")
                    ->hidden(function (User $record) {
                        /** @var User */
                        $user = auth()->user();
                        return filled($record->email_verified_at) || !$user->isAdministrator();
                    }),
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
