<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show login form for admin
     */
    /**
     * Admin login (API)
     */
    public function login(Request $request)
    {
        // 1. Récupération robuste des données (Support JSON sans Content-Type correct)
        $data = $request->all();
        Log::info('Admin Login attempt', ['data' => $data]);
        if (empty($data)) {
            $rawContent = $request->getContent();
            if (!empty($rawContent)) {
                $jsonData = json_decode($rawContent, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = $jsonData;
                }
            }
        }

        // 2. Validation
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'Format d\'email invalide',
            'password.required' => 'Le mot de passe est obligatoire',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
                'debug' => [
                    'received_data' => $data,
                    'raw_content' => $request->getContent(),
                    'content_type' => $request->header('Content-Type')
                ]
            ], 422);
        }

        // 3. Authentification
        if (Auth::guard('admin')->attempt($validator->validated())) {
            $admin = Auth::guard('admin')->user();
            
            // Vérifier si le compte est actif
            if (!$admin->isActive()) {
                Auth::guard('admin')->logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte est désactivé. Contactez le super administrateur.'
                ], 403);
            }

            // Générer le token d'accès
            $token = $admin->createToken('admin_token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $admin->id,
                    'nom' => $admin->nom,
                    'prenom' => $admin->prenom,
                    'email' => $admin->email,
                    'role' => $admin->role,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'
        ], 401);
    }

    /**
     * Admin logout (API)
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }
    

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe changé avec succès'
        ]);
    }

    /**
     * Obtenir les permissions de l'utilisateur selon son rôle
     */
    private function getUserPermissions(Admin $admin)
    {
        $permissions = [
            'can_view_dashboard' => false,
            'can_manage_users' => false,
            'can_manage_agences' => false,
            'can_manage_voyages' => false,
            'can_manage_tickets' => false,
            'can_view_reports' => false,
            'can_manage_settings' => false,
        ];

        switch ($admin->role) {
            case 'admin':
                $permissions = [
                    'can_view_dashboard' => true,
                    'can_manage_users' => true,
                    'can_manage_agences' => true,
                    'can_manage_voyages' => true,
                    'can_manage_tickets' => true,
                    'can_view_reports' => true,
                    'can_manage_settings' => true,
                ];
                break;
                
            case 'gestionnaire':
                $permissions = [
                    'can_view_dashboard' => true,
                    'can_manage_users' => false,
                    'can_manage_agences' => false,
                    'can_manage_voyages' => true,
                    'can_manage_tickets' => true,
                    'can_view_reports' => true,
                    'can_manage_settings' => false,
                ];
                break;
                
            case 'client':
                $permissions = [
                    'can_view_dashboard' => false,
                    'can_manage_users' => false,
                    'can_manage_agences' => false,
                    'can_manage_voyages' => false,
                    'can_manage_tickets' => false,
                    'can_view_reports' => false,
                    'can_manage_settings' => false,
                ];
                break;

            case 'controleur':
                $permissions = [
                    'can_view_dashboard' => true,
                    'can_manage_users' => false,
                    'can_manage_agences' => false,
                    'can_manage_voyages' => false, // View only, no manage
                    'can_manage_tickets' => true,  // For validation/checking
                    'can_view_reports' => false,
                    'can_manage_settings' => false,
                ];
                break;
        }

        return $permissions;
    }

    /**
     * Rafraîchir le token
     */
    public function refreshToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}