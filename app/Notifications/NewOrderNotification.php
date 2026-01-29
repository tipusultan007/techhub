<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Order Received: ' . $this->order->invoice_no)
                    ->greeting('Hello Admin,')
                    ->line('A new order has been placed on Tech Hub Information Technology.')
                    ->line('**Invoice No:** ' . $this->order->invoice_no)
                    ->line('**Customer:** ' . ($this->order->customer_name ?? 'Guest'))
                    ->line('**Total Amount:** ' . number_format($this->order->total, 2) . ' AED')
                    ->action('View Order Details', route('orders.show', $this->order->id))
                    ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order',
            'title' => 'New Order Received',
            'message' => "Order #{$this->order->invoice_no} has been placed by " . ($this->order->customer_name ?? 'Guest'),
            'icon' => 'fas fa-shopping-cart',
            'color' => 'blue',
            'action_url' => route('orders.show', $this->order->id),
            'order_id' => $this->order->id
        ];
    }
}
