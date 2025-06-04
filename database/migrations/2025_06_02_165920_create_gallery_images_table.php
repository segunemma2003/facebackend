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
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_event_id')->constrained()->cascadeOnDelete();
            $table->string('gallery_image')->nullable(); // Uploaded file path
            $table->string('image_url')->nullable(); // Fallback URL - moved after gallery_image
            $table->text('caption')->nullable(); // Made nullable since it might be empty
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('gallery_event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
