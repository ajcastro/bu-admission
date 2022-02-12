<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Filament\Widgets\NeedToVerifyEmail;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected $queryString = [
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
        'tableFilters',
    ];

    protected function getHeaderWidgets(): array
    {
        return [
            NeedToVerifyEmail::class
        ];
    }

    protected function getActions(): array
    {
        return array_filter(array_merge(
            [
                $this->getExportToExcelButton(),
            ],
            parent::getActions(),
        ));
    }

    private function getExportToExcelButton()
    {
        $user = auth()->user();

        return ButtonAction::make('approve')
            ->label('Export to Excel')
            ->icon('heroicon-s-document-download')
            ->color('success')
            ->action(function () {
            })
            ->hidden(false);
    }
}
