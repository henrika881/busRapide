<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Bus;
use App\Models\Siege;

class TestSetup extends Command
{
    protected $signature = 'test:setup';
    protected $description = 'Test la configuration et crée un bus de test';

    public function handle()
    {
        $this->info('=== Test 1: Vérifier les admins ===');
        $admins = Admin::all();
        if ($admins->count() > 0) {
            $this->line('✓ Admins trouvés: ' . $admins->count());
            foreach ($admins as $admin) {
                $this->line("  - {$admin->email} ({$admin->role})");
            }
        } else {
            $this->error('✗ Aucun admin trouvé');
        }

        $this->info('\n=== Test 2: Créer un bus de test ===');
        try {
            $immat = 'TEST-' . bin2hex(random_bytes(4));
            $bus = Bus::create([
                'immatriculation' => $immat,
                'marque' => 'Mercedes',
                'modele' => 'Sprinter',
                'capaciteTotale' => 50,
                'statut' => 'en_service',
                'dateMiseEnService' => date('Y-m-d')
            ]);
            $this->line("✓ Bus créé avec ID: {$bus->idBus}");

            $this->info('=== Test 3: Créer des sièges ===');
            for ($i = 1; $i <= 3; $i++) {
                Siege::create([
                    'idBus' => $bus->idBus,
                    'numeroSiege' => 'VIP-' . $i,
                    'classe' => 'vip',
                    'type' => 'premium',
                    'statut' => 'libre'
                ]);
            }
            $this->line('✓ 3 sièges VIP créés avec succès');

            // Vérifier les sièges
            $siegeCount = Siege::where('idBus', $bus->idBus)->count();
            $this->line("✓ Total sièges dans la base: {$siegeCount}");

            // Nettoyer
            $bus->delete();
            $this->line('✓ Bus test supprimé');

            $this->info('\n✓✓✓ Tous les tests passés! ✓✓✓');
        } catch (\Exception $e) {
            $this->error('✗ Erreur: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
