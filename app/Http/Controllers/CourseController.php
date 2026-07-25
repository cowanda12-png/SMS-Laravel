<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index()
    {
        $courses = Course::withCount('students')
                         ->orderBy('course_name')
                         ->paginate(15);
        
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:0|max:60',
            'status' => 'required|in:active,inactive,pending',
        ]);

        $course = Course::create($validated);

        return redirect()->route('courses.index')
                         ->with('success', "Course '{$course->course_name}' created successfully!");
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $students = $course->students()->with('fees')->get();
        
        $totalFees = $students->sum(function($student) {
            return $student->fees->sum('amount') ?? 0;
        });
        $totalPaid = $students->sum(function($student) {
            return $student->fees->where('status', 'paid')->sum('amount') ?? 0;
        });
        
        return view('courses.show', compact('course', 'students', 'totalFees', 'totalPaid'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('courses')->ignore($course->id)],
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:0|max:60',
            'status' => 'required|in:active,inactive,pending',
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
                         ->with('success', "Course '{$course->course_name}' updated successfully!");
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        // Check if course has students
        if ($course->students()->count() > 0) {
            return redirect()->route('courses.index')
                             ->with('error', "Cannot delete '{$course->course_name}' because it has enrolled students.");
        }
        
        $courseName = $course->course_name;
        $course->delete();

        return redirect()->route('courses.index')
                         ->with('success', "Course '{$courseName}' deleted successfully!");
    }

    /**
     * Display students for a specific course.
     */
    public function students(Course $course)
    {
        $students = $course->students()->with('fees')->get();
        
        // Calculate fee statistics for each student
        $students->each(function($student) {
            $student->total_fees = $student->fees->sum('amount') ?? 0;
            $student->total_paid = $student->fees->where('status', 'paid')->sum('amount') ?? 0;
            $student->total_pending = $student->fees->where('status', 'pending')->sum('amount') ?? 0;
            $student->total_overdue = $student->fees->where('status', 'overdue')->sum('amount') ?? 0;
            $student->outstanding_balance = $student->total_fees - $student->total_paid;
            $student->payment_percentage = $student->total_fees > 0 
                ? round(($student->total_paid / $student->total_fees) * 100, 1) 
                : 0;
        });
        
        // Get course statistics
        $totalStudents = $course->students()->count();
        $totalFees = $course->students->sum(function($student) {
            return $student->fees->sum('amount') ?? 0;
        });
        $totalPaid = $course->students->sum(function($student) {
            return $student->fees->where('status', 'paid')->sum('amount') ?? 0;
        });
        $totalPending = $course->students->sum(function($student) {
            return $student->fees->where('status', 'pending')->sum('amount') ?? 0;
        });
        $totalOverdue = $course->students->sum(function($student) {
            return $student->fees->where('status', 'overdue')->sum('amount') ?? 0;
        });
        $collectionRate = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 1) : 0;
        
        $stats = [
            'total_students' => $totalStudents,
            'total_fees' => $totalFees,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'total_overdue' => $totalOverdue,
            'collection_rate' => $collectionRate,
        ];
        
        return view('courses.students', compact('course', 'students', 'stats'));
    }

    /**
     * Assign a student to a course.
     */
    public function assignStudent(Request $request, Course $course)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Students::findOrFail($validated['student_id']);
        
        // Check if student is already assigned
        if ($student->course_id == $course->id) {
            return redirect()->route('courses.students', $course)
                             ->with('info', "Student '{$student->name}' is already assigned to this course.");
        }
        
        $student->update(['course_id' => $course->id]);

        return redirect()->route('courses.students', $course)
                         ->with('success', "Student '{$student->name}' assigned to '{$course->course_name}' successfully!");
    }

    /**
     * Remove a student from a course.
     */
    public function removeStudent(Request $request, Course $course)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Students::findOrFail($validated['student_id']);
        
        if ($student->course_id != $course->id) {
            return redirect()->route('courses.students', $course)
                             ->with('error', "Student '{$student->name}' is not assigned to this course.");
        }
        
        $student->update(['course_id' => null]);

        return redirect()->route('courses.students', $course)
                         ->with('success', "Student '{$student->name}' removed from '{$course->course_name}' successfully!");
    }

    /**
     * Export courses to CSV.
     */
    public function export()
    {
        $courses = Course::withCount('students')->get();
        
        $filename = "courses_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($courses) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($handle, ['ID', 'Course Name', 'Code', 'Credits', 'Students', 'Status', 'Created At']);
            
            // Data
            foreach ($courses as $course) {
                fputcsv($handle, [
                    $course->id,
                    $course->course_name,
                    $course->code,
                    $course->credits ?? 0,
                    $course->students_count,
                    $course->status,
                    $course->created_at ? $course->created_at->format('Y-m-d') : '',
                ]);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: Get students for a course.
     */
    public function apiStudents(Course $course)
    {
        $students = $course->students()->with('fees')->get()->map(function($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'admission_number' => $student->admission_number,
                'email' => $student->email,
                'phone' => $student->phone,
                'total_fees' => $student->fees->sum('amount') ?? 0,
                'total_paid' => $student->fees->where('status', 'paid')->sum('amount') ?? 0,
                'total_pending' => $student->fees->where('status', 'pending')->sum('amount') ?? 0,
                'total_overdue' => $student->fees->where('status', 'overdue')->sum('amount') ?? 0,
            ];
        });
        
        return response()->json([
            'success' => true,
            'course' => $course->name,
            'total_students' => $students->count(),
            'students' => $students,
        ]);
    }

    /**
     * API Index for courses.
     */
    public function apiIndex()
    {
        $courses = Course::withCount('students')->get();
        
        return response()->json([
            'success' => true,
            'data' => $courses,
            'total' => $courses->count(),
        ]);
    }

    /**
     * API Show for a single course.
     */
    public function apiShow(Course $course)
    {
        $course->loadCount('students');
        
        return response()->json([
            'success' => true,
            'data' => $course,
        ]);
    }
}