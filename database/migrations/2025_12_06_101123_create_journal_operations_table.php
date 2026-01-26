<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('journal_operations', function (Blueprint $table) {
            $table->id('id_operation');
            $table->string('type_operation', 100);
            $table->string('user_id', 50)->nullable();
            $table->string('entite_affectee', 100)->nullable();
            $table->text('details')->nullable();
            $table->dateTime('date_operation')->useCurrent();
            $table->enum('statut_operation', ['succes', 'echec', 'annule'])->default('succes');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_operations');
    }
};