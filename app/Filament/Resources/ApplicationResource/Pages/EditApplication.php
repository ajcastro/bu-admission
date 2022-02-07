<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    use Traits\HasViewAuditButton;

    protected static string $resource = ApplicationResource::class;

    protected function getActions(): array
    {
        return array_filter(array_merge(
            [
                $this->getViewAuditButton(),
            ],
            parent::getActions(),
        ));
    }
}
