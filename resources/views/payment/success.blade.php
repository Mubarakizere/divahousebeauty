@extends('layouts.payment-result')

@php
    $isShippingPayment = $order->payment && $order->payment->payment_type === 'shipping';
    $amount = $isShippingPayment ? $order->shipping_total : $order->total;
    $reference = $order->masked_order_id ?? $order->order_number ?? ('Order #' . $order->id);
    $paymentReference = $order->payment?->payment_ref ?? $order->payment?->request_token;
@endphp

@section('title', 'Payment confirmed')
@section('accent', '#5f876c')
@section('eyebrow', 'Payment confirmed')
@section('heading', 'Your order is confirmed.')
@section('message', 'Thank you for your payment. We have received your order and will begin preparing it shortly.')
@section('details')
    <dl class="details">
        <div class="detail"><dt>Order</dt><dd>{{ $reference }}</dd></div>
        <div class="detail"><dt>{{ $isShippingPayment ? 'Shipping paid' : 'Amount paid' }}</dt><dd>RWF {{ number_format($amount, 0) }}</dd></div>
        @if($paymentReference)<div class="detail"><dt>Payment reference</dt><dd>{{ $paymentReference }}</dd></div>@endif
    </dl>
@endsection
@section('actions')
    <a class="action primary" href="{{ route('orders.show', $order->id) }}">View order</a>
    <a class="action" href="{{ route('home') }}">Continue shopping</a>
@endsection
@section('note', 'A confirmation has been sent to ' . ($order->customer_email ?? 'your email address') . '.')
