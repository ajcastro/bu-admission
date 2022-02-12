<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Filament\Widgets\Widget;

class ApplicationsPending extends Widget
{
    protected static string $view = 'filament.widgets.applications-status-number';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return true;
    }

    protected function getViewData(): array
    {
        return [
            'status' => $status = ApplicationStatus::PENDING,
            'count' => Application::accessibleBy(auth()->user())->where('status', $status)->count(),
            'color' => 'orange',
        ];
    }
}
