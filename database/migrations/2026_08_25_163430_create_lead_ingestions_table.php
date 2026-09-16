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
        Schema::create('lead_ingestions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->nullable()
                ->constrained('leads')
                ->nullOnDelete();

            $table->foreignId('source_id')
                    ->nullable()
                    ->constrained('lead_sources')
                    ->nullOnDelete();
                    
            $table->string('channel', 50);

            $table->string('external_reference')
                ->nullable();

            $table->json('payload');

            $table->string('status', 30)
                ->default('pending');

            $table->text('error_message')
                ->nullable();

            $table->timestamp('processed_at')
                ->nullable();

            $table->timestamps();

            $table->index('lead_id');

            $table->index([
                'channel',
                'external_reference',
            ]);

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_ingestions');
    }
};