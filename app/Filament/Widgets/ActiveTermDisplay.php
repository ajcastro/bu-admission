<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ActiveTermDisplay extends Widget
{
    protected static string $view = 'filament.widgets.active-term-display';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return true;
    }
}
