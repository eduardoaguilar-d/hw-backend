<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RewardConfig;

class RewardConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rewards = [
            [
                'order_count' => 6,
                'discount_percentage' => 10.00,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'order_count' => 7,
                'discount_percentage' => 10.00,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'order_count' => 8,
                'discount_percentage' => 10.00,
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($rewards as $reward) {
            RewardConfig::create($reward);
        }
    }
}
