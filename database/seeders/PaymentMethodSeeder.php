<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash', 'paid_by' => 'Ameer'],
            ['name' => 'PhonePe', 'paid_by' => 'Jinsi'],
            ['name' => 'GooglePay', 'paid_by' => 'Jinsi'],
            ['name' => 'HDFC CC', 'paid_by' => 'Ameer'],
            ['name' => 'SBI CC (Ameer)', 'paid_by' => 'Ameer'],
            ['name' => 'SBI CC (Jinsi)', 'paid_by' => 'Jinsi'],
            ['name' => 'ICICI CC', 'paid_by' => 'Ameer'],
            ['name' => 'FederalBank CC', 'paid_by' => 'Ameer'],
            ['name' => 'InduslandBank CC', 'paid_by' => 'Ameer'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(['name' => $method['name']], ['paid_by' => $method['paid_by']]);
        }
    }
}
