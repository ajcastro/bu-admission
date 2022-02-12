<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AccountWidget extends Widget
{
    protected static ?int $sort = 1;

    protected static string $view = 'filament::widgets.account-widget';

    public static function canView(): bool
    {
        return true;
    }
}
