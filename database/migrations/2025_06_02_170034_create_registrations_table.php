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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('organization')->nullable();
            $table->string('country');
            $table->string('city');
            $table->text('dietary_requirements')->nullable();
            $table->enum('ticket_type', ['standard', 'vip', 'corporate']);
            $table->decimal('amount', 10, 2);
            $table->string('reference_number')->unique();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->datetime('event_date');
            $table->timestamps();

            $table->index(['status', 'event_date']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
