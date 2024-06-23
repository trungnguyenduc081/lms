<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\StudentsInClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $studentInClass = StudentsInClass::inRandomOrder()->first();
        
        return [
            'user_id'=>$studentInClass->user_id,
            'class_id'=>$studentInClass->class_id,
            'date'=>fake()->date(),
            'note'=>fake()->text(100),
            'status'=>0
        ];
    }
}
