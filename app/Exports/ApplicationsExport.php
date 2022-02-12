<?php

namespace App\Exports;

use App\Enums\ApproverAction;
use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicationsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $applications;

    const DATETIME_FORMAT = 'm/d/Y H:i';

    public function __construct($applications)
    {
        $this->applications = $applications;
    }

    public function headings(): array
    {
        return [
            'Application ID',
            'Applicant',
            'Program',
            'Term',
            'Status',
            'Total Units',
            'Created At',

            'Program Adviser',
            'Endorsed?',
            'Endorsed/Rejected At',

            'Dean',
            'Admitted?',
            'Admitted/Rejected At',

            'Registrar',
            'Processed At',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->applications->map(function (Application $item) {
            return [
                'application_id' => $item->id,
                'applicant' => $item->applicant_name,
                'program' => $item->program->label,
                'term' => $item->term->label,
                'status' => $item->status,
                'total_units' => $item->total_units,
                'created_at' => $item->created_at->format(static::DATETIME_FORMAT),

                'program_adviser' => ($approver = $item->getApproverByAction(ApproverAction::RECOMMEND))->user->name,
                'endorsed' => $approver->approved_at
                    ? 'ENDORSED'
                    : ($approver->rejected_at
                        ? 'REJECTED'
                        : ''),
                'endorse_datetime' => optional($approver->approved_at ?? $approver->rejected_at)->format(static::DATETIME_FORMAT),

                // 'endorsed_at' => optional($approver->approved_at)->format(static::DATETIME_FORMAT),
                // 'endorse_rejected_at' => optional($approver->rejected_at)->format(static::DATETIME_FORMAT),

                'dean' => ($approver = $item->getApproverByAction(ApproverAction::ADMIT))->user->name,
                'admitted' => $approver->approved_at
                    ? 'ADMITTED'
                    : ($approver->rejected_at
                        ? 'REJECTED'
                        : ''),
                'admit_datetime' => optional($approver->approved_at ?? $approver->rejected_at)->format(static::DATETIME_FORMAT),
                // 'admitted_at' => optional($approver->approved_at)->format(static::DATETIME_FORMAT),
                // 'admit_rejected_at' => optional($approver->rejected_at)->format(static::DATETIME_FORMAT),

                'registrar' => ($approver = $item->getApproverByAction(ApproverAction::PROCESS))->user->name,
                'processed_datetime' => optional($approver->approved_at)->format(static::DATETIME_FORMAT),
            ];
        });
    }
}
