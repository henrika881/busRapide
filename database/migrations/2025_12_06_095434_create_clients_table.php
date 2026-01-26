<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id('id_client');
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 255)->unique();
            $table->string('motDePasse', 255);
            $table->string('telephone', 20)->nullable();
            $table->string('numeroCNI', 50)->unique()->nullable();
            $table->datetime('dateInscription')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('statut', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->timestamps();
            
            $table->index('email');
            $table->index(['nom', 'prenom']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}