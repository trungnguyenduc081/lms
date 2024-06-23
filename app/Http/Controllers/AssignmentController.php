<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AssignmentRequest;
use App\Models\ClassModel;
use App\Models\Submission;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $assignments = Assignment::paginate();

        return view('assignment.index', compact('assignments'))
            ->with('i', ($request->input('page', 1) - 1) * $assignments->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $assignment = new Assignment();
        $defaultClass = $this->getSubmittedClass($assignment);

        return view('assignment.create', compact('assignment', 'defaultClass'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignmentRequest $request): RedirectResponse
    {
        Assignment::create($request->validated());

        return Redirect::route('assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $assignment = Assignment::find($id);

        return view('assignment.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $assignment = Assignment::find($id);
        $defaultClass = $this->getSubmittedClass($assignment);

        return view('assignment.edit', compact('assignment', 'defaultClass'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $assignment->update($request->validated());

        return Redirect::route('assignments.index')
            ->with('success', 'Assignment updated successfully');
    }

    public function destroy($id): RedirectResponse
    {

        $assignment = Assignment::findOrFail($id);

        $submission = Submission::query()
        ->where('assignment_id', $id)
        ->count();

        if($submission > 0){
            return back()->with('error-message', 'Can not remove assignment "'.$assignment->title.'" since that has some submissions');
        }

        Assignment::find($id)->delete();

        return Redirect::route('assignments.index')
            ->with('success', 'Assignment deleted successfully');
    }

    private function getSubmittedClass($assignment):mixed{
        $classId = old('class_id', $assignment->class_id);
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
}
