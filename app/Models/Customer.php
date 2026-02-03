<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $guard = 'customer';

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'trn_number', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send the password reset notification.
     * We override this to point to the customer named route.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function deliveryChallans()
    {
        return $this->hasMany(DeliveryChallan::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }
}
