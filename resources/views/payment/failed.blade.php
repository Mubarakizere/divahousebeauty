@extends('layouts.payment-result')

@php
    $reference = $order->masked_order_id ?? $order->order_number ?? ('Order #' . $order->id);
@endphp

@section('title', 'Payment not completed')
@section('accent', '#b55f50')
@section('eyebrow', 'Payment not completed')
@section('heading', 'Your payment was not completed.')
@section('message', 'No payment has been confirmed for this order. You can safely try again, or return to your cart and review your order.')
@section('details')
    <dl class="details">
        <div class="detail"><dt>Order</dt><dd>{{ $reference }}</dd></div>
        <div class="detail"><dt>Amount due</dt><dd>RWF {{ number_format($order->total, 0) }}</dd></div>
    </dl>
@endsection
@section('actions')
    <form action="{{ route('payment.initiate') }}" method="POST">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <button class="action primary" type="submit">Try payment again</button>
    </form>
    <a class="action" href="{{ route('cart') }}">Return to cart</a>
@endsection
@section('note', 'If you were charged but do not see a confirmation, please contact us before making another payment.')
