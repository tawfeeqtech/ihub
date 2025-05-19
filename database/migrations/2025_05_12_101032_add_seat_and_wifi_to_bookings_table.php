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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('seat_number')->after('package_id')->nullable();
            $table->string('wifi_username')->after('seat_number')->nullable();
            $table->string('wifi_password')->after('wifi_username')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['seat_number', 'wifi_username', 'wifi_password']);
        });
    }
};
