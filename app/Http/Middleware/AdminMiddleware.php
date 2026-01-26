<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérifier si l'utilisateur est authentifié avec le guard 'admin'
        if (!Auth::guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié. Veuillez vous connecter.'
            ], 401);
        }

        $admin = Auth::guard('admin')->user();

        // Vérifier si le compte est actif
        if (!$admin->isActive()) {
            Auth::guard('admin')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Votre compte a été désactivé.'
            ], 403);
        }

        // Vérifier les rôles si spécifiés
        if (!empty($roles)) {
            if (!in_array($admin->role, $roles)) {
                abort(403, 'Accès non autorisé.');
            }
        }

        return $next($request);
    }
}