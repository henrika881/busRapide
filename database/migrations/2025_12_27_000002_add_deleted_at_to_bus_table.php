<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bus', function (Blueprint $table) {
            if (!Schema::hasColumn('bus', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('bus', function (Blueprint $table) {
            $table->dropSoftDeletesIfExists();
        });
    }
};
