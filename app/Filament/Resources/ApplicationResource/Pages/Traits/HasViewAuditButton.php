<?php

namespace App\Filament\Resources\ApplicationResource\Pages\Traits;

use App\Enums\UserRole;
use Filament\Pages\Actions\ButtonAction;

trait HasViewAuditButton
{
    public function getViewAuditButton()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return ButtonAction::make('view_audit')
            ->label('View Audit')
            ->icon('heroicon-s-zoom-in')
            ->url(route('filament.resources.applications.audit', $this->record))
            ->hidden(
                $user->cant('viewAudit', $this->record)
            );
    }
}
