<?php

namespace App\Http\Controllers;

use App\Events\StudentInClassChanging;
use App\Models\StudentsInClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StudentsInClassRequest;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class StudentsInClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request): View
    // {
    //     $studentsInClasses = StudentsInClass::paginate();

    //     return view('students-in-class.index', compact('studentsInClasses'))
    //         ->with('i', ($request->input('page', 1) - 1) * $studentsInClasses->perPage());
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create(): View
    // {
    //     $studentsInClass = new StudentsInClass();

    //     return view('students-in-class.create', compact('studentsInClass'));
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentsInClassRequest $request):JsonResponse
    {
        $classId = $request->class_id;

        $studentsInClass = new StudentsInClass();
        $studentsInClass->start_on = date('Y-m-d');
        $studentsInClass->user_id = $request->user_id;
        $studentsInClass->class_id = $request->class_id;
        $studentsInClass->save();

        $user = $studentsInClass->user()
        ->select('id', 'name')
        ->first();

        StudentInClassChanging::dispatch($classId);

        return response()->json(['status'=>1, 'data'=>['studentsInClass'=>$studentsInClass, 'user'=>$user]]);
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show($id): View
    // {
    //     $studentsInClass = StudentsInClass::find($id);

    //     return view('students-in-class.show', compact('studentsInClass'));
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit($id): View
    // {
    //     $studentsInClass = StudentsInClass::find($id);

    //     return view('students-in-class.edit', compact('studentsInClass'));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(StudentsInClassRequest $request, StudentsInClass $studentsInClass): RedirectResponse
    // {
    //     $studentsInClass->update($request->validated());

    //     return Redirect::route('students-in-classes.index')
    //         ->with('success', 'StudentsInClass updated successfully');
    // }

    public function destroy($id): JsonResponse
    {
        $record = StudentsInClass::find($id);
        $classId = $record->class_id; 
        
        $record->delete();
        StudentInClassChanging::dispatch($classId);
        
        return response()->json(['status'=>1]);
    }

    public function manageStudents($id): View{
        $class = ClassModel::with('studentsInClass')->findOrFail($id);
        $students = $class->studentsInClass()
        ->with('user')
        ->get();
        $i = 0;
        // dd($students);
        return view('students-in-class.manage-students', compact('class', 'students', 'i'));
    }

    public function searchStudentsForClass($id):JsonResponse{
        $term = request('term', '');
        $userTbl = (new User())->getTable();
        $studentTbl = (new Student())->getTable();

        $studentsInClass = StudentsInClass::query()
        ->where('class_id', $id)
        ->select('class_id', 'user_id')
        ->get();

        $exclusiveStudents = $studentsInClass->pluck('user_id');
        
        $matchedStudents = DB::table($studentTbl)
        ->leftJoin($userTbl, "{$studentTbl}.user_id", '=', "{$userTbl}.id")
        ->where("{$userTbl}.name", "like", "%{$term}%");

        if(count($exclusiveStudents) > 0){
            $matchedStudents->whereNotIn("{$studentTbl}.user_id", $exclusiveStudents->toArray());
        }

        $matchedStudents = $matchedStudents->select("{$userTbl}.name", "{$studentTbl}.user_id")
        ->limit(100)
        ->get();

        $students = [];
        foreach($matchedStudents as $student){
            $students[] = ['id'=>$student->user_id, 'text'=>$student->name];
        }
    
        return response()->json(['results'=>$students, "pagination"=> [
            "more"=> false
        ]]);
    }
}
