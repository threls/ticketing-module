<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->bigInteger('original_amount')->nullable()->after('time');
            $table->string('original_amount_currency')->nullable()->default('EUR')->after('original_amount');
            $table->bigInteger('discount_amount')->nullable()->after('original_amount_currency');
            $table->string('discount_amount_currency')->nullable()->default('EUR')->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('original_amount');
            $table->dropColumn('original_amount_currency');
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_amount_currency');
        });
    }
};
