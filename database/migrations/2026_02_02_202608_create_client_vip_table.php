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
        Schema::create('client_vip', function (Blueprint $table) {
            $table->id('idVIP');
            $table->unsignedBigInteger('id_client');
            $table->string('niveauVIP')->default('bronze'); // bronze, argent, or, platine
            $table->date('dateAdhesion');
            $table->date('dateRenouvellement')->nullable();
            $table->enum('statutAbonnement', ['actif', 'inactif'])->default('actif');
            $table->integer('prioriteEmbarquement')->default(0);
            $table->integer('reductionPermanente')->default(0);
            $table->timestamps();

            $table->foreign('id_client')->references('id_client')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_vip');
    }
};
