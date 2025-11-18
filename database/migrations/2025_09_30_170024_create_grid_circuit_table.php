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
        Schema::create('grid_circuit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('position');
            $table->uuid('driver_team_id');
            $table->uuid('edition_circuit_id');
            $table->string('time')->nullable();

            $table->foreign('driver_team_id')->references('id')->on('driver_team')->onDelete('cascade');
            $table->foreign('edition_circuit_id')->references('id')->on('edition_circuit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grid_circuit');
    }
};
