<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\FeesTable;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use App\Models\Subject;
use App\Services\Countries;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\BelongsToManyCheckboxList;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Application')
                    ->tabs([
                        Tabs\Tab::make('Applicant Information')
                            ->schema(static::applicantInformationFields()),
                        Tabs\Tab::make('Upload Requirements')
                            ->schema([
                                Forms\Components\FileUpload::make('requirements')
                                    ->multiple()
                                    ->disk('public')
                                    ->image()
                                    ->imagePreviewHeight('250')
                            ]),
                        Tabs\Tab::make('Subject Selection')
                            ->schema(static::subjectSelectionFields()),
                        Tabs\Tab::make('Fees')
                            ->schema(static::feesFields()),
                    ])
                    ->columnSpan(2),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('status')
                            ->content(fn (Application $record)
                            => new HtmlString("<span style='color: {$record->status_color}'> {$record->status} </span>")),
                        Forms\Components\Placeholder::make('total_units')
                            ->content(fn (Application $record) => number_format($record->getTotalUnits(), 2)),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    private static function applicantInformationFields()
    {
        return [
            Fieldset::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('birthdate')
                        ->required(),
                    Forms\Components\Select::make('gender')
                        ->required()
                        ->options([
                            'Male' => 'Male',
                            'Female' => 'Female',
                        ]),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('mobile_number')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone_number')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('work_number')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('company')
                        ->maxLength(255),
                ]),
            Fieldset::make('Residence Address')
                ->schema([
                    Forms\Components\TextInput::make('residence_address_line_1')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('residence_address_line_2')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('residence_municipality')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('residence_province')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('residence_zip_code')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\Select::make('residence_country')
                        ->required()
                        ->options(Countries::asSelectOptions())
                        ->searchable(),
                ]),

            Forms\Components\Toggle::make('same_with_residence_address')
                ->reactive()
                ->afterStateUpdated(function ($get, $set, $state) {
                    if ($state) {
                        $set('permanent_address_line_1', $get('residence_address_line_1'));
                        $set('permanent_address_line_2', $get('residence_address_line_2'));
                        $set('permanent_municipality', $get('residence_municipality'));
                        $set('permanent_province', $get('residence_province'));
                        $set('permanent_zip_code', $get('residence_zip_code'));
                        $set('permanent_country', $get('residence_country'));
                    } else {
                        $set('permanent_address_line_1', '');
                        $set('permanent_address_line_2', '');
                        $set('permanent_municipality', '');
                        $set('permanent_province', '');
                        $set('permanent_zip_code', '');
                        $set('permanent_country', '');
                    }
                }),

            Fieldset::make('Permanent Address')
                ->schema([
                    // Forms\Components\Toggle::make('same_with_residence_address'),
                    Forms\Components\TextInput::make('permanent_address_line_1')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('permanent_address_line_2')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('permanent_municipality')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('permanent_province')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('permanent_zip_code')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\Select::make('permanent_country')
                        ->required()
                        ->options(Countries::asSelectOptions())
                        ->searchable(),
                ]),
        ];
    }

    public static function subjectSelectionFields()
    {
        return [
            Forms\Components\Select::make('program_id')
                ->label('Program')
                ->required()
                ->options(\App\Models\Program::pluck('label', 'id'))
                ->searchable()
                ->reactive(),
            BelongsToManyCheckboxList::make('subjects')
                ->relationship('subjects', 'label')
                ->options(function (callable $get) {
                    /** @var Collection */ $subjects = Subject::where('program_id', $get('program_id'))->get();
                    return $subjects->keyBy('id')->map(function ($subject) {
                        return "{$subject->label} ({$subject->code}), {$subject->units} units, {$subject->professor}";
                    });
                })
                ->saveRelationshipsUsing(function (BelongsToManyCheckboxList $component, ?array $state) {
                    /** @var Collection */ $subjects = Subject::find(collect()->wrap($state), ['id', 'units']);
                    $newState = $subjects->mapWithKeys(fn ($subject) => [$subject->id => ['units' => $subject->units]]);
                    $component->getRelationship()->sync($newState);
                })
                ->reactive()
                ->required()
                ->rules([
                    function (callable $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $subjects = Subject::find(collect()->wrap($get('subjects')), ['id', 'units']);
                            $max = Subject::MAX_UNITS;
                            if ($subjects->sum('units') > $max) {
                                $fail("The maximum units can be taken is {$max} units only.");
                            }
                        };
                    }
                ])
        ];
    }

    public static function feesFields()
    {
        return [
            FeesTable::make('fees')->label('')->setFees(function (Application $record) {
                return $record->getFeesTabulation();
            }),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant_name')->label('Applicant Name')->sortable(['first_name', 'last_name'])->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('program.label')->label('Program')->sortable(),
                Tables\Columns\TextColumn::make('status')->sortable(),
                Tables\Columns\TextColumn::make('total_units')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y h:i a')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y h:i a')
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
