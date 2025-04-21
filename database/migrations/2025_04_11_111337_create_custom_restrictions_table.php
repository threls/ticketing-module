<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->timestamp('from_date');
            $table->timestamp('to_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_restrictions');
    }
};
