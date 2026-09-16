<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_lead_groups', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lead_group_id');

            // Assignment eligibility
            $table->boolean('is_active')->default(true);

            // Position within the group's assignment order
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // A user can belong to a group only once
            $table->unique(['user_id', 'lead_group_id']);

            $table->index(['lead_group_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_lead_groups');
    }
};