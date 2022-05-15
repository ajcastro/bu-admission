<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\ApproverAction;
use App\Enums\UserRole;
use App\Filament\Forms\Components\FeesTable;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Forms\Components\FilesDownloader;
use App\Models\Application;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
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
use Filament\Tables\Actions\LinkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\TemporaryUploadedFile;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Application')
                    ->tabs([
                        Tabs\Tab::make('Applicant Information')
                            ->schema(static::applicantInformationFields()),
                        Tabs\Tab::make('Upload Requirements')
                            ->schema(static::uploadRequirementsFields()),
                        Tabs\Tab::make('Subject Selection')
                            ->schema(static::subjectSelectionFields()),
                        // Tabs\Tab::make('Fees')
                        //     ->schema(static::feesFields()),
                    ])
                    ->columnSpan(2),

                Forms\Components\Card::make()
                    ->schema(array_merge([
                        Forms\Components\Placeholder::make('application_id')
                            ->label('Application ID')
                            ->content(function (?Application $record = null) {
                                return $record->id ?? '';
                            }),
                        Forms\Components\Placeholder::make('status')
                            ->content(function (?Application $record = null) {
                                $record = $record ?? new Application;
                                return new HtmlString("<span style='color: {$record->status_color}'> {$record->status} </span>");
                            }),
                        Forms\Components\Placeholder::make('term')
                            ->content(function (?Application $record = null) {
                                return $record
                                    ? $record->term->label
                                    : Term::getActive()->label;
                            }),
                        Forms\Components\Placeholder::make('total_units')
                            ->content(function (?Application $record = null) {
                                $record = $record ?? new Application;
                                return number_format($record->getTotalUnits(), 2);
                            }),
                    ], static::getApproverFields()))
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    private static function uploadRequirementsFields()
    {
        return [
            Forms\Components\FileUpload::make('requirements')
                ->multiple()
                ->disk('public')
                ->enableReordering()
                ->imagePreviewHeight('250')
                ->required()
                ->disabled(function (?Application $record) {
                    if (is_null($record)) {
                        return false;
                    }

                    return $record->status !== ApplicationStatus::PENDING;
                })
                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                    $baseFilename = head(explode('.', $file->getClientOriginalName()));
                    return (string) Str::of($baseFilename)
                        ->append('-'.Str::random(8))
                        ->append('.'.$file->getClientOriginalExtension());
                }),
            FilesDownloader::make('download_files')
                ->hidden(function (?Application $record) {
                    return !$record;
                }),
        ];
    }

    private static function getApproverFields()
    {
        return [
            Forms\Components\Placeholder::make('recommending_approval')
                ->label('Recommending Approval')
                ->content(function (?Application $record = null) {
                    if (!$record) return;

                    $approver = $record->getApproverByAction(ApproverAction::RECOMMEND);
                    return new HtmlString(collect([
                        $approver->user->name,
                        $approver->approved_at
                            ? 'RECOMMENDED'
                            : ($approver->rejected_at
                                ? 'REJECTED'
                                : ''),
                        optional($approver->approved_at ?? $approver->rejected_at)->format('m/d/Y h:i a'),
                        $approver->remarks ? 'Remarks: '.$approver->remarks : '',
                    ])->implode('<br> '));
                }),

            Forms\Components\Placeholder::make('approval')
                ->label('Approval')
                ->content(function (?Application $record = null) {
                    if (!$record) return;

                    $approver = $record->getApproverByAction(ApproverAction::ADMIT);
                    return new HtmlString(collect([
                        $approver->user->name,
                        $approver->approved_at
                            ? 'APPROVED'
                            : ($approver->rejected_at
                                ? 'REJECTED'
                                : ''),
                        optional($approver->approved_at ?? $approver->rejected_at)->format('m/d/Y h:i a'),
                        $approver->remarks ? 'Remarks: '.$approver->remarks : '',
                    ])->implode('<br> '));
                }),

            Forms\Components\Placeholder::make('processed by')
                ->label('Processed by:')
                ->content(function (?Application $record = null) {
                    if (!$record) return;

                    $approver = $record->getApproverByAction(ApproverAction::PROCESS);
                    return new HtmlString(collect([
                        $approver->user->name,
                        $approver->approved_at
                            ? 'PROCESSED'
                            : ($approver->rejected_at
                                ? 'REJECTED'
                                : ''),
                        optional($approver->approved_at ?? $approver->rejected_at)->format('m/d/Y h:i a'),
                        $approver->remarks ? 'Remarks: '.$approver->remarks : '',
                    ])->implode('<br> '));
                }),
        ];
    }

    private static function applicantInformationFields()
    {
        return [
            Fieldset::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255)
                        ->default(function () {
                            if (auth()->user()->role === UserRole::Applicant) {
                                return auth()->user()->last_name;
                            }
                        }),
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255)
                        ->default(function () {
                            if (auth()->user()->role === UserRole::Applicant) {
                                return auth()->user()->first_name;
                            }
                        }),
                    Forms\Components\TextInput::make('middle_name')
                        ->maxLength(255)
                        ->default(function () {
                            if (auth()->user()->role === UserRole::Applicant) {
                                return auth()->user()->middle_name;
                            }
                        }),
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
                        ->maxLength(255)
                        ->required(),
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

    private static function subjectSelectionFields()
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
                    /** @var Collection */
                    $subjects = Subject::query()
                        ->where('program_id', $get('program_id'))
                        ->where('term_id', Term::getActive()->id)
                        ->where('is_enabled', 1)
                        ->get();

                    return $subjects->keyBy('id')->map(function ($subject) {
                        return "{$subject->label} ({$subject->code}), {$subject->units} units, {$subject->professor}";
                    });
                })
                ->saveRelationshipsUsing(function (BelongsToManyCheckboxList $component, ?array $state) {
                    /** @var Collection */ $subjects = Subject::find(collect()->wrap($state), ['id', 'units']);
                    $newState = $subjects->mapWithKeys(fn ($subject) => [$subject->id => ['units' => $subject->units]]);
                    $component->getRelationship()->sync($newState);
                })
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

    private static function feesFields()
    {
        return [
            FeesTable::make('fees')->label('')->setFees(function (?Application $record = null) {
                return Application::getFeesTabulation($record);
            }),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Application ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('applicant_name')->label('Applicant Name')->sortable(['first_name', 'last_name'])->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('program.label')->label('Program')->sortable(),
                Tables\Columns\TextColumn::make('term.label')->label('Term')->sortable(),
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
                Tables\Filters\MultiSelectFilter::make('program')
                    ->relationship('program', 'label')
                    ->hidden(function () {
                        $user = request()->user();
                        return $user->role === UserRole::ProgramAdviser;
                    }),
                Tables\Filters\MultiSelectFilter::make('subjects')
                    ->options(Subject::pluck('label', 'id'))
                    ->query(function (Builder $query, $data): Builder {
                        if (blank($data['values'])) return $query;

                        return $query->whereHas('subjects', function (Builder $query) use ($data) {
                            return $query->whereIn('subjects.id', $data['values']);
                        });
                    })
                    ->hidden(function () {
                        return request()->user()->role === UserRole::ProgramAdviser
                            || request()->user()->role === UserRole::Applicant;
                    }),
                Tables\Filters\MultiSelectFilter::make('term')
                    ->relationship('term', 'label')
                    ->default(function () {
                        return [Term::getActive()->id];
                    }),
                Tables\Filters\MultiSelectFilter::make('status')
                    ->options(ApplicationStatus::asSelectArray())
                    ->default(function () {
                        /** @var User */
                        $user = request()->user();
                        return $user->getDefaultApplicationStatus();
                    }),
            ])
            ->prependActions([
                LinkAction::make('view_audit')
                    ->label('View Audit')
                    ->url(function (Application $record) {
                        return url(route('filament.resources.applications.audit', $record));
                    })
                    ->hidden(function (Application $record) {
                        /** @var User */
                        $user = auth()->user();
                        return $user->cant('viewAudit', $record);
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
            'view' => Pages\ViewApplication::route('/{record}'),
            'audit' => Pages\ViewApplicationAudit::route('/{record}/audit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::accessibleBy(auth()->user());
    }
}
