@component('mail::message')
# Shipping Cost Ready

Hi {{ $order->customer_name ?? $order->user->name ?? 'Customer' }},

Your shipping cost for **Order #{{ $order->masked_order_id ?: $order->id }}** has been calculated.

**Shipping Cost (DHL):** RWF {{ number_format($order->shipping_cost, 0) }}
**Service Fee (5%):** RWF {{ number_format($order->shipping_service_fee, 0) }}
**Total Shipping:** RWF {{ number_format($order->shipping_total, 0) }}

**Order Total:** RWF {{ number_format($order->total, 0) }}
**Grand Total:** RWF {{ number_format($order->grand_total, 0) }}

Please log in to your dashboard to complete the shipping payment.

@component('mail::button', ['url' => url('/orders/' . $order->id)])
Pay Shipping Now
@endcomponent

@if($order->shipping_notes)
**Shipping Notes:** {{ $order->shipping_notes }}
@endif

Thanks for shopping with us!
{{ config('app.name') }}
@endcomponent
