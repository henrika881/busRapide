<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('voyage_siege', function (Blueprint $table) {
            // Utilisation des noms EXACTS de tes autres migrations
            $table->foreignId('idVoyage')->constrained('voyages', 'idVoyage')->onDelete('cascade');
            $table->foreignId('idSiege')->constrained('sieges', 'idSiege')->onDelete('cascade');
            
            // Doit correspondre à 'numeroBillet' de ta table tickets
            $table->string('numeroBillet', 50)->nullable();
            
            $table->enum('statut', ['libre', 'reserve', 'occupe'])->default('libre');
            
            // Clé primaire composée
            $table->primary(['idVoyage', 'idSiege']);

            // Liaison vers la table tickets
            $table->foreign('numeroBillet')->references('numeroBillet')->on('tickets')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('voyage_siege');
    }
};