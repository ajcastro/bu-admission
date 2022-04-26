<?php

namespace App\Filament\DTO;

use App\Models\Program;

class RecordByStatus
{
    public Program $program;

    public function __construct(Program $program, $counts = [])
    {
        $this->program = $program;
        foreach ($counts as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
