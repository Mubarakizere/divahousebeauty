<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payment Successful - {{ config('app.name', 'Diva House Beauty') }}</title>
    
    <!-- Your existing CSS files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <style>
        .success-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 60px 0;
        }
        
        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .success-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        
        .success-body {
            padding: 40px 30px;
        }
        
        .order-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .order-detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            color: #cc9966;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .next-steps {
            background: #e8f4f8;
            border-radius: 10px;
            padding: 25px;
            border-left: 4px solid #17a2b8;
            margin-bottom: 30px;
        }
        
        .step-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .step-item:last-child {
            margin-bottom: 0;
        }
        
        .step-icon {
            width: 30px;
            height: 30px;
            background: #17a2b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            font-size: 14px;
        }
        
        .action-buttons {
            text-align: center;
            margin: 40px 0;
        }
        
        .btn-success-primary {
            background: linear-gradient(135deg, #cc9966, #b8845a);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 10px 10px 0;
            transition: all 0.3s ease;
        }
        
        .btn-success-primary:hover {
            background: linear-gradient(135deg, #b8845a, #cc9966);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(204, 153, 102, 0.3);
        }
        
        .btn-success-secondary {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 10px 10px 0;
            transition: all 0.3s ease;
        }
        
        .btn-success-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
        }
        
        .support-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .contact-item {
            display: inline-flex;
            align-items: center;
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 0 10px 15px 0;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s ease;
        }
        
        .contact-item:hover {
            background: #cc9966;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        .contact-icon {
            margin-right: 10px;
            font-size: 18px;
        }
        
        @media (max-width: 768px) {
            .success-page {
                padding: 30px 0;
            }
            
            .success-header,
            .success-body {
                padding: 30px 20px;
            }
            
            .action-buttons .btn {
                display: block;
                width: 100%;
                margin: 10px 0;
            }
            
            .contact-item {
                display: block;
                width: 100%;
                margin: 10px 0;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="success-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <!-- Success Header -->
                    <div class="success-card">
                        <div class="success-header">
                            <div class="success-icon">
                                <i class="icon-check" style="font-size: 40px;"></i>
                            </div>
                            <h1 class="mb-2">Payment Successful!</h1>
                            <p class="mb-0">Thank you for your order. Your payment has been processed successfully.</p>
                        </div>
                        
                        @if(isset($order) && $order)
                        <div class="success-body">
                            <!-- Order Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-3">Order Information</h5>
                                    <p class="mb-1"><strong>Order #{{ $order->id }}</strong></p>
                                    <p class="mb-1">Date: {{ $order->created_at->format('M d, Y • H:i') }}</p>
                                    <p class="mb-0">Status: <span class="badge badge-success">Confirmed</span></p>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-3">Payment Information</h5>
                                    <p class="mb-1">Method: {{ $order->payment_method ?? 'WeFlexfy' }}</p>
                                    <p class="mb-1">Amount: <strong>RWF {{ number_format($order->total, 0) }}</strong></p>
                                    @if($order->transaction_id)
                                    <p class="mb-0"><small class="text-muted">Transaction: {{ $order->transaction_id }}</small></p>
                                    @endif
                                </div>
                            </div>

                            @if($order->customer_name || $order->customer_email)
                            <div class="border-top pt-4 mb-4">
                                <h5 class="text-muted mb-3">Customer Details</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        @if($order->customer_name)
                                        <p class="mb-1">Name: {{ $order->customer_name }}</p>
                                        @endif
                                        @if($order->customer_email)
                                        <p class="mb-1">Email: {{ $order->customer_email }}</p>
                                        @endif
                                        @if($order->customer_phone)
                                        <p class="mb-0">Phone: {{ $order->customer_phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Order Items -->
                            @if($order->items && $order->items->count() > 0)
                            <div class="order-summary">
                                <h5 class="mb-3">Order Items</h5>
                                @foreach($order->items as $item)
                                <div class="order-detail-row">
                                    <div>
                                        <strong>{{ $item->product->name ?? 'Product' }}</strong>
                                        <br><small class="text-muted">Qty: {{ $item->quantity ?? 1 }}</small>
                                    </div>
                                    <div>RWF {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 0) }}</div>
                                </div>
                                @endforeach
                                
                                <div class="order-detail-row">
                                    <div>Total Amount</div>
                                    <div>RWF {{ number_format($order->total, 0) }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Next Steps -->
                    <div class="next-steps">
                        <h5 class="text-info mb-3">What Happens Next?</h5>
                        <div class="step-item">
                            <div class="step-icon">1</div>
                            <span>You will receive an order confirmation email shortly</span>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">2</div>
                            <span>We'll prepare your order for delivery or pickup</span>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">3</div>
                            <span>You can track your order status in your account</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ url('/') }}" class="btn btn-success-primary">
                            <i class="icon-home mr-2"></i>Continue Shopping
                        </a>
                        
                        @if(isset($order) && $order)
                        @if(Auth::check())
                        <a href="{{ route('dashboard') }}" class="btn btn-success-secondary">
                            <i class="icon-user mr-2"></i>My Account
                        </a>
                        @endif
                        @endif
                    </div>

                    <!-- Support Section -->
                    <div class="support-section">
                        <h5 class="mb-3">Need Help?</h5>
                        <p class="text-muted mb-3">Our customer support team is here to assist you with any questions about your order.</p>
                        
                        <div class="d-flex flex-wrap justify-content-center">
                            <a href="mailto:support@divahouse.com" class="contact-item">
                                <i class="icon-envelope contact-icon"></i>
                                <span>Email Support</span>
                            </a>
                            
                            <a href="tel:+250780159059" class="contact-item">
                                <i class="icon-phone contact-icon"></i>
                                <span>Call Us</span>
                            </a>
                            
                            <a href="https://wa.me/250780159059" class="contact-item" target="_blank">
                                <i class="icon-whatsapp contact-icon"></i>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                        
                        @if(isset($order) && $order)
                        <p class="text-muted mt-3 mb-0">
                            <small>Please reference Order #{{ $order->id }} when contacting support</small>
                        </p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer" style="background: #333; color: white; padding: 40px 0; margin-top: 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ asset('assets/images/demos/demo-14/logo-footer.png') }}" alt="Footer Logo" width="105" height="25" class="mb-3">
                    <p class="text-light">Thank you for choosing Diva House Beauty. We're committed to providing you with the best beauty products and services.</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <div class="social-icons social-icons-color">
                        <span class="social-label text-light">Follow Us</span>
                        <a href="#" class="social-icon social-facebook ml-2" title="Facebook" target="_blank">
                            <i class="icon-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon social-instagram ml-2" title="Instagram" target="_blank">
                            <i class="icon-instagram"></i>
                        </a>
                        <a href="#" class="social-icon social-twitter ml-2" title="Twitter" target="_blank">
                            <i class="icon-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr class="my-4" style="border-color: #555;">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-light mb-0">
                        © {{ date('Y') }} Diva House Beauty. All Rights Reserved. 
                        Designed by <strong>Izere Moubarak</strong>.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Your existing JS files -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Add some celebration animation
            $('.success-icon').addClass('animated bounceIn');
            
            // Auto-hide any flash messages
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);

            @if(isset($order))
            // Log successful payment for analytics
            console.log('Payment successful', {
                orderId: {{ $order->id }},
                amount: {{ $order->total }},
                method: '{{ $order->payment_method ?? "unknown" }}'
            });

            // Google Analytics tracking (if available)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'purchase', {
                    transaction_id: '{{ $order->id }}',
                    value: {{ $order->total }},
                    currency: 'RWF',
                    items: [
                        @if($order->items)
                        @foreach($order->items as $item)
                        {
                            item_id: '{{ $item->product->id ?? "unknown" }}',
                            item_name: '{{ $item->product->name ?? "Product" }}',
                            category: '{{ $item->product->category->name ?? "General" }}',
                            quantity: {{ $item->quantity ?? 1 }},
                            price: {{ $item->price ?? 0 }}
                        }{{ !$loop->last ? ',' : '' }}
                        @endforeach
                        @endif
                    ]
                });
            }

            // Facebook Pixel tracking (if available)
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Purchase', {
                    value: {{ $order->total }},
                    currency: 'RWF'
                });
            }
            @endif

            // Add smooth scrolling for any anchor links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });

            // Add hover effects to buttons
            $('.btn-success-primary, .btn-success-secondary').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );
        });

        // Add some CSS animation classes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes bounceIn {
                0%, 20%, 40%, 60%, 80% {
                    animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
                }
                0% {
                    opacity: 0;
                    transform: scale3d(.3, .3, .3);
                }
                20% {
                    transform: scale3d(1.1, 1.1, 1.1);
                }
                40% {
                    transform: scale3d(.9, .9, .9);
                }
                60% {
                    opacity: 1;
                    transform: scale3d(1.03, 1.03, 1.03);
                }
                80% {
                    transform: scale3d(.97, .97, .97);
                }
                100% {
                    opacity: 1;
                    transform: scale3d(1, 1, 1);
                }
            }
            
            .animated {
                animation-duration: 1s;
                animation-fill-mode: both;
            }
            
            .bounceIn {
                animation-name: bounceIn;
            }
            
            .shadow-lg {
                box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            }
        `;
        document.head.appendChild(style);
    </script>

    @if(session('success'))
    <script>
        // Show success toast if there's a session message
        $(document).ready(function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                // Bootstrap 5 toast
                const toastHTML = `
                    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                $('body').append(toastHTML);
                const toast = new bootstrap.Toast($('.toast').last());
                toast.show();
            } else {
                // Fallback alert
                alert('{{ session('success') }}');
            }
        });
    </script>
    @endif
</body>
</html>