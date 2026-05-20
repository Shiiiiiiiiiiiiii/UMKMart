<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     * Validates the X-API-KEY header against active shops.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            return $this->errorResponse('API key is required', 401);
        }

        $shop = Shop::where('api_key', $apiKey)->where('status', 'active')->first();

        if (!$shop) {
            return $this->errorResponse('Invalid or inactive API key', 403);
        }

        // Attach the shop to the request for downstream use
        $request->merge(['verified_shop' => $shop]);

        return $next($request);
    }
}
