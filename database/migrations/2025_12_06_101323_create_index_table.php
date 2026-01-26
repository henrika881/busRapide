<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Index pour VOYAGES
        Schema::table('voyages', function (Blueprint $table) {
            if (!Schema::hasIndex('voyages', 'idx_voyage_dates')) {
                $table->index(['dateHeureDepart', 'dateHeureArrivee'], 'idx_voyage_dates');
            }
        });
        
        // 2. Index pour TICKETS
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasIndex('tickets', 'idx_ticket_voyage')) {
                $table->index(['idVoyage', 'statut'], 'idx_ticket_voyage');
            }
        });
        
        // 3. Index pour EMBARQUEMENTS - SIMPLIFIÉ
        Schema::table('embarquements', function (Blueprint $table) {
            if (!Schema::hasIndex('embarquements', 'idx_embarquement_ticket_statut')) {
                // Version simplifiée sans vérification de contrainte
                $table->index(['idTicket', 'statut'], 'idx_embarquement_ticket_statut');
            }
        });
        
        // 4. Index pour PAIEMENTS
        if (Schema::hasTable('paiements')) {
            Schema::table('paiements', function (Blueprint $table) {
                if (!Schema::hasIndex('paiements', 'idx_paiement_statut')) {
                    $table->index('statut', 'idx_paiement_statut');
                }
            });
        }
        
        // 5. Autres index utiles (ajoutez selon vos besoins)
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasIndex('clients', 'idx_client_email')) {
                $table->index('email', 'idx_client_email');
            }
        });
        
        Schema::table('bus', function (Blueprint $table) {
            if (!Schema::hasIndex('bus', 'idx_bus_immatriculation')) {
                $table->index('immatriculation', 'idx_bus_immatriculation');
            }
        });
    }

    public function down()
    {
        // Désactiver temporairement les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Supprimer les indexes en gérant les erreurs
        try {
            Schema::table('embarquements', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_embarquement_ticket_statut');
            });
        } catch (\Exception $e) {
            // Ignorer l'erreur si l'index n'existe pas
        }
        
        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_ticket_voyage');
            });
        } catch (\Exception $e) {
            // Ignorer
        }
        
        try {
            Schema::table('voyages', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_voyage_dates');
            });
        } catch (\Exception $e) {
            // Ignorer
        }
        
        try {
            if (Schema::hasTable('paiements')) {
                Schema::table('paiements', function (Blueprint $table) {
                    $table->dropIndexIfExists('idx_paiement_statut');
                });
            }
        } catch (\Exception $e) {
            // Ignorer
        }
        
        try {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_client_email');
            });
        } catch (\Exception $e) {
            // Ignorer
        }
        
        try {
            Schema::table('bus', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_bus_immatriculation');
            });
        } catch (\Exception $e) {
            // Ignorer
        }
        
        // Réactiver les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};