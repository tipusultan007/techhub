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
        'name', 'email', 'phone', 'password',
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
        $url = route('customer.password.reset', ['token' => $token, 'email' => $this->email]);

        $this->notify(new ResetPassword($url));
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
