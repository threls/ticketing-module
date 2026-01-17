<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->bigInteger('original_amount')->nullable()->after('amount_currency');
            $table->string('original_amount_currency')->nullable()->default('EUR')->after('original_amount');
        });
    }

    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn('original_amount_currency');
            $table->dropColumn('original_amount');
        });
    }
};
