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
        // Bus table already has SoftDeletes - no action needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
