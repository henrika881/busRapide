<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('idTicket');
            $table->string('numeroBillet', 50)->unique();
            $table->foreignId('idVoyage')->constrained('voyages', 'idVoyage');
            $table->foreignId('id_client')->constrained('clients', 'id_client');
            $table->foreignId('idSiege')->constrained('sieges', 'idSiege');
            $table->decimal('prixPaye', 10, 2);
            $table->string('codeQR', 500)->nullable();
            $table->datetime('dateEmission')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('datePaiement')->nullable();
            $table->enum('statut', ['confirme', 'annule', 'utilise', 'en_attente'])->default('en_attente');
            $table->enum('modePaiement', ['carte', 'especes', 'mobile', 'virement']);
            $table->timestamps();
            
            $table->index('numeroBillet');
            $table->index(['id_client', 'dateEmission']);
            $table->index(['idVoyage', 'statut']);
            $table->index(['statut', 'dateEmission']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}