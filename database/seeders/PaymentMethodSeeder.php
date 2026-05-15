<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            'Cash',
            'PhonePe',
            'GooglePay',
            'HDFC CC',
            'SBI CC (Ameer)',
            'SBI CC (Jinsi)',
            'ICICI CC',
            'FederalBank CC',
            'InduslandBank CC',
        ];

        foreach ($methods as $name) {
            PaymentMethod::firstOrCreate(['name' => $name]);
        }
    }
}
