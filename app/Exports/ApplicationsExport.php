<?php

namespace App\Exports;

use App\Enums\ApproverAction;
use App\Models\Application;
use Illuminate\Support\Carbon;
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
            'Last Name',
            'First Name',
            'Middle Name',
            'Birthdate',
            'Gender',
            'Email',
            'Mobile Number',
            'Phone Number',
            'Work Number',
            'Company',
            'Residence Address Line 1',
            'Residence Address Line 2',
            'Residence Municipality',
            'Residence Province',
            'Residence Zip Code',
            'Residence Country',
            'Same with Residence Address?',
            'Permanent Address Line 1',
            'Permanent Address Line 2',
            'Permanent Municipality',
            'Permanent Province',
            'Permanent Zip Code',
            'Permanent Country',

            'Program',
            'Term',
            'Status',
            'Total Units',
            'Created At',
            'Last Updated At',

            'Program Adviser',
            'Recommended?',
            'Recommended/Rejected At',

            'Dean',
            'Approved?',
            'Approved/Rejected At',

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
                'last_name' => $item->last_name,
                'first_name' => $item->first_name,
                'middle_name' => $item->middle_name,
                'birthdate' => Carbon::parse($item->birthdate)->format('m/d/Y'),
                'gender' => $item->gender,
                'email' => $item->email,
                'mobile_number' => $item->mobile_number,
                'phone_number' => $item->phone_number,
                'work_number' => $item->work_number,
                'company' => $item->company,
                'residence_address_line_1' => $item->residence_address_line_1,
                'residence_address_line_2' => $item->residence_address_line_2,
                'residence_municipality' => $item->residence_municipality,
                'residence_province' => $item->residence_province,
                'residence_zip_code' => $item->residence_zip_code,
                'residence_country' => $item->residence_country,
                'same_with_residence_address' => $item->same_with_residence_address,
                'permanent_address_line_1' => $item->permanent_address_line_1,
                'permanent_address_line_2' => $item->permanent_address_line_2,
                'permanent_municipality' => $item->permanent_municipality,
                'permanent_province' => $item->permanent_province,
                'permanent_zip_code' => $item->permanent_zip_code,
                'permanent_country' => $item->permanent_country,

                'program' => $item->program->label,
                'term' => $item->term->label,
                'status' => $item->status,
                'total_units' => $item->total_units,
                'created_at' => $item->created_at->format(static::DATETIME_FORMAT),
                'updated_at' => $item->updated_at->format(static::DATETIME_FORMAT),

                'program_adviser' => ($approver = $item->getApproverByAction(ApproverAction::RECOMMEND))->user->name,
                'recommend' => $approver->approved_at
                    ? 'RECOMMENDED'
                    : ($approver->rejected_at
                        ? 'REJECTED'
                        : ''),
                'endorse_datetime' => optional($approver->approved_at ?? $approver->rejected_at)->format(static::DATETIME_FORMAT),

                // 'endorsed_at' => optional($approver->approved_at)->format(static::DATETIME_FORMAT),
                // 'endorse_rejected_at' => optional($approver->rejected_at)->format(static::DATETIME_FORMAT),

                'dean' => ($approver = $item->getApproverByAction(ApproverAction::ADMIT))->user->name,
                'approved' => $approver->approved_at
                    ? 'APPROVED'
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
