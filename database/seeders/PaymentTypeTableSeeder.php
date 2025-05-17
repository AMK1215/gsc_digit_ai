<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'AYA Bank',
                'image' => 'ayabank.png',
            ],
            [
                'name' => 'AYA Pay',
                'image' => 'ayapay.png',
            ],
            [
                'name' => 'CB Bank',
                'image' => 'cbbank.png',
            ],
            [
                'name' => 'CB Pay',
                'image' => 'cbpay.png',
            ],
            [
                'name' => 'KBZ Bank',
                'image' => 'kbzbank.webp',
            ],
            [
                'name' => 'KBZ Pay',
                'image' => 'kpay.png',
            ],
            [
                'name' => 'MAB Bank',
                'image' => 'mabbank.png',
            ],
            [
                'name' => 'UAB Pay',
                'image' => 'uabpay.png',
            ],
            [
                'name' => 'WAVE Pay',
                'image' => 'wave.png',
            ],
            [
                'name' => 'YOMA Bank',
                'image' => 'yomabank.png',
            ],
        ];

        DB::table('payment_types')->insert($types);
    }
}
