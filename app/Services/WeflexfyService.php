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
     * Verify webhook JWT token or decode payload
     */
    public function verifyWebhookToken(?string $token)
    {
        if (empty($token)) {
            return [];
        }

        try {
            // Check if JWT library is available
            if (!class_exists('Firebase\JWT\JWT')) {
                $parts = explode('.', $token);
                if (count($parts) === 3) {
                    $payload = base64_decode($parts[1]);
                    return json_decode($payload, true) ?? [];
                }
                return [];
            }
            
            // Proper JWT verification
            if (!empty($this->secretKey)) {
                $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($this->secretKey, 'HS256'));
            } else {
                // If secret key is not set, decode payload without signature check
                $parts = explode('.', $token);
                if (count($parts) === 3) {
                    $payload = base64_decode($parts[1]);
                    return json_decode($payload, true) ?? [];
                }
                return [];
            }

            return json_decode(json_encode($decoded), true) ?? [];
            
        } catch (\Exception $e) {
            \Log::error('Webhook JWT verification failed', [
                'error' => $e->getMessage(),
            ]);
            
            // Fallback decoding if token signature check fails but token structure is valid
            $parts = explode('.', $token);
            if (count($parts) === 3) {
                $payload = base64_decode($parts[1]);
                $decodedArray = json_decode($payload, true);
                if (is_array($decodedArray)) {
                    \Log::warning('Using fallback JWT payload decoding without signature verification');
                    return $decodedArray;
                }
            }

            throw new \Exception('Webhook verification failed: ' . $e->getMessage());
        }
    }
}
