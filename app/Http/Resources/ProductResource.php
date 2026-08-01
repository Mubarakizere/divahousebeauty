<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'express_price' => $this->express_price,
            'standard_price' => $this->standard_price,
            'formatted_price' => $this->formatted_price,
            'formatted_express_price' => $this->formatted_express_price,
            'image_urls' => $this->image_urls,
            'in_stock' => $this->in_stock,
            'is_new' => $this->is_new,
            'is_on_sale' => $this->is_on_sale,
            'sale_price' => $this->sale_price,
            'average_rating' => $this->average_rating,
            'review_count' => $this->review_count,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
        ];
    }
}
