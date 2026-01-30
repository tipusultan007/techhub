<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallan extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(DeliveryChallanItem::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
