<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('telephone', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->date('date_creation')->nullable();
            $table->string('numero_cni', 50)->unique();
            $table->enum('type_utilisateur', ['client', 'employe']);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};