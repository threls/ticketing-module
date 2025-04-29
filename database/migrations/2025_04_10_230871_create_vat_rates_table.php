<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('vat_rates')) {
            Schema::create('vat_rates', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('rate')->unique();
                $table->timestamps();
            });
        }

    }

    public function down()
    {
        Schema::dropIfExists('vat_rates');
    }
};
