<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FiewPreviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentsInClassController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function(){
    // var_dump(auth()->check());die;
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::middleware('can:course')->group(function(){
        Route::get('courses/select2', [CourseController::class, 'searchCourses'])->name('courses.select2');
        Route::resource('courses', CourseController::class)->middleware('can:course');
    });
    
    Route::middleware('can:class')->group(function(){
        Route::get('classes/detail/{id}', [ClassController::class, 'jsonDetailOneClass'])->name('classes.json.detail');
        Route::get('classes/manage-students/{id}', [StudentsInClassController::class, 'manageStudents'])->name('classes.manage.student');
        Route::get('classes/manage-students/search-students/{id}', [StudentsInClassController::class, 'searchStudentsForClass'])->name('classes.manage.student.search');
        Route::delete('classes/manage-students/destroy/{id}', [StudentsInClassController::class, 'destroy'])->name('classes.manage.student.destroy');
        Route::post('classes/manage-students/store', [StudentsInClassController::class, 'store'])->name('classes.manage.student.insert');
        Route::get('classes/select2', [ClassController::class, 'searchClass'])->name('classes.select2');
        Route::resource('classes', ClassController::class);
    });

    Route::middleware('can:teacher')->group(function(){
        Route::get('teachers/select2', [TeacherController::class, 'searchTeacher'])->name('teachers.select2');
        Route::resource('teachers', TeacherController::class);
    });
    
    Route::middleware('can:student')->group(function(){
        Route::get('students/select2', [StudentController::class, 'searchStudent'])->name('students.select2');
        Route::resource('students', StudentController::class);
    });

    Route::middleware('can:attendance')->group(function(){
        Route::get('attendances/search-class', [AttendanceController::class, 'searchClass'])->name('attendances.search.class');
        Route::get('attendances/v2', [AttendanceController::class, 'v2'])->name('attendances.v2');
        Route::post('attendances/v2', [AttendanceController::class, 'v2multipleUpdate'])->name('attendances.v2.update');
        Route::resource('attendances', AttendanceController::class);
    });

    Route::middleware('can:assignment')->group(function(){
        Route::resource('assignments', AssignmentController::class);
    });

    Route::middleware('can:submission')->group(function(){
        Route::get('submissions/search-by-keyword', [SubmissionController::class, 'searchAssignments'])->name('submissions.search.assignments.keyword');
        Route::get('submissions/search-student-by-key', [SubmissionController::class, 'searchStudents'])->name('submissions.search.students.keyword');
        Route::post('submissions/multiple-storing', [SubmissionController::class, 'storeMultipleSubmissions'])->name('submissions.multiple-storing');
        Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    });
    // Route::resource('submissions', SubmissionController::class);

    Route::get('file-preview/{path}', [FiewPreviewController::class, 'preview'])->name('file.preview');

    Route::resource('users', UserController::class)->middleware('can:admin');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 
});

require __DIR__.'/auth.php';


