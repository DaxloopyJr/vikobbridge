<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('member_number')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('gender');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->text('profile_picture')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->string('village')->nullable();
            $table->string('street')->nullable();
            $table->date('join_date');
            $table->string('cell_leader_name')->nullable();
            $table->string('cell_leader_phone')->nullable();
            $table->string('lg_chairperson_name')->nullable();
            $table->string('lg_chairperson_phone')->nullable();
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_phone')->nullable();
            $table->string('guarantor_relationship')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_phone')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->json('dependents')->nullable();
            $table->json('inheritors')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'deceased'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
