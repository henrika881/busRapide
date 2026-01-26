<?php

namespace App\Http\Controllers;

use App\Models\Embarquement;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmbarquementController extends Controller
{
    /**
     * Valider l'embarquement d'un ticket
     */
    public function validerEmbarquement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_billet' => 'required|string|exists:tickets,numeroBillet',
            'porte_embarquement' => 'required|string|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 1. Récupération du ticket avec ses relations pour éviter les requêtes N+1
        $ticket = Ticket::with(['voyage', 'siege'])
            ->where('numeroBillet', $request->numero_billet)
            ->first();

        // 2. Vérifications de sécurité
        if ($ticket->statut === 'utilise') {
            return response()->json(['success' => false, 'message' => 'Ce ticket a déjà été utilisé.'], 400);
        }

        if (Embarquement::where('idTicket', $ticket->idTicket)->valide()->exists()) {
            return response()->json(['success' => false, 'message' => 'Passager déjà enregistré.'], 400);
        }

        // 3. Traitement avec Transaction
        try {
            return DB::transaction(function () use ($ticket, $request) {
                // Création de l'instance
                $embarquement = new Embarquement([
                    'idTicket' => $ticket->idTicket,
                    'admin_id' => auth()->id(), // Utilise l'ID de l'admin connecté
                    'porteEmbarquement' => $request->porte_embarquement,
                    'statut' => 'en_attente'
                ]);

                // Appel de la méthode de ton modèle
                if ($embarquement->valider()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Embarquement réussi. Siège ' . $ticket->siege->numeroSiege . ' occupé.',
                        'data' => $embarquement->load(['ticket.client', 'admin'])
                    ]);
                }

                throw new \Exception("Échec de la validation du modèle.");
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Erreur lors de la validation : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des embarquements du jour pour l'employé
     */
    public function embarquementsDuJour(Request $request)
    {
        $embarquements = Embarquement::with(['ticket.client', 'ticket.voyage', 'admin'])
            ->where('admin_id', auth()->id())
            ->aujourdhui()
            ->orderBy('dateHeureValidation', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $embarquements
        ]);
    }
}