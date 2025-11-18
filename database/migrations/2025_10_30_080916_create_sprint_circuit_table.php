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
        Schema::create('sprint_circuit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('position');
            $table->uuid('driver_team_id');
            $table->uuid('circuit_id');
            $table->string('fast_lap')->nullable();

            $table->foreign('driver_team_id')->references('id')->on('driver_team')->onDelete('cascade');
            $table->foreign('circuit_id')->references('id')->on('circuits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprint_circuit');
    }
};
