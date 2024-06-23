<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\V2AttendanceRequest;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\StudentsInClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AttendanceController extends Controller
{

    public function v2(Request $request){
        $classId = $request->get('class', null);
        $date = $request->get('date', null);
        $class = null;
        $students = [];
        $attendanceLogs = new Collection();
        if(!is_null($classId)){
            $class = ClassModel::findOrFail($classId);
        }

        if(!is_null($classId) && !is_null($date)){
            $students = $class->studentsInClass()
            ->with('user')
            ->get();

            $attendanceLogs = $class->attendances()
            ->where('date', $date)
            ->get();
        }

        return view('attendance.index-v2', compact('students', 'class', 'date', 'attendanceLogs'));
    }

    public function v2multipleUpdate(V2AttendanceRequest $request){
        $status = $request->status;
        $notes = $request->note;
        $class = $request->class;
        $date = $request->date;
        // dd($notes);
        
        foreach($status as $userId=>$s){
            $note = $notes[$userId] ?? null;
            $attendance = Attendance::query()
            ->where('user_id', $userId)
            ->where('date', $date)
            ->where('class_id', $class)
            ->first();

            if(!$attendance){
                $attendance = new Attendance();
                $attendance->user_id = $userId;
                $attendance->date = $date;
                $attendance->class_id = $class;
            }

            $attendance->note = $note;
            $attendance->status = $s;
            $attendance->save();
        }

        return back();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $attendances = Attendance::query()
        ->with('student:id,name', 'class:id,class_name')
        ->paginate();

        return view('attendance.index', compact('attendances'))
            ->with('i', ($request->input('page', 1) - 1) * $attendances->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $attendance = new Attendance();
        $defaultStudent = $this->getSubmittedStudent($attendance);
        $defaultClass = $this->getSubmittedClass($attendance);

        return view('attendance.create', compact('attendance', 'defaultStudent', 'defaultClass'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttendanceRequest $request): RedirectResponse
    {
        Attendance::create($request->validated());

        return Redirect::route('attendances.index')
            ->with('success', 'Attendance created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $attendance = Attendance::find($id);

        return view('attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $attendance = Attendance::find($id);
        $defaultStudent = $this->getSubmittedStudent($attendance);
        $defaultClass = $this->getSubmittedClass($attendance);

        return view('attendance.edit', compact('attendance', 'defaultStudent', 'defaultClass'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        $attendance->update($request->validated());

        return Redirect::route('attendances.index')
            ->with('success', 'Attendance updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Attendance::find($id)->delete();

        return Redirect::route('attendances.index')
            ->with('success', 'Attendance deleted successfully');
    }

    private function getSubmittedStudent($attendance):mixed{
        $studentId = old('user_id', $attendance?->user_id);
        if(is_numeric($studentId)){
            $student = User::query()
            ->where('id', $studentId)
            ->first();
            
            if($student){
                return ['id'=>$student->id, 'text'=>$student->name];
            }  
        }

        return null;
    }

    private function getSubmittedClass($attendance):mixed{
        $classId = old('class_id', $attendance?->class_id);
        if(is_numeric($classId)){
            $class = ClassModel::query()
            ->where('id', $classId)
            ->first();
            
            if($class){
                return ['id'=>$class->id, 'text'=>$class->class_name];
            }  
        }

        return null;
    }

    public function searchClass():JsonResponse {
        $term = request('term', '');
        $userTbl = (new User())->getTable();
        $courseTbl = (new Course())->getTable();
        $classTbl = (new ClassModel())->getTable();
        
        $matchedClasses = DB::table($classTbl)
        ->leftJoin($userTbl, "{$classTbl}.teacher_id", '=', "{$userTbl}.id")
        ->leftJoin($courseTbl, "{$classTbl}.course_id", '=', "{$courseTbl}.id")
        ->where(function($query) use ($userTbl, $term, $courseTbl, $classTbl){
            $query->where("{$classTbl}.class_name", "like", "%{$term}%")
            ->where("{$userTbl}.name", "like", "%{$term}%")
            ->orWhere("{$courseTbl}.course_name", "like", "%{$term}%");
        })
        ->select("{$classTbl}.class_name", "{$classTbl}.id")
        ->limit(100)
        ->get();

        $classes = [];
        foreach($matchedClasses as $class){
            $classes[] = ['id'=>$class->id, 'text'=>$class->class_name];
        }
    
        return response()->json(['results'=>$classes, "pagination"=> [
            "more"=> false
        ]]);
    }
}
