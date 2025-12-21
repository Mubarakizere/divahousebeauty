<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderPendingApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
{
    $this->order = $order;
    $this->total = $order->total_amount; // Ensure this is set correctly
}

    public function build()
    {
        return $this->subject('Order Payment Received - Awaiting Approval')
                    ->view('emails.order_pending_approval');
    }
}
