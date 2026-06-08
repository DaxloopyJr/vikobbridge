<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('collection_fund_id')->constrained('collection_funds')->cascadeOnDelete();
            $table->foreignId('calendar_year_id')->constrained('calendar_years')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->enum('payment_channel', ['bank', 'mobile_money', 'cash', 'selcom', 'other'])->default('cash');
            $table->string('payment_control_number')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->enum('source', ['manual', 'api', 'import'])->default('manual');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
