<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrajetsTable extends Migration
{
    public function up()
    {
        Schema::create('trajets', function (Blueprint $table) {
            $table->id('idTrajet');
            $table->string('villeDepart', 100);
            $table->string('villeArrivee', 100);
            $table->decimal('distance', 8, 2)->nullable();
            $table->time('dureeEstimee')->nullable();
            $table->text('arretsIntermediaires')->nullable();
            $table->decimal('prixBase', 10, 2);
            $table->timestamps();
            
            $table->unique(['villeDepart', 'villeArrivee']);
            $table->index(['villeDepart', 'villeArrivee']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trajets');
    }
}