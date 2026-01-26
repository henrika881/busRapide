<?php

namespace App\Http\Controllers;

use App\Models\Voyage;
use App\Models\Bus;
use App\Models\Trajet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoyageController extends Controller
{
    /**
     * Liste tous les voyages
     */
    public function index(Request $request)
    {
        $query = Voyage::query();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_depart')) {
            $query->whereDate('dateHeureDepart', $request->date_depart);
        }

        if ($request->filled('trajet_id')) {
            $query->where('idTrajet', $request->trajet_id);
        }

        $limit = $request->get('limit', 100);
        $voyages = $query->orderBy('dateHeureDepart', 'desc')->with('bus', 'trajet')->limit($limit)->get();

        return response()->json(['success' => true, 'data' => $voyages]);
    }

    /**
     * Créer un nouveau voyage
     */
    public function store(Request $request)
    {
        // Debug: log les données reçues
        Log::info('=== NOUVEAU VOYAGE - DONNEES RECUES ===', $request->all());

        $validator = Validator::make($request->all(), [
            'idBus' => 'required|exists:bus,idBus',
            'idTrajet' => 'required|exists:trajets,idTrajet',
            'dateHeureDepart' => 'required|date|after:now',
            'prixStandard' => 'required|numeric|min:0',
            'prixVIP' => 'required|numeric|min:0',
            'dateHeureArrivee' => 'nullable|date|after:dateHeureDepart',
        ], [
            'idBus.required' => 'Le bus est obligatoire',
            'idBus.exists' => 'Le bus sélectionné n\'existe pas',
            'idTrajet.required' => 'Le trajet est obligatoire',
            'idTrajet.exists' => 'Le trajet sélectionné n\'existe pas',
            'dateHeureDepart.required' => 'La date de départ est obligatoire',
            'dateHeureDepart.after' => 'La date de départ doit être dans le futur',
            'prixStandard.required' => 'Le prix standard est obligatoire',
            'prixVIP.required' => 'Le prix VIP est obligatoire',
        ]);

        if ($validator->fails()) {
            Log::error('=== ERREURS DE VALIDATION ===', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $bus = Bus::with(['sieges'])->findOrFail($request->idBus);
                $trajet = Trajet::findOrFail($request->idTrajet);

                // Vérification conflit (2h de marge entre voyages)
                $debut = Carbon::parse($request->dateHeureDepart)->subHours(2);

                // Convertir dureeEstimee en minutes
                $durationMinutes = 0;
                if ($trajet->dureeEstimee instanceof Carbon) {
                    $durationMinutes = ($trajet->dureeEstimee->hour * 60) + $trajet->dureeEstimee->minute;
                } elseif (is_string($trajet->dureeEstimee)) {
                    $time = explode(':', $trajet->dureeEstimee);
                    $durationMinutes = ($time[0] * 60) + ($time[1] ?? 0);
                }

                // Si date d'arrivée n'est pas fournie, utiliser durée du trajet
                if ($request->filled('dateHeureArrivee')) {
                    $fin = Carbon::parse($request->dateHeureArrivee)->addHours(2);
                } else {
                    $fin = Carbon::parse($request->dateHeureDepart)
                        ->addMinutes($durationMinutes)
                        ->addHours(2);
                }

                $conflit = Voyage::where('idBus', $request->idBus)
                    ->whereBetween('dateHeureDepart', [$debut, $fin])
                    ->where('statut', '!=', 'annule')
                    ->exists();

                if ($conflit) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bus déjà occupé à cette période'
                    ], 400);
                }

                // Calculer la date d'arrivée si non fournie
                if ($request->dateHeureArrivee) {
                    $dateArrivee = Carbon::parse($request->dateHeureArrivee);
                } else {
                    $dateArrivee = Carbon::parse($request->dateHeureDepart)->addMinutes($durationMinutes);
                }

                // Compter les sièges par classe
                $siegesVIP = $bus->sieges()->where('classe', 'vip')->count();
                $siegesStandard = $bus->sieges()->where('classe', 'standard')->count();
                $totalSieges = $siegesVIP + $siegesStandard;

                $voyage = Voyage::create([
                    'idBus' => $request->idBus,
                    'idTrajet' => $request->idTrajet,
                    'dateHeureDepart' => $request->dateHeureDepart,
                    'dateHeureArrivee' => $dateArrivee,
                    'prixStandard' => $request->prixStandard,
                    'prixVIP' => $request->prixVIP,
                    'prixActuel' => $request->prixStandard,
                    'siegesStandardDisponibles' => $siegesStandard,
                    'siegesVIPDisponibles' => $siegesVIP,
                    'placesDisponiblesTotal' => $totalSieges,
                    'placesDisponibles' => $totalSieges,
                    'statut' => 'planifie'
                ]);

                Log::info('=== VOYAGE CREE AVEC SUCCES ===', ['id' => $voyage->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Voyage créé avec succès',
                    'data' => $voyage->load(['bus', 'trajet'])
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error('=== ERREUR CREATION VOYAGE ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du voyage',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Afficher un voyage spécifique
     */
    public function show($id)
    {
        $voyage = Voyage::with(['bus', 'trajet', 'tickets.client', 'bus.sieges'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $voyage]);
    }

    /**
     * Mettre à jour un voyage
     */
    public function update(Request $request, $id)
    {
        $voyage = Voyage::findOrFail($id);

        // Ne pas permettre la modification si le voyage a déjà commencé
        if ($voyage->dateHeureDepart < now() && $voyage->statut != 'planifie') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de modifier un voyage déjà commencé'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'idBus' => 'sometimes|exists:bus,idBus',
            'dateHeureDepart' => 'sometimes|date|after:now',
            'prixStandard' => 'sometimes|numeric|min:0',
            'prixVIP' => 'sometimes|numeric|min:0',
            'statut' => 'sometimes|in:planifie,en_cours,termine,annule',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Si changement de bus, vérifier la disponibilité
        if ($request->filled('idBus') && $request->idBus != $voyage->idBus) {
            $nouveauBus = Bus::with(['sieges'])->findOrFail($request->idBus);

            // Vérifier conflit sur le nouveau bus
            $debut = Carbon::parse($request->dateHeureDepart ?? $voyage->dateHeureDepart)->subHours(2);
            $fin = Carbon::parse($voyage->dateHeureArrivee)->addHours(2);

            $conflit = Voyage::where('idBus', $request->idBus)
                ->where('id', '!=', $id)
                ->whereBetween('dateHeureDepart', [$debut, $fin])
                ->where('statut', '!=', 'annule')
                ->exists();

            if ($conflit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nouveau bus déjà occupé à cette période'
                ], 400);
            }

            // Mettre à jour les compteurs de sièges
            $siegesVIP = $nouveauBus->sieges()->where('classe', 'vip')->count();
            $siegesStandard = $nouveauBus->sieges()->where('classe', 'standard')->count();

            // Ajuster les sièges disponibles (ne pas dépasser la nouvelle capacité)
            $voyage->siegesVIPDisponibles = min($voyage->siegesVIPDisponibles, $siegesVIP);
            $voyage->siegesStandardDisponibles = min($voyage->siegesStandardDisponibles, $siegesStandard);
            $voyage->placesDisponiblesTotal = $siegesVIP + $siegesStandard;
        }

        $voyage->update($request->only(['idBus', 'dateHeureDepart', 'prixStandard', 'prixVIP', 'statut']));

        return response()->json([
            'success' => true,
            'message' => 'Voyage mis à jour avec succès',
            'data' => $voyage->load(['bus', 'trajet'])
        ]);
    }

    /**
     * Annuler un voyage
     */
    public function annuler($id)
    {
        $voyage = Voyage::findOrFail($id);

        // Vérifier si le voyage n'a pas déjà commencé
        if ($voyage->dateHeureDepart < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'annuler un voyage déjà commencé'
            ], 400);
        }

        // Annuler tous les tickets associés
        $voyage->tickets()->update(['statut' => 'annule']);

        // Marquer le voyage comme annulé
        $voyage->update(['statut' => 'annule']);

        return response()->json([
            'success' => true,
            'message' => 'Voyage et tickets associés annulés avec succès'
        ]);
    }

    /**
     * Supprimer un voyage (soft delete)
     */
    public function destroy($id)
    {
        $voyage = Voyage::findOrFail($id);

        // Vérifier qu'il n'y a pas de tickets actifs
        $ticketsActifs = $voyage->tickets()
            ->whereIn('statut', ['confirme', 'reserve'])
            ->exists();

        if ($ticketsActifs) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un voyage avec des tickets actifs'
            ], 400);
        }

        $voyage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voyage supprimé avec succès'
        ]);
    }

    /**
     * Recherche de voyages (publique)
     */
    public function search(Request $request)
    {
        // 1. Smart Mapping (Normalisation des villes)
        $cityMapping = [
            'dla' => 'douala',
            'yde' => 'yaounde',
            'bfs' => 'bafoussam',
            'kbi' => 'kribi',
            'bue' => 'buea',
            'gar' => 'garoua',
            'nga' => 'ngaoundere',
            'bda' => 'bamenda',
            'ebl' => 'ebolowa'
        ];

        $villeDepart = strtolower($request->ville_depart);
        $villeArrivee = strtolower($request->ville_arrivee);

        $request->merge([
            'ville_depart' => $cityMapping[$villeDepart] ?? $request->ville_depart,
            'ville_arrivee' => $cityMapping[$villeArrivee] ?? $request->ville_arrivee
        ]);

        $validator = Validator::make($request->all(), [
            'ville_depart' => 'required|string',
            'ville_arrivee' => 'required|string',
            'date_voyage' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Helper pour formater les voyages
        $formatVoyage = function ($v) {
            $hasVipSeats = $v->bus->sieges->where('classe', 'vip')->count() > 0;
            $typeBus = $hasVipSeats ? 'VIP' : 'Standard';
            
            // Le prix affiché dépend du type de bus principal
            $prixAffiche = $hasVipSeats ? $v->prixVIP : $v->prixStandard;

            return [
                'idVoyage' => $v->idVoyage,
                'bus' => $v->bus,
                'trajet' => $v->trajet,
                'prix' => (int) $prixAffiche,
                'prixStandard' => (int) $v->prixStandard,
                'prixVIP' => (int) $v->prixVIP,
                'heure_depart' => \Carbon\Carbon::parse($v->dateHeureDepart)->format('H:i'),
                'heure_arrivee' => \Carbon\Carbon::parse($v->dateHeureArrivee)->format('H:i'),
                'date_depart' => \Carbon\Carbon::parse($v->dateHeureDepart)->format('Y-m-d'),
                'date_depart_fr' => \Carbon\Carbon::parse($v->dateHeureDepart)->isoFormat('dddd D MMMM YYYY'),
                'places_disponibles' => (int) $v->placesDisponiblesTotal,
                'type_bus' => $typeBus, 
                'categorie' => strtolower($typeBus),
                'duree_estimee' => $v->trajet->dureeEstimee,
                'climatisation' => true,
                'wifi' => true
            ];
        };

        // 2. Recherche Flexible (± 14 jours)
        $dateVoyage = \Carbon\Carbon::parse($request->date_voyage);
        $dateDeb = $dateVoyage->copy()->subDays(14)->startOfDay();
        $dateFin = $dateVoyage->copy()->addDays(14)->endOfDay();

        $voyages = Voyage::with(['bus.sieges', 'trajet'])
            ->whereHas('trajet', function ($q) use ($request) {
                $q->where('villeDepart', 'like', "%{$request->ville_depart}%")
                  ->where('villeArrivee', 'like', "%{$request->ville_arrivee}%");
            })
            ->whereBetween('dateHeureDepart', [$dateDeb, $dateFin])
            ->where('dateHeureDepart', '>=', now())
            ->where('statut', 'planifie')
            ->where('placesDisponiblesTotal', '>', 0)
            // Tri par proximité absolue de la date demandée
            ->orderByRaw("ABS(DATEDIFF(dateHeureDepart, ?)) ASC", [$dateVoyage->toDateString()])
            ->orderBy('dateHeureDepart', 'asc')
            ->get();

        $results = $voyages->map(function($v) use ($request) {
            $formatted = $v->toArray();
            
            // Calculer si c'est la date exacte
            $isExact = \Carbon\Carbon::parse($v->dateHeureDepart)->isSameDay($request->date_voyage);
            
            $hasVipSeats = $v->bus->sieges->where('classe', 'vip')->count() > 0;
            $typeBus = $hasVipSeats ? 'VIP' : 'Standard';
            $prixAffiche = $hasVipSeats ? $v->prixVIP : $v->prixStandard;

            return [
                'idVoyage' => $v->idVoyage,
                'bus' => $v->bus,
                'trajet' => $v->trajet,
                'prix' => (int) $prixAffiche,
                'prixStandard' => (int) $v->prixStandard,
                'prixVIP' => (int) $v->prixVIP,
                'heure_depart' => \Carbon\Carbon::parse($v->dateHeureDepart)->format('H:i'),
                'heure_arrivee' => \Carbon\Carbon::parse($v->dateHeureArrivee)->format('H:i'),
                'date_depart' => \Carbon\Carbon::parse($v->dateHeureDepart)->format('Y-m-d'),
                'date_depart_fr' => \Carbon\Carbon::parse($v->dateHeureDepart)->isoFormat('dddd D MMMM YYYY'),
                'places_disponibles' => (int) $v->placesDisponiblesTotal,
                'type_bus' => $typeBus, 
                'categorie' => strtolower($typeBus),
                'duree_estimee' => $v->trajet->dureeEstimee,
                'climatisation' => true,
                'wifi' => true,
                'is_exact' => $isExact
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count(),
            'requested_date' => $request->date_voyage
        ]);
    }

    /**
     * Obtenir les sièges disponibles pour un voyage
     */
    public function getSiegesDisponibles($id)
    {
        $voyage = Voyage::with(['bus.sieges'])->findOrFail($id);

        // Récupérer les numéros de sièges déjà réservés ou confirmés pour ce voyage
        $occupations = DB::table('tickets')
            ->where('idVoyage', $id)
            ->whereIn('statut', ['confirme', 'reserve'])
            ->pluck('idSiege')
            ->toArray();

        // Récupérer les réservations temporaires actives
        $temps = DB::table('reservation_temps')
            ->where('idVoyage', $id)
            ->where('statut', 'attente')
            ->where('dateExpiration', '>', now())
            ->pluck('idSiege')
            ->toArray();

        $allOccupied = array_unique(array_merge($occupations, $temps));

        $sieges = $voyage->bus->sieges->map(function ($siege) use ($allOccupied, $voyage) {
            $isOccupied = in_array($siege->idSiege, $allOccupied);
            return [
                'idSiege' => $siege->idSiege,
                'numero' => (int) $siege->numeroSiege,
                'classe' => $siege->classe,
                'statut' => $isOccupied ? 'occupe' : 'libre',
                'prix' => $siege->classe === 'vip' ? (int) $voyage->prixVIP : (int) $voyage->prixStandard
            ];
        })->sortBy('numero')->values();

        return response()->json([
            'success' => true,
            'data' => $sieges,
            'voyage' => [
                'id' => $voyage->idVoyage,
                'prixStandard' => (int) $voyage->prixStandard,
                'prixVIP' => (int) $voyage->prixVIP
            ]
        ]);
    }

    /**
     * Statistiques pour le dashboard admin
     */
    public function statistiques(Request $request)
    {
        // Total des voyages par statut
        $totalVoyages = Voyage::count();
        $voyagesPlanifies = Voyage::where('statut', 'planifie')->count();
        $voyagesEnCours = Voyage::where('statut', 'en_cours')->count();
        $voyagesTermines = Voyage::where('statut', 'termine')->count();

        // Voyages aujourd'hui
        $voyagesAujourdhui = Voyage::whereDate('dateHeureDepart', now()->toDateString())->count();

        // Bus en service aujourd'hui
        $busEnService = Bus::where('statut', 'en_service')->count();

        // Occupation moyenne
        $voyagesRecents = Voyage::where('dateHeureDepart', '>=', now()->subDays(30))->get();
        $occupationMoyenne = $voyagesRecents->avg(function ($voyage) {
            $totalSieges = $voyage->bus->sieges->count();
            $siegesOccupees = $totalSieges - $voyage->placesDisponiblesTotal;
            return $totalSieges > 0 ? ($siegesOccupees / $totalSieges) * 100 : 0;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total_voyages' => $totalVoyages,
                'voyages_planifies' => $voyagesPlanifies,
                'voyages_en_cours' => $voyagesEnCours,
                'voyages_termines' => $voyagesTermines,
                'voyages_aujourdhui' => $voyagesAujourdhui,
                'bus_en_service' => $busEnService,
                'occupation_moyenne' => round($occupationMoyenne, 2),
                'derniers_voyages' => Voyage::with(['bus', 'trajet'])
                    ->orderBy('dateHeureDepart', 'desc')
                    ->limit(5)
                    ->get()
            ]
        ]);
    }

    /**
     * Marquer un voyage comme départ
     */
    public function marquerDepart($id)
    {
        $voyage = Voyage::findOrFail($id);

        if ($voyage->statut != 'planifie') {
            return response()->json([
                'success' => false,
                'message' => 'Le voyage n\'est pas en statut planifié'
            ], 400);
        }

        $voyage->update([
            'statut' => 'en_cours',
            'heureDepartReelle' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Départ du voyage enregistré'
        ]);
    }

    /**
     * Marquer un voyage comme arrivé
     */
    public function marquerArrivee($id)
    {
        $voyage = Voyage::findOrFail($id);

        if ($voyage->statut != 'en_cours') {
            return response()->json([
                'success' => false,
                'message' => 'Le voyage n\'est pas en cours'
            ], 400);
        }

        $voyage->update([
            'statut' => 'termine',
            'heureArriveeReelle' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Arrivée du voyage enregistrée'
        ]);
    }
}