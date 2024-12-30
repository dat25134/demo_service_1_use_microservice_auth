<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthService
{
    protected $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = config('services.auth.url');
    }

    public function validateToken(string $token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($this->baseUrl . '/api/validate-token');

            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Auth service error: ' . $e->getMessage());
            return null;
        }
    }
} 