<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Exports\ApplicationsExport;
use App\Filament\Resources\ApplicationResource;
use App\Filament\Widgets\NeedToVerifyEmail;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

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
        return ButtonAction::make('export')
            ->label('Export to Excel')
            ->icon('heroicon-s-document-download')
            ->color('success')
            ->action('export')
            ->hidden(false);
    }

    public function export()
    {
        return Excel::download(new ApplicationsExport($this->getExportRecords()), 'applications.xlsx');
    }

    /** @see \Filament\Tables\Concerns\HasRecords getTableRecords() */
    public function getExportRecords()
    {
        $query = $this->getFilteredTableQuery();
        $query->with('approvers.user');
        $this->applySortingToTableQuery($query);

        return $query->get();
    }
}
