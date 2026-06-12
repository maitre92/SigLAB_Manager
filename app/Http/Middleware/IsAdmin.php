<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Shared\Enums\UserRole;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour vérifier que l'utilisateur est administrateur
 */
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (string) $user->role === UserRole::FORMATEUR->value && $request->routeIs('admin.suivi-pedagogique.*')) {
            return $next($request);
        }

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Accès refusé. Vous devez être administrateur.');
        }

        return $next($request);
    }
}
