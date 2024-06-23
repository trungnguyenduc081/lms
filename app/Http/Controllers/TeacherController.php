<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TeacherRequest;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $teachers = Teacher::query()
        ->with('user:id,name')
        ->paginate();

        return view('teacher.index', compact('teachers'))
            ->with('i', ($request->input('page', 1) - 1) * $teachers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $teacher = new Teacher();

        return view('teacher.create', compact('teacher'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeacherRequest $request): RedirectResponse
    {
        DB::transaction(function() use ($request){
            $user = new User();
            $user->email = str()->random(100).'@';
            $user->password = Hash::make(str()->random(50));
            $user->fake_email = 1;
            $user->fake_password = 1;
            $user->name = $request->name;
            $user->save();

            $teacherAttr = $request->validated();
            $teacherAttr['user_id'] = $user->id;

            Teacher::create($teacherAttr);
        });
        

        return Redirect::route('teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $teacher = Teacher::findOrFail($id);

        return view('teacher.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $teacher = Teacher::findOrFail($id);

        return view('teacher.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        DB::transaction(function() use ($teacher, $request){
            $teacher->user->update(['name'=>$request->name]);
            $teacher->update($request->validated());
        });

        return Redirect::route('teachers.index')
            ->with('success', 'Teacher updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $teacher = Teacher::findOrFail($id);
        $userId = $teacher->user_id;

        $class = ClassModel::query()
        ->where('teacher_id', $userId)
        ->count();

        if($class > 0){
            return back()->with('error-message', 'Can not remove a teacher that is in a class');
        }

        DB::transaction(function() use ($id, $teacher){
            $teacher->delete();
            User::findOrFail($teacher->user_id)->delete();
        });

        return Redirect::route('teachers.index')
            ->with('success', 'Teacher deleted successfully');
    }

    public function searchTeacher():JsonResponse {
        $term = request('term', '');
        $userTbl = (new User())->getTable();
        $teacherTbl = (new Teacher())->getTable();
        
        $matchedTeachers = DB::table($teacherTbl)
        ->leftJoin($userTbl, "{$teacherTbl}.user_id", '=', "{$userTbl}.id")
        ->where("{$userTbl}.name", "like", "%{$term}%")
        ->select("{$userTbl}.name", "{$userTbl}.id")
        ->limit(100)
        ->get();

        $newTeachers = [];
        foreach($matchedTeachers as $user){
            $newTeachers[] = ['id'=>$user->id, 'text'=>$user->name];
        }
    
        return response()->json(['results'=>$newTeachers, "pagination"=> [
            "more"=> false
        ]]);
    }
}
