<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_number')->unique();
            $table->foreignId('chairman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->string('village')->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained('wards')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->string('street')->nullable();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans');
            $table->timestamp('subscription_date');
            $table->timestamp('subscription_end_date')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'trial', 'expired', 'cancelled'])->default('trial');
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended', 'expired'])->default('pending');
            $table->text('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->boolean('profile_completed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
