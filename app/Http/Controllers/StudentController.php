<?php

namespace App\Http\Controllers;

use App\Models\Students;  // Changed from Student to Students
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is authenticated (Breeze handles this via middleware)
        $students = Students::with('course')->orderBy('id', 'asc')->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::all();
        return view('students.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'admission_number' => 'nullable|string|max:50|unique:students,admission_number',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'course_id' => 'nullable|exists:courses,id',
            'status' => 'nullable|in:active,inactive,pending,graduated',
            'address' => 'nullable|string'
        ]);

        Students::create($validated);  // Changed from Student to Students

        return redirect()->route('students.index')
                         ->with('success', 'Student created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Students $student)  // Changed from Student to Students
    {
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Students $student)  // Changed from Student to Students
    {
        $courses = Course::all();
        return view('students.edit', compact('student', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Students $student)  // Changed from Student to Students
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'admission_number' => 'nullable|string|max:50|unique:students,admission_number,' . $student->id,
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'course_id' => 'nullable|exists:courses,id',
            'status' => 'nullable|in:active,inactive,pending,graduated',
            'address' => 'nullable|string'
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
                         ->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Students $student)  // Changed from Student to Students
    {
        $student->delete();

        return redirect()->route('students.index')
                         ->with('success', 'Student deleted successfully!');
    }
}