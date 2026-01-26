<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('passager', function (Blueprint $table) {
            $table->id('id_passager');
            $table->string('nom_passager', 100);
            $table->string('prenom_passager', 100);
            $table->date('date_naissance')->nullable();
            $table->enum('type_piece', ['cni', 'passeport', 'permis'])->default('cni');
            $table->string('numero_piece', 50);
            $table->string('telephone_passager', 20)->nullable();
            $table->string('email_passager', 100)->nullable();
            $table->string('numero_billet', 50);
            
            $table->foreignId('idTicket')->references('idTicket')->on('tickets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('passager');
    }
};