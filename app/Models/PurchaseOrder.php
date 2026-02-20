<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PurchaseOrder extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receptions()
    {
        return $this->hasMany(PurchaseReception::class);
    }

    /**
     * Generate the next sequential PO number.
     * Format: PO-000001, PO-000002, etc.
     */
    public static function generateNextPONumber()
    {
        $lastOrder = self::where('reference_no', 'LIKE', 'PO-%')
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastOrder) {
            return 'PO-000001';
        }

        // Extract numeric part from PO-000001
        $lastNumber = intval(str_replace('PO-', '', $lastOrder->reference_no));
        $nextNumber = $lastNumber + 1;

        return 'PO-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
