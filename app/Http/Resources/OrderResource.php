<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'masked_order_id' => $this->masked_order_id,
            'order_number' => $this->order_number,
            'total' => $this->total,
            'formatted_total' => $this->formatted_total,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'items' => collect($this->whenLoaded('items'))->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'line_total' => $item->line_total,
                    'product' => new ProductResource($item->product),
                ];
            }),
        ];
    }
}
