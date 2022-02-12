<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Filament\Widgets\Widget;

class ApplicationsRecommended extends Widget
{
    protected static string $view = 'filament.widgets.applications-status-number';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return true;
    }

    protected function getViewData(): array
    {
        return [
            'status' => $status = ApplicationStatus::RECOMMENDED,
            'count' => Application::accessibleBy(auth()->user())->where('status', $status)->count(),
            'color' => 'green',
        ];
    }
}
