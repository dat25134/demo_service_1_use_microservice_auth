<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\AuthService;
use App\Models\ExternalUser;
use App\Models\User;
use App\Services\TokenBlacklistService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ExternalAuthMiddleware
{
    protected $authService;
    const CACHE_PREFIX = 'token:';
    const CACHE_TTL = 300; // 5 minutes

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        if (TokenBlacklistService::isBlacklisted($token)) {
            Redis::del(self::CACHE_PREFIX . md5($token));
            return response()->json(['error' => 'Token has been revoked'], 401);
        }

        $cacheKey = self::CACHE_PREFIX . md5($token);
        
        $userData = Redis::get($cacheKey);
        if (!$userData) {
            $userData = $this->authService->validateToken($token);
            
            if ($userData) {
                Redis::setex(
                    $cacheKey,
                    self::CACHE_TTL,
                    json_encode($userData)
                );
            } else {
                TokenBlacklistService::blacklist($token);
                return response()->json(['error' => 'Invalid token'], 401);
            }
        } else {
            $userData = json_decode($userData, true);
        }

        $request->setUserResolver(function () use ($userData) {
            return new User($userData);
        });

        return $next($request);
    }
} 