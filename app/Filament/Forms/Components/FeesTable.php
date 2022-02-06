<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class FeesTable extends Field
{
    protected string $view = 'filament.forms.components.fees-table';

    protected $fees = [];

    public function setFees($fees)
    {
        $this->fees = $fees;

        return $this;
    }

    public function getFees()
    {
        return $this->evaluate($this->fees);
    }
}
