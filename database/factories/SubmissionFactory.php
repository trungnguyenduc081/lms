<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\StudentsInClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
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
            'assignment_id'=>Assignment::inRandomOrder()->first()->id,
            'student_id'=>$studentInClass->user_id,
            'submission_time'=>fake()->dateTime('now'),
            'files'=>[],
            'feedback'=>fake()->text('150'),
            'grade'=>fake()->text('10'),
        ];
    }
}
