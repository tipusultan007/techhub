<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCustomerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Customer Registered: ' . $this->customer->name)
                    ->greeting('Hello Admin,')
                    ->line('A new customer has joined the Tech Hub Information Technology platform.')
                    ->line('**Name:** ' . $this->customer->name)
                    ->line('**Email:** ' . ($this->customer->email ?? 'Not provided'))
                    ->action('View Profile', route('customers.show', $this->customer->id))
                    ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'customer',
            'title' => 'New CRM Entry',
            'message' => "A new customer profile '{$this->customer->name}' has been registered.",
            'icon' => 'fas fa-user-plus',
            'color' => 'emerald',
            'action_url' => route('customers.show', $this->customer->id),
            'customer_id' => $this->customer->id
        ];
    }
}
