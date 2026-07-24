<?php
// app/Http/Controllers/CourseController.php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('students')->orderBy('id', 'asc')->paginate(10);
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'code' => 'required|string|unique:courses|max:50',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:1|max:6',
            'status' => 'nullable|in:active,inactive'
        ]);

        Course::create($validated);

        return redirect()->route('courses.index')
                         ->with('success', 'Course created successfully!');
    }

    public function show($id)
    {
        $course = Course::with('students')->findOrFail($id);
        return view('courses.show', compact('course'));
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code,' . $id,
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:1|max:6',
            'status' => 'nullable|in:active,inactive'
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
                         ->with('success', 'Course updated successfully!');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        
        if ($course->students()->count() > 0) {
            return redirect()->route('courses.index')
                             ->with('error', 'Cannot delete course with enrolled students!');
        }

        $course->delete();

        return redirect()->route('courses.index')
                         ->with('success', 'Course deleted successfully!');
    }
}