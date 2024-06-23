<?php

namespace App\Http\Controllers;

use App\Events\StudentInClassChanging;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StudentRequest;
use App\Models\Attendance;
use App\Models\StudentsInClass;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $students = Student::paginate();

        return view('student.index', compact('students'))
            ->with('i', ($request->input('page', 1) - 1) * $students->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $student = new Student();

        return view('student.create', compact('student'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentRequest $request): RedirectResponse
    {
        DB::transaction(function() use ($request){
            $user = new User();
            $user->email = str()->random(100).'@';
            $user->password = Hash::make(str()->random(50));
            $user->fake_email = 1;
            $user->fake_password = 1;
            $user->name = $request->name;
            $user->save();

            $studentAttr = $request->validated();
            $studentAttr['user_id'] = $user->id;

            Student::create($studentAttr);
        });
        

        return Redirect::route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $student = Student::findOrFail($id);

        return view('student.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $student = Student::findOrFail($id);

        return view('student.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        DB::transaction(function() use ($student, $request){
            $student->user->update(['name'=>$request->name]);
            $student->update($request->validated());
        });

        return Redirect::route('students.index')
            ->with('success', 'Student updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        
        DB::transaction(function() use ($id){
            $student = Student::findOrFail($id);
            $userId = $student->user_id;

            Attendance::query()
            ->where('user_id', $userId)
            ->delete();

            Submission::query()
            ->where('student_id', $userId)
            ->delete();

            StudentsInClass::query()
            ->where('user_id', $userId)
            ->delete();

            $student->delete();
            
            User::findOrFail($student->user_id)->delete();

            StudentInClassChanging::dispatch($id);
        });

        return Redirect::route('students.index')
            ->with('success', 'Student deleted successfully');
    }

    public function searchStudent(): JsonResponse{
        $term = request('term', '');
        $userTbl = (new User())->getTable();
        $studentTbl = (new Student())->getTable();

        $matchedStudents = DB::table($studentTbl)
        ->leftJoin($userTbl, "{$studentTbl}.user_id", '=', "{$userTbl}.id")
        ->where("{$userTbl}.name", "like", "%{$term}%")
        ->select("{$userTbl}.name", "{$userTbl}.id")
        ->limit(100)
        ->get();

        $students = [];
        foreach($matchedStudents as $student){
            $students[] = ['id'=>$student->id, 'text'=>$student->name];
        }
    
        return response()->json(['results'=>$students, "pagination"=> [
            "more"=> false
        ]]);
    }
}
