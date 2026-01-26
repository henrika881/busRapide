<?php

namespace App\Http\Controllers;

use App\Models\PlanVoyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanVoyageController extends Controller
{
    /**
     * Afficher le catalogue complet des plans de voyage
     */
    public function index(Request $request)
    {
        $query = PlanVoyage::with('agence');

        // Filtrer par jour de la semaine
        if ($request->has('jour')) {
            $query->where('jour_semaine', $request->jour);
        }

        // Filtrer par ville/gare de départ
        if ($request->has('depart')) {
            $query->where('gare_depart', 'like', '%' . $request->depart . '%');
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('heure_depart')->get()
        ]);
    }

    /**
     * Créer un nouvel horaire récurrent
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_agence' => 'required|exists:agences,nom_agence',
            'jour_semaine' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi,Dimanche,Quotidien',
            'heure_depart' => 'required|date_format:H:i',
            'gare_depart' => 'required|string',
            'gare_arrivee' => 'required|string',
            'duree_estimee' => 'required|string', // ex: "04:30"
            'prix' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $plan = PlanVoyage::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan de voyage ajouté au catalogue.',
            'data' => $plan
        ], 201);
    }

    /**
     * Voir les détails d'un plan spécifique
     */
    public function show($id)
    {
        $plan = PlanVoyage::with('agence')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    /**
     * Mettre à jour un plan (modifier le prix ou l'heure)
     */
    public function update(Request $request, $id)
    {
        $plan = PlanVoyage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'heure_depart' => 'sometimes|date_format:H:i',
            'prix' => 'sometimes|numeric|min:0',
            'jour_semaine' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $plan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan de voyage mis à jour.',
            'data' => $plan
        ]);
    }

    /**
     * Obtenir tous les départs pour une ville spécifique
     */
    public function getDepartsParVille($ville)
    {
        $plans = PlanVoyage::where('gare_depart', 'like', "%$ville%")
            ->orderBy('heure_depart')
            ->get();

        return response()->json([
            'success' => true,
            'ville' => $ville,
            'data' => $plans
        ]);
    }

    /**
     * Supprimer un plan du catalogue
     */
    public function destroy($id)
    {
        $plan = PlanVoyage::findOrFail($id);
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan de voyage supprimé du catalogue.'
        ]);
    }
}