<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'customer_id',
        'price',
        'customer_barcode',
        'customer_ref',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

