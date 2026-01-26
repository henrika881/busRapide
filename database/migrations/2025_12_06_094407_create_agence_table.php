<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agence', function (Blueprint $table) {
            $table->string('nom_agence', 100)->primary();
            $table->text('adresse');
            $table->string('telephone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->date('date_creation')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agence');
    }
};