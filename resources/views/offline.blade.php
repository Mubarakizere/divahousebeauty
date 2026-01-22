<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Diva House Beauty</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Playfair Display', serif; }
        .btn-gold { 
            background-color: #C5A059; 
            color: white; 
            transition: all 0.3s ease; 
        }
        .btn-gold:hover { background-color: #B08D4C; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen text-center px-4">
    <div>
        <div class="mb-6">
            <img src="{{ asset('assets/images/logo-loader.jpg') }}" alt="Diva House Beauty" class="h-16 w-auto mx-auto object-contain">
        </div>
        <h1 class="text-3xl font-bold text-slate-800 mb-4">You are offline</h1>
        <p class="text-slate-600 mb-8 max-w-md mx-auto">It looks like you've lost your internet connection. Please check your network and try again.</p>
        <button onclick="window.location.reload()" class="btn-gold px-8 py-3 rounded-full font-semibold uppercase tracking-wider text-sm shadow-lg">
            Try Again
        </button>
    </div>
</body>
</html>
