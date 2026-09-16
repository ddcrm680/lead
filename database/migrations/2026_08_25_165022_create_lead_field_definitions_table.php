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
        Schema::create('lead_field_definitions', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('key', 100);

            $table->string('type', 30);

            $table->json('options')
                ->nullable();

            $table->json('validation_rules')
                ->nullable();

            $table->boolean('is_required')
                ->default(false);

            $table->boolean('is_filterable')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->unique('key');

            $table->index([
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
        Schema::dropIfExists('lead_field_definitions');
    }
};