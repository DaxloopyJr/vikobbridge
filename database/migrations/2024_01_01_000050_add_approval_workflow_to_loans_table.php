<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('source', ['recorded', 'applied'])->default('recorded')->after('status');
            $table->boolean('cosigners_approved')->default(false)->after('source');
            $table->foreignId('treasurer_approved_by')->nullable()->after('cosigners_approved')->constrained('users')->nullOnDelete();
            $table->timestamp('treasurer_approved_at')->nullable()->after('treasurer_approved_by');
            $table->foreignId('secretary_approved_by')->nullable()->after('treasurer_approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('secretary_approved_at')->nullable()->after('secretary_approved_by');
            $table->foreignId('chairman_approved_by')->nullable()->after('secretary_approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('chairman_approved_at')->nullable()->after('chairman_approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['treasurer_approved_by']);
            $table->dropForeign(['secretary_approved_by']);
            $table->dropForeign(['chairman_approved_by']);
            $table->dropColumn([
                'source',
                'cosigners_approved',
                'treasurer_approved_by',
                'treasurer_approved_at',
                'secretary_approved_by',
                'secretary_approved_at',
                'chairman_approved_by',
                'chairman_approved_at',
            ]);
        });
    }
};
