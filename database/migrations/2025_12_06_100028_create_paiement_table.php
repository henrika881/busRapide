<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paiement', function (Blueprint $table) {
            $table->string('ref_transaction', 50)->primary();
            $table->unsignedBigInteger('id_client');
            $table->decimal('montant', 10, 2);
            $table->dateTime('date_paiement')->useCurrent();
            $table->enum('mode_paiement', ['carte', 'mobile_money', 'especes', 'virement']);
            $table->enum('statut', ['en_attente', 'valide', 'echoue', 'rembourse'])->default('en_attente');
            $table->string('banque', 100)->nullable();
            $table->string('numero_autorisation', 100)->nullable();
            
            $table->foreign('id_client')->references('id_client')->on('clients');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiement');
    }
};