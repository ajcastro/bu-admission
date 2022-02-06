<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    /** @var \App\Models\Application */
    public $record;

    protected function getActions(): array
    {
        return array_filter(array_merge(
            parent::getActions(),
            [
                $this->getApproveButton(),
                $this->getRejectButton(),
                $this->getUndoApprovalButton(),
            ]
        ));
    }

    protected function getApproveButton()
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $approver = $this->record->getCurrentApprover();

        return ButtonAction::make('approve')
            ->label($approver->action_display)
            ->icon('heroicon-s-check')
            ->color('success')
            ->action(function () {
                $this->record->approve($this->record->findApprover(auth()->user()));
                return redirect()->route('filament.resources.applications.view', $this->record);
            })
            ->hidden($user->cant('approve', $this->record))
            ->requiresConfirmation()
            ;
    }

    protected function getRejectButton()
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $approver = $this->record->getCurrentApprover();

        return ButtonAction::make('reject')
            ->label('Reject')
            ->icon('heroicon-s-ban')
            ->color('danger')
            ->action(function () {
                $this->record->reject($this->record->findApprover(auth()->user()));
                return redirect()->route('filament.resources.applications.view', $this->record);
            })
            ->hidden(
                $user->cant('approve', $this->record) ||
                $this->record->status === ApplicationStatus::REJECTED
            )
            ->requiresConfirmation()
            ;
    }

    public function getUndoApprovalButton()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return ButtonAction::make('undo_approval')
            ->label('Undo')
            ->icon('heroicon-s-backspace')
            ->action(function () {
                $this->record->undoApproval($this->record->findApprover(auth()->user()));
                return redirect()->route('filament.resources.applications.view', $this->record);
            })
            ->hidden(
                $user->cant('undoApproval', $this->record)
            )
            ->requiresConfirmation();
    }
}
