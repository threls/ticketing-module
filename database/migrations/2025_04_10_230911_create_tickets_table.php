<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('parent_id')->constrained('tickets');
            $table->string('name');
            $table->integer('pax_number')->default(1);
            $table->bigInteger('price');
            $table->string('currency')->default('EUR');
            $table->foreignId('vat_id')->constrained('vat_rates');
            $table->bigInteger('vat_amount')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
