<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Enums\UserRole;
use App\Exports\ApplicationsDetailedExport;
use App\Exports\ApplicationsSummaryExport;
use App\Filament\Resources\ApplicationResource;
use App\Filament\Widgets\NeedToVerifyEmail;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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
            [$this->getRefreshButton()],
            $this->getExportButtons(),
            parent::getActions(),
        ));
    }

    private function isApplicant()
    {
        return request()->user()->role === UserRole::Applicant;
    }

    private function getRefreshButton()
    {
        return ButtonAction::make('refresh')
            ->label('Refresh')
            ->icon('heroicon-s-refresh')
            ->url(request()->getRequestUri());
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
            ->label('Export Summary')
            ->icon('heroicon-s-document-download')
            ->color('success')
            ->action('export_summary_to_excel')
            ->hidden($this->isApplicant()),
            ButtonAction::make('export_full_data')
            ->label('Export Full Data')
            ->icon('heroicon-s-document-download')
            ->color('success')
            ->action('export_full_data')
            ->hidden($this->isApplicant()),
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

    private function makeApplicationSummaryExport(): ApplicationsSummaryExport
    {
        $filters = $this->getTableFiltersForm()->getState();
        $models = new ModelsFromFilter($filters);

        $recordsByStatuses = $models->getRecordsByStatuses();
        $applications = $this->getExportSummaryRecords();

        return new ApplicationsSummaryExport(
            $models->getTerms(),
            $models->getPrograms(),
            $models->getStatuses(),
            $recordsByStatuses,
            $applications
        );
    }

    public function export_summary_to_excel()
    {
        return Excel::download($this->makeApplicationSummaryExport(), 'applications_summary.xlsx');
    }

    private function makeApplicationSummaryExportToPdf(): string
    {
        $exportId = Str::random(16);
        $filters = $this->getTableFiltersForm()->getState();
        Cache::put("export_summary_to_pdf.{$exportId}", $filters, now()->addMinute());

        return $exportId;
    }

    public function export_summary_to_pdf()
    {
        return redirect()->route('export_summary_to_pdf', [
            'export_id' => $this->makeApplicationSummaryExportToPdf(),
        ]);
    }
}
