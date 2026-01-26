<?php

namespace App\Http\Controllers;

use App\Models\Passager;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PassagerController extends Controller
{
    /**
     * Liste tous les passagers (avec recherche par nom ou billet)
     */
    public function index(Request $request)
    {
        $query = Passager::with('ticket.voyage.trajet');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom_passager', 'like', "%$search%")
                  ->orWhere('prenom_passager', 'like', "%$search%")
                  ->orWhere('numero_piece', 'like', "%$search%");
            });
        }

        if ($request->has('numero_billet')) {
            $query->where('numero_billet', $request->numero_billet);
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(20)
        ]);
    }

    /**
     * Enregistrer les détails d'un passager pour un billet spécifique
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_billet' => 'required|exists:tickets,numeroBillet|unique:passager,numero_billet',
            'nom_passager' => 'required|string|max:100',
            'prenom_passager' => 'required|string|max:100',
            'date_naissance' => 'required|date|before:today',
            'type_piece' => 'required|in:CNI,Passeport,Permis,Scolaire',
            'numero_piece' => 'required|string|max:50',
            'telephone_passager' => 'nullable|string|max:20',
            'email_passager' => 'nullable|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $passager = Passager::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Informations du passager enregistrées.',
            'data' => $passager
        ], 201);
    }

    /**
     * Afficher les détails d'un passager spécifique
     */
    public function show($id)
    {
        $passager = Passager::with(['ticket.voyage.bus', 'ticket.siege'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $passager
        ]);
    }

    /**
     * Mettre à jour les informations d'un passager (ex: correction d'erreur sur la pièce)
     */
    public function update(Request $request, $id)
    {
        $passager = Passager::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom_passager' => 'sometimes|string|max:100',
            'prenom_passager' => 'sometimes|string|max:100',
            'type_piece' => 'sometimes|in:CNI,Passeport,Permis,Scolaire',
            'numero_piece' => 'sometimes|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $passager->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Informations passager mises à jour.',
            'data' => $passager
        ]);
    }

    /**
     * Obtenir la liste des passagers pour un voyage spécifique (Manifeste de bord)
     */
    public function manifesteParVoyage($idVoyage)
    {
        $passagers = Passager::whereHas('ticket', function($query) use ($idVoyage) {
            $query->where('idVoyage', $idVoyage);
        })->with('ticket.siege')->get();

        return response()->json([
            'success' => true,
            'idVoyage' => $idVoyage,
            'nombre_passagers' => $passagers->count(),
            'data' => $passagers
        ]);
    }

    /**
     * Supprimer un passager (Généralement lors de l'annulation d'un billet)
     */
    public function destroy($id)
    {
        $passager = Passager::findOrFail($id);
        $passager->delete();

        return response()->json([
            'success' => true,
            'message' => 'Passager retiré de la liste.'
        ]);
    }
}