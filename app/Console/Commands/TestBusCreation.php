<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class TestBusCreation extends Command
{
    protected $signature = 'test:bus-creation';
    protected $description = 'Test la création d\'un bus via l\'API';

    public function handle()
    {
        $this->info('=== Test Création Bus via API ===');

        // Nettoyer les buses précédentes
        $this->info('\n🧹 Nettoyage...');
        Bus::query()->delete();
        $this->line('✓ Buses supprimées');

        // Récupérer un admin
        $admin = Admin::where('email', 'gestionnaire@example.com')->first();
        if (!$admin) {
            $this->error('✗ Admin non trouvé');
            return 1;
        }

        // Créer un token
        $token = $admin->createToken('test', ['admin'])->plainTextToken;
        $this->line("✓ Token créé: " . substr($token, 0, 20) . '...');

        // Test POST /api/admin/bus
        $this->info('\n📡 Envoi de la requête POST /api/admin/bus...');
        $busData = [
            'immatriculation' => 'TEST-' . uniqid(),
            'marque' => 'Mercedes',
            'modele' => 'Sprinter',
            'capaciteTotale' => 50,
            'nbSiegesVIP' => 5,
            'statut' => 'en_service',
            'dateMiseEnService' => now()->format('Y-m-d')
        ];

        $this->line('Payload: ' . json_encode($busData, JSON_PRETTY_PRINT));

        try {
            $response = Http::withToken($token)
                ->post('http://127.0.0.1:8000/api/admin/bus', $busData);

            $this->info("\n📊 Response Status: {$response->status()}");
            $this->line('Response Body:');
            $this->line(json_encode($response->json(), JSON_PRETTY_PRINT));

            if ($response->successful()) {
                $this->info('\n✅ Bus créé avec succès');
                
                // Vérifier en BD
                $bus = DB::table('bus')->where('immatriculation', $busData['immatriculation'])->first();
                if ($bus) {
                    $this->line("✓ Bus trouvé en BD: ID={$bus->idBus}");
                    
                    $seats = DB::table('siege')->where('idBus', $bus->idBus)->get();
                    $this->line("✓ {$seats->count()} sièges créés");
                } else {
                    $this->error('✗ Bus non trouvé en BD');
                }
            } else {
                $this->error('✗ Erreur lors de la création');
            }
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
        }

        return 0;
    }
}
