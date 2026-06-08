<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('fund_type', ['savings', 'contribution', 'fee', 'fine', 'project', 'other'])->default('contribution');
            $table->boolean('is_mandatory')->default(false);
            $table->decimal('default_amount', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['group_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_funds');
    }
};
