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
        Schema::create('ranking_driver', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('points');
            $table->uuid('driver_team_id');

            $table->foreign('driver_team_id')->references('id')->on('driver_team')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_driver');
    }
};
