<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VoyageController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\EmbarquementController;
use App\Http\Controllers\AdminResourceController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;

/* --- ROUTES PUBLIQUES --- */
Route::post('/clients/register', [ClientController::class, 'register']);
Route::post('/clients/login', [ClientController::class, 'login'])->middleware('throttle:login');
Route::post('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->middleware('throttle:login');
Route::get('/voyages/search', [VoyageController::class, 'search']);
Route::get('/voyages/{id}/sieges-disponibles', [VoyageController::class, 'getSiegesDisponibles']);

// Routes de test (temporaires)
Route::post('/test/payment', [App\Http\Controllers\TestController::class, 'testPayment']);
Route::get('/test/email', [App\Http\Controllers\TestController::class, 'testEmail']);

/* --- ROUTES PROTÉGÉES ADMIN (Toute l'équipe) --- */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout']);
    Route::post('/admin/profile/update', [\App\Http\Controllers\Admin\AuthController::class, 'updateProfile']);
    Route::post('/admin/change-password', [\App\Http\Controllers\Admin\AuthController::class, 'changePassword']);
    Route::get('/admin/me', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }
        $user->sessions_count = $user->getActiveSessionsCount();
        return response()->json(['success' => true, 'data' => $user]);
    }); // Info user connecté avec count sessions

    // Dashboard Stats (pour tout le monde ou restreint selon controleur)
    Route::get('/admin/dashboard/stats', [ReportController::class, 'dashboardStats']);

    // Rapports
    Route::get('/admin/reports/ventes', [ReportController::class, 'ventes']);
    Route::get('/admin/reports/occupation', [ReportController::class, 'occupation']);
    Route::get('/admin/reports/financier', [ReportController::class, 'financier']);
    Route::get('/admin/reports/top-trajets', [ReportController::class, 'topTrajets']);
    Route::get('/admin/reports/top-buses', [ReportController::class, 'topBuses']);

    // Gestion complète des bus, trajets, voyages (CRUD)
    // Gestion technique (Bus, Trajets, Voyages) - Réservé aux Admins et Gestionnaires
    Route::middleware(['role:super_admin,gestionnaire'])->group(function () {
        Route::apiResource('admin/bus', BusController::class);
        Route::apiResource('admin/trajets', TrajetController::class);
        Route::apiResource('admin/voyages', VoyageController::class);
        Route::post('/admin/voyages/{voyage}/annuler', [VoyageController::class, 'annuler']);
    });

    Route::post('/admin/voyages/{voyage}/depart', [VoyageController::class, 'marquerDepart']);
    Route::post('/admin/voyages/{voyage}/arrivee', [VoyageController::class, 'marquerArrivee']);

    // Gestion des embarquements (Contrôleurs et Admins)
    Route::post('/embarquements/valider', [EmbarquementController::class, 'validerEmbarquement']);
    Route::get('/embarquements/journalier', [EmbarquementController::class, 'embarquementsDuJour']);

    /* === GESTION COMPLÈTE DES TICKETS (ADMIN) === */
    // Liste des tickets avec filtres
    Route::get('/admin/tickets', [TicketController::class, 'index']);

    // Détails d'un ticket spécifique
    Route::get('/admin/tickets/{ticket}', [TicketController::class, 'show']);

    // Confirmer un ticket (réservé -> confirmé)
    Route::post('/admin/tickets/{ticket}/confirm', [TicketController::class, 'confirmer']);

    // Annuler un ticket
    Route::post('/admin/tickets/{ticket}/cancel', [TicketController::class, 'annuler']);

    // Marquer un ticket comme utilisé (à l'embarquement)
    Route::post('/admin/tickets/{ticket}/mark-used', [TicketController::class, 'markAsUsed']);

    // Envoyer un ticket par email
    Route::post('/admin/tickets/{ticket}/send-email', [TicketController::class, 'sendEmail']);

    // Supprimer un ticket (archiver)
    Route::delete('/admin/tickets/{ticket}', [TicketController::class, 'destroy']);

    // Actions groupées
    Route::post('/admin/tickets/batch-confirm', [TicketController::class, 'batchConfirm']);
    Route::post('/admin/tickets/batch-cancel', [TicketController::class, 'batchCancel']);
    Route::post('/admin/tickets/batch-delete', [TicketController::class, 'batchDelete']);

    // Export des tickets
    Route::get('/admin/tickets/export', [TicketController::class, 'export']);
    Route::get('/admin/tickets/batch-export', [TicketController::class, 'batchExport']);

    // Statistiques des tickets
    Route::get('/admin/tickets/stats', [TicketController::class, 'stats']);
});

/* --- ROUTES PROTÉGÉES CLIENTS --- */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/clients/logout', [ClientController::class, 'logout']);
    Route::get('/clients/profile', [ClientController::class, 'profile']);
    Route::put('/clients/profile', [ClientController::class, 'updateProfile']);
    Route::get('/clients/historique', [ClientController::class, 'historique']);
    Route::get('/clients/notifications', [ClientController::class, 'notifications']);
    Route::post('/clients/vip/souscrire', [ClientController::class, 'souscrireVIP']);

    // Routes tickets clients (CRUD)
    Route::apiResource('tickets', TicketController::class)->except(['destroy', 'update']);
    Route::post('/tickets/check-status', [TicketController::class, 'checkPaymentStatus']);
    Route::post('/tickets/{id}/confirmer', [TicketController::class, 'confirmer']);
    Route::post('/tickets/{id}/annuler', [TicketController::class, 'annuler']);
    Route::get('/tickets/{id}/qrcode', [TicketController::class, 'getQRCode']);
    Route::get('/tickets/{id}/download', [TicketController::class, 'download']);

    // Routes VOYAGES CLIENTS
    Route::get('/voyages', [VoyageController::class, 'index']);
    Route::get('/voyages/{id}', [VoyageController::class, 'show']);
});

/* --- ROUTES SUPER ADMIN SEULEMENT --- */
Route::middleware(['auth:sanctum', 'ability:super_admin'])->group(function () {
    // Gestion du personnel (Admins)
    Route::apiResource('admin/users', AdminResourceController::class);

    // Gestion clients CRUD
    Route::apiResource('admin/clients', ClientController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
});

Route::get('/health', function () {
    return response()->json(['status' => 'OK', 'timestamp' => now()]);
});

Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation');