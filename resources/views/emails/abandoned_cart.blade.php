<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #cc9966 0%, #b8885d 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 30px 20px; }
        .cart-item { background: white; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin: 15px 0; display: flex; align-items: center; }
        .cart-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; margin-right: 15px; }
        .item-details { flex: 1; }
        .item-name { font-weight: bold; font-size: 16px; margin-bottom: 5px; }
        .item-price { color: #666; }
        .total { background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 15px; margin: 20px 0; text-align: center; }
        .total-label { font-size: 14px; color: #666; margin-bottom: 5px; }
        .total-amount { font-size: 28px; font-weight: bold; color: #cc9966; }
        .btn { display: inline-block; padding: 15px 40px; background: #cc9966; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
        .btn:hover { background: #b8885d; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
        .urgency { background: #e8f4f8; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 You Left Something Behind!</h1>
        </div>
        
        <div class="content">
            <p>Hi there,</p>
            
            <p>We noticed you left these items in your cart. They're waiting for you!</p>

            @foreach($cart->cart_items as $item)
                <div class="cart-item">
                    @if(isset($item['image']))
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                    @endif
                    <div class="item-details">
                        <div class="item-name">{{ $item['name'] ?? 'Product' }}</div>
                        <div class="item-price">
                            {{ number_format($item['price'] ?? 0, 0) }} RWF × {{ $item['quantity'] ?? 1 }}
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="total">
                <div class="total-label">Cart Total</div>
                <div class="total-amount">{{ $cart->formatted_total }}</div>
            </div>

            <div class="urgency">
                ⚡ <strong>Complete your purchase now!</strong> Your cart will be saved for 7 days.
            </div>

            <p style="text-align: center;">
                <a href="{{ url('/cart/recover/' . $cart->recovery_token) }}" class="btn">
                    Complete My Purchase →
                </a>
            </p>

            <p>Click the button above to restore your cart and checkout in seconds!</p>

            <p>Need help? Reply to this email and we'll be happy to assist you.</p>
            
            <p>Happy shopping!<br>
            <strong>Diva House Beauty Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated reminder. You're receiving this because you added items to your cart.</p>
            <p>If you don't want to receive these emails, you can unsubscribe from your account settings.</p>
        </div>
    </div>
</body>
</html>
