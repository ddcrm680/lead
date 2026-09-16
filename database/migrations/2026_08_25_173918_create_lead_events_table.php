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
        Schema::create('lead_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained('leads')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('type', 50);

            $table->string('title');

            $table->text('description')
                ->nullable();

            $table->json('payload')
                ->nullable();

            $table->timestamp('occurred_at');

            $table->timestamps();

            $table->index([
                'lead_id',
                'occurred_at',
            ]);

            $table->index([
                'lead_id',
                'type',
                'occurred_at',
            ]);

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_events');
    }
};