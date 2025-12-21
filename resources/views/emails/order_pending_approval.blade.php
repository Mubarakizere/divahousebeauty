<!DOCTYPE html>
<html>
<head>
    <title>Order Payment Received</title>
</head>
<body>
    <h2>Dear {{ $order->user->name }},</h2>
    <p>Thank you for placing your order! We have received your payment details.</p>
    
    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Total Amount:</strong> RWF {{ number_format($order->total, 2) }}</p>
    <p><strong>MoMo Transaction Code:</strong> {{ $order->transaction_id }}</p>

    

    <p>Your order is currently <strong>awaiting admin approval</strong>. You will receive another email once it is approved or rejected.</p>

    <p>Thank you for shopping with us!</p>
</body>
</html>
