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
        Schema::create('gallery_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->date('event_date');
            $table->text('description');
            $table->string('attendees')->nullable();
            $table->string('highlights')->nullable();
            $table->integer('year');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['year', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_events');
    }
};
