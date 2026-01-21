<!DOCTYPE html>
<html>
<head>
    <title>Currency Converter Test</title>
    <link rel="stylesheet" href="{{ asset('css/currency-styles.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        h2 {
            color: #1e293b;
        }
        .info {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 6px;
            margin: 10px 0;
        }
        .price-example {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <h1>🧪 Currency Converter Test Page</h1>
    
    <div class="test-section">
        <h2>1. Currency Selector</h2>
        <p>Click the selector below to switch currencies:</p>
        
        <!-- Currency Selector -->
        <div class="currency-selector">
            <div class="currency-selected" onclick="toggleCurrencyDropdown()">
                <span class="currency-flag">🇺🇸</span>
                <span class="currency-code">USD</span>
                <span class="currency-arrow">▼</span>
            </div>
            <div class="currency-dropdown">
                <div class="currency-dropdown-header">Select Currency</div>
                <a href="#" class="currency-item" data-currency="USD">
                    <span class="currency-item-flag">🇺🇸</span>
                    <div class="currency-item-info">
                        <span class="currency-item-code">USD</span>
                        <span class="currency-item-name">US Dollar</span>
                    </div>
                </a>
                <a href="#" class="currency-item" data-currency="EUR">
                    <span class="currency-item-flag">🇪🇺</span>
                    <div class="currency-item-info">
                        <span class="currency-item-code">EUR</span>
                        <span class="currency-item-name">Euro</span>
                    </div>
                </a>
                <a href="#" class="currency-item" data-currency="GBP">
                    <span class="currency-item-flag">🇬🇧</span>
                    <div class="currency-item-info">
                        <span class="currency-item-code">GBP</span>
                        <span class="currency-item-name">British Pound</span>
                    </div>
                </a>
                <a href="#" class="currency-item" data-currency="RWF">
                    <span class="currency-item-flag">🇷🇼</span>
                    <div class="currency-item-info">
                        <span class="currency-item-code">RWF</span>
                        <span class="currency-item-name">Rwandan Franc</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="test-section">
        <h2>2. Price Conversion Test</h2>
        <p>These prices should automatically convert based on selected currency:</p>
        
        <div class="info">
            <strong>Original RWF Price:</strong> RWF 10,000
        </div>
        
        <div class="price-example convertible-price" data-price-rwf="10000" data-currency="USD">
            RWF 10,000
        </div>
        
        <div class="info">
            <strong>Original RWF Price:</strong> RWF 50,000
        </div>
        
        <div class="price-example convertible-price" data-price-rwf="50000" data-currency="USD">
            RWF 50,000
        </div>
        
        <div class="info">
            <strong>Original RWF Price:</strong> RWF 100,000
        </div>
        
        <div class="price-example convertible-price" data-price-rwf="100000" data-currency="USD">
            RWF 100,000
        </div>
    </div>

    <div class="test-section">
        <h2>3. Browser Console</h2>
        <div class="info">
            <p><strong>Open your browser console (F12)</strong> and look for these messages:</p>
            <ul>
                <li>✅ "Currency Converter initializing..."</li>
                <li>✅ "Rates loaded: {USD: ..., EUR: ..., GBP: ..., RWF: 1}"</li>
                <li>✅ "Currency Converter initialized! Current currency: USD"</li>
            </ul>
            <p>If you see errors instead, the JavaScript isn't loading properly.</p>
        </div>
    </div>

    <div class="test-section">
        <h2>4. Expected Behavior</h2>
        <div class="info">
            <ul>
                <li>✅ Prices should show in USD by default ($6.85 for RWF 10,000)</li>
                <li>✅ Currency selector should display USD flag and code</li>
                <li>✅ Clicking selector should open dropdown smoothly</li>
                <li>✅ Selecting RWF should show original RWF prices</li>
                <li>✅ Prices should fade during currency switch</li>
                <li>✅ Selected currency persists after page refresh</li>
            </ul>
        </div>
    </div>

    <!-- Currency Converter Script -->
    <script src="{{ asset('js/currency-converter.js') }}"></script>
</body>
</html>
