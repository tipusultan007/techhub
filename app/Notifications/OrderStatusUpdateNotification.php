<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $comment;

    public function __construct($order, $comment = null)
    {
        $this->order = $order;
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Update: #' . $this->order->invoice_no)
            ->view('mail.customer.order-status-update', [
                'order' => $this->order,
                'comment' => $this->comment
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
        ];
    }
}
