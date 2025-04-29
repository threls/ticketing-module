<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('booking_item_id')->constrained('booking_items');
            $table->string('ticket_number')->unique();
            $table->boolean('used')->default(false)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_tickets');
    }
};
