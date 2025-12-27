<?php

namespace App\Mail;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbandonedCartEmail extends Mailable
{
    use Queueable, SerializesModels;

    public AbandonedCart $cart;

    public function __construct(AbandonedCart $cart)
    {
        $this->cart = $cart;
    }

    public function build()
    {
        return $this->subject('You left something in your cart! 🛒')
                    ->view('emails.abandoned_cart');
    }
}
