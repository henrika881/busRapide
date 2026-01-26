<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer tous les trajets disponibles
        $trajets = Trajet::where('statut', 'actif')->get();
        
        // Récupérer les paramètres de recherche
        $depart = $request->get('depart', 'douala');
        $arrivee = $request->get('arrivee', 'yaounde');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        // Si vous avez besoin des voyages
        $voyages = Voyage::with(['trajet', 'bus'])
            ->whereDate('dateHeureDepart', $date)
            ->where('statut', 'planifie')
            ->get();
        
        return view('reservation', compact('trajets', 'voyages', 'depart', 'arrivee', 'date'));
    }
}