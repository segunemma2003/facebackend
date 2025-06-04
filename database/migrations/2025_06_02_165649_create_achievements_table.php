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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nominee_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->date('date'); // Changed from string to date
            $table->string('achievement_image')->nullable(); // Uploaded file path
            $table->string('image_url')->nullable(); // Fallback URL
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('nominee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
