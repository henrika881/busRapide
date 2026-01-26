<?php

namespace App\Http\Controllers;

use App\Models\Siege;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiegeController extends Controller
{
    /**
     * Liste des sièges d'un bus spécifique
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idBus' => 'required|exists:bus,idBus'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $sieges = Siege::where('idBus', $request->idBus)
            ->orderBy('numeroSiege')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sieges
        ]);
    }

    /**
     * Vérifier la disponibilité d'un siège pour un voyage précis
     */
    public function verifierDisponibilite(Request $request, $id)
    {
        $siege = Siege::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'idVoyage' => 'required|exists:voyages,idVoyage'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $disponible = $siege->estDisponiblePourVoyage($request->idVoyage);

        return response()->json([
            'success' => true,
            'idSiege' => $siege->idSiege,
            'disponible' => $disponible,
            'message' => $disponible ? "Le siège est libre." : "Le siège est déjà réservé ou occupé pour ce voyage."
        ]);
    }

    /**
     * Mettre à jour les paramètres d'un siège (ex: passer en VIP ou changer le surcoût)
     */
    public function update(Request $request, $id)
    {
        $siege = Siege::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|in:fenetre,couloir,premium',
            'classe' => 'sometimes|in:standard,vip',
            'statut' => 'sometimes|in:libre,reserve,occupe,maintenance',
            'estVIP' => 'sometimes|boolean',
            'surcoutVIP' => 'sometimes|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $siege->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Siège mis à jour avec succès.',
            'data' => $siege
        ]);
    }

    /**
     * Obtenir le plan de salle (Layout) pour un bus
     */
    public function layout($idBus)
    {
        $sieges = Siege::where('idBus', $idBus)
            ->orderBy('numeroSiege')
            ->get();

        // On groupe pour faciliter l'affichage côté Front-end (Mobile/Web)
        $layout = [
            'vip' => $sieges->where('classe', 'vip')->values(),
            'standard' => $sieges->where('classe', 'standard')->values(),
            'total' => $sieges->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $layout
        ]);
    }

    /**
     * Libérer manuellement un siège (Urgence/Maintenance)
     */
    public function liberer($id)
    {
        $siege = Siege::findOrFail($id);
        $siege->liberer();

        return response()->json([
            'success' => true,
            'message' => "Le siège {$siege->numeroSiege} est désormais libre."
        ]);
    }
}