<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->foreignId('governorate_id')->after('location')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('region_id')->after('governorate_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['region_id']);
            $table->dropColumn(['governorate_id', 'region_id']);
        });
    }
};
