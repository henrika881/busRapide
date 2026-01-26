<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteractionsTable extends Migration
{
    public function up()
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id('idInteraction');
            $table->foreignId('idChatbot')->constrained('chatbots', 'idChatbot');
            $table->foreignId('id_client')->nullable()->constrained('clients', 'id_client')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->enum('typeInteraction', ['question', 'reservation', 'reclamation', 'information']);
            $table->string('sujet', 255)->nullable();
            $table->text('message');
            $table->datetime('dateHeureDebut')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('dateHeureFin')->nullable();
            $table->enum('statut', ['en_cours', 'termine', 'transfere'])->default('en_cours');
            $table->timestamps();
            
            $table->index(['id_client', 'dateHeureDebut']);
            $table->index(['statut', 'dateHeureDebut']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('interactions');
    }
}