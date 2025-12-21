<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    protected $fillable = ['users_id', 'product_id', 'product_title', 'quantity', 'price', 'image'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id'); 
        // Ensures Laravel correctly maps `product_id` in `carts` to `id` in `products`
    }
}
