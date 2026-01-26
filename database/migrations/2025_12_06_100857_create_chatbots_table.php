<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatbotsTable extends Migration
{
    public function up()
    {
        Schema::create('chatbots', function (Blueprint $table) {
            $table->id('idChatbot');
            $table->string('nomChatbot', 100);
            $table->string('version', 20)->nullable();
            $table->enum('statut', ['actif', 'inactif', 'maintenance'])->default('actif');
            $table->datetime('dateMiseAJour')->default(DB::raw('CURRENT_TIMESTAMP'))->onUpdate(DB::raw('CURRENT_TIMESTAMP'));
            $table->json('parametres')->nullable();
            $table->timestamps();
            
            $table->index('statut');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chatbots');
    }
}