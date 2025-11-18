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
        Schema::create('edition_circuit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('edition_id');
            $table->uuid('circuit_id');
            $table->integer('round')->nullable();
            $table->date('date')->nullable();

            $table->foreign('edition_id')->references('id')->on('editions')->onDelete('cascade');
            $table->foreign('circuit_id')->references('id')->on('circuits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edition_circuit');
    }
};
