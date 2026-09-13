@component('mail::message')
# Shipping Payment Received

A customer has paid their shipping fee.

**Order:** #{{ $order->masked_order_id ?: $order->id }}
**Customer:** {{ $order->customer_name ?? 'N/A' }}
**Email:** {{ $order->customer_email ?? 'N/A' }}
**Phone:** {{ $order->customer_phone ?? 'N/A' }}

---

**Product Total:** RWF {{ number_format($order->total, 0) }}
**Shipping (DHL base):** RWF {{ number_format($order->shipping_cost, 0) }}
**Service Fee (5%):** RWF {{ number_format($order->shipping_service_fee, 0) }}
**Shipping Total Paid:** RWF {{ number_format($order->shipping_total, 0) }}
**Grand Total Collected:** RWF {{ number_format($order->grand_total, 0) }}

**Shipping Address:** {{ $order->shipping_address ?? 'N/A' }}

@component('mail::button', ['url' => url('/admin/orders/' . $order->id)])
View Order in Admin
@endcomponent

{{ config('app.name') }} — Admin Notification
@endcomponent
