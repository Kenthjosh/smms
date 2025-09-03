<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureStudentRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->isStudent()) {
            Auth::logout();
            return redirect()->route('filament.student.auth.login')
                ->withErrors(['email' => 'Access denied. Students only.']);
        }

        return $next($request);
    }
}
