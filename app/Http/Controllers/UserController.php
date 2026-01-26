<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs (réservé aux administrateurs)
     */
    public function index()
    {
        $users = User::paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Enregistrement d'un nouvel utilisateur (Inscription)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|unique:users,telephone',
            'password' => ['required', Password::min(8)],
            'numero_cni' => 'nullable|string|unique:users,numero_cni',
            'type_utilisateur' => 'required|in:client,passager,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password), // Le cast 'hashed' s'en occupe aussi mais c'est plus sûr ici
            'numero_cni' => $request->numero_cni,
            'type_utilisateur' => $request->type_utilisateur,
            'date_creation' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès.',
            'data' => $user
        ], 201);
    }

    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Mettre à jour les informations de profil
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'telephone' => 'sometimes|string|unique:users,telephone,' . $user->id,
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'numero_cni' => 'sometimes|string|unique:users,numero_cni,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['nom', 'prenom', 'telephone', 'email', 'numero_cni']));

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'data' => $user
        ]);
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.'
            ], 403);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès.'
        ]);
    }

    /**
     * Supprimer un compte
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compte utilisateur supprimé.'
        ]);
    }
}