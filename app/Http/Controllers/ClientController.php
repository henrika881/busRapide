<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientVIP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Inscription d'un nouveau client
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:clients,email',
            'motDePasse' => 'required|string|min:8|confirmed',
            'telephone' => 'required|string|max:20',
            'numeroCNI' => 'required|string|max:50|unique:clients,numeroCNI',
            'accept_terms' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $client = Client::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'motDePasse' => Hash::make($request->motDePasse),
            'telephone' => $request->telephone,
            'numeroCNI' => $request->numeroCNI,
            'statut' => 'actif'
        ]);

        $token = $client->createToken('client-token', ['client'])->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'client' => $client->makeHidden(['motDePasse']),
                'token' => $token
            ],
            'message' => 'Inscription réussie'
        ], 201);
    }

    /**
     * Connexion d'un client
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'motDePasse' => 'required|string'
        ]);

        // On cherche le client
        $client = Client::where('email', $request->email)->first();

        // Vérification manuelle du Hash puisque tu n'utilises pas le guard par défaut
        if (!$client || !Hash::check($request->motDePasse, $client->motDePasse)) {
            return response()->json(['success' => false, 'message' => 'Identifiants incorrects'], 401);
        }

        if ($client->statut !== 'actif') {
            return response()->json(['success' => false, 'message' => "Compte {$client->statut}"], 403);
        }

        // On génère le token avec la capacité 'client'
        $token = $client->createToken('client-token', ['client'])->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'client' => $client,
                'token' => $token,
                // 'est_vip' => $client->estVIP()
            ]
        ]);
    }

    /**
     * Profil complet avec statistiques
     */
    public function profile(Request $request)
    {
        $client = $request->user();

        // Sécuriser le chargement de VIP car la table peut manquer
        try {
            $client->load(['vip']);
        } catch (\Exception $e) {
            \Log::warning("VIP relationship failed: " . $e->getMessage());
        }

        // Calcul des stats directement pour éviter des erreurs de méthodes manquantes
        $statistiques = [
            'total_tickets' => $client->tickets()->count(),
            'montant_total' => $client->tickets()->sum('prixPaye'),
            'prochain_voyage' => $client->tickets()
                ->whereHas('voyage', fn($q) => $q->where('dateHeureDepart', '>=', now()->format('Y-m-d H:i:s')))
                ->with('voyage.trajet')
                ->first()
        ];

        return response()->json([
            'success' => true,
            'data' => ['client' => $client, 'statistiques' => $statistiques]
        ]);
    }

    /**
     * Mise à jour du profil sécurisée
     */
    public function updateProfile(Request $request)
    {
        $client = $request->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:clients,email,' . $client->id_client . ',id_client',
            'telephone' => 'sometimes|string|max:20',
            'current_password' => 'required_with:new_password',
            'new_password' => 'sometimes|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['nom', 'prenom', 'email', 'telephone']);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $client->motDePasse)) {
                return response()->json(['success' => false, 'message' => 'Ancien mot de passe incorrect'], 422);
            }
            $data['motDePasse'] = Hash::make($request->new_password);
        }

        $client->update($data);

        return response()->json(['success' => true, 'data' => $client->fresh(), 'message' => 'Profil mis à jour']);
    }

    /**
     * Historique paginé et filtré
     */
    public function historique(Request $request)
    {
        $tickets = $request->user()->tickets()
            ->with(['voyage.trajet', 'siege'])
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json(['success' => true, 'data' => $tickets]);
    }

    /**
     * Obtenir les notifications du client
     */
    public function notifications(Request $request)
    {
        $notifications = $request->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $notifications]);
    }

    /**
     * Souscription VIP avec Transaction
     */
    public function souscrireVIP(Request $request)
    {
        $client = $request->user();

        if ($client->estVIP()) {
            return response()->json(['success' => false, 'message' => 'Déjà VIP'], 400);
        }

        $request->validate([
            'niveau' => 'required|in:bronze,argent,or,platine',
        ]);

        try {
            return DB::transaction(function () use ($client, $request) {
                $niveau = $request->niveau;

                $clientVIP = ClientVIP::create([
                    'idClient' => $client->id_client,
                    'niveauVIP' => $niveau,
                    'dateAdhesion' => now(),
                    'dateRenouvellement' => now()->addYear(),
                    'statutAbonnement' => 'actif',
                    'prioriteEmbarquement' => $this->getPriorite($niveau),
                    'reductionPermanente' => $this->getReduction($niveau)
                ]);

                return response()->json(['success' => true, 'data' => $clientVIP]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur technique'], 500);
        }
    }

    private function getPriorite($n)
    {
        return ['bronze' => 1, 'argent' => 2, 'or' => 3, 'platine' => 5][$n] ?? 1;
    }

    private function getReduction($n)
    {
        return ['bronze' => 5, 'argent' => 10, 'or' => 15, 'platine' => 20][$n] ?? 0;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Déconnecté']);
    }

    /* --- MÉTHODES ADMIN (CRUD) --- */

    /**
     * Liste des clients (Admin)
     */
    public function index()
    {
        // On retourne tous les clients avec le nombre de tickets
        $clients = Client::withCount('tickets')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $clients]);
    }

    /**
     * Détails d'un client (Admin)
     */
    public function show($id)
    {
        $client = Client::with('tickets.voyage.trajet')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $client]);
    }

    /**
     * Mise à jour d'un client (Admin)
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:clients,email,' . $id . ',id_client',
            'telephone' => 'sometimes|string|max:20',
            'statut' => 'sometimes|in:actif,bloque',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $client->update($request->all());

        return response()->json(['success' => true, 'data' => $client, 'message' => 'Client mis à jour']);
    }

    /**
     * Supprimer un client (Admin)
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(['success' => true, 'message' => 'Client supprimé']);
    }
}