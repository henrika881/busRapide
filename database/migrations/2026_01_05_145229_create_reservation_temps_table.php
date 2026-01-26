<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservation_temps', function (Blueprint $table) {
            $table->id('idReservationTemp');
            $table->foreignId('idClient')->constrained('clients', 'id_client')->onDelete('cascade');
            $table->foreignId('idVoyage')->constrained('voyages', 'idVoyage')->onDelete('cascade');
            $table->foreignId('idSiege')->constrained('sieges', 'idSiege')->onDelete('cascade');
            $table->dateTime('dateExpiration');
            $table->enum('statut', ['attente', 'confirme', 'annule'])->default('attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_temps');
    }
};
