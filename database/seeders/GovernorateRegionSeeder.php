<?php

namespace Database\Seeders;

use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Database\Seeder;

class GovernorateRegionSeeder extends Seeder
{
    public function run()
    {
        // $governorates = [
        //     'غزة' => ['الرمال', 'الشيخ رضوان', 'الصبرة', 'الزيتون'],
        //     'الشمال' => ['جباليا', 'بيت لاهيا', 'بيت حانون'],
        //     'الوسطى' => ['دير البلح', 'النصيرات', 'المغازي'],
        //     'خان يونس' => ['القرارة', 'بني سهيلا', 'عبسان'],
        //     'رفح' => ['الشابورة', 'النصر', 'تل السلطان'],
        // ];

        // foreach ($governorates as $govName => $regions) {
        //     $governorate = Governorate::create(['name' => $govName]);
        //     foreach ($regions as $regionName) {
        //         Region::create([
        //             'name' => $regionName,
        //             'governorate_id' => $governorate->id,
        //         ]);
        //     }
        // }

        $governorates = [
            [
                'name' => ['ar' => 'غزة', 'en' => 'Gaza'],
                'regions' => [
                    ['ar' => 'الرمال', 'en' => 'Rimal'],
                    ['ar' => 'الشيخ رضوان', 'en' => 'Sheikh Radwan'],
                    ['ar' => 'الصبرة', 'en' => 'Sabra'],
                    ['ar' => 'الزيتون', 'en' => 'Zaytoun'],
                ],
            ],
            [
                'name' => ['ar' => 'الشمال', 'en' => 'North'],
                'regions' => [
                    ['ar' => 'جباليا', 'en' => 'Jabalia'],
                    ['ar' => 'بيت لاهيا', 'en' => 'Beit Lahia'],
                    ['ar' => 'بيت حانون', 'en' => 'Beit Hanoun'],
                ],
            ],
            [
                'name' => ['ar' => 'الوسطى', 'en' => 'Central'],
                'regions' => [
                    ['ar' => 'دير البلح', 'en' => 'Deir al-Balah'],
                    ['ar' => 'النصيرات', 'en' => 'Nuseirat'],
                    ['ar' => 'المغازي', 'en' => 'Maghazi'],
                ],
            ],
            [
                'name' => ['ar' => 'خان يونس', 'en' => 'Khan Younis'],
                'regions' => [
                    ['ar' => 'القرارة', 'en' => 'Qarara'],
                    ['ar' => 'بني سهيلا', 'en' => 'Bani Suheila'],
                    ['ar' => 'عبسان', 'en' => 'Abasan'],
                ],
            ],
            [
                'name' => ['ar' => 'رفح', 'en' => 'Rafah'],
                'regions' => [
                    ['ar' => 'الشابورة', 'en' => 'Shaboura'],
                    ['ar' => 'النصر', 'en' => 'Nasr'],
                    ['ar' => 'تل السلطان', 'en' => 'Tel al-Sultan'],
                ],
            ],
        ];

        foreach ($governorates as $govData) {
            $governorate = Governorate::create(['name' => $govData['name']]);
            foreach ($govData['regions'] as $regionName) {
                Region::create([
                    'name' => $regionName,
                    'governorate_id' => $governorate->id,
                ]);
            }
        }
    }
}
