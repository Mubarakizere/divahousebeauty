<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>Secure payment | Diva House Beauty</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
    <script>tailwind = { config: { corePlugins: { preflight: false } } }</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --ink: #24211e; --paper: #faf8f4; --accent: #bd674f; --line: #dfdad2; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .display { font-family: 'Playfair Display', serif; }
        [x-cloak] { display: none !important; }
        .payment-shell { width: min(100% - 40px, 1120px); margin: 0 auto; }
        .brand { color: inherit; text-decoration: none; font-size: 12px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
        .brand-mark { display: inline-block; width: 19px; height: 19px; margin-right: 8px; vertical-align: -4px; border: 1.5px solid var(--accent); border-radius: 50% 50% 50% 5px; transform: rotate(-25deg); }
        .payment-frame { min-height: 640px; background: #fff; border: 1px solid var(--line); border-radius: 2px; overflow: hidden; position: relative; }
        .loader { position: absolute; inset: 0; z-index: 2; display: grid; place-items: center; background: #fff; }
        .spinner { width: 28px; height: 28px; border: 2px solid #e8e3dc; border-top-color: var(--accent); border-radius: 50%; animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .action { display: inline-flex; align-items: center; justify-content: center; min-height: 43px; padding: 0 17px; border: 1px solid var(--ink); background: transparent; color: var(--ink); font: 600 12px/1 'DM Sans', sans-serif; letter-spacing: .03em; text-decoration: none; cursor: pointer; transition: background .2s, color .2s; }
        .action:hover { background: var(--ink); color: #fff; }
        .action.primary { border-color: var(--accent); background: var(--accent); color: #fff; }
        .action.primary:hover { background: #a85844; border-color: #a85844; }
        @media (max-width: 700px) { .payment-shell { width: min(100% - 32px, 1120px); } .payment-frame { min-height: 570px; } }
    </style>
</head>
<body x-data="paymentPage({
    statusUrl: @js(isset($isShipping) && $isShipping ? route('payment.status', $order->id) . '?type=shipping' : route('payment.status', $order->id)),
    successUrl: @js(isset($isShipping) && $isShipping ? route('shipping.payment.success', ['order' => $order->id]) : route('payment.success', ['order' => $order->id])),
    failedUrl: @js(route('payment.failed', ['order' => $order->id])),
    iframeOrigins: ['https://weflexfy.com', 'https://api.weflexfy.com']
})" x-init="init()">
    @php
        $amount = isset($isShipping) && $isShipping ? $order->shipping_total : $order->total;
        $reference = $order->masked_order_id ?? $order->order_number ?? ('Order #' . $order->id);
    @endphp

    <main class="payment-shell py-6 sm:py-9">
        <div class="flex items-center justify-between border-b border-[var(--line)] pb-5">
            <a class="brand" href="{{ route('home') }}" aria-label="Diva House Beauty home"><span class="brand-mark" aria-hidden="true"></span>Diva House Beauty</a>
            <span class="text-[11px] font-semibold uppercase tracking-[.12em] text-stone-500">Secure checkout</span>
        </div>

        <div class="grid gap-8 py-8 lg:grid-cols-[minmax(0,1fr)_270px] lg:gap-12">
            <section aria-labelledby="payment-title">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-[.14em] text-stone-500">Payment</p>
                <h1 id="payment-title" class="display m-0 text-[clamp(30px,4vw,48px)] leading-none tracking-[-.035em]">Complete your payment</h1>
                <p class="mb-6 mt-3 max-w-xl text-sm leading-6 text-stone-600">Choose a payment method in the secure window below. Please keep this page open until your payment is confirmed.</p>

                <div x-show="state === 'failed'" x-cloak class="mb-4 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    We could not confirm this payment. You will be taken to the retry page shortly.
                </div>
                <div x-show="state === 'success'" x-cloak class="mb-4 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                    Payment confirmed. Taking you to your order now.
                </div>
                <div x-show="state === 'waiting'" x-cloak class="mb-4 border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-700" role="status">
                    We are still waiting for confirmation. If you have completed payment, check your order status before trying again.
                </div>

                <div class="payment-frame">
                    <div class="loader" x-show="loading" x-cloak>
                        <div class="text-center">
                            <div class="spinner mx-auto"></div>
                            <p class="mb-0 mt-4 text-sm text-stone-600">Opening secure payment</p>
                        </div>
                    </div>
                    <iframe id="payment-iframe" title="Secure payment form" src="{{ $payment->iframe_url }}" class="h-[640px] w-full border-0 transition-opacity duration-300" :class="loading ? 'opacity-0' : 'opacity-100'" @load="handleIframeLoad"></iframe>
                </div>
            </section>

            <aside class="self-start border-t border-[var(--line)] pt-5 lg:mt-[55px]" aria-label="Order summary">
                <p class="m-0 text-[11px] font-semibold uppercase tracking-[.14em] text-stone-500">Your order</p>
                <p class="mt-3 text-sm font-semibold text-stone-800">{{ $reference }}</p>
                <p class="mb-5 mt-1 text-xs text-stone-500">{{ isset($isShipping) && $isShipping ? 'Shipping payment' : 'Order payment' }}</p>
                <div class="border-y border-[var(--line)] py-4">
                    <p class="m-0 text-xs text-stone-500">Amount due</p>
                    <p class="display mb-0 mt-1 text-3xl tracking-[-.03em]">RWF {{ number_format($amount, 0) }}</p>
                </div>
                <p class="mb-0 mt-5 text-xs leading-5 text-stone-500">Payment details are entered directly with our payment provider and are not stored by Diva House Beauty.</p>
                <a href="mailto:info@divahousebeauty.com" class="mt-5 inline-block text-xs font-semibold text-stone-700 underline decoration-stone-300 underline-offset-4 hover:text-[var(--accent)]">Need help with this order?</a>
            </aside>
        </div>
    </main>

    <script>
        function paymentPage(cfg) {
            return {
                loading: true,
                state: null,
                done: false,
                attempts: 0,
                pollTimer: null,
                init() {
                    this.listenForPaymentUpdate();
                    window.setTimeout(() => this.startPolling(), 2500);
                    document.addEventListener('visibilitychange', () => {
                        if (this.done) return;
                        document.hidden ? this.stopPolling() : this.startPolling();
                    });
                    window.addEventListener('beforeunload', () => this.stopPolling());
                },
                listenForPaymentUpdate() {
                    window.addEventListener('message', (event) => {
                        if (!cfg.iframeOrigins.includes(event.origin)) return;
                        const message = event.data || {};
                        if (message.type !== 'PAYMENT_STATUS') return;
                        if (message.status === 'init') this.handleIframeLoad();
                        if (message.status === 'success') this.complete('success');
                        if (message.status === 'failed') this.complete('failed');
                    });
                },
                startPolling() {
                    if (this.pollTimer || this.done) return;
                    this.pollTimer = window.setInterval(() => this.checkStatus(), 5000);
                },
                stopPolling() {
                    if (!this.pollTimer) return;
                    window.clearInterval(this.pollTimer);
                    this.pollTimer = null;
                },
                async checkStatus() {
                    this.attempts++;
                    if (this.attempts >= 120) {
                        this.stopPolling();
                        this.state = 'waiting';
                        return;
                    }
                    try {
                        const response = await fetch(cfg.statusUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                        if (!response.ok) return;
                        const data = await response.json();
                        if (data?.paid === true) this.complete('success');
                        if (data?.status === 'failed') this.complete('failed');
                    } catch (error) {
                        // A brief network interruption should not interrupt an in-progress payment.
                    }
                },
                complete(result) {
                    if (this.done) return;
                    this.done = true;
                    this.state = result;
                    this.stopPolling();
                    window.setTimeout(() => window.location.assign(result === 'success' ? cfg.successUrl : cfg.failedUrl), result === 'success' ? 1800 : 3500);
                },
                handleIframeLoad() { this.loading = false; }
            };
        }
    </script>
</body>
</html>
