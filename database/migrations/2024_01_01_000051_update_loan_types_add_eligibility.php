<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_types', function (Blueprint $table) {
            // Remove member_withdraw_percent (moved to group_settings)
            $table->dropColumn('member_withdraw_percent');

            // Add processing fee
            $table->decimal('processing_fee', 5, 2)->default(0)->after('rate_percentage');

            // Eligibility toggle
            $table->boolean('uses_eligibility_rules')->default(false)->after('processing_fee');

            // Eligibility parameters
            $table->integer('min_membership_months')->nullable()->after('uses_eligibility_rules');
            $table->boolean('requires_active_status')->default(true)->after('min_membership_months');
            $table->decimal('max_loan_hisa_multiplier', 5, 2)->nullable()->after('requires_active_status');
        });
    }

    public function down(): void
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->decimal('member_withdraw_percent', 5, 2)->default(100.00);

            $table->dropColumn([
                'processing_fee',
                'uses_eligibility_rules',
                'min_membership_months',
                'requires_active_status',
                'max_loan_hisa_multiplier',
            ]);
        });
    }
};
