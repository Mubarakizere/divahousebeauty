@component('mail::message')
# 🎉 Order Confirmed

Hi {{ $order->user->name ?? 'Customer' }},

We’ve received your payment successfully!

**Order ID:** #{{ $order->id }}  
**Payment Method:** {{ $order->payment_method }}  
**Total Paid:** {{ number_format($order->total, 0) }} RWF

@component('mail::button', ['url' => url('/')])
View Our Products
@endcomponent

Thanks for choosing Diva House!  
{{ config('app.name') }}
@endcomponent
