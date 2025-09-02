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
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('facebook_link')->nullable();
            $table->boolean('is_facebook')->default(false);
            $table->string('twitter_link')->nullable();
            $table->boolean('is_twitter')->default(false);
            $table->string('instagram_link')->nullable();
            $table->boolean('is_instagram')->default(false);
            $table->text('footer_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
