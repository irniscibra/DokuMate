<?php
namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PremiumUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->plan !== 'premium') {
            return response()->json(['message' => 'Diese Funktion ist nur für Premium-Nutzer verfügbar'], 403);
        }
        return $next($request);
    }  
}