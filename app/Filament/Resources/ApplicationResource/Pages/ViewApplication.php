<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;
use Filament\Forms;

class ViewApplication extends ViewRecord
{
    use Traits\HasViewAuditButton;

    protected static string $resource = ApplicationResource::class;

    /** @var \App\Models\Application */
    public $record;

    protected function getActions(): array
    {
        return array_filter(array_merge(
            [
                $this->getViewAuditButton(),
            ],
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
            ->label($approver->action_display ?? '')
            ->icon('heroicon-s-check')
            ->color('success')
            ->action(function (array $data) {
                $this->record->approve($this->record->getCurrentApprover(), $data['remarks'] ?? '');
                return redirect()->route('filament.resources.applications.view', $this->record);
            })
            ->hidden(
                $this->record->status === ApplicationStatus::REJECTED ||
                is_null($approver) ||
                $user->cant('approve', $this->record)
            )
            ->requiresConfirmation()
            ->form([
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
            ]);
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
                $this->record->reject($this->record->getCurrentApprover());
                return redirect()->route('filament.resources.applications.view', $this->record);
            })
            ->hidden(
                is_null($approver) ||
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
        $lastApprover = $this->record->getLastApprover();

        return ButtonAction::make('undo_approval')
            ->label('Undo Approval')
            ->icon('heroicon-s-backspace')
            ->action(function () {
                $this->record->undoApproval($this->record->getLastApprover());
                return redirect()->route('filament.resources.applications.view', $this->record);
            })
            ->hidden(
                is_null($lastApprover) ||
                $user->cant('undoApproval', $this->record)
            )
            ->requiresConfirmation();
    }
}
