<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_cosigners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->boolean('approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->boolean('rejected')->default(false);
            $table->timestamp('rejected_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['loan_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_cosigners');
    }
};
