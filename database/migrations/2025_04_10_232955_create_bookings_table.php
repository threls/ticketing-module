<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->bigInteger('amount')->nullable()->default('EUR');
            $table->string('amount_currency')->nullable();
            $table->bigInteger('vat_amount');
            $table->string('vat_amount_currency')->default('EUR');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
