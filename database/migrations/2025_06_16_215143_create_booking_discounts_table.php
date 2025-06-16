<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained();
            $table->morphs('discountable');
            $table->bigInteger('amount');
            $table->string('amount_currency')->default('EUR');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_discounts');
    }
};
