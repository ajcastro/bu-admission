<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Exports\ApplicationsDetailedExport;
use App\Exports\ApplicationsSummaryExport;
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
            $this->getExportButtons(),
            parent::getActions(),
        ));
    }

    private function getExportButtons()
    {
        return [
            ButtonAction::make('export_summary_to_pdf')
            ->label('Export Summary (PDF)')
            ->icon('heroicon-s-document-download')
            ->color('danger')
            ->action('export_summary_to_pdf')
            ->hidden(false),
            ButtonAction::make('export_summary_to_excel')
            ->label('Export Summary (Excel)')
            ->icon('heroicon-s-document-download')
            ->color('success')
            ->action('export_summary_to_excel')
            ->hidden(false),
            ButtonAction::make('export_full_data')
            ->label('Export Full Data')
            ->icon('heroicon-s-document-download')
            ->color('success')
            ->action('export_full_data')
            ->hidden(false),
        ];
    }

    /** @see \Filament\Tables\Concerns\HasRecords getTableRecords() */
    private function getExportDetailedRecords()
    {
        $query = $this->getFilteredTableQuery();
        $query->with(['program', 'term', 'approvers.user', 'subjects:id,label,code']);
        $this->applySortingToTableQuery($query);

        return $query->get();
    }

    public function export_full_data()
    {
        return Excel::download(new ApplicationsDetailedExport($this->getExportDetailedRecords()), 'applications.xlsx');
    }

    private function getExportSummaryRecords()
    {
        $query = $this->getFilteredTableQuery();
        $query->with(['program', 'term']);
        $this->applySortingToTableQuery($query);

        return $query->get();
    }

    public function export_summary_to_excel()
    {
        $data = $this->getTableFiltersForm()->getState();
        $models = new ModelsFromFilter($data);

        $recordsByStatuses = $models->getRecordsByStatuses();
        $applications = $this->getExportSummaryRecords();

        $export = new ApplicationsSummaryExport(
            $models->getTerms(),
            $models->getPrograms(),
            $models->getStatuses(),
            $recordsByStatuses,
            $applications
        );

        return Excel::download($export, 'applications_summary.xlsx');
    }
}
