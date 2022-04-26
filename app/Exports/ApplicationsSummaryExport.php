<?php

namespace App\Exports;

use App\Enums\ApproverAction;
use App\Models\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicationsSummaryExport implements FromView, ShouldAutoSize
{
    protected $terms;
    protected $programs;
    protected $statuses;
    protected $recordsByStatuses;
    protected $applications;

    public function __construct($terms, $programs, $statuses, $recordsByStatuses, $applications)
    {
        $this->terms = $terms;
        $this->programs = $programs;
        $this->statuses = $statuses;
        $this->recordsByStatuses = $recordsByStatuses;
        $this->applications = $applications;
    }

    public function view(): View
    {
        return view('exports.applications_summary', [
            'terms' => $this->terms,
            'programs' => $this->programs,
            'statuses' => $this->statuses,
            'recordsByStatuses' => $this->recordsByStatuses,
            'applications' => $this->applications,
        ]);
    }
}
