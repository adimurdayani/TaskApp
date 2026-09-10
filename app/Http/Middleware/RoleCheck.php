<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user(); // Periksa role yang diizinkan
        
        foreach ($roles as $role) {
            if ($user->role?->value === $role) {
                return $next($request);
            }
        }
        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
