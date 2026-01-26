<?php

namespace App\Http\Controllers;

use App\Models\Bagage;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BagageController extends Controller
{
    /**
     * Liste tous les bagages (avec filtres optionnels)
     */
    public function index(Request $request)
    {
        $query = Bagage::with('ticket.client');

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('numero_billet')) {
            $query->where('numero_billet', $request->numero_billet);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    /**
     * Enregistrer un nouveau bagage pour un ticket
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_billet' => 'required|exists:tickets,numeroBillet',
            'poids' => 'required|numeric|min:0.5',
            'type_bagage' => 'required|string|in:valise,sac_dos,carton,autre',
            'dimensions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Génération d'un Tag ID unique (format: TAG-XXXXXXXX)
        $tagId = 'TAG-' . strtoupper(Str::random(8));

        $bagage = Bagage::create([
            'tag_id' => $tagId,
            'numero_billet' => $request->numero_billet,
            'poids' => $request->poids,
            'type_bagage' => $request->type_bagage,
            'dimensions' => $request->dimensions,
            'statut' => 'enregistre',
            'date_enregistrement' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bagage enregistré avec succès.',
            'data' => $bagage
        ], 201);
    }

    /**
     * Afficher les détails d'un bagage via son Tag ID
     */
    public function show($tag_id)
    {
        $bagage = Bagage::with(['ticket.voyage.trajet', 'ticket.client'])->findOrFail($tag_id);

        return response()->json([
            'success' => true,
            'data' => $bagage
        ]);
    }

    /**
     * Mettre à jour le statut du bagage (ex: chargé dans le bus)
     */
    public function updateStatus(Request $request, $tag_id)
    {
        $bagage = Bagage::findOrFail($tag_id);

        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:enregistre,charge,decharge,recupere,perdu'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $bagage->update(['statut' => $request->statut]);

        return response()->json([
            'success' => true,
            'message' => 'Statut du bagage mis à jour.',
            'data' => $bagage
        ]);
    }

    /**
     * Liste des bagages pour un voyage spécifique
     * Utile pour le chauffeur ou le chargeur
     */
    public function getBagagesParVoyage($idVoyage)
    {
        $bagages = Bagage::whereHas('ticket', function($query) use ($idVoyage) {
            $query->where('idVoyage', $idVoyage);
        })->get();

        return response()->json([
            'success' => true,
            'idVoyage' => $idVoyage,
            'total_bagages' => $bagages->count(),
            'poids_total' => $bagages->sum('poids'),
            'data' => $bagages
        ]);
    }

    /**
     * Supprimer un enregistrement de bagage
     */
    public function destroy($tag_id)
    {
        $bagage = Bagage::findOrFail($tag_id);
        $bagage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bagage supprimé.'
        ]);
    }
}