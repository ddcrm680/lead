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
        Schema::create('lead_contacts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained('leads')
                ->cascadeOnDelete();

            $table->string('type', 30);

            $table->string('value');

            $table->string('normalized_value');

            $table->boolean('is_primary')
                ->default(false);

            $table->timestamp('verified_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'type',
                'normalized_value',
            ]);

            $table->index([
                'lead_id',
                'type',
                'is_primary',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_contacts');
    }
};