<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShippingCostSet extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Shipping Cost Ready — Order #' . ($this->order->masked_order_id ?: $this->order->id))
                    ->markdown('emails.orders.shipping_cost_set');
    }
}
