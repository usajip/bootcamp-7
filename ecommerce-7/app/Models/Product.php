<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'product_category_id',
    ];

    public function product_category() // many to 1 relationship with ProductCategory model
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function order_items() // 1 to many relationship with OrderItem model
    {
        return $this->hasMany(OrderItem::class);
    }
}
