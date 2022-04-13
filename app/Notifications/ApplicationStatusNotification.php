<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\Approver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification
{
    use Queueable;

    protected Application $application;
    protected Approver $approver;

    protected $isRejected;
    protected $isApproved;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Application $application, Approver $approver, $isRejected = false)
    {
        $this->application = $application;
        $this->approver = $approver;
        $this->isRejected = $isRejected;
        $this->isApproved = !$isRejected;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $newStatus = $this->isRejected ? 'Rejected' : $this->approver->getApplicationStatus();
        $approverUser = $this->approver->user;

        return (new MailMessage)
            ->subject("Application Status: {$newStatus}")
            ->greeting("Hi {$notifiable->name}!")
            ->when($this->isApproved, function ($message) use ($approverUser, $newStatus) {
                $message->line("{$approverUser->name} changed your application status to {$newStatus}.");
            })
            ->when($this->isRejected, function ($message) use ($approverUser) {
                $message->line("{$approverUser->name} rejected your application status.");
            })
            ->line("Remarks: {$this->approver->remarks}")
            ->action('View Application', route('filament.resources.applications.view', $this->application->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
