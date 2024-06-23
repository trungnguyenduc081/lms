<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentsInClass>
 */
class StudentsInClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=>Student::inRandomOrder()->first()->user_id,
            'class_id'=>ClassModel::inRandomOrder()->first()->id,
            'start_on'=>date('Y-m-d')
        ];
    }
}
