<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentsInClass;
use App\Models\Submission;
use App\Models\Teacher;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'adminadmin@example.com',
            'password' => 'adminadmin',
            'fake_email'=>0,
            'fake_password'=>0,
        ]);
        Course::factory(50)->create();
        Student::factory(100)->create();
        Teacher::factory(20)->create();
        ClassModel::factory(10)->create();
        StudentsInClass::factory(200)->create();
        Attendance::factory(100)->create();
        Assignment::factory(10)->create();
        Submission::factory(10)->create();
    }
}
