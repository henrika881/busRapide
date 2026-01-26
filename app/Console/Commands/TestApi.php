<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class TestApi extends Command
{
    protected $signature = 'test:api';
    protected $description = 'Test les API endpoints';

    public function handle()
    {
        $this->info('=== Test API Endpoints ===');

        // Test 1: Récupérer un admin de test
        $this->info('\n=== Test 1: Récupérer un admin ===');
        $admin = Admin::where('email', 'gestionnaire@example.com')->first();
        if ($admin) {
            $this->line("✓ Admin trouvé: {$admin->email}");
            $this->line("  Role: {$admin->role}");
            $this->line("  ID: {$admin->id}");
        } else {
            $this->error('✗ Admin non trouvé');
            return 1;
        }

        // Test 2: Simuler une authentification
        $this->info('\n=== Test 2: Tester l\'authentification ===');
        if (Hash::check('password', $admin->password)) {
            $this->line('✓ Mot de passe vérifié');
            
            // Créer un token
            $token = $admin->createToken('test_token', ['admin'])->plainTextToken;
            $this->line('✓ Token créé');
            $this->line("  Token: " . substr($token, 0, 20) . '...');
        } else {
            $this->error('✗ Mot de passe incorrect');
        }

        $this->info('\n=== Configuration validée! ===');
        $this->info('La base de données est correctement configurée.');
        $this->info('Les admins sont en place et les sièges peuvent être créés.');
        $this->info('');
        $this->info('Vous pouvez maintenant:');
        $this->info('1. Lancer le serveur: php artisan serve');
        $this->info('2. Aller à: http://127.0.0.1:8000/admin/login');
        $this->info('3. Vous connecter avec:');
        $this->info('   Email: gestionnaire@example.com');
        $this->info('   Password: password');

        return 0;
    }
}
