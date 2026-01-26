<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmbarquementsTable extends Migration
{
    public function up()
    {
        Schema::create('embarquements', function (Blueprint $table) {
            $table->id('idEmbarquement');
            $table->foreignId('idTicket')->constrained('tickets', 'idTicket')->unique();
            $table->foreignId('admin_id')->constrained('admins');
            $table->datetime('dateHeureValidation')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('porteEmbarquement', 10)->nullable();
            $table->enum('statut', ['valide', 'refuse', 'en_attente'])->default('valide');
            $table->text('commentaire')->nullable();
            $table->timestamps();
            
            $table->index('dateHeureValidation');
            $table->index(['admin_id', 'dateHeureValidation']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('embarquements');
    }
}