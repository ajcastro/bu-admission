<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Filament\DTO\ApplicationSummaryItem;
use App\Models\Application;
use Filament\Widgets\Widget;

class Announcement extends Widget
{
    protected static string $view = 'filament.widgets.announcement';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return true;
    }
}
