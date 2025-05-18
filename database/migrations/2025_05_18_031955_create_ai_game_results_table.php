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
        Schema::create('ai_game_results', function (Blueprint $table) {
            $table->id();
            // Store the period number (string or integer, depending on your format)
            $table->string('period')->unique()->comment('Unique identifier for the game period');
            // Store the game duration for this result (e.g., 1, 3, 5, 10 minutes)
            $table->integer('duration')->comment('Game duration in minutes');
            // Store the winning digit (0-9)
            $table->integer('winning_digit')->comment('The winning digit for this period');
            // Store the winning color (e.g., Green, Violet, Red)
            $table->string('winning_color')->comment('The winning color for this period');
            // Store the winning size (e.g., Big, Small) - if applicable
            $table->string('winning_size')->nullable()->comment('The winning size for this period (optional)');
            // You might add other fields like start_time, end_time, etc.
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_game_results');
    }
};