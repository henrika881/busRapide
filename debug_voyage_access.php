<?php
// debug_voyage_access.php
use App\Models\Admin;
use App\Http\Controllers\VoyageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// 1. Create or Find an Admin
$admin = Admin::firstOrCreate(
    ['email' => 'admin_debug@example.com'],
    [
        'matricule' => 'DEBUG001',
        'nom' => 'Debug',
        'prenom' => 'Admin',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'statut' => 'actif'
    ]
);

echo "Admin ID: " . $admin->id . "\n";

// 2. Mock Authenticated Request
// IMPORTANT: We must login this admin so Auth::user() returns it.
// Since we are not in a full HTTP request cycle, we mock the guard.

Auth::guard('admin')->login($admin);
Auth::shouldUse('admin'); 

// Check if Auth::user() works
$currentUser = Auth::user();
echo "Current User Class: " . ($currentUser ? get_class($currentUser) : 'NULL') . "\n";

// 3. Create Request
$request = Request::create('/api/voyages', 'POST', [
    'date_depart' => now()->addDay()->toDateString(),
    'heure_depart' => '08:00',
    'date_arrivee' => now()->addDay()->toDateString(),
    'heure_arrivee' => '12:00',
    'gare_depart' => 'TestGareA',
    'gare_arrivee' => 'TestGareB',
    'prix_base' => 5000,
    'nom_agence' => 'TestAgence',
    'matricule_bus' => 'BUS-001',
    'places_disponibles' => 50,
]);

// Inject user into request (Laravel does this via middleware usually)
$request->setUserResolver(function () use ($admin) {
    return $admin;
});

// 4. Instantiate Controller
$controller = new VoyageController();

try {
    echo "Attempting VoyageController::store...\n";
    $response = $controller->store($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
