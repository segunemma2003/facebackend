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
        // Drop the existing table to recreate it properly
        Schema::dropIfExists('nominees');

        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            // Basic nominee information
            $table->string('name');
            $table->string('organization');
            $table->string('position')->nullable();
            $table->string('location')->nullable();
            $table->text('description');
            $table->text('long_bio')->nullable();
            $table->text('impact_summary')->nullable();

            // Media fields
            $table->string('profile_image')->nullable(); // Uploaded file path
            $table->string('cover_image')->nullable(); // Uploaded file path
            $table->json('gallery_images')->nullable(); // Array of uploaded file paths
            $table->string('image_url')->nullable(); // Fallback URL
            $table->string('cover_image_url')->nullable(); // Fallback URL
            $table->string('video_url')->nullable();

            // Social links
            $table->json('social_links')->nullable();

            // Voting fields
            $table->integer('votes')->default(0);
            $table->decimal('voting_percentage', 5, 2)->default(0.00);
            $table->boolean('can_vote')->default(true);
            $table->boolean('is_winner')->default(false);

            // Administrative fields
            $table->integer('year')->default(2025);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index(['category_id', 'year']);
            $table->index(['is_winner', 'year']);
            $table->index('votes');
            $table->index(['can_vote', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominees');
    }
};
