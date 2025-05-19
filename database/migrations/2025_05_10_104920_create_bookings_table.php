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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('payment_method'); // آلية الدفع: بنك أو كاش
            $table->string('payment_reference')->nullable(); // رقم الدفع أو إثبات الدفع
            $table->timestamp('start_at')->nullable(); // بداية الحجز
            $table->timestamp('end_at')->nullable(); // نهاية الحجز
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending'); // حالة الحجز
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
