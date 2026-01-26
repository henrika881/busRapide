<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bagage', function (Blueprint $table) {
            $table->string('tag_id', 50)->primary();
            $table->decimal('poids', 5, 2);
            $table->enum('type_bagage', ['cabine', 'soute']);
            $table->string('dimensions', 50)->nullable();
            $table->enum('statut', ['enregistre', 'embarque', 'recupere', 'perdu'])->default('enregistre');
            $table->dateTime('date_enregistrement')->useCurrent();
            $table->string('numero_billet', 50);
            
            $table->foreignId('idTicket')->references('idTicket')->on('tickets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bagage');
    }
};