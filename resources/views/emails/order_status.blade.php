<!DOCTYPE html>
<html>
<head>
    <title>Order Status Update</title>
</head>
<body>
    <p>Dear {{ $order->user->name }},</p>
    <p>Your order #{{ $order->id }} has been <strong>{{ $order->status }}</strong>.</p>
    <p>Thank you for shopping with us!</p>
</body>
</html>
