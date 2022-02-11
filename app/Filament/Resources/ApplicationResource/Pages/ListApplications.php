<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Filament\Widgets\NeedToVerifyEmail;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            NeedToVerifyEmail::class
        ];
    }
}
