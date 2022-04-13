<?php

namespace App\Events;

use App\Models\Application;
use App\Models\Approver;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Application $application;
    public Approver $approver;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Application $application, Approver $approver)
    {
        $this->application = $application;
        $this->approver = $approver;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
