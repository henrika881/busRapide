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
        Schema::table('voyages', function (Blueprint $table) {
            if (!Schema::hasColumn('voyages', 'prixActuel')) {
                $table->decimal('prixActuel', 10, 2)->nullable()->default(null)->after('placesDisponiblesTotal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyages', function (Blueprint $table) {
            $table->dropColumn('prixActuel');
        });
    }
};
