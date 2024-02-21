<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StakingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $upline;
    protected $downline;

    public function __construct(Authenticatable $upline, Authenticatable $downline)
    {
        $this->upline = $upline;
        $this->downline = $downline;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Send notification via mail and save to database
    }

    public function toMail($notifiable)
    {
        $referralViewUrl = route('affiliate.referral_view', ['type' => 'binary']);
        return (new MailMessage)
            ->subject('Pending Binary Placement')
            ->greeting('Hello ' . $this->upline->name . ',')
            ->line('One of your referee, '. $this->downline->name .' has subscribed to a staking plan. You have a pending binary tree placement.')
            ->action('Do Placement', $referralViewUrl);
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Pending Binary Placement',
            'content' => 'One of your referee, '. $this->downline->name .' has subscribed to a staking plan. You have a pending binary tree placement.',
            'post_date' => now()->toDateTimeString(),
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Pending Binary Placement',
            'content' => 'One of your referee, '. $this->downline->name .' has subscribed to a staking plan. You have a pending binary tree placement.',
            'post_date' => now()->toDateTimeString(),
        ];
    }
}
