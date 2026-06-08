<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('calendar_year_id')->nullable()->constrained('calendar_years')->nullOnDelete();
            $table->string('type'); // fine or penalty
            $table->string('fine_type'); // late_payment, absence, misconduct, violation, damage, other
            $table->string('month');
            $table->year('year');
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->text('reason');
            $table->enum('status', ['pending', 'paid', 'skipped'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplines');
    }
};
