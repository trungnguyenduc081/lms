<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassModel>
 */
class ClassModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_name'=>fake()->text(10),
            'course_id'=>Course::inRandomOrder()->first()->id,
            'teacher_id'=>Teacher::inRandomOrder()->first()->user_id,
            'schedule_from'=>fake()->date('Y-m-d'),
            'status'=>1
        ];
    }
}
