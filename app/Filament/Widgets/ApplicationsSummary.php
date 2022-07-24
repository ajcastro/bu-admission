<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Filament\DTO\ApplicationSummaryItem;
use App\Models\Application;
use App\Models\Term;
use Filament\Widgets\Widget;

class ApplicationsSummary extends Widget
{
    protected static string $view = 'filament.widgets.applications-summary';

    protected static ?int $sort = 4;

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

    private function baseQuery()
    {
        $activeTerm = Term::getActive();

        return Application::accessibleBy(auth()->user())
            ->where('term_id', $activeTerm->id);
    }

    private function getItems()
    {
        return collect([
            [
                'status' => $status = ApplicationStatus::PENDING,
                'count' => $this->baseQuery()->where('status', $status)->count(),
                'color' => 'orange',
            ],
            [
                'status' => $status = ApplicationStatus::RECOMMENDED,
                'count' => $this->baseQuery()->where('status', $status)->count(),
                'color' => 'green',
            ],
            [
                'status' => $status = ApplicationStatus::ADMITTED,
                'count' => $this->baseQuery()->where('status', $status)->count(),
                'color' => 'green',
            ],
            [
                'status' => $status = ApplicationStatus::PROCESSED,
                'count' => $this->baseQuery()->where('status', $status)->count(),
                'color' => 'green',
            ],
            [
                'status' => $status = ApplicationStatus::REJECTED,
                'count' => $this->baseQuery()->where('status', $status)->count(),
                'color' => 'red',
            ],
        ])->map(function ($item) {
            return new ApplicationSummaryItem($item['status'], $item['count'], $item['color']);
        });
    }
}
