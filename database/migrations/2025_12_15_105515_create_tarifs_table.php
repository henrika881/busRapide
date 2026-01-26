<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifsTable extends Migration
{
    public function up()
    {
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id('idTarif');
            $table->foreignId('idTrajet')->constrained('trajets', 'idTrajet')->onDelete('cascade');
            $table->enum('typeTarif', ['normal', 'reduit', 'enfant', 'groupe', 'premium'])->default('normal');
            $table->decimal('montant', 10, 2);
            $table->date('dateDebut');
            $table->date('dateFin')->nullable();
            $table->text('conditions')->nullable();
            $table->timestamps();
            
            $table->index(['idTrajet', 'dateDebut', 'dateFin']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tarifs');
    }
}