<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositRequestNotification extends Notification
{
    use Queueable;

    protected $transaction;
    protected $user;

    public function __construct($transaction, $user) {
        $this->transaction = $transaction;
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $token = \Crypt::encryptString('depositMetafinX2024|' . $this->transaction->transaction_number);
        return (new MailMessage)
            ->subject('Transaction Number - ' . $this->transaction->transaction_number)
            ->greeting('Transaction Number - ' . $this->transaction->transaction_number)
            ->line('Email: ' . $this->user->email)
            ->line('Name: ' . $this->user->name)
            ->line('To Wallet Address: ' . $this->transaction->to_wallet_address)
            ->line('Deposit Amount: $ ' . $this->transaction->amount)
            ->line('Click the button to proceed with approval')
            ->action('Approval', route('approval', [
                'token' => $token,
            ]))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
