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
            $table->renameColumn('payment_method', 'payment_methods'); // نغير الاسم
        });

        Schema::table('workspaces', function (Blueprint $table) {
            $table->json('payment_methods')->nullable()->change(); // نغير نوعه إلى json
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('payment_methods')->nullable()->change(); // نرجّع النوع لـ string
        });

        Schema::table('workspaces', function (Blueprint $table) {
            $table->renameColumn('payment_methods', 'payment_method'); // نرجّع الاسم زي ما كان
        });
    }
};
