<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitalTransaction extends Model
{
    protected $guarded = [];
    protected $casts = ['date' => 'date'];

    public function bankAccount()
    {
        return $this->belongsTo(Account::class, 'bank_account_id');
    }

    public function equityAccount()
    {
        return $this->belongsTo(Account::class, 'equity_account_id');
    }

    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'reference');
    }
}
