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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->ulid('public_id')
                ->unique();

            $table->string('display_name');

            $table->foreignId('source_id')
                ->nullable()
                ->constrained('lead_sources')
                ->nullOnDelete();

            $table->foreignId('status_id')
                ->constrained('lead_statuses')
                ->restrictOnDelete();

            $table->foreignId('pipeline_stage_id')
                ->nullable()
                ->constrained('pipeline_stages')
                ->nullOnDelete();

            $table->foreignId('assigned_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
                
            $table->unsignedTinyInteger('priority')
                ->default(20);

            $table->string('city', 150)
                ->nullable();

            $table->string('country', 100)
                ->nullable();

            $table->json('attributes')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('source_id');
            $table->index('status_id');
            $table->index('pipeline_stage_id');
            $table->index('assigned_user_id');
            $table->index('priority');
            $table->index('city');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};