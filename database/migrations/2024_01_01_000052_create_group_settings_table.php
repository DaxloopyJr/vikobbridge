<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, decimal, boolean, json
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_settings');
    }
};
