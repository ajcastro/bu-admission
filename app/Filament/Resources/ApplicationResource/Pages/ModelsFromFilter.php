<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\DTO\RecordByStatus;
use App\Models\Application;
use App\Models\Program;
use App\Models\Term;
use Illuminate\Support\Arr;

class ModelsFromFilter
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function getTerms()
    {
        if ($ids = Arr::get($this->filters, 'term.values')) {
            return Term::find($ids);
        }

        return Term::get();
    }

    public function getPrograms()
    {
        if ($ids = Arr::get($this->filters, 'program.values')) {
            return Program::find($ids);
        }

        return Program::get();
    }

    public function getStatuses()
    {
        if ($ids = Arr::get($this->filters, 'status.values')) {
            return collect($ids);
        }

        return collect(['Pending', 'Recommended', 'Admitted', 'Processed', 'Rejected']);
    }

    public function getRecordsByStatuses()
    {
        return $this->getPrograms()->map(function ($program) {
            $statuses = $this->getStatuses()->map(fn ($status) => strtolower($status))
                ->mapWithKeys(function ($status) use ($program) {
                    return ["{$status}_count" => $this->countByStatus($program, $status)];
                });

            return new RecordByStatus($program, $statuses);
        });
    }

    protected function countByStatus($program, $status)
    {
        return Application::query()
            ->whereIn('term_id', $this->getTerms()->pluck('id'))
            ->where('program_id', $program->id)
            ->where('status', ucfirst($status))
            ->count();
    }
}
