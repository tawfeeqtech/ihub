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
            $table->json('name')->nullable()->change();
            $table->json('location')->nullable()->change();
            $table->json('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->string('description')->nullable()->change();
        });
    }
};
