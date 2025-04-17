<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('custom_restriction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_restriction_id')->constrained('custom_restrictions');
            $table->foreignId('ticket_restriction_id')->constrained('ticket_restrictions');
            $table->string('key');
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_restriction_items');
    }
};
