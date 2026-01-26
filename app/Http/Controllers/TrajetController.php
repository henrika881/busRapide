<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class TrajetController extends Controller
{
    /**
     * Liste tous les trajets
     */
    public function index(Request $request)
    {
        // $limit = $request->get('limit', 100);
        // $trajets = Trajet::select('idTrajet', 'villeDepart', 'villeArrivee', 'dureeEstimee', 'distance', 'prixStandard', 'prixVIP', 'created_at')
        //     ->limit($limit)
        //     ->get();
        // return response()->json(['success' => true, 'data' => $trajets]);
        try {
            $trajets = Trajet::limit(100)->get(); // ou votre logique
            return response()->json([
                'success' => true,
                'data' => $trajets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des trajets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouveau trajet
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'villeDepart' => 'required|string|max:100',
            'villeArrivee' => 'required|string|max:100|different:villeDepart',
            'duree' => 'required|integer|min:1',
            'distance' => 'required|numeric|min:0.1',
            'prixStandard' => 'required|numeric|min:0',
            'prixVIP' => 'required|numeric|min:0|gte:prixStandard'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Convert minutes to H:i:s
        $hours = floor($validated['duree'] / 60);
        $minutes = $validated['duree'] % 60;
        $dureeFormatted = sprintf('%02d:%02d:00', $hours, $minutes);

        // Préparer les données pour créer le trajet
        $trajetData = [
            'villeDepart' => $validated['villeDepart'],
            'villeArrivee' => $validated['villeArrivee'],
            'distance' => $validated['distance'],
            'dureeEstimee' => $dureeFormatted,
            'prixBase' => $validated['prixStandard'],
        ];

        // Ajouter prixStandard et prixVIP si les colonnes existent
        if (Schema::hasColumn('trajets', 'prixStandard')) {
            $trajetData['prixStandard'] = $validated['prixStandard'];
        }
        if (Schema::hasColumn('trajets', 'prixVIP')) {
            $trajetData['prixVIP'] = $validated['prixVIP'];
        }

        $trajet = Trajet::create($trajetData);

        return response()->json(['success' => true, 'data' => $trajet], 201);
    }

    /**
     * Afficher un trajet spécifique
     */
    public function show(Trajet $trajet)
    {
        $trajet->load(['voyages', 'bus']);
        return response()->json(['success' => true, 'data' => $trajet]);
    }

    /**
     * Mettre à jour un trajet
     */
    public function update(Request $request, Trajet $trajet)
    {
        $validator = Validator::make($request->all(), [
            'villeDepart' => 'sometimes|string|max:100',
            'villeArrivee' => 'sometimes|string|max:100|different:villeDepart',
            'duree' => 'sometimes|integer|min:1',
            'distance' => 'sometimes|numeric|min:0.1',
            'prixStandard' => 'sometimes|numeric|min:0',
            'prixVIP' => 'sometimes|numeric|min:0|gte:prixStandard'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $trajetData = $validated;

        // Convert minutes to H:i:s if provided
        if (isset($validated['duree'])) {
            $hours = floor($validated['duree'] / 60);
            $minutes = $validated['duree'] % 60;
            $trajetData['dureeEstimee'] = sprintf('%02d:%02d:00', $hours, $minutes);
        }

        // Map prixStandard to prixBase if provided
        if (isset($validated['prixStandard'])) {
            $trajetData['prixBase'] = $validated['prixStandard'];
        }

        $trajet->update($trajetData);
        return response()->json(['success' => true, 'data' => $trajet]);
    }

    /**
     * Supprimer un trajet
     */
    public function destroy(Trajet $trajet)
    {
        $trajet->delete();
        return response()->json(['success' => true, 'message' => 'Trajet supprimé']);
    }
}
