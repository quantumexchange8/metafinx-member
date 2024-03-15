<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $senderName;
    protected $amount;

    public function __construct($senderName, $amount)
    {
        $this->senderName = $senderName;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Send notification via mail and save to database
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Transfer Notification')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a transfer of $' . $this->amount . ' from ' . $this->senderName . '.')
            ->line('Thank you for using our service.');
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Transfer Notification',
            'content' => 'You have received a transfer of $' . $this->amount . ' from ' . $this->senderName . '.',
            'post_date' => now()->toDateTimeString(),
        ];
    }
    
    public function toArray($notifiable)
    {
        return [
            'title' => 'Transfer Notification',
            'content' => 'You have received a transfer of $' . $this->amount . ' from ' . $this->senderName . '.',
            'post_date' => now()->toDateTimeString(),
        ];
    }
}
