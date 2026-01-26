<?php

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
        Schema::table('trajets', function (Blueprint $table) {
            // Ajouter les deux colonnes de prix si elles n'existent pas
            if (!Schema::hasColumn('trajets', 'prixStandard')) {
                $table->decimal('prixStandard', 10, 2)->nullable()->after('prixBase');
            }
            if (!Schema::hasColumn('trajets', 'prixVIP')) {
                $table->decimal('prixVIP', 10, 2)->nullable()->after('prixStandard');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trajets', function (Blueprint $table) {
            $table->dropColumn(['prixStandard', 'prixVIP']);
        });
    }
};
