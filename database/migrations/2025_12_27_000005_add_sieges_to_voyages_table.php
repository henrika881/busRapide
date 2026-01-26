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
            if (!Schema::hasColumn('voyages', 'siegesStandardDisponibles')) {
                $table->integer('siegesStandardDisponibles')->default(0)->after('prixVIP');
            }
            if (!Schema::hasColumn('voyages', 'siegesVIPDisponibles')) {
                $table->integer('siegesVIPDisponibles')->default(0)->after('siegesStandardDisponibles');
            }
            if (!Schema::hasColumn('voyages', 'placesDisponiblesTotal')) {
                $table->integer('placesDisponiblesTotal')->default(0)->after('siegesVIPDisponibles');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyages', function (Blueprint $table) {
            $table->dropColumn(['siegesStandardDisponibles', 'siegesVIPDisponibles', 'placesDisponiblesTotal']);
        });
    }
};
