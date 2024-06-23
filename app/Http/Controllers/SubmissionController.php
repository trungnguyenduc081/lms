<?php

namespace App\Http\Controllers;

use App\Http\Requests\MultipleSubmissionsRequest;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\SubmissionRequest;
use App\Models\Assignment;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\StudentsInClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpParser\Builder\Class_;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request): View
    // {
    //     $submissions = Submission::paginate();

    //     return view('submission.index', compact('submissions'))
    //         ->with('i', ($request->input('page', 1) - 1) * $submissions->perPage());
    // }

    public function index(Request $request): View
    {
        $assignmentId = $request->get('assignment_id', null);
        $assignment = null;
        $students = [];
        $class = null;
        $submissionLogs = new Collection();

        if(!is_null($assignmentId)){
            $assignment = Assignment::findOrFail($assignmentId);
            $class = $assignment->class;
            $students = $assignment->class->studentsInClass()
            ->with('user')
            ->get();

            $submissionLogs = Submission::query()
            ->where('assignment_id', $assignmentId)
            ->get();
        }
        
        return view('submission.index', compact('students', 'assignment', 'class', 'submissionLogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $submission = new Submission();
        $defaultAssignment = $this->getSubmittedClass($submission);
        $defaultStudent = null;

        return view('submission.create', compact('submission', 'defaultAssignment', 'defaultStudent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubmissionRequest $request): RedirectResponse
    {
        Submission::create($request->validated());

        return Redirect::route('submissions.index')
            ->with('success', 'Submission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $submission = Submission::find($id);

        return view('submission.show', compact('submission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $submission = Submission::find($id);

        return view('submission.edit', compact('submission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubmissionRequest $request, Submission $submission): RedirectResponse
    {
        $submission->update($request->validated());

        return Redirect::route('submissions.index')
            ->with('success', 'Submission updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Submission::find($id)->delete();

        return Redirect::route('submissions.index')
            ->with('success', 'Submission deleted successfully');
    }

    private function getSubmittedClass($submission):mixed{
        $assignmentId = old('assignment_id', $submission->assignment_id);
        if(is_numeric($assignmentId)){
            $class = Assignment::query()
            ->where('id', $assignmentId)
            ->first();
            if($class){
                return ['id'=>$class->id, 'text'=>$class->title];
            }  
        }

        return null;
    }

    public function searchAssignments():JsonResponse{
        $term = request('term', '');
        $classTbl = (new ClassModel())->getTable();
        $assignmentTbl = (new Assignment())->getTable();

        $matchedAssignments = DB::table($assignmentTbl)
        ->leftJoin($classTbl, "{$assignmentTbl}.class_id", '=', "{$classTbl}.id")
        ->where(function($query) use ($classTbl, $assignmentTbl, $term){
            $query->where("{$classTbl}.class_name", "like", "%{$term}%")
            ->orWhere("{$assignmentTbl}.title", "like", "%{$term}%");
        })
        ->select("{$assignmentTbl}.title", "{$assignmentTbl}.id")
        ->limit(100)
        ->get();

        $assignments = [];
        foreach($matchedAssignments as $matchedAssignment){
            $assignments[] = ['id'=>$matchedAssignment->id, 'text'=>$matchedAssignment->title];
        }
    
        return response()->json(['results'=>$assignments, "pagination"=> [
            "more"=> false
        ]]);
    }

    public function searchStudents(Request $request):JsonResponse{
        $term = $request->get('term', '');
        $assignmentId = $request->get('assignment_id', '');
        $assignment = Assignment::find($assignmentId);
        if(!$assignment){
            $students = [];
        }else{
            $studentInClassTbl = (new StudentsInClass())->getTable();
            $userTbl = (new User())->getTable();
    
            $matchedStudents = DB::table($studentInClassTbl)
            ->leftJoin($userTbl, "{$studentInClassTbl}.user_id", '=', "{$userTbl}.id")
            ->where("{$userTbl}.name", 'like', "%{$term}%")
            ->select("{$userTbl}.id", "{$userTbl}.name")
            ->get();
    
            $students = [];
            foreach($matchedStudents as $matchedStudent){
                $students[] = ['id'=>$matchedStudent->id, 'text'=>$matchedStudent->name];
            }
        }

        return response()->json(['results'=>$students, "pagination"=> [
            "more"=> false
        ]]);
        
    }

    public function storeMultipleSubmissions(MultipleSubmissionsRequest $request){
        $grades = $request->grade;
        $feedbacks = $request->feedback;
        $assignmentId = $request->assignment_id;
        $assignment = Assignment::findOrFail($assignmentId);
        $students = $assignment->class->studentsInClass;

        if($assignment->due_date > date('Y-m-d')){
            return back()->with(['message'=>'Expired']);
        }

        foreach($feedbacks as $studentId=>$feedback){

            $grade = $grades[$studentId] ?? '';
            $files = $request->{"files_{$studentId}"};

            $student = $students
            ->where('user_id', $studentId)
            ->firstOrFail();

            $submission = Submission::query()
            ->where('student_id', $studentId)
            ->where('assignment_id', $assignmentId)
            ->first();

            if(!$submission){
                $submission = new Submission();
                $submission->student_id = $studentId;
                $submission->assignment_id = $assignmentId;
                $submission->submission_time = date('Y-m-d');
            }

            if(!is_null($files)){
                $newUploadFiles = $this->uploadSubmissionFiles($files);
                if(is_null($submission->files)){
                    $submission->files = $newUploadFiles;
                }else{
                    $submission->files += $newUploadFiles;
                }
            }

            $submission->grade = $grade;
            $submission->feedback = $feedback;
            $submission->save();
        }
        
        return back();
    }

    private function uploadSubmissionFiles($files){
        // dd($files);
        if(is_null($files)){
            return [];
        }

        $paths = [];
        foreach($files as $file){
            $paths[] = Storage::putFile('submissions', $file);
        }

        return $paths;
    }
}
