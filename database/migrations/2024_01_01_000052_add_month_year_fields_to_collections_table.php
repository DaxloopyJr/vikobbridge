<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->string('month', 20)->nullable()->after('payment_date');
            $table->year('year')->nullable()->after('month');
            $table->boolean('is_new_calendar_year')->default(false)->after('year');
            $table->decimal('balance_carried_forward', 12, 2)->nullable()->after('is_new_calendar_year');
        });
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['month', 'year', 'is_new_calendar_year', 'balance_carried_forward']);
        });
    }
};
