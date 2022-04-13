<?php

namespace App\Listeners\ApplicationStatus;

use App\Events\ApplicationApproved;
use App\Events\ApplicationRejected;
use App\Models\Application;
use App\Models\Approver;
use App\Models\User;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyApplicant
{
    public function __construct()
    {
        //
    }

    public function handle(ApplicationApproved|ApplicationRejected $event)
    {
        /** @var Application */
        $application = $event->application;
        /** @var Approver */
        $approver = $event->approver;
        /** @var User */
        $user = $application->user;

        $user->notify(
            new ApplicationStatusNotification($application, $approver, $event instanceof ApplicationRejected)
        );
    }
}
