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
        Schema::create('home_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('current_program_title')->nullable();
            $table->text('current_program_description')->nullable();
            $table->boolean('coming_soon')->default(false);
            $table->boolean('timer')->default(false);
            $table->datetime('event_date')->nullable();
            $table->boolean('is_button')->default(false);
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('about_title')->nullable();
            $table->text('about_description')->nullable();
            $table->longText('section_face_1')->nullable();
            $table->longText('section_face_2')->nullable();
            $table->json('section_pics')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
