<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiegesTable extends Migration
{
    public function up()
    {
        Schema::create('sieges', function (Blueprint $table) {
            $table->id('idSiege');
            $table->foreignId('idBus')->constrained('bus', 'idBus')->onDelete('cascade');
            $table->string('numeroSiege', 10);
            $table->enum('type', ['fenetre', 'couloir', 'premium'])->default('fenetre');
            $table->enum('classe', ['standard', 'premium'])->default('standard');
            $table->enum('statut', ['libre', 'reserve', 'occupe'])->default('libre');
            $table->timestamps();
            
            $table->unique(['idBus', 'numeroSiege']);
            $table->index(['idBus', 'statut']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sieges');
    }
}