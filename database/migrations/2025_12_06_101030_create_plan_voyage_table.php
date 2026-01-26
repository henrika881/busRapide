<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plan_voyage', function (Blueprint $table) {
            $table->id('id_plan');
            $table->string('nom_agence', 100);
            $table->enum('jour_semaine', ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'])->nullable();
            $table->time('heure_depart');
            $table->string('gare_depart', 100);
            $table->string('gare_arrivee', 100);
            $table->integer('duree_estimee')->nullable()->comment('en minutes');
            $table->decimal('prix', 10, 2);
            
            $table->foreign('nom_agence')->references('nom_agence')->on('agence');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plan_voyage');
    }
};