<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Secure Payment') | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO Meta --}}
    <meta name="description" content="Secure payment for your order with {{ config('app.name') }}">
    <meta name="robots" content="noindex, nofollow">

    {{-- CSS Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .payment-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: center;
            align-items: center;
            padding: 0;
        }

        iframe {
            width: 100%;
            max-width: 720px;
            height: 90vh;
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            iframe {
                max-width: 100%;
                border-radius: 0;
                height: 100vh;
            }
        }
    </style>
</head>
<body>
    <div class="payment-wrapper">
        @yield('content')
    </div>

    {{-- JS Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
