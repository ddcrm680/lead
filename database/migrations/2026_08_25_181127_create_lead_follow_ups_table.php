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
        Schema::create('lead_follow_ups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained('leads')
                ->cascadeOnDelete();

            $table->foreignId('assigned_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('type_id')
                ->constrained('lead_follow_up_types')
                ->restrictOnDelete();

            $table->foreignId('status_id')
                ->constrained('lead_follow_up_statuses')
                ->restrictOnDelete();

            $table->string('title');

            $table->text('notes')
                ->nullable();

            $table->timestamp('due_at');

            $table->timestamp('completed_at')
                ->nullable();

            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'lead_id',
                'status_id',
                'due_at',
            ]);

            $table->index([
                'assigned_user_id',
                'status_id',
                'due_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_follow_ups');
    }
};