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
        Schema::create('past_winners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organization');
            $table->string('category');
            $table->text('achievement');
            $table->string('profile_image')->nullable(); // Uploaded file path
            $table->string('image_url')->nullable(); // Fallback URL - made nullable
            $table->integer('year');
            $table->timestamps();

            $table->index(['year', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_winners');
    }
};
