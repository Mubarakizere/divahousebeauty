<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #cc9966; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .status { background: #e8f4f8; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; }
        .tracking { background: #fff3cd; padding: 15px; border: 1px solid #ffc107; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 24px; background: #cc9966; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚚 Your Order is On the Way!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $order->customer_name }},</p>
            
            <p>Great news! Your order has been shipped and is on its way to you.</p>
            
            <div class="status">
                <strong>Order Number:</strong> {{ $order->order_number }}<br>
                <strong>Status:</strong> Shipped<br>
                @if($order->tracking_number)
                    <strong>Tracking Number:</strong> {{ $order->tracking_number }}<br>
                @endif
                @if($order->estimated_delivery_date)
                    <strong>Estimated Delivery:</strong> {{ $order->estimated_delivery_date->format('F d, Y') }}
                @endif
            </div>

            @if($order->tracking_number)
                <div class="tracking">
                    <strong>📦 Track Your Package:</strong><br>
                    Tracking Number: <code style="background: #fff; padding: 5px;">{{ $order->tracking_number }}</code>
                </div>
            @endif

            <h3>Order Summary:</h3>
            <table style="width: 100%; border-collapse: collapse;">
                @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">{{ $item->product->name ?? 'Product' }}</td>
                        <td style="padding: 10px; text-align: right;">x{{ $item->quantity }}</td>
                        <td style="padding: 10px; text-align: right;">{{ number_format($item->price, 0) }} RWF</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td colspan="2" style="padding: 10px; text-align: right;">Total:</td>
                    <td style="padding: 10px; text-align: right;">{{ number_format($order->total, 0) }} RWF</td>
                </tr>
            </table>

            <p style="text-align: center;">
                <a href="{{ url('/my-orders/' . $order->id) }}" class="btn">Track Your Order</a>
            </p>

            <p>If you have any questions, please don't hesitate to contact us.</p>
            
            <p>Thank you for shopping with us!<br>
            <strong>Diva House Beauty</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply directly to this message.</p>
        </div>
    </div>
</body>
</html>
