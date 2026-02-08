<?php

function parseOcrText($text) {
    if (empty($text)) {
        return false;
    }

    // 1. Clean up common OCR noise
    $text = trim($text);
    // Remove timestamps (e.g., 20:15) used in some screenshots
    $text = preg_replace('/\b\d{1,2}:\d{2}\b/', '', $text);
    // Remove "<-" arrows often seen in screenshots
    $text = str_replace(['<-', '->'], '', $text);
    
    $text = trim($text);
    echo "Processing clean text: '$text'\n";
    
    // 2. Look for the @ separator
    if (strpos($text, '@') !== false) {
        $parts = explode('@', $text, 2);
        
        $parsed_name = trim($parts[0]);
        
        // Extract numeric value from price part
        $priceText = trim($parts[1]);
        $priceValue = preg_replace('/[^0-9.]/', '', $priceText);
        
        if (is_numeric($priceValue)) {
            $parsed_price = (float) $priceValue;
            echo "SUCCESS (@ split): Name: '$parsed_name', Price: $parsed_price\n";
            return true;
        }
    }

    // 3. Fallback: Try to find price patterns at the END of the string
    // Matches: 100, 100.00, 100 RWF, etc. at the end
    if (preg_match('/[\s@]+(\d+(?:[.,]\d{2})?)\s*(?:RWF|USD|KES)?$/i', $text, $matches)) {
            $priceValue = str_replace(',', '.', $matches[1]);
            $parsed_price = (float) $priceValue;
            
            // Name is everything before the match
            $nameText = substr($text, 0, -strlen($matches[0]));
            $parsed_name = trim($nameText) ?: $text;
            
            echo "SUCCESS (End Regex): Name: '$parsed_name', Price: $parsed_price\n";
            return true;
    }

    // 4. Fallback: Regex for price anywhere if strict end match fails
    preg_match('/[\$€£]?\s*(\d+(?:[.,]\d{2})?)\s*(?:RWF|USD|KES)?/i', $text, $matches);
    
    if (!empty($matches[1])) {
        // Found a price pattern
        $priceValue = str_replace(',', '.', $matches[1]);
        $parsed_price = (float) $priceValue;
        
        // Remove the price from the text to get the name
        $nameText = preg_replace('/[\$€£]?\s*\d+(?:[.,]\d{2})?\s*(?:RWF|USD|KES)?/i', '', $text);
        $parsed_name = trim($nameText) ?: $text;
        
        echo "SUCCESS (Any Regex): Name: '$parsed_name', Price: $parsed_price\n";
        return true;
    }

    echo "FAILED: No price found.\n";
    return false;
}

// Test cases
$tests = [
    "20:15 AO <- Almatic Cosme... @ 100",
    "Product Name @ 100",
    "Product Name @100",
    "20:15 Almatic @100",
    "20:15 Almatic 100",
    "20:15 AO <- Almatic Cosme..."
];

// Output results as JSON for safe reading
$results = [];
foreach ($tests as $t) {
    ob_start();
    $success = parseOcrText($t);
    $output = ob_get_clean();
    $results[] = [
        'input' => $t,
        'success' => $success,
        'output' => $output
    ];
}
file_put_contents('debug.json', json_encode($results, JSON_PRETTY_PRINT));


