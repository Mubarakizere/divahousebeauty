@component('mail::message')
# Shipping Payment Confirmed ✓

Hi {{ $order->customer_name ?? $order->user->name ?? 'Customer' }},

Great news! Your shipping payment for **Order #{{ $order->masked_order_id ?: $order->id }}** has been processed successfully.

**Shipping Paid:** RWF {{ number_format($order->shipping_total, 0) }}
**Product Total:** RWF {{ number_format($order->total, 0) }}
**Grand Total:** RWF {{ number_format($order->grand_total, 0) }}

Your order is now fully paid and will be shipped soon!

@component('mail::button', ['url' => url('/orders/' . $order->id)])
View Order Details
@endcomponent

Thanks for choosing Diva House Beauty!
{{ config('app.name') }}
@endcomponent
