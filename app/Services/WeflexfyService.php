<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeflexfyService
{
    private $apiUrl;
    private $accessKey;
    private $secretKey;
    
    public function __construct()
    {
        $this->apiUrl = config('services.weflexfy.api_url', 'https://api.weflexfy.com');
        $this->accessKey = config('services.weflexfy.access_key');
        $this->secretKey = config('services.weflexfy.secret_key');
    }
    
    /**
     * Initiate a new payment with Weflexfy
     */
    public function initiatePayment(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_key' => $this->accessKey,
        ])->post($this->apiUrl . '/api/v1/payment/initiate', $data);
        
        if ($response->successful()) {
            return $response->json();
        }
        
        \Log::error('Weflexfy payment initiation failed', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        
        throw new \Exception('Payment initiation failed: ' . $response->body());
    }
    
    /**
     * Verify webhook JWT token
     * Note: Install firebase/php-jwt via composer for production
     */
    public function verifyWebhookToken(string $token)
    {
        try {
            // Check if JWT library is available
            if (!class_exists('Firebase\JWT\JWT')) {
                // Fallback: Basic verification (decode without signature check)
                // WARNING: This is NOT secure for production!
                \Log::warning('JWT library not installed. Install: composer require firebase/php-jwt');
                $parts = explode('.', $token);
                if (count($parts) === 3) {
                    $payload = base64_decode($parts[1]);
                    return json_decode($payload, true);
                }
                throw new \Exception('Invalid token format');
            }
            
            // Proper JWT verification
            $jwt = new \Firebase\JWT\JWT();
            $decoded = $jwt::decode($token, new \Firebase\JWT\Key($this->secretKey, 'HS256'));
            return (array) $decoded;
            
        } catch (\Exception $e) {
            \Log::error('Webhook JWT verification failed', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Webhook verification failed: ' . $e->getMessage());
        }
    }
}
