<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureCommitteeRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Allow super admin to access committee panel for oversight
        if (!$user || (!$user->isCommittee() && !$user->isSuperAdmin())) {
            Auth::logout();
            return redirect()->route('filament.committee.auth.login')
                ->withErrors(['email' => 'Access denied. Committee members only.']);
        }

        return $next($request);
    }
}
