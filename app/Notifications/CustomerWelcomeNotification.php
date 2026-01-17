<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ElectroMart!')
            ->view('mail.customer.welcome', ['customer' => $this->customer]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Welcome to ElectroMart!',
            'type' => 'welcome'
        ];
    }
}
