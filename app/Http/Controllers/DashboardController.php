<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Submission;
use App\Models\Teacher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(){
        $totalStudents = Student::count();
        $totalClasses = ClassModel::count();
        $totalAssignments = Assignment::count();
        $totalSubmissions = Submission::count();
        $totalTeachers = Teacher::count();

        return view('dashboard', compact('totalStudents', 'totalClasses', 'totalAssignments', 'totalSubmissions', 'totalTeachers'));
    }
}
