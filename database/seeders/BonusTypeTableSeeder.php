<?php

namespace Database\Seeders;

use App\Models\BonusType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BonusTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'New Member First Deposit Bonus  100%',
            'New Member First Deposit Bonus  200%',
            'New Member First Deposit Bonus 300%',
            'Holiday Bonus 100%',
            'Daily First Charge Reward',
            'Reward for inviting friends',
            'Recharge Bonus     ',
            'Platform Bonus  ',
            'Registration Event',
            'Recharge Bonus    ',
            'Birthday',
            'Monthly reward ',
            'Agent Rebate  ',
            'Sign-in Reward   ',
            'Continuous recharge reward',
            'High recharge reward   ',
            'Recharge Reward Bonus   ',
            'Betting Reward  ',
            'Game Loss Reward ',
            'Other',
        ];

        foreach ($data as $name) {
            BonusType::create(['name' => $name]);
        }
    }
}
