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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nominee_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('role');
            $table->string('organization');
            $table->text('content');
            $table->string('image_url')->nullable();
            $table->string('testimonial_image')->nullable(); // Moved to proper position
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
        Schema::dropIfExists('testimonials');
    }
};
