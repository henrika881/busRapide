<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Siege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BusController extends Controller
{
    /**
     * Liste des bus avec leurs statistiques de sièges
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 100);
        $bus = Bus::with('sieges')
            ->where('deleted_at', null)
            ->limit($limit)
            ->get()
            ->map(function($b) {
                return [
                    'idBus' => $b->idBus,
                    'immatriculation' => $b->immatriculation,
                    'marque' => $b->marque,
                    'modele' => $b->modele,
                    'capaciteTotale' => $b->capaciteTotale,
                    'statut' => $b->statut,
                    'created_at' => $b->created_at,
                    'sieges_count' => $b->sieges->count(),
                    'sieges_v_i_p_count' => $b->sieges->where('classe', 'vip')->count(),
                    'sieges_standard_count' => $b->sieges->where('classe', 'standard')->count()
                ];
            });
        
        return response()->json(['success' => true, 'data' => $bus->values()]);
    }

    /**
     * Créer un bus et générer automatiquement ses sièges
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'immatriculation' => 'required|string|unique:bus,immatriculation',
            'marque' => 'required|string',
            'modele' => 'required|string',
            'capaciteTotale' => 'required|integer|min:1',
            'nbSiegesVIP' => 'required|integer|min:0|max:'.$request->capaciteTotale,
            'statut' => 'required|in:en_service,maintenance,hors_service',
            'dateMiseEnService' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            // 1. Création du bus
            $bus = Bus::create($request->all());

            // 2. Génération automatique des sièges
            $nbVIP = $request->nbSiegesVIP;
            $nbStandard = $request->capaciteTotale - $nbVIP;

            // Création des sièges VIP
            for ($i = 1; $i <= $nbVIP; $i++) {
                Siege::create([
                    'idBus' => $bus->idBus,
                    'numeroSiege' => 'VIP-' . $i,
                    'classe' => 'vip',
                    'type' => 'premium',
                    'statut' => 'libre'
                ]);
            }

            // Création des sièges Standard
            for ($i = 1; $i <= $nbStandard; $i++) {
                Siege::create([
                    'idBus' => $bus->idBus,
                    'numeroSiege' => 'S-' . $i,
                    'classe' => 'standard',
                    'type' => $i % 2 == 0 ? 'couloir' : 'fenetre',
                    'statut' => 'libre'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Bus créé avec {$bus->capaciteTotale} sièges générés.",
                'data' => $bus->load('sieges')
            ], 201);
        });
    }

    /**
     * Détails d'un bus spécifique avec ses sièges
     */
    public function show($id)
    {
        $bus = Bus::with(['sieges', 'voyages' => function($q) {
            $q->where('dateHeureDepart', '>=', now())->take(5);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $bus
        ]);
    }

    /**
     * Mettre à jour les informations du bus
     */
    public function update(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'immatriculation' => 'sometimes|string|unique:bus,immatriculation,'.$id.',idBus',
            'statut' => 'sometimes|in:en_service,en_maintenance,hors_service',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $bus->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Informations du bus mises à jour.',
            'data' => $bus
        ]);
    }

    /**
     * Supprimer un bus (Soft Delete)
     */
    public function destroy($id)
    {
        $bus = Bus::findOrFail($id);

        // On vérifie s'il y a des voyages planifiés avant de supprimer
        if ($bus->voyages()->where('dateHeureDepart', '>', now())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un bus ayant des voyages futurs planifiés.'
            ], 400);
        }

        $bus->delete(); // Utilise le SoftDeletes défini dans ton modèle

        return response()->json([
            'success' => true,
            'message' => 'Bus archivé avec succès.'
        ]);
    }
}