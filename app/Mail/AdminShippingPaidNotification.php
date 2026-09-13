<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminShippingPaidNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('[Admin] Shipping Paid — Order #' . ($this->order->masked_order_id ?: $this->order->id) . ' by ' . ($this->order->customer_name ?? 'Customer'))
                    ->markdown('emails.orders.admin_shipping_paid');
    }
}
