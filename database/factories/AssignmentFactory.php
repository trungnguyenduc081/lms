<?php

namespace Database\Factories;

use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_id'=>ClassModel::inRandomOrder()->first()->id,
            'title'=>fake()->text(10),
            'description'=>fake()->text('100'),
            'due_date'=>fake()->date('Y-m-d')
        ];
    }
}
