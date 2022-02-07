<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewApplicationAudit extends Page
{
    use InteractsWithRecord;

    public $record;

    protected static string $resource = ApplicationResource::class;

    protected static string $view = 'filament.resources.application-resource.pages.audit-application';

    public function mount($record): void
    {
        $this->record = $this->getRecord($record);

        // abort_unless(static::getResource()::canView($this->record), 403);
    }

    protected function getActions(): array
    {
        return array_filter(array_merge(
            [
                $this->getBackButton(),
            ],
        ));
    }

    protected function getBackButton()
    {
        return ButtonAction::make('back')
            ->label('Back')
            ->icon('heroicon-s-arrow-left')
            ->url(function () {
                return url()->previous();
            });
    }
}
