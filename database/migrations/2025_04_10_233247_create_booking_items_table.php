<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->bigInteger('qty');
            $table->bigInteger('amount');
            $table->bigInteger('currency');
            $table->bigInteger('vat_amount');
            $table->integer('pax_number');
            $table->foreignId('vat_id')->constrained('vat_rates');
            $table->string('reference_number')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_items');
    }
};
