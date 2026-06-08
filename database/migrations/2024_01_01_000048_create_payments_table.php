<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->restrictOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('selcom_transaction_id')->nullable();
            $table->string('order_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('TZS');
            $table->enum('payment_method', ['mobile_money', 'bank_transfer', 'card', 'other'])->default('mobile_money');
            $table->string('payment_channel')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('control_number')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('payment_response')->nullable();
            $table->text('failure_reason')->nullable();
            $table->boolean('is_renewal')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
