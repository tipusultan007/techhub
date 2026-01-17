<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderHistory extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'comment',
        'user_id'
    ];

    /**
     * Get the order associated with this history log.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user (admin/staff) who triggered this change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
