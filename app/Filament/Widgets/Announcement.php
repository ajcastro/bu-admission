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

    protected function getViewData(): array
    {
        return [
            'items' => $this->getItems(),
        ];
    }

    private function getItems()
    {
        return collect([
            [
                'status' => $status = ApplicationStatus::PENDING,
                'count' => Application::accessibleBy(auth()->user())->where('status', $status)->count(),
                'color' => 'orange',
            ],
            [
                'status' => $status = ApplicationStatus::RECOMMENDED,
                'count' => Application::accessibleBy(auth()->user())->where('status', $status)->count(),
                'color' => 'green',
            ],
            [
                'status' => $status = ApplicationStatus::ADMITTED,
                'count' => Application::accessibleBy(auth()->user())->where('status', $status)->count(),
                'color' => 'green',
            ],
            [
                'status' => $status = ApplicationStatus::PROCESSED,
                'count' => Application::accessibleBy(auth()->user())->where('status', $status)->count(),
                'color' => 'green',
            ],
            [
                'status' => $status = ApplicationStatus::REJECTED,
                'count' => Application::accessibleBy(auth()->user())->where('status', $status)->count(),
                'color' => 'red',
            ],
        ])->map(function ($item) {
            return new ApplicationSummaryItem($item['status'], $item['count'], $item['color']);
        });
    }
}
