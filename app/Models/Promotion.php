<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'product_image', 'discount_percentage', 'start_time', 'end_time'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper method to get the first image of the related product
    public function getProductImage()
    {
        return $this->product?->images[0] ?? null;
    }
}
