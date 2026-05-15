<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->optional()->sentence(3),
            'expense_category_id' => ExpenseCategory::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'paid_by' => fake()->randomElement(['Ameer', 'Jinsi']),
            'amount' => fake()->randomFloat(2, 10, 5000),
        ];
    }
}
