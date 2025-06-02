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
        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->json('criteria')->nullable();
            $table->string('region')->default('Global');
            $table->integer('current_nominees')->default(0);
            $table->boolean('voting_open')->default(false);
            $table->datetime('voting_starts_at')->nullable();
            $table->datetime('voting_ends_at')->nullable();
            $table->string('color')->default('bg-blue-50');
            $table->string('icon')->default('trophy');
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['category_id', 'year']);
            $table->index(['is_winner', 'year']);
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
