<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_groups', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('description', 500)->nullable();

            // Assignment strategy
            $table->string('assignment_method', 30)
                ->default('round_robin');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'assignment_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_groups');
    }
};