<?php

namespace App\Filament\DTO;

class ApplicationSummaryItem
{
    public $status;

    public $count;

    public $color;

    public function __construct($status, $count, $color)
    {
        $this->status = $status;
        $this->count = $count;
        $this->color = $color;
    }
}
