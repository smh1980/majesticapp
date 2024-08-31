<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'persons_name',
        'phone',
        'is_active',
    ];

    /**
     * Get the orders associated with the customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the prices associated with the customer.
     */
    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}

