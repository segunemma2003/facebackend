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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
              $table->foreignId('nominee_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['nominee_id', 'ip_address']);
            $table->index('nominee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
