
LLL<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('classeBillet', 20)->nullable()->after('idSiege');
            $table->decimal('prixBase', 10, 2)->nullable()->after('classeBillet');
            $table->decimal('surcoutVIP', 10, 2)->default(0)->after('prixBase');
            $table->boolean('promotionVIP')->default(false)->after('surcoutVIP');
            $table->decimal('reductionVIP', 10, 2)->default(0)->after('promotionVIP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['classeBillet', 'prixBase', 'surcoutVIP', 'promotionVIP', 'reductionVIP']);
        });
    }
};
