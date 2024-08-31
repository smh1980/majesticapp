<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'images',
        'item_description',
        'item_no',
        // 'customer_barcode',
        // 'customer_ref',
        'unit_measure',
        'is_active',
        'category_id',
    ];

    protected $casts = [
        'images' => 'array', // Cast the image attribute to an array
    ];

    public function category()
    {
        return $this->belongsTo(ProductsCategory::class, 'category_id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'item_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'item_id');
    }
}
