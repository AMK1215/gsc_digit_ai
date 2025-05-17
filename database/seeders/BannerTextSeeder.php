<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bannerTexts = [
            ['text' => 'မြန်မာနိုင်ငံရဲ့ အယုံကြည်ရဆုံး 2D - 3D Website - ကြီး', 'agent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['text' => 'မြန်မာနိုင်ငံရဲ့ အယုံကြည်ရဆုံး 2D - 3D Website - ကြီး', 'agent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['text' => 'မြန်မာနိုင်ငံရဲ့ အယုံကြည်ရဆုံး 2D - 3D Website - ကြီး', 'agent_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['text' => 'မြန်မာနိုင်ငံရဲ့ အယုံကြည်ရဆုံး 2D - 3D Website - ကြီး', 'agent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            // Add more banner texts here if needed
        ];

        DB::table('banner_texts')->insert($bannerTexts);
    }
}
