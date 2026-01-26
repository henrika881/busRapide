<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoyagesTable extends Migration
{
    public function up()
    {
        Schema::create('voyages', function (Blueprint $table) {
            $table->id('idVoyage');
            $table->foreignId('idBus')->constrained('bus', 'idBus');
            $table->foreignId('idTrajet')->constrained('trajets', 'idTrajet');
            $table->datetime('dateHeureDepart');
            $table->datetime('dateHeureArrivee')->nullable();
            $table->decimal('prixActuel', 10, 2);
            $table->integer('placesDisponibles');
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule', 'retarde'])->default('planifie');
            $table->timestamps();
            
            $table->index('dateHeureDepart');
            $table->index('statut');
            
            // Contrainte CHECK gérée via validation ou trigger
        });
    }

    public function down()
    {
        Schema::dropIfExists('voyages');
    }
}