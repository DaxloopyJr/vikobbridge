<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->integer('installment_number');
            $table->date('due_date');
            $table->decimal('emi_amount', 12, 2);
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_amount', 12, 2);
            $table->decimal('balance_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'skipped', 'overdue'])->default('pending');
            $table->enum('payment_channel', ['bank', 'mobile_money', 'cash', 'selcom', 'other'])->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};
