<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            // Permission information
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('module', 100);
            $table->string('description', 500)->nullable();

            $table->timestamps();

            // Permission listing/filtering
            $table->index('module');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};