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
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
             $table->string('page')->default('home'); // home, about, contact, etc.
            $table->string('section'); // hero, about, registration, etc.
            $table->string('key'); // specific content key
            $table->string('type')->default('text'); // text, image, json, html
            $table->longText('content')->nullable();
            $table->json('meta')->nullable(); // for additional data like alt text, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['page', 'section', 'key']);
            $table->index(['page', 'section', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};
