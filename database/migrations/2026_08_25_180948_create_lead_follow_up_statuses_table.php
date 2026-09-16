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
        Schema::create('lead_follow_up_statuses', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('key')
                ->unique();

            $table->string('color_code', 7)
                ->default('#6B7280');

            $table->text('description')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->boolean('is_open')
                ->default(true);

            $table->boolean('is_completed')
                ->default(false);

            $table->boolean('is_cancelled')
                ->default(false);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

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
        Schema::dropIfExists('lead_follow_up_statuses');
    }
};