<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $quantity;

    public function __construct($product, $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->error()
                    ->subject('Low Stock Alert: ' . $this->product->name)
                    ->greeting('Hello Admin,')
                    ->line('One of the products in your catalog is running low on stock.')
                    ->line('**Product:** ' . $this->product->name)
                    ->line('**Current Stock:** ' . $this->quantity)
                    ->action('Update Stock', route('products.edit', $this->product->id))
                    ->line('Please restock as soon as possible to avoid order cancellations.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'stock',
            'title' => 'Low Stock Alert',
            'message' => "{$this->product->name} is running low on stock ({$this->quantity} units left)",
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'amber',
            'action_url' => route('products.edit', $this->product->id),
            'product_id' => $this->product->id
        ];
    }
}
