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
        Schema::table('lead_contacts', function (Blueprint $table) {
            $table->dropIndex([
                'type',
                'normalized_value',
            ]);

            $table->unique([
                'lead_id',
                'type',
                'normalized_value',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_contacts', function (Blueprint $table) {
            $table->dropUnique([
                'lead_id',
                'type',
                'normalized_value',
            ]);

            $table->index([
                'type',
                'normalized_value',
            ]);
        });
    }
};