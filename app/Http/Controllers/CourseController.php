<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $courses = Course::paginate();

        return view('course.index', compact('courses'))
            ->with('i', ($request->input('page', 1) - 1) * $courses->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $course = new Course();

        return view('course.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request): RedirectResponse
    {
        Course::create($request->validated());

        return Redirect::route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $course = Course::find($id);

        return view('course.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $course = Course::find($id);

        return view('course.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return Redirect::route('courses.index')
            ->with('success', 'Course updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Course::find($id)->delete();

        return Redirect::route('courses.index')
            ->with('success', 'Course deleted successfully');
    }
}
