<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public function scopeAssets($query) { return $query->where('type', 'asset'); }
    public function scopeLiabilities($query) { return $query->where('type', 'liability'); }
    public function scopeEquity($query) { return $query->where('type', 'equity'); }
    public function scopeRevenue($query) { return $query->where('type', 'revenue'); }
    public function scopeExpenses($query) { return $query->where('type', 'expense'); }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function getBalanceAttribute()
    {
        $debits = $this->ledgerEntries()->sum('debit');
        $credits = $this->ledgerEntries()->sum('credit');

        if (in_array($this->type, ['asset', 'expense'])) {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }
}
