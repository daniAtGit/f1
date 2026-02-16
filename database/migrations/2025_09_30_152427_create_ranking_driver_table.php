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
            $table->uuid('edition_id');
            $table->uuid('team_id');
            $table->uuid('driver_id');

            $table->foreign('edition_id')->references('id')->on('editions')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
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
