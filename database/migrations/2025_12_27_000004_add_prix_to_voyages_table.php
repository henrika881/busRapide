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
            if (!Schema::hasColumn('voyages', 'prixStandard')) {
                $table->decimal('prixStandard', 10, 2)->nullable()->after('dateHeureArrivee');
            }
            if (!Schema::hasColumn('voyages', 'prixVIP')) {
                $table->decimal('prixVIP', 10, 2)->nullable()->after('prixStandard');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyages', function (Blueprint $table) {
            $table->dropColumn(['prixStandard', 'prixVIP']);
        });
    }
};
