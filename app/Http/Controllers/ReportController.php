<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Voyage;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Rapport de ventes
     */
    public function ventes(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::now()->startOfMonth());
        $dateFin = $request->input('date_fin', Carbon::now()->endOfMonth());

        $ventes = Ticket::whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as nombre_tickets'),
                DB::raw('SUM(prixPaye) as montant_total')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ventes,
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Rapport d'occupation des bus
     */
    public function occupation(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::now()->startOfMonth());
        $dateFin = $request->input('date_fin', Carbon::now()->endOfMonth());

        $occupation = Voyage::with('bus', 'trajet')
            ->whereBetween('dateDepart', [$dateDebut, $dateFin])
            ->select(
                'idVoyage',
                'idBus',
                'dateDepart',
                DB::raw('(SELECT COUNT(*) FROM sieges WHERE idBus = voyages.idBus) as capacite_total'),
                DB::raw('(SELECT COUNT(*) FROM sieges s 
                    JOIN voyages_sieges vs ON s.idSiege = vs.idSiege 
                    WHERE vs.idVoyage = voyages.idVoyage AND vs.statut = "occupe") as places_occupees')
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $occupation,
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Rapport détaillé des paiements
     */
    public function paiements(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::now()->startOfMonth());
        $dateFin = $request->input('date_fin', Carbon::now()->endOfMonth());

        $paiements = Paiement::with('client', 'ticket')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(
                'methode',
                'statut',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(montant) as montant_total')
            )
            ->groupBy('methode', 'statut')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $paiements,
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Stats pour le dashboard principal
     */
    public function dashboardStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Tickets
        $ticketsToday = Ticket::whereDate('created_at', $today)->count();
        $ticketsYesterday = Ticket::whereDate('created_at', $yesterday)->count();
        $ticketsTrend = $ticketsYesterday > 0 ? (($ticketsToday - $ticketsYesterday) / $ticketsYesterday) * 100 : 0;

        // Revenue
        $revenueToday = Ticket::whereDate('created_at', $today)->sum('prixPaye');
        $revenueYesterday = Ticket::whereDate('created_at', $yesterday)->sum('prixPaye');
        $revenueTrend = $revenueYesterday > 0 ? (($revenueToday - $revenueYesterday) / $revenueYesterday) * 100 : 0;

        // Voyages
        $voyagesToday = Voyage::whereDate('dateHeureDepart', $today)->count();
        $voyagesYesterday = Voyage::whereDate('dateHeureDepart', $yesterday)->count();
        $voyagesTrend = $voyagesYesterday > 0 ? (($voyagesToday - $voyagesYesterday) / $voyagesYesterday) * 100 : 0;

        // Occupation rate (average for today's voyages)
        $occupationToday = Voyage::whereDate('dateHeureDepart', $today)
            ->get()
            ->map(function ($v) {
                return $v->getOccupationRate(); // Assuming this method exists in Voyage model or we calculate it here
            })->avg() ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'kpis' => [
                    [
                        'label' => 'Voyages du jour',
                        'value' => (string) $voyagesToday,
                        'trendUp' => $voyagesTrend >= 0,
                        'trendValue' => number_format(abs($voyagesTrend), 1) . '%',
                        'icon' => 'fa-solid fa-route',
                        'color' => 'text-blue-600 bg-blue-100'
                    ],
                    [
                        'label' => 'Billets vendus',
                        'value' => (string) $ticketsToday,
                        'trendUp' => $ticketsTrend >= 0,
                        'trendValue' => number_format(abs($ticketsTrend), 1) . '%',
                        'icon' => 'fa-solid fa-ticket',
                        'color' => 'text-emerald-600 bg-emerald-100'
                    ],
                    [
                        'label' => 'Taux occupation',
                        'value' => number_format($occupationToday, 1) . '%',
                        'trendUp' => true, // Simplification for now
                        'trendValue' => '--',
                        'icon' => 'fa-solid fa-chart-line',
                        'color' => 'text-amber-600 bg-amber-100'
                    ],
                    [
                        'label' => 'Revenus du jour',
                        'value' => number_format($revenueToday, 0, '.', ' ') . ' FCFA',
                        'trendUp' => $revenueTrend >= 0,
                        'trendValue' => number_format(abs($revenueTrend), 1) . '%',
                        'icon' => 'fa-solid fa-money-bill-wave',
                        'color' => 'text-purple-600 bg-purple-100'
                    ]
                ],
                'stats_fast' => [
                    ['label' => 'CA Journalier', 'value' => number_format($revenueToday / 1000000, 1) . 'M FCFA'],
                    ['label' => 'Voyages Actifs', 'value' => (string) $voyagesToday],
                    ['label' => 'Taux Occupation', 'value' => number_format($occupationToday, 0) . '%'],
                    ['label' => 'Retards', 'value' => '0'] // Mocked for now
                ],
                'counts' => [
                    'total_clients' => \App\Models\Client::count(),
                    'active_buses' => \App\Models\Bus::where('statut', 'en_service')->count(),
                    'tickets_today' => $ticketsToday,
                    'revenue_today' => $revenueToday
                ],
                'client_stats' => [
                    'total' => \App\Models\Client::count(),
                    'actifs' => \App\Models\Client::where('statut', 'actif')->count(),
                    'actifsPourcentage' => \App\Models\Client::count() > 0 ? round((\App\Models\Client::where('statut', 'actif')->count() / \App\Models\Client::count()) * 100) : 0,
                    'nouveauxMois' => \App\Models\Client::whereMonth('created_at', Carbon::now()->month)->count(),
                    'variationMois' => 10, // Mock for now or calculate complex trend
                    'chiffreAffaire' => Ticket::sum('prixPaye'),
                    'caMoyen' => \App\Models\Client::count() > 0 ? round(Ticket::sum('prixPaye') / \App\Models\Client::count()) : 0
                ],
                'recent_voyages' => Voyage::with('trajet', 'bus')
                    ->orderBy('dateHeureDepart', 'desc')
                    ->limit(4)
                    ->get()
                    ->map(function ($v) {
                        return [
                            'idVoyage' => $v->idVoyage,
                            'trajet' => $v->trajet->villeDepart . ' → ' . $v->trajet->villeArrivee,
                            'bus' => $v->bus->immatriculation,
                            'horaire' => $v->dateHeureDepart->format('H:i'),
                            'taux' => round($v->getOccupationRate())
                        ];
                    }),
                'live_activity' => collect([])
                    ->concat(Ticket::with('client')->orderBy('created_at', 'desc')->limit(3)->get()->map(function ($t) {
                        return [
                            'id' => 'ticket-' . $t->idTicket,
                            'icon' => 'fa-solid fa-ticket',
                            'color' => 'text-emerald-500',
                            'message' => 'Billet vendu pour ' . ($t->client->prenom ?? 'Client'),
                            'time' => $t->created_at->diffForHumans()
                        ];
                    }))
                    ->concat(\App\Models\Client::orderBy('created_at', 'desc')->limit(2)->get()->map(function ($c) {
                        return [
                            'id' => 'client-' . $id = $c->idClient,
                            'icon' => 'fa-solid fa-user-plus',
                            'color' => 'text-purple-500',
                            'message' => 'Nouveau client: ' . $c->prenom . ' ' . $c->nom,
                            'time' => $c->created_at->diffForHumans()
                        ];
                    }))
                    ->sortByDesc('time')
                    ->values()
            ]
        ]);
    }

    /**
     * Top Trajets (les plus rentables)
     */
    public function topTrajets(Request $request)
    {
        $limit = $request->input('limit', 5);

        $topTrajets = DB::table('trajets')
            ->join('voyages', 'trajets.idTrajet', '=', 'voyages.idTrajet')
            ->join('tickets', 'voyages.idVoyage', '=', 'tickets.idVoyage')
            ->select(
                'trajets.idTrajet',
                'trajets.villeDepart',
                'trajets.villeArrivee',
                'trajets.distance',
                DB::raw('COUNT(DISTINCT voyages.idVoyage) as total_voyages'),
                DB::raw('COUNT(tickets.idTicket) as total_tickets'),
                DB::raw('SUM(tickets.prixPaye) as total_revenue')
            )
            ->groupBy('trajets.idTrajet', 'trajets.villeDepart', 'trajets.villeArrivee', 'trajets.distance')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topTrajets
        ]);
    }

    /**
     * Performance des Bus
     */
    public function topBuses(Request $request)
    {
        $limit = $request->input('limit', 5);

        $topBuses = DB::table('bus')
            ->join('voyages', 'bus.idBus', '=', 'voyages.idBus')
            ->join('tickets', 'voyages.idVoyage', '=', 'tickets.idVoyage')
            ->select(
                'bus.idBus',
                'bus.immatriculation',
                'bus.marque',
                'bus.modele',
                DB::raw('COUNT(DISTINCT voyages.idVoyage) as total_voyages'),
                DB::raw('COUNT(tickets.idTicket) as total_tickets'),
                DB::raw('SUM(tickets.prixPaye) as total_revenue')
            )
            ->groupBy('bus.idBus', 'bus.immatriculation', 'bus.marque', 'bus.modele')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topBuses
        ]);
    }
}

