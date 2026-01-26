<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    /**
     * Liste des paiements avec informations clients et tickets
     */
    public function index(Request $request)
    {
        $query = Paiement::with(['client', 'ticket']);

        // Filtre par statut (complete, en_attente, echoue)
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par mode de paiement
        if ($request->has('mode')) {
            $query->where('mode_paiement', $request->mode);
        }

        return response()->json([
            'success' => true,
            'data' => $query->latest('date_paiement')->get()
        ]);
    }

    /**
     * Enregistrer un nouveau paiement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_client' => 'required|exists:clients,id_client',
            'id_ticket' => 'required|exists:tickets,idTicket',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:especes,carte,mobile_money,virement',
            'banque' => 'nullable|string',
            'numero_autorisation' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            // 1. Générer une référence de transaction unique
            $ref = 'PAY-' . strtoupper(Str::random(10));

            // 2. Créer le paiement
            $paiement = Paiement::create([
                'ref_transaction' => $ref,
                'id_client' => $request->id_client,
                'montant' => $request->montant,
                'date_paiement' => now(),
                'mode_paiement' => $request->mode_paiement,
                'statut' => 'complete', // Ou 'en_attente' selon le flux
                'banque' => $request->banque,
                'numero_autorisation' => $request->numero_autorisation,
            ]);

            // 3. Lier le paiement au ticket et mettre à jour le statut du ticket
            $ticket = Ticket::findOrFail($request->id_ticket);
            $ticket->update([
                'statut' => 'confirme',
                'datePaiement' => now()
            ]);

            // 4. Envoyer la notification (Email + DB)
            $client = \App\Models\Client::find($request->id_client);
            if ($client) {
                // On charge les relations nécessaires pour l'email
                $ticket->load(['voyage.trajet', 'siege', 'client']);
                $client->notify(new \App\Notifications\ReservationConfirmed($ticket));
                
                // Enregistrer aussi dans la table notifications personnalisée si besoin (redondance ou système spécifique)
                // Ici on utilise le système de notification Laravel standard qui peut écrire en DB
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré et ticket confirmé.',
                'data' => $paiement->load('ticket')
            ], 201);
        });
    }

    /**
     * Détails d'un paiement spécifique
     */
    public function show($ref)
    {
        $paiement = Paiement::with(['client', 'ticket.voyage.trajet'])->findOrFail($ref);

        return response()->json([
            'success' => true,
            'data' => $paiement
        ]);
    }

    /**
     * Annuler un paiement (remboursement ou erreur)
     */
    public function annuler($ref)
    {
        $paiement = Paiement::findOrFail($ref);

        if ($paiement->statut === 'annule') {
            return response()->json(['success' => false, 'message' => 'Déjà annulé.'], 400);
        }

        DB::transaction(function () use ($paiement) {
            $paiement->update(['statut' => 'annule']);
            
            // Si le paiement est annulé, on peut aussi annuler le ticket associé
            if ($paiement->ticket) {
                $paiement->ticket->update(['statut' => 'annule']);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Paiement et ticket associé annulés.'
        ]);
    }

    /**
     * Rapport financier simple
     */
    public function rapportJournalier()
    {
        $total = Paiement::whereDate('date_paiement', now())
            ->where('statut', 'complete')
            ->sum('montant');

        $modes = Paiement::whereDate('date_paiement', now())
            ->select('mode_paiement', DB::raw('sum(montant) as total'))
            ->groupBy('mode_paiement')
            ->get();

        return response()->json([
            'success' => true,
            'date' => now()->format('Y-m-d'),
            'recette_totale' => $total,
            'repartition' => $modes
        ]);
    }
}