<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Voyage;
use App\Models\Siege;
use App\Models\Paiement;
use App\Models\VoyageSiege;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketConfirmation;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Models\Passager;

use App\Services\NelsiusPayService;
use Laravel\Sanctum\PersonalAccessToken;

class TicketController extends Controller
{
    protected $nelsiusPay;

    public function __construct(NelsiusPayService $nelsiusPay)
    {
        $this->nelsiusPay = $nelsiusPay;
    }
    /**
     * Liste des tickets pour l'admin
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            \Log::info('API Tickets appelée', [
                'user_id' => auth()->id(),
                'user_role' => $user ? ($user instanceof \App\Models\Client ? 'client' : ($user->role ?? 'none')) : 'guest',
                'query_params' => $request->all()
            ]);

            $query = Ticket::with([
                'client:id_client,nom,prenom,email,telephone',
                'voyage:idVoyage,dateHeureDepart,idTrajet',
                'voyage.trajet:idTrajet,villeDepart,villeArrivee',
                'siege:idSiege,numeroSiege,classe'
            ]);

            // Si c'est un client qui appelle, on filtre par son ID
            $user = auth()->user();

            // Logique de filtrage par rôle
            if ($user instanceof \App\Models\Client) {
                $query->where('id_client', $user->id_client);
            } elseif ($user && property_exists($user, 'role') && !in_array($user->role, ['admin', 'super_admin'])) {
                // Si c'est un utilisateur avec un rôle autre qu'admin, on tente de filtrer par son ID s'il est lié à un client
                // Note: Ceci est une sécurité supplémentaire au cas où le modèle ne serait pas Client explicitement
                if (isset($user->id_client)) {
                    $query->where('id_client', $user->id_client);
                }
            }

            // Filtres
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('statut', $request->status);
            }

            if ($request->filled('period')) {
                $now = now();
                switch ($request->period) {
                    case 'today':
                        $query->whereDate('created_at', $now->toDateString());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [
                            $now->startOfWeek()->toDateString(),
                            $now->endOfWeek()->toDateString()
                        ]);
                        break;
                    case 'month':
                        $query->whereBetween('created_at', [
                            $now->startOfMonth()->toDateString(),
                            $now->endOfMonth()->toDateString()
                        ]);
                        break;
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numeroBillet', 'like', "%{$search}%")
                        ->orWhere('idTicket', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($q2) use ($search) {
                            $q2->where('nom', 'like', "%{$search}%")
                                ->orWhere('prenom', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('voyage.trajet', function ($q3) use ($search) {
                            $q3->where('villeDepart', 'like', "%{$search}%")
                                ->orWhere('villeArrivee', 'like', "%{$search}%");
                        });
                });
            }

            // Pagination ou limit
            if ($request->filled('limit')) {
                $tickets = $query->orderBy('created_at', 'desc')
                    ->limit($request->limit)
                    ->get();
            } else {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $tickets = $query->orderBy('created_at', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);
            }

            \Log::info('Tickets trouvés', ['count' => $tickets->count()]);

            // Format pour la pagination côté frontend
            if (method_exists($tickets, 'currentPage')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'data' => $tickets->items(),
                        'current_page' => $tickets->currentPage(),
                        'last_page' => $tickets->lastPage(),
                        'per_page' => $tickets->perPage(),
                        'total' => $tickets->total(),
                    ],
                    'count' => $tickets->total()
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $tickets,
                'count' => $tickets->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur dans TicketController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Afficher un ticket spécifique
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::with([
                'client',
                'voyage.trajet',
                'siege',
                'paiement'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $ticket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket non trouvé'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idVoyage' => 'required|exists:voyages,idVoyage',
            'idSieges' => 'required|array',
            'idSieges.*' => 'exists:sieges,idSiege',
            'classe' => 'required|in:standard,vip',
            'modePaiement' => 'required',
            'passagers' => 'required|array',
            'passagers.*.nom' => 'required|string',
            'passagers.*.prenom' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $idSieges = $request->idSieges;
        $client = $request->user();
        $voyage = Voyage::findOrFail($request->idVoyage);

        // Si paiement mobile (Orange/MTN), on passe par Nelsius Pay
        if (in_array($request->modePaiement, ['orange', 'mtn', 'mobile_money'])) {
            return $this->initiateMobilePayment($request, $client, $voyage);
        }

        // Sinon, simulation classique (Carte, Espèces) - Code existant (simulé)
        try {
            return DB::transaction(function () use ($request, $client, $voyage, $idSieges) {
                // ... (Logique existante pour les paiements simulés/carte) ...
                // Note: On garde la logique existante pour la compatibilité, mais on pourra la migrer plus tard.

                $tickets = [];
                $totalAmount = 0;
                $refTransaction = 'PAY-' . strtoupper(Str::random(10));
                $datePaiement = now();

                foreach ($idSieges as $index => $idSiege) {
                    $siege = Siege::findOrFail($idSiege);
                    $passagerData = $request->passagers[$index] ?? $request->passagers[0];

                    if ($voyage->estComplet() || !$siege->estDisponiblePourVoyage($request->idVoyage)) {
                        throw new \Exception("Le siège {$siege->numeroSiege} n'est plus disponible.");
                    }

                    $prixBase = ($request->classe === 'vip') ? $voyage->prixVIP : $voyage->prixStandard;
                    $surcout = ($request->classe === 'vip') ? ($siege->surcoutVIP ?? 0) : 0;
                    $prixTicket = $prixBase + $surcout;
                    $totalAmount += $prixTicket;

                    $ticket = Ticket::create([
                        'numeroBillet' => 'TICK-' . strtoupper(Str::random(8)) . '-' . date('Ymd'),
                        'idVoyage' => $voyage->idVoyage,
                        'id_client' => $client->id_client,
                        'idSiege' => $siege->idSiege,
                        'prixPaye' => $prixTicket,
                        'classeBillet' => $request->classe,
                        'prixBase' => $prixBase,
                        'statut' => 'confirme', // Directement confirmé pour la simulation
                        'modePaiement' => 'carte',
                        'ref_transaction' => $refTransaction,
                        'datePaiement' => $datePaiement
                    ]);

                    // Créer passager
                    Passager::create([
                        'idTicket' => $ticket->idTicket,
                        'numero_billet' => $ticket->numeroBillet,
                        'nom_passager' => $passagerData['nom'] ?? 'Inconnu',
                        'prenom_passager' => $passagerData['prenom'] ?? 'Inconnu',
                        'numero_piece' => $passagerData['numero_piece'] ?? ($passagerData['cni'] ?? 'N/A')
                    ]);

                    $ticket->genererQR();

                    VoyageSiege::updateOrCreate(
                        ['idVoyage' => $voyage->idVoyage, 'idSiege' => $siege->idSiege],
                        ['statut' => 'reserve', 'numeroBillet' => $ticket->numeroBillet]
                    );

                    $tickets[] = $ticket;

                    // Envoi Email Simulation
                    try {
                        if ($client->email) {
                            Mail::to($client->email)->send(new TicketConfirmation($ticket));
                        }
                    } catch (\Exception $e) {
                        \Log::error('Erreur email simu: ' . $e->getMessage());
                    }
                }

                $voyage->mettreAJourDisponibilite();

                Paiement::create([
                    'ref_transaction' => $refTransaction,
                    'id_client' => $client->id_client,
                    'montant' => $totalAmount,
                    'mode_paiement' => 'carte',
                    'statut' => 'valide',
                    'date_paiement' => $datePaiement
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $tickets[0],
                    'tickets' => $tickets,
                    'message' => 'Paiement simulé réussi et tickets générés.'
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Initier un paiement mobile via Nelsius Pay
     */
    protected function initiateMobilePayment($request, $client, $voyage)
    {
        $idSieges = $request->idSieges;

        try {
            return DB::transaction(function () use ($request, $client, $voyage, $idSieges) {
                $totalAmount = 0;
                $tickets = [];
                // Référence temporaire en attendant celle de l'API
                $tempRef = 'PENDING-' . Str::random(10);

                // 1. Calcul du montant total et vérification dispo
                foreach ($idSieges as $idSiege) {
                    $siege = Siege::findOrFail($idSiege);

                    // Check if seat is available
                    if ($voyage->estComplet() || !$siege->estDisponiblePourVoyage($request->idVoyage)) {

                        // Self-cleaning: Check if it's reserved by THIS user in a PENDING state
                        // This allows retrying a failed/cancelled payment immediately
                        $pendingReservation = VoyageSiege::where('idVoyage', $request->idVoyage)
                            ->where('idSiege', $idSiege)
                            ->where('statut', 'reserve')
                            ->first();

                        if ($pendingReservation) {
                            $oldTicket = Ticket::where('numeroBillet', $pendingReservation->numeroBillet)
                                ->where('id_client', $client->id_client) // Must be same user
                                ->where('statut', 'en_attente') // Must be pending
                                ->first();

                            if ($oldTicket) {
                                // Cancel old ticket and free seat for retry
                                $oldTicket->update(['statut' => 'annule']);
                                $pendingReservation->delete(); // Or update to 'libre'
                                // We continue as if it was available
                            } else {
                                throw new \Exception("Le siège {$siege->numeroSiege} n'est plus disponible.");
                            }
                        } else {
                            throw new \Exception("Le siège {$siege->numeroSiege} n'est plus disponible.");
                        }
                    }

                    $prixBase = ($request->classe === 'vip') ? $voyage->prixVIP : $voyage->prixStandard;
                    $surcout = ($request->classe === 'vip') ? ($siege->surcoutVIP ?? 0) : 0;
                    $totalAmount += $prixBase + $surcout;
                }

                // 2. Appel à Nelsius Pay
                $operator = ($request->modePaiement === 'orange') ? 'orange_money' : 'mtn_money';
                // Utiliser le téléphone fourni pour le paiement, ou celui du client par défaut
                $paymentPhone = $request->telephone_paiement ?? $client->telephone;
                // Nettoyage du numéro (garder 9 chiffres)
                $paymentPhone = preg_replace('/[^0-9]/', '', $paymentPhone);
                if (strlen($paymentPhone) > 9)
                    $paymentPhone = substr($paymentPhone, -9);

                $paymentResponse = $this->nelsiusPay->initiatePayment(
                    $totalAmount,
                    $paymentPhone,
                    $operator,
                    "Ticket BusRapide {$voyage->trajet->villeDepart}-{$voyage->trajet->villeArrivee}"
                );

                if (!$paymentResponse['success']) {
                    throw new \Exception("Echec initiation paiement: " . $paymentResponse['message']);
                }

                $nelsiusRef = $paymentResponse['reference'];

                if (!$nelsiusRef) {
                    Log::error("Nelsius Pay Error: No reference returned", ['response' => $paymentResponse]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur lors de l\'initiation du paiement : Référence manquante'
                    ], 500);
                }

                // 3. Créer les tickets en statut "en_attente"
                foreach ($idSieges as $index => $idSiege) {
                    $siege = Siege::findOrFail($idSiege);
                    $passagerData = $request->passagers[$index] ?? $request->passagers[0];

                    $prixBase = ($request->classe === 'vip') ? $voyage->prixVIP : $voyage->prixStandard;
                    $surcout = ($request->classe === 'vip') ? ($siege->surcoutVIP ?? 0) : 0;
                    $prixTicket = $prixBase + $surcout;

                    $ticket = Ticket::create([
                        'numeroBillet' => 'TICK-' . strtoupper(Str::random(8)) . '-' . date('Ymd'),
                        'idVoyage' => $voyage->idVoyage,
                        'id_client' => $client->id_client,
                        'idSiege' => $siege->idSiege,
                        'prixPaye' => $prixTicket,
                        'classeBillet' => $request->classe,
                        'prixBase' => $prixBase,
                        'statut' => 'en_attente', // En attente de validation
                        'modePaiement' => 'mobile',
                        'ref_transaction' => $nelsiusRef, // On stocke la réf Nelsius
                        'datePaiement' => null
                    ]);

                    Passager::create([
                        'idTicket' => $ticket->idTicket,
                        'numero_billet' => $ticket->numeroBillet,
                        'nom_passager' => $passagerData['nom'] ?? 'Inconnu',
                        'prenom_passager' => $passagerData['prenom'] ?? 'Inconnu',
                        'numero_piece' => $passagerData['numero_piece'] ?? ($passagerData['cni'] ?? 'N/A')
                    ]);

                    // On réserve temporairement le siège
                    VoyageSiege::updateOrCreate(
                        ['idVoyage' => $voyage->idVoyage, 'idSiege' => $siege->idSiege],
                        ['statut' => 'reserve', 'numeroBillet' => $ticket->numeroBillet]
                        // Note: Idéalement 'bloque' ou 'en_cours', ici on met 'reserve' pour bloquer la place.
                        // Si le paiement échoue, un cron ou webhook devra libérer.
                    );

                    $tickets[] = $ticket;
                }

                // Créer l'entrée paiement en attente
                Paiement::create([
                    'ref_transaction' => $nelsiusRef,
                    'id_client' => $client->id_client,
                    'montant' => $totalAmount,
                    'mode_paiement' => 'mobile_money',
                    'statut' => 'en_attente',
                    'date_paiement' => now() // La date ne peut pas être null
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'reference' => $nelsiusRef,
                    'message' => 'Paiement initié. Veuillez valider sur votre téléphone.',
                    'tickets' => $tickets // Tickets en attente
                ], 202); // 202 Accepted
            });

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Vérifier le statut d'un paiement (Polling)
     */
    public function checkPaymentStatus(Request $request)
    {
        $reference = $request->reference;
        if (!$reference) {
            return response()->json(['success' => false, 'message' => 'Référence manquante'], 400);
        }

        // 1. Vérifier via le service Nelsius
        $statusData = $this->nelsiusPay->checkTransactionStatus($reference);

        if (!$statusData['success']) {
            // Log the error but don't break the polling with a 400
            Log::error('Check payment status API error', ['reference' => $reference, 'details' => $statusData]);

            return response()->json([
                'success' => true, // Return true so frontend continues polling
                'status' => 'unknown',
                'message' => 'Vérification en cours... (passerelle temporairement indisponible)'
            ]);
        }

        $remoteStatus = $statusData['status']; // 'completed', 'pending', 'failed'

        if ($remoteStatus === 'completed') {
            // 2. Si succès, mettre à jour les tickets et envoyer email
            $updated = DB::transaction(function () use ($reference) {
                $paiement = Paiement::where('ref_transaction', $reference)->first();

                // Si déjà validé, on renvoie juste succès
                if ($paiement && $paiement->statut === 'valide') {
                    return true;
                }

                if ($paiement) {
                    $paiement->update(['statut' => 'valide', 'date_paiement' => now()]);
                }

                // Mettre à jour les tickets
                $tickets = Ticket::where('ref_transaction', $reference)->get();
                foreach ($tickets as $ticket) {
                    $ticket->update([
                        'statut' => 'confirme',
                        'datePaiement' => now()
                    ]);
                    $ticket->genererQR();

                    // Envoyer mail
                    try {
                        if ($ticket->client && $ticket->client->email) {
                            Mail::to($ticket->client->email)->send(new TicketConfirmation($ticket));
                        }
                    } catch (\Exception $e) {
                        \Log::error("Erreur envoi mail ticket {$ticket->numeroBillet}: " . $e->getMessage());
                    }
                }
                return true;
            });

            return response()->json([
                'success' => true,
                'status' => 'completed',
                'message' => 'Paiement validé et tickets émis.'
            ]);

        } elseif ($remoteStatus === 'failed') {
            // Annuler la réservation
            DB::transaction(function () use ($reference) {
                Paiement::where('ref_transaction', $reference)->update(['statut' => 'echoue']);
                $tickets = Ticket::where('ref_transaction', $reference)->get();
                foreach ($tickets as $ticket) {
                    $ticket->update(['statut' => 'annule']);
                    if ($ticket->siege) {
                        VoyageSiege::where('idVoyage', $ticket->idVoyage)
                            ->where('idSiege', $ticket->idSiege)
                            ->update(['statut' => 'libre', 'numeroBillet' => null]);
                    }
                }
            });

            return response()->json([
                'success' => false,
                'status' => 'failed',
                'message' => 'Le paiement a échoué ou a été annulé.'
            ]);
        }

        // Toujours en attente
        return response()->json([
            'success' => true,
            'status' => 'pending',
            'message' => 'En attente de validation...'
        ]);
    }

    /**
     * Confirmer un ticket (pour admin)
     */
    public function confirm($id)
    {
        try {
            $ticket = Ticket::with('client')->findOrFail($id);

            if (!in_array($ticket->statut, ['en_attente', 'reserve'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seuls les tickets en attente ou réservés peuvent être confirmés'
                ], 400);
            }

            $ticket->statut = 'confirme';
            $ticket->datePaiement = now();
            $ticket->save();

            // Envoyer un email de confirmation si le client a un email
            if ($ticket->client && $ticket->client->email) {
                try {
                    Mail::to($ticket->client->email)->send(new TicketConfirmation($ticket));
                } catch (\Exception $mailError) {
                    \Log::error('Erreur envoi email confirmation: ' . $mailError->getMessage());
                }
            }

            \Log::info('Ticket confirmé', ['ticket_id' => $ticket->idTicket]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket confirmé avec succès',
                'data' => $ticket
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur confirmation ticket', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation'
            ], 500);
        }
    }

