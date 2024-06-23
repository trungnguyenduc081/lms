<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassEditRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ClassRequest;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentsInClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $classes = ClassModel::query()
        ->with('teacher:id,name', 'course:id,course_name')
        ->paginate();

        return view('class.index', compact('classes'))
            ->with('i', ($request->input('page', 1) - 1) * $classes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $class = new ClassModel();
        $defaultTeacher = $this->getSubmittedTeacher($class);
        $defaultCourse = $this->getSubmittedCourse($class);

        return view('class.create', compact('class', 'defaultTeacher', 'defaultCourse'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClassRequest $request): RedirectResponse
    {
        $classAttrs = $request->validated();
        $classAttrs['status'] = 0;
        $classAttrs['exclude_dates'] = $request->exclude_dates;

        ClassModel::create($classAttrs);

        return Redirect::route('classes.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $class = ClassModel::find($id);

        return view('class.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $class = ClassModel::find($id);
        $defaultTeacher = $this->getSubmittedTeacher($class);
        $defaultCourse = $this->getSubmittedCourse($class);

        return view('class.edit', compact('class', 'defaultTeacher', 'defaultCourse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClassEditRequest $request, ClassModel $class): RedirectResponse
    {
        $class->update($request->validated());

        return Redirect::route('classes.index')
            ->with('success', 'Class updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $class = ClassModel::findOrFail($id);

        $assignment = Assignment::query()
        ->where('class_id', $id)
        ->count();

        if($assignment > 0){
            return back()->with('error-message', 'Can not remove class "'.$class->class_name.'" since it has some assignments');
        }

        $attendance = Attendance::query()
        ->where('class_d', $id)
        ->count();

        if($attendance > 0){
            return back()->with('error-message', 'Can not remove class "'.$class->class_name.'" since it has some attendances');
        }

        DB::transaction(function() use ($id, $class){
            StudentsInClass::query()
            ->where('class_id', $id)
            ->delete();
        
            $class->delete();
        });

        return Redirect::route('classes.index')
            ->with('success', 'Class deleted successfully');
    }

    private function getSubmittedTeacher($class):mixed{
        $teacherId = old('teacher_id', $class?->teacher_id);
        if(is_numeric($teacherId)){
            $teacher = User::query()
            ->where('id', $teacherId)
            ->first();
            if($teacher){
                return ['id'=>$teacher->id, 'text'=>$teacher->name];
            }  
        }

        return null;
    }

    private function getSubmittedCourse($class):mixed{
        $courseId = old('course_id', $class?->course_id);
        if(is_numeric($courseId)){
            $course = Course::query()
            ->where('id', $courseId)
            ->first();

            if($course){
                return ['id'=>$course->id, 'text'=>$course->course_name];
            }  
        }

        return null;
    }

    public function jsonDetailOneClass($id):JsonResponse{
        $class = ClassModel::find($id);
        return response()->json(['data'=>['class'=>$class], 'status'=>1]);
    }

    public function searchClass():JsonResponse{
        $term = request('term', '');
        $matchedClasses = ClassModel::query()
        ->where("class_name", "like", "%{$term}%")
        ->limit(100)
        ->get();

        $courses = [];
        foreach($matchedClasses as $class){
            $courses[] = ['id'=>$class->id, 'text'=>$class->class_name];
        }
    
        return response()->json([
            'results'=>$courses, 
            "pagination"=> [
                "more"=> false
                ]
            ]);
    }
}
