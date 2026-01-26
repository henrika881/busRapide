<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminResourceController extends Controller
{
    /**
     * Liste des employés/admins
     */
    public function index()
    {
        $admins = Admin::all();

        return response()->json([
            'success' => true,
            'data' => $admins
        ]);
    }

    /**
     * Créer un nouvel admin/employé
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:admins,email',
            'telephone' => 'nullable|string',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,gestionnaire,controleur',
            'date_embauche' => 'nullable|date',
            'statut' => 'required|in:actif,inactif'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Génération automatique du matricule (ex: ADM-2023-XXXX)
        $matricule = 'ADM-' . date('Y') . '-' . strtoupper(Str::random(4));

        $admin = Admin::create([
            'matricule' => $matricule,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password), 
            'role' => $request->role,
            'date_embauche' => $request->date_embauche,
            'statut' => $request->statut,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès.',
            'data' => $admin
        ], 201);
    }

    /**
     * Détails d'un admin
     */
    public function show($id)
    {
        $admin = Admin::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $admin
        ]);
    }

    /**
     * Mettre à jour les informations
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|email|unique:admins,email,' . $id,
            'statut' => 'sometimes|in:actif,inactif',
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:super_admin,gestionnaire,controleur'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Informations mises à jour.',
            'data' => $admin
        ]);
    }

    /**
     * Supprimer un admin
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        
        // Soft delete manuel ou vrai delete
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé.'
        ]);
    }
}