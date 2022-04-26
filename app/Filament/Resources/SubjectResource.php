<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Filament\Resources\SubjectResource\RelationManagers;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Term;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\LinkAction;
use Illuminate\Database\Eloquent\Builder;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_id')
                    ->label('Program')
                    ->required()
                    ->options(\App\Models\Program::pluck('label', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('term_id')
                    ->label('Term')
                    ->required()
                    ->options(\App\Models\Term::pluck('label', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('category')
                    ->required()
                    ->options(\App\Enums\SubjectCategory::asSelectArray()),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('label')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('units')
                    ->required(),
                Forms\Components\TextInput::make('professor')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Checkbox::make('is_enabled'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program')->sortable(),
                Tables\Columns\TextColumn::make('term')->sortable(),
                Tables\Columns\TextColumn::make('category')->sortable(),
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('label')->label('Title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('units')->sortable(),
                Tables\Columns\TextColumn::make('professor')->searchable()->sortable(),
                Tables\Columns\BooleanColumn::make('is_enabled')
                    ->label('Enabled')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\MultiSelectFilter::make('program')
                    ->relationship('program', 'label'),
                Tables\Filters\MultiSelectFilter::make('category')
                    ->options(\App\Enums\SubjectCategory::asSelectArray()),
            ])
            ->pushActions([
                LinkAction::make('enable')
                    ->label('Enable')
                    ->action(function (Subject $record) {
                        $record->is_enabled = true;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->hidden(function (Subject $record) {
                        return $record->is_enabled;
                    })
                    ->modalSubheading("Are you sure to enable this subject?"),
                LinkAction::make('disable')
                    ->label('Disable')
                    ->action(function (Subject $record) {
                        $record->is_enabled = false;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->hidden(function (Subject $record) {
                        return !$record->is_enabled;
                    })
                    ->modalSubheading("Are you sure to disable this subject?"),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->addSelect([
                'program' => Program::query()
                    ->select('label')
                    ->whereColumn('subjects.program_id', 'programs.id'),
                'term' => Term::query()
                    ->select('label')
                    ->whereColumn('subjects.term_id', 'terms.id')
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
