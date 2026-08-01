<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_title' => $this->product_title,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'shipping_type' => $this->shipping_type,
            'image' => $this->image,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
