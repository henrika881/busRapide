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

/* --- ROUTES PUBLIQUES --- */
Route::post('/clients/register', [ClientController::class, 'register']);
Route::post('/clients/login', [ClientController::class, 'login']);
Route::post('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'login']);
Route::get('/voyages/search', [VoyageController::class, 'search']);
Route::get('/voyages/{id}/sieges-disponibles', [VoyageController::class, 'getSiegesDisponibles']);

/* --- ROUTES PROTÉGÉES CLIENTS --- */
Route::middleware(['auth:sanctum', 'ability:client'])->group(function () {
    Route::post('/clients/logout', [ClientController::class, 'logout']);
    Route::get('/clients/profile', [ClientController::class, 'profile']);
    Route::put('/clients/profile', [ClientController::class, 'updateProfile']);
    Route::get('/clients/historique', [ClientController::class, 'historique']);
    Route::post('/clients/vip/souscrire', [ClientController::class, 'souscrireVIP']);
    
    Route::apiResource('tickets', TicketController::class)->except(['destroy', 'update']);
    Route::post('/tickets/{id}/confirmer', [TicketController::class, 'confirmer']);
    Route::post('/tickets/{id}/annuler', [TicketController::class, 'annuler']);
    Route::get('/tickets/{id}/qrcode', [TicketController::class, 'getQRCode']);
    
    Route::get('/voyages', [VoyageController::class, 'index']);
    Route::get('/voyages/{id}', [VoyageController::class, 'show']);
});

/* --- ROUTES PROTÉGÉES ADMIN (Toute l'équipe) --- */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout']);
    Route::post('/admin/profile/update', [\App\Http\Controllers\Admin\AuthController::class, 'updateProfile']);
    Route::post('/admin/change-password', [\App\Http\Controllers\Admin\AuthController::class, 'changePassword']);
    Route::get('/admin/me', function() { 
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

    // Gestion complète des bus, trajets, voyages (CRUD)
    Route::apiResource('admin/bus', BusController::class);
    Route::apiResource('admin/trajets', TrajetController::class);
    Route::apiResource('admin/voyages', VoyageController::class);
    Route::post('/admin/voyages/{id}/annuler', [VoyageController::class, 'annuler']);
    Route::post('/admin/voyages/{id}/depart', [VoyageController::class, 'marquerDepart']);
    Route::post('/admin/voyages/{id}/arrivee', [VoyageController::class, 'marquerArrivee']);

    // Gestion des embarquements (Contrôleurs et Admins)
    Route::post('/embarquements/valider', [EmbarquementController::class, 'validerEmbarquement']);
    Route::get('/embarquements/journalier', [EmbarquementController::class, 'embarquementsDuJour']);
    
    // Gestion des Tickets (Admin view)
    Route::get('/admin/tickets', [TicketController::class, 'index']);
});

/* --- ROUTES SUPER ADMIN SEULEMENT --- */
Route::middleware(['auth:sanctum', 'ability:super_admin'])->group(function () {
    // Gestion du personnel (Admins)
    Route::apiResource('admin/users', AdminResourceController::class);
    
    // Gestion clients CRUD
    Route::apiResource('admin/clients', ClientController::class)->only(['index', 'show', 'update', 'destroy']);
});

Route::get('/health', function () {
    return response()->json(['status' => 'OK', 'timestamp' => now()]);
});