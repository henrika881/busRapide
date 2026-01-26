<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::all();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|max:50|unique:admins',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:100|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,gestionnaire',
            'statut' => 'sometimes|in:actif,inactif,suspendu',
            'date_embauche' => 'nullable|date',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        Admin::create($validated);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'sometimes|string|email|max:100|unique:admins,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'sometimes|in:super_admin,admin,gestionnaire',
            'statut' => 'sometimes|in:actif,inactif,suspendu',
            'date_embauche' => 'nullable|date',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::findOrFail($id);
        
        // Empêcher la suppression de soi-même
        if ($admin->id === Auth::id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrateur supprimé avec succès.');
    }

    /**
     * Toggle admin status
     */
    public function toggleStatus(string $id)
    {
        $admin = Admin::findOrFail($id);
        
        // Empêcher de désactiver son propre compte
        if ($admin->id === Auth::id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Vous ne pouvez pas modifier votre propre statut.');
        }

        $admin->statut = $admin->statut === 'actif' ? 'inactif' : 'actif';
        $admin->save();

        $status = $admin->statut === 'actif' ? 'activé' : 'désactivé';
        
        return redirect()->route('admin.admins.index')
            ->with('success', "Administrateur {$status} avec succès.");
    }

    /**
     * Admin profile
     */
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'sometimes|string|email|max:100|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.profile')
            ->with('success', 'Profil mis à jour avec succès.');
    }
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

