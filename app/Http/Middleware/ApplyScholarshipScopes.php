<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ApplyScholarshipScopes
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->scholarship_id && !$user->isSuperAdmin()) {
            // Apply global scope to applications - users can only see applications from their scholarship
            Application::addGlobalScope('scholarship', function ($query) use ($user) {
                $query->where('scholarship_id', $user->scholarship_id);
            });

            // Apply global scope to users - committee can only see users from their scholarship
            if ($user->isCommittee()) {
                User::addGlobalScope('scholarship_committee', function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('scholarship_id', $user->scholarship_id)
                            ->orWhere('role', 'admin'); // Allow seeing admin users
                    });
                });
            }

            // Students can only see their own data
            if ($user->isStudent()) {
                User::addGlobalScope('own_data', function ($query) use ($user) {
                    $query->where('id', $user->id);
                });
            }

            // Store current scholarship context in the service container
            app()->instance('current-scholarship', $user->scholarship);
        }

        return $next($request);
    }
}