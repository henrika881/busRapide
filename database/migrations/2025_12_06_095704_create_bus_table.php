<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusTable extends Migration
{
    public function up()
    {
        Schema::create('bus', function (Blueprint $table) {
            $table->id('idBus');
            $table->string('immatriculation', 20)->unique();
            $table->string('marque', 50)->nullable();
            $table->string('modele', 50)->nullable();
            $table->integer('capaciteTotale');
            $table->enum('statut', ['en_service', 'maintenance', 'hors_service'])->default('en_service');
            $table->date('dateMiseEnService')->nullable();
            $table->timestamps();
            
            $table->index('immatriculation');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bus');
    }
}