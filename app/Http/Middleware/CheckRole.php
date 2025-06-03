<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        
        // Check if user has any of the required roles
        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses untuk halaman ini.'
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk halaman tersebut.');
        }

        return $next($request);
    }
}

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Hanya admin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->isManager())) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
        }

        return $next($request);
    }
}