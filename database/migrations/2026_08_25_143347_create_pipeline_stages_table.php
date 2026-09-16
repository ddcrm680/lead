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
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pipeline_id')
                ->constrained('pipelines')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('key');

            $table->string('color_code', 7)
                ->default('#6B7280');

            $table->text('description')
                ->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'pipeline_id',
                'key',
            ]);

            $table->index([
                'pipeline_id',
                'is_active',
                'sort_order',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};