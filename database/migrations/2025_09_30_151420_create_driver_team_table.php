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
        Schema::create('driver_team', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('edition_id');
            $table->uuid('driver_id');
            $table->uuid('team_id');
            $table->uuid('car_id')->nullable();
            $table->text('number')->nullable();

            $table->foreign('edition_id')->references('id')->on('editions')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_team');
    }
};
