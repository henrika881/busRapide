<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TicketSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🎫 Création de tickets de test...');
        
        // 1. Vérifier s'il y a déjà des clients, sinon en créer un seul
        $clientsCount = DB::table('clients')->count();
        if ($clientsCount === 0) {
            $this->command->info('⚠️  Aucun client trouvé. Création d\'un client admin...');
            
            // Essaie avec différentes structures
            try {
                DB::table('clients')->insert([
                    'nom' => 'Admin',
                    'prenom' => 'Test',
                    'email' => 'admin@test.com',
                    'telephone' => '0123456789',
                    'password' => bcrypt('admin123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                try {
                    DB::table('clients')->insert([
                        'nom' => 'Admin',
                        'prenom' => 'Test',
                        'email' => 'admin@test.com',
                        'telephone' => '0123456789',
                        'mot_de_passe' => bcrypt('admin123'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e2) {
                    $this->command->error('Impossible de créer un client. Utilisation du client ID 1...');
                }
            }
        }
        
        // 2. Créer les autres tables si nécessaires (version minimaliste)
        $this->createMinimalData();
        
        // 3. Créer les tickets
        $this->createTickets();
        
        $this->command->info('✅ Tickets créés avec succès!');
    }
    
    private function createMinimalData()
    {
        // Vérifier/Créer un trajet
        if (DB::table('trajets')->count() === 0) {
            DB::table('trajets')->insert([
                'villeDepart' => 'Paris',
                'villeArrivee' => 'Lyon',
                'distance' => 450,
                'duree' => 270,
                'prixStandard' => 8500,
                'prixVIP' => 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Vérifier/Créer un bus
        if (DB::table('bus')->count() === 0) {
            $busId = DB::table('bus')->insertGetId([
                'immatriculation' => 'TEST-001',
                'marque' => 'Mercedes',
                'modele' => 'Test',
                'capaciteTotale' => 50,
                'nbSiegesVIP' => 10,
                'statut' => 'en_service',
                'dateMiseEnService' => '2023-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Créer des sièges
            for ($i = 1; $i <= 50; $i++) {
                $classe = $i <= 10 ? 'vip' : 'standard';
                DB::table('sieges')->insert([
                    'idBus' => $busId,
                    'numeroSiege' => $i,
                    'classe' => $classe,
                    'statut' => 'libre',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Vérifier/Créer un voyage
        if (DB::table('voyages')->count() === 0) {
            $trajet = DB::table('trajets')->first();
            $bus = DB::table('bus')->first();
            
            DB::table('voyages')->insert([
                'idTrajet' => $trajet->idTrajet,
                'idBus' => $bus->idBus,
                'dateHeureDepart' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
                'prixStandard' => 8500,
                'prixVIP' => 15000,
                'statut' => 'planifie',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    private function createTickets()
    {
        $clients = DB::table('clients')->pluck('idClient')->toArray();
        $voyages = DB::table('voyages')->get();
        
        if (empty($clients) || $voyages->isEmpty()) {
            $this->command->error('❌ Données insuffisantes pour créer des tickets');
            return;
        }
        
        $statuts = ['en_attente', 'reserve', 'confirme', 'annule', 'utilise'];
        $classes = ['standard', 'vip'];
        $modesPaiement = ['carte', 'especes', 'mobile', 'virement'];
        
        for ($i = 1; $i <= 10; $i++) {
            $clientId = $clients[array_rand($clients)];
            $voyage = $voyages->random();
            $statut = $statuts[array_rand($statuts)];
            $classe = $classes[array_rand($classes)];
            $modePaiement = $modesPaiement[array_rand($modesPaiement)];
            
            // Prix selon la classe
            $prix = $classe === 'vip' ? $voyage->prixVIP : $voyage->prixStandard;
            
            // Prendre un siège disponible
            $siege = DB::table('sieges')
                ->where('idBus', $voyage->idBus)
                ->where('classe', $classe)
                ->where('statut', 'libre')
                ->inRandomOrder()
                ->first();
            
            if (!$siege) {
                // Prendre n'importe quel siège de la bonne classe
                $siege = DB::table('sieges')
                    ->where('idBus', $voyage->idBus)
                    ->where('classe', $classe)
                    ->inRandomOrder()
                    ->first();
            }
            
            if (!$siege) {
                $this->command->warn("⚠️  Pas de siège disponible pour le ticket {$i}");
                continue;
            }
            
            // Créer le ticket
            DB::table('tickets')->insert([
                'numeroBillet' => 'TICK-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'idVoyage' => $voyage->idVoyage,
                'idClient' => $clientId,
                'idSiege' => $siege->idSiege,
                'prixPaye' => $prix,
                'classeBillet' => $classe,
                'statut' => $statut,
                'modePaiement' => $modePaiement,
                'dateAchat' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d H:i:s'),
                'codeQR' => 'QR-' . Str::random(10),
                'idAgent' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Marquer le siège comme occupé
            DB::table('sieges')->where('idSiege', $siege->idSiege)->update(['statut' => 'occupe']);
            
            $this->command->info("   Ticket {$i}/10 créé: TICK-" . str_pad($i, 6, '0', STR_PAD_LEFT));
        }
    }
}