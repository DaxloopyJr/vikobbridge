<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('loan_type_id')->constrained('loan_types')->restrictOnDelete();
            $table->foreignId('calendar_year_id')->constrained('calendar_years')->restrictOnDelete();
            $table->string('loan_number')->unique();
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->enum('rate_type', ['flat', 'reducing_balance', 'simple'])->default('flat');
            $table->integer('loan_term_months');
            $table->decimal('total_interest_amount', 12, 2);
            $table->decimal('total_repayment_amount', 12, 2);
            $table->decimal('monthly_installment', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('amount_remaining', 12, 2);
            $table->decimal('total_defaulted_amount', 12, 2)->default(0);
            $table->date('application_date');
            $table->date('disbursement_date')->nullable();
            $table->date('first_installment_date')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'disbursed', 'repaying', 'completed', 'defaulted', 'rejected', 'written_off'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignId('disbursed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disbursed_at')->nullable();
            $table->text('disbursement_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
