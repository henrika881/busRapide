<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_deleted_at_to_voyages_table.php
public function up()
{
    Schema::table('voyages', function (Blueprint $table) {
        $table->softDeletes(); // Ceci ajoute la colonne deleted_at
    });
}

public function down()
{
    Schema::table('voyages', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}
};
