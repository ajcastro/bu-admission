<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Filament\Widgets\Widget;

class ApplicationsProcessed extends Widget
{
    protected static string $view = 'filament.widgets.applications-status-number';

    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return true;
    }

    protected function getViewData(): array
    {
        return [
            'status' => $status = ApplicationStatus::PROCESSED,
            'count' => Application::accessibleBy(auth()->user())->where('status', $status)->count(),
            'color' => 'green',
        ];
    }
}
