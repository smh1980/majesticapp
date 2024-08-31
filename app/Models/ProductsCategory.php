<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsCategory extends Model
{
    use HasFactory;

    // protected $table = 'products_categories'; // Specify the table name if it's not the plural form of the model

    protected $fillable = [
        'name',
        'slug',
        'image',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
