<?php

namespace Database\Seeders;

use App\Models\ExpenseTag;
use Illuminate\Database\Seeder;

class ExpenseTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Amazon',
            'Flipkart',
            'Swiggy',
            'Zomato',
            'Instamart',
            'Zepto',
            'Shop',
            'Zudio',
            'Trends',
        ];

        foreach ($tags as $name) {
            ExpenseTag::firstOrCreate(['name' => $name]);
        }
    }
}