    /**
     * Annuler un ticket
     */
    public function cancel($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            // Vérifier si le ticket peut être annulé
            if (!in_array($ticket->statut, ['en_attente', 'reserve', 'confirme'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce ticket ne peut pas être annulé dans son état actuel'
                ], 400);
            }

            // Vérifier si le voyage n'a pas déjà commencé
            if ($ticket->voyage && $ticket->voyage->dateHeureDepart < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'annuler un ticket pour un voyage déjà commencé'
                ], 400);
            }

            // Annuler le ticket
            $ticket->statut = 'annule';
            $ticket->save();

            // Libérer le siège si nécessaire
            if ($ticket->siege) {
                $ticket->siege->update(['statut' => 'libre']);
            }

            // Mettre à jour la disponibilité du voyage
            if ($ticket->voyage) {
                $ticket->voyage->mettreAJourDisponibilite();
            }

            \Log::info('Ticket annulé', ['ticket_id' => $ticket->idTicket]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket annulé avec succès',
                'data' => $ticket
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur annulation ticket', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation'
            ], 500);
        }
    }

    /**
     * Marquer un ticket comme utilisé (à l'embarquement)
     */
    public function markAsUsed($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            if ($ticket->statut !== 'confirme') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seuls les tickets confirmés peuvent être marqués comme utilisés'
                ], 400);
            }

            // Vérifier si le voyage a déjà commencé
            if ($ticket->voyage && $ticket->voyage->dateHeureDepart > now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le voyage n\'a pas encore commencé'
                ], 400);
            }

            $ticket->statut = 'utilise';
            $ticket->save();

            \Log::info('Ticket marqué comme utilisé', ['ticket_id' => $ticket->idTicket]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket marqué comme utilisé',
                'data' => $ticket
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur marquage ticket comme utilisé', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage du ticket'
            ], 500);
        }
    }

    /**
     * Envoyer un ticket par email
     */
    public function sendEmail($id)
    {
        try {
            $ticket = Ticket::with('client')->findOrFail($id);

            if (!$ticket->client || !$ticket->client->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le client n\'a pas d\'email enregistré'
                ], 400);
            }

            Mail::to($ticket->client->email)->send(new TicketConfirmation($ticket));

            \Log::info('Ticket envoyé par email', ['ticket_id' => $ticket->idTicket]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket envoyé par email avec succès'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email ticket', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email'
            ], 500);
        }
    }

    /**
     * Supprimer un ticket (archiver)
     */
    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            // Utilise la soft-delete de Laravel.
            // Assurez-vous que le modèle Ticket utilise le trait "SoftDeletes".
            $ticket->delete();

            \Log::info('Ticket supprimé/archivé', ['ticket_id' => $ticket->idTicket]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket supprimé/archivé avec succès'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression ticket', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    /**
     * Actions groupées - Confirmer plusieurs tickets
     */
    public function batchConfirm(Request $request)
    {
        $request->validate([
            'tickets' => 'required|array',
            'tickets.*' => 'exists:tickets,idTicket'
        ]);

        try {
            $tickets = Ticket::whereIn('idTicket', $request->tickets)
                ->whereIn('statut', ['en_attente', 'reserve'])
                ->get();

            $confirmedCount = 0;

            DB::beginTransaction();

            foreach ($tickets as $ticket) {
                $ticket->statut = 'confirme';
                $ticket->datePaiement = now();
                $ticket->save();
                $confirmedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$confirmedCount} ticket(s) confirmé(s) avec succès",
                'data' => [
                    'confirmed' => $confirmedCount,
                    'total' => count($request->tickets)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur confirmation groupée tickets', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation groupée'
            ], 500);
        }
    }

    /**
     * Actions groupées - Annuler plusieurs tickets
     */
    public function batchCancel(Request $request)
    {
        $request->validate([
            'tickets' => 'required|array',
            'tickets.*' => 'exists:tickets,idTicket'
        ]);

        try {
            $tickets = Ticket::whereIn('idTicket', $request->tickets)
                ->whereIn('statut', ['en_attente', 'reserve', 'confirme'])
                ->get();

            $cancelledCount = 0;

            DB::beginTransaction();

            foreach ($tickets as $ticket) {
                // Vérifier si le voyage n'a pas déjà commencé
                if ($ticket->voyage && $ticket->voyage->dateHeureDepart > now()) {
                    $ticket->statut = 'annule';
                    $ticket->save();

                    // Libérer le siège
                    if ($ticket->siege) {
                        $ticket->siege->update(['statut' => 'libre']);
                    }

                    $cancelledCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$cancelledCount} ticket(s) annulé(s) avec succès",
                'data' => [
                    'cancelled' => $cancelledCount,
                    'total' => count($request->tickets)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur annulation groupée tickets', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation groupée'
            ], 500);
        }
    }

    /**
     * Actions groupées - Supprimer plusieurs tickets
     */
    public function batchDelete(Request $request)
    {
        $request->validate([
            'tickets' => 'required|array',
            'tickets.*' => 'exists:tickets,idTicket'
        ]);

        try {
            // La méthode delete() déclenchera les soft deletes pour les modèles qui utilisent le trait.
            // C'est plus idiomatique et déclenche les événements de modèle.
            $deletedCount = Ticket::whereIn('idTicket', $request->tickets)->delete();

            \Log::info('Tickets supprimés groupés', ['count' => $deletedCount]);

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} ticket(s) supprimé(s) avec succès"
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression groupée tickets', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression groupée'
            ], 500);
        }
    }

    /**
     * Exporter des tickets (CSV ou PDF)
     */
    public function export(Request $request)
    {
        try {
            $query = Ticket::with([
                'client:idClient,nom,prenom,email',
                'voyage.trajet:idTrajet,villeDepart,villeArrivee',
                'siege:idSiege,numeroSiege'
            ]);

            // Appliquer les mêmes filtres que pour l'index
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('statut', $request->status);
            }

            if ($request->filled('period')) {
                $now = now();
                switch ($request->period) {
                    case 'today':
                        $query->whereDate('created_at', $now->toDateString());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [
                            $now->startOfWeek()->toDateString(),
                            $now->endOfWeek()->toDateString()
                        ]);
                        break;
                    case 'month':
                        $query->whereBetween('created_at', [
                            $now->startOfMonth()->toDateString(),
                            $now->endOfMonth()->toDateString()
                        ]);
                        break;
                }
            }

            $tickets = $query->orderBy('created_at', 'desc')->get();

            $format = $request->get('format', 'csv');

            if ($format === 'pdf') {
                $pdf = PDF::loadView('exports.tickets-pdf', [
                    'tickets' => $tickets,
                    'filters' => [
                        'status' => $request->status,
                        'period' => $request->period,
                        'search' => $request->search
                    ]
                ]);

                return $pdf->download('tickets-' . date('Y-m-d') . '.pdf');
            }

            // CSV par défaut
            $fileName = 'tickets-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$fileName}",
            ];

            $callback = function () use ($tickets) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Numéro Billet',
                    'Client',
                    'Email',
                    'Voyage',
                    'Date Voyage',
                    'Siège',
                    'Classe',
                    'Prix',
                    'Statut',
                    'Date Achat'
                ]);

                foreach ($tickets as $ticket) {
                    fputcsv($file, [
                        $ticket->numeroBillet,
                        $ticket->client ? $ticket->client->nom . ' ' . $ticket->client->prenom : 'N/A',
                        $ticket->client ? $ticket->client->email : 'N/A',
                        $ticket->voyage && $ticket->voyage->trajet
                        ? $ticket->voyage->trajet->villeDepart . ' → ' . $ticket->voyage->trajet->villeArrivee
                        : 'N/A',
                        $ticket->voyage ? $ticket->voyage->dateHeureDepart : 'N/A',
                        $ticket->siege ? $ticket->siege->numeroSiege : 'N/A',
                        ucfirst($ticket->classeBillet),
                        $ticket->prixPaye ? number_format($ticket->prixPaye, 0, ',', ' ') . ' FCFA' : 'N/A',
                        $ticket->status_text, // Utilisation de l'accesseur
                        $ticket->created_at->format('d/m/Y H:i')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Erreur export tickets', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export'
            ], 500);
        }
    }

    /**
     * Exporter des tickets spécifiques (batch)
     */
    public function batchExport(Request $request)
    {
        $request->validate([
            'tickets' => 'required|array',
            'tickets.*' => 'exists:tickets,idTicket'
        ]);

        try {
            $tickets = Ticket::with([
                'client:idClient,nom,prenom,email',
                'voyage.trajet:idTrajet,villeDepart,villeArrivee',
                'siege:idSiege,numeroSiege'
            ])->whereIn('idTicket', $request->tickets)->get();

            $format = $request->get('format', 'pdf');

            if ($format === 'pdf') {
                $pdf = PDF::loadView('exports.tickets-batch-pdf', [
                    'tickets' => $tickets
                ]);

                return $pdf->download('tickets-selection-' . date('Y-m-d') . '.pdf');
            }

            // CSV pour batch
            $fileName = 'tickets-selection-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$fileName}",
            ];

            $callback = function () use ($tickets) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Numéro Billet',
                    'Client',
                    'Voyage',
                    'Date Voyage',
                    'Prix',
                    'Statut'
                ]);

                foreach ($tickets as $ticket) {
                    fputcsv($file, [
                        $ticket->numeroBillet,
                        $ticket->client ? $ticket->client->nom . ' ' . $ticket->client->prenom : 'N/A',
                        $ticket->voyage && $ticket->voyage->trajet
                        ? $ticket->voyage->trajet->villeDepart . ' → ' . $ticket->voyage->trajet->villeArrivee
                        : 'N/A',
                        $ticket->voyage ? $ticket->voyage->dateHeureDepart : 'N/A',
                        $ticket->prixPaye ? number_format($ticket->prixPaye, 0, ',', ' ') . ' FCFA' : 'N/A',
                        $ticket->status_text // Utilisation de l'accesseur
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Erreur batch export tickets', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export'
            ], 500);
        }
    }

    /**
     * Statistiques des tickets
     */
    public function stats(Request $request)
    {
        try {
            $stats = [];

            // Comptage par statut
            $stats['par_statut'] = Ticket::select('statut', DB::raw('count(*) as total'))
                ->groupBy('statut')
                ->get()
                ->pluck('total', 'statut');

            // Chiffre d'affaires
            $stats['chiffre_affaires'] = Ticket::whereIn('statut', ['confirme', 'utilise'])
                ->sum('prixPaye');

            // Tickets du jour
            $stats['aujourdhui'] = Ticket::whereDate('created_at', today())
                ->count();

            // Tickets de la semaine
            $stats['cette_semaine'] = Ticket::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count();

            // Top voyages
            $stats['top_voyages'] = DB::table('tickets')
                ->join('voyages', 'tickets.idVoyage', '=', 'voyages.idVoyage')
                ->join('trajets', 'voyages.idTrajet', '=', 'trajets.idTrajet')
                ->select(
                    'voyages.idVoyage',
                    DB::raw('CONCAT(trajets.villeDepart, " → ", trajets.villeArrivee) as trajet'),
                    DB::raw('COUNT(tickets.idTicket) as nombre_tickets'),
                    DB::raw('SUM(tickets.prixPaye) as revenus')
                )
                ->whereIn('tickets.statut', ['confirme', 'utilise'])
                ->groupBy('voyages.idVoyage', 'trajets.villeDepart', 'trajets.villeArrivee')
                ->orderBy('nombre_tickets', 'desc')
                ->take(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur stats tickets', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }

    /**
     * Obtenir le QR Code d'un ticket
     */
    public function getQRCode($id)
    {
        $ticket = Ticket::findOrFail($id);

        if (empty($ticket->codeQR)) {
            $ticket->genererQR();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qrcode' => $ticket->codeQR,
                'numeroBillet' => $ticket->numeroBillet
            ]
        ]);
    }

    /**
     * Télécharger un ticket individuel en PDF (Supporte auth par token dans l'URL)
     */
    public function download(Request $request, $id)
    {
        try {
            // Tentative d'authentification manuelle via le token dans l'URL si non connecté
            if (!auth()->check() && $request->has('token')) {
                $token = PersonalAccessToken::findToken($request->token);
                if ($token && $token->tokenable) {
                    auth()->setUser($token->tokenable);
                }
            }

            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
            }

            $ticket = Ticket::with([
                'client',
                'voyage.trajet',
                'siege'
            ])->findOrFail($id);

            // Vérification de propriété (seul le propriétaire ou un admin peut télécharger)
            if ($user instanceof \App\Models\Client) {
                if ($ticket->id_client !== $user->id_client) {
                    return response()->json(['success' => false, 'message' => 'Accès refusé à ce billet'], 403);
                }
            } elseif ($user && property_exists($user, 'role') && !in_array($user->role, ['admin', 'super_admin'])) {
                return response()->json(['success' => false, 'message' => 'Permission insuffisante'], 403);
            }

            $pdf = Pdf::loadView('exports.ticket-individuel-pdf', [
                'ticket' => $ticket
            ]);

            return $pdf->download('billet-' . $ticket->numeroBillet . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Erreur téléchargement ticket PDF', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible de générer le PDF'
            ], 500);
        }
    }
}