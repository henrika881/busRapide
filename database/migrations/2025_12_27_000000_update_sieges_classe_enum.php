<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ajouter la valeur 'vip' à l'enum 'classe'
        DB::statement("ALTER TABLE `sieges` MODIFY `classe` ENUM('standard', 'vip', 'premium') NOT NULL DEFAULT 'standard'");
    }

    public function down()
    {
        // Revenir à l'ancien état (supprime 'vip')
        DB::statement("ALTER TABLE `sieges` MODIFY `classe` ENUM('standard', 'premium') NOT NULL DEFAULT 'standard'");
    }
};
