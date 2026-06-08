<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('rate_type', ['flat', 'reducing_balance', 'simple'])->default('flat');
            $table->decimal('rate_percentage', 5, 2);
            $table->decimal('member_withdraw_percent', 5, 2)->default(100.00);
            $table->integer('max_loan_term_months')->default(12);
            $table->decimal('max_loan_amount', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_types');
    }
};
