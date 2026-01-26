<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Super Admin
            $adminEmail = 'admin@example.com';
            if (!Admin::where('email', $adminEmail)->exists()) {
                Admin::create([
                    'matricule' => 'ADM001',
                    'nom' => 'Super',
                    'prenom' => 'Admin',

                    
                    'email' => $adminEmail,
                    'password' => Hash::make('password'),
                    'role' => 'super_admin',
                    'statut' => 'actif',
                    'date_embauche' => now(),
                ]);
                
                $this->command->info('Super Admin created successfully.');
            }

            // 2. Controleur
            $controleurEmail = 'controleur@example.com';
            if (!Admin::where('email', $controleurEmail)->exists()) {
                Admin::create([
                    'matricule' => 'CTR001',
                    'nom' => 'Test',
                    'prenom' => 'Controleur',
                    'email' => $controleurEmail,
                    'password' => Hash::make('password'),
                    'role' => 'controleur',
                    'statut' => 'actif',
                    'date_embauche' => now(),
                ]);
                
                $this->command->info('Controleur created successfully.');
            }

            // 3. Gestionnaire
            $gestionnaireEmail = 'gestionnaire@example.com';
            if (!Admin::where('email', $gestionnaireEmail)->exists()) {
                Admin::create([
                    'matricule' => 'GST001',
                    'nom' => 'Test',
                    'prenom' => 'Gestionnaire',
                    'email' => $gestionnaireEmail,
                    'password' => Hash::make('password'),
                    'role' => 'gestionnaire',
                    'statut' => 'actif',
                    'date_embauche' => now(),
                ]);
                
                $this->command->info('Gestionnaire created successfully.');
            }
        });
    }
}
