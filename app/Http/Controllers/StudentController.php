<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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

        Students::create($validated);

        return redirect()->route('students.index')
                         ->with('success', 'Student created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Students $student)
    {
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Students $student)
    {
        $courses = Course::all();
        return view('students.edit', compact('student', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Students $student)
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
    public function destroy(Students $student)
    {
        $student->delete();

        return redirect()->route('students.index')
                         ->with('success', 'Student deleted successfully!');
    }

    /**
     * Search for students by name, admission number, or ID (API)
     * ⭐ IMPORTANT: This method name MUST NOT conflict with route model binding
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query', '');
            
            // Return empty if query is too short
            if (strlen($query) < 2) {
                return response()->json([]);
            }
            
            // Search for students
            $students = Students::with('course')
                ->where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$query}%")
                      ->orWhere('admission_number', 'like', "%{$query}%")
                      ->orWhere('id', $query);
                })
                ->limit(20)
                ->get();
            
            // Format the response
            $formattedStudents = $students->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'admission_number' => $student->admission_number ?? 'N/A',
                    'phone' => $student->phone ?? '',
                    'course' => $student->course->course_name ?? 'N/A',
                ];
            });
            
            return response()->json($formattedStudents);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Student search error: ' . $e->getMessage());
            
            // Return empty array instead of error to prevent UI issues
            return response()->json([]);
        }
    }

    /**
     * Get a single student by ID (API)
     */
    public function getStudent(Request $request)
    {
        try {
            $id = $request->input('id');
            
            if (!$id) {
                return response()->json(['error' => 'Student ID is required'], 400);
            }
            
            $student = Students::with('course')->find($id);
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }
            
            return response()->json([
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'admission_number' => $student->admission_number ?? 'N/A',
                'phone' => $student->phone ?? '',
                'course' => $student->course->course_name ?? 'N/A',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get student error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch student'], 500);
        }
    }

    // Add any missing methods that are referenced in routes
    public function dashboard() 
    {
        return view('students.dashboard');
    }

    public function export() 
    {
        // Export logic
        return redirect()->back()->with('info', 'Export feature coming soon');
    }

    public function trash() 
    {
        $students = Students::onlyTrashed()->get();
        return view('students.trash', compact('students'));
    }

    public function restore($id) 
    {
        $student = Students::onlyTrashed()->findOrFail($id);
        $student->restore();
        return redirect()->route('students.index')->with('success', 'Student restored successfully!');
    }

    public function forceDelete($id) 
    {
        $student = Students::onlyTrashed()->findOrFail($id);
        $student->forceDelete();
        return redirect()->route('students.trash')->with('success', 'Student permanently deleted!');
    }

    public function apiIndex() 
    {
        return response()->json(Students::all());
    }

    public function apiShow($id) 
    {
        return response()->json(Students::findOrFail($id));
    }
}