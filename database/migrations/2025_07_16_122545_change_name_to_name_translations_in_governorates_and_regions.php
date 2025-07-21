<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Governorate;
use App\Models\Region;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->json('name_translations')->after('id');
        });

        // نقل البيانات من name إلى name_translations
        Governorate::all()->each(function ($governorate) {
            $governorate->name_translations = [
                'ar' => $governorate->name,
                'en' => $governorate->name, // أو ترجمة إنجليزية إذا كانت متوفرة
            ];
            $governorate->save();
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->json('name_translations')->after('governorate_id');
        });

        // نقل البيانات من name إلى name_translations
        Region::all()->each(function ($region) {
            $region->name_translations = [
                'ar' => $region->name,
                'en' => $region->name, // أو ترجمة إنجليزية إذا كانت متوفرة
            ];
            $region->save();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        Governorate::all()->each(function ($governorate) {
            $governorate->name = $governorate->name_translations['ar'] ?? '';
            $governorate->save();
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn('name_translations');
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->string('name')->after('governorate_id');
        });

        Region::all()->each(function ($region) {
            $region->name = $region->name_translations['ar'] ?? '';
            $region->save();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('name_translations');
        });
    }
};
