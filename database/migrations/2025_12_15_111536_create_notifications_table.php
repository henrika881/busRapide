<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('idNotification');
            $table->foreignId('id_client')->constrained('clients', 'id_client')->onDelete('cascade');
            $table->foreignId('idTicket')->nullable()->constrained('tickets', 'idTicket')->onDelete('set null');
            $table->foreignId('idVoyage')->nullable()->constrained('voyages', 'idVoyage')->onDelete('set null');
            $table->enum('typeNotification', ['reservation', 'annulation', 'rappel', 'retard', 'promotion']);
            $table->string('titre', 255);
            $table->text('contenu');
            $table->enum('typeEnvoi', ['email', 'sms', 'push'])->default('email');
            $table->datetime('dateEnvoi')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('dateLecture')->nullable();
            $table->enum('statut', ['envoye', 'lu', 'erreur'])->default('envoye');
            $table->timestamps();
            
            $table->index(['id_client', 'typeNotification']);
            $table->index('dateEnvoi');
            $table->index(['id_client', 'dateEnvoi']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}