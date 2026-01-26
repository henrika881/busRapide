<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Admin login (API)
     */
    public function login(Request $request)
    {
        // 1. Récupération robuste des données
        $data = $request->all();
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Authentification
        // On vérifie d'abord si l'utilisateur existe
        $admin = Admin::where('email', $data['email'])->first();

        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects.'
            ], 401);
        }

        // 4. Vérifications supplémentaires
        if (!$admin->isActive()) {
            return response()->json(['success' => false, 'message' => 'Votre compte est désactivé.'], 403);
        }

        // 5. Génération du token
        // On attache des "abilities" (capacités) au token en fonction du rôle pour Sanctum
        $abilities = ['admin']; // Capacité de base pour tous les admins
        if ($admin->isSuperAdmin()) {
            $abilities[] = 'super_admin';
        }

        $token = $admin->createToken('admin_token', $abilities)->plainTextToken;
        
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
                'telephone' => $admin->telephone,
                'role' => $admin->getFrontendRole(), // admin, gestionnaire, controleur
                'permissions' => $this->getUserPermissions($admin)
            ]
        ]);
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
     * Mettre à jour le profil de l'admin
     */
    public function updateProfile(Request $request)
    {
        $admin = $request->user();
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'telephone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin->update($request->only(['nom', 'prenom', 'email', 'telephone']));

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $admin->id,
                'nom' => $admin->nom,
                'prenom' => $admin->prenom,
                'email' => $admin->email,
                'telephone' => $admin->telephone,
                'role' => $admin->getFrontendRole()
            ]
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

        if ($admin->isSuperAdmin()) {
            return [
                'can_view_dashboard' => true,
                'can_manage_users' => true,
                'can_manage_agences' => true,
                'can_manage_voyages' => true,
                'can_manage_tickets' => true,
                'can_view_reports' => true,
                'can_manage_settings' => true,
            ];
        }

        if ($admin->isGestionnaire()) {
            return [
                'can_view_dashboard' => true,
                'can_manage_users' => false,
                'can_manage_agences' => false,
                'can_manage_voyages' => true,
                'can_manage_tickets' => true,
                'can_view_reports' => true,
                'can_manage_settings' => false,
            ];
        }

        if ($admin->isControleur()) {
            return [
                'can_view_dashboard' => false,
                'can_manage_users' => false,
                'can_manage_agences' => false,
                'can_manage_voyages' => false,
                'can_manage_tickets' => true, // Peut controller les tickets
                'can_view_reports' => false,
                'can_manage_settings' => false,
            ];
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