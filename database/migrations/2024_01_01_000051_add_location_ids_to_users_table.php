<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('village')->constrained('regions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('region_id')->constrained('districts')->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->after('district_id')->constrained('wards')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->after('ward_id')->constrained('villages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['ward_id']);
            $table->dropForeign(['village_id']);
            $table->dropColumn(['region_id', 'district_id', 'ward_id', 'village_id']);
        });
    }
};
