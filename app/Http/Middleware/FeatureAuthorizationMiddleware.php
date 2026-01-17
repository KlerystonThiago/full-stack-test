<?php

namespace App\Http\Middleware;

use Closure;
use App\Features;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $name): Response
    {
        $user = $request->user();
        
        if (! $user) {
            return redirect()->route('login');
        }
        
        if ($user->role?->name !== 'god') {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
