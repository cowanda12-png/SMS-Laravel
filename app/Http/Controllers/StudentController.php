<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\Course;
use App\Models\Fee;
use App\Models\FeeStructure;
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
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'alternate_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'enrollment_date' => 'nullable|date',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // Handle profile image upload if present
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('students', 'public');
            $validated['profile_image'] = $path;
        }

        Students::create($validated);

        return redirect()->route('students.index')
                         ->with('success', 'Student created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Students $student)
    {
        // Load relationships
        $student->load([
            'course', 
            'class', 
            'grade',
            'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'feeStructures' => function($query) {
                $query->where('status', 'active');
            }
        ]);

        // Get or calculate fee structures if not directly related
        if ($student->feeStructures->isEmpty()) {
            // Try to get fee structures based on class and grade
            $feeStructures = FeeStructure::where('status', 'active')
                ->where(function($query) use ($student) {
                    $query->where('class_id', $student->class_id)
                          ->orWhereNull('class_id');
                })
                ->where(function($query) use ($student) {
                    $query->where('grade_id', $student->grade_id)
                          ->orWhereNull('grade_id');
                })
                ->get();
            
            $student->setRelation('feeStructures', $feeStructures);
        }

        // Calculate financial data
        $student->total_fees = $student->feeStructures->sum('amount') ?? 0;
        
        // Get paid amount from payments
        $student->total_paid = $student->payments->where('status', 'paid')->sum('amount') ?? 0;
        
        // Calculate balance
        $student->balance = $student->total_fees - $student->total_paid;
        
        // Determine payment status
        if ($student->balance <= 0 && $student->total_fees > 0) {
            $student->payment_status = 'paid';
        } elseif ($student->balance > 0 && $student->balance < $student->total_fees) {
            // Check if any payments are overdue
            $hasOverdue = $student->payments->where('status', 'overdue')->count() > 0;
            $student->payment_status = $hasOverdue ? 'overdue' : 'partial';
        } elseif ($student->balance > 0 && $student->total_fees == 0) {
            $student->payment_status = 'pending';
        } else {
            $student->payment_status = 'pending';
        }

        // Counts for quick stats
        $student->courses_count = $student->course ? 1 : 0;
        $student->payments_count = $student->payments->count();
        $student->overdue_count = $student->payments->where('status', 'overdue')->count();
        $student->exams_count = $student->exams_count ?? 0;

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
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'alternate_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'enrollment_date' => 'nullable|date',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // Handle profile image upload if present
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($student->profile_image) {
                \Storage::disk('public')->delete($student->profile_image);
            }
            $path = $request->file('profile_image')->store('students', 'public');
            $validated['profile_image'] = $path;
        }

        $student->update($validated);

        return redirect()->route('students.index')
                         ->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Students $student)
    {
        // Check if student has related payments before deleting
        if ($student->payments()->count() > 0) {
            return redirect()->route('students.index')
                ->with('error', 'Cannot delete student with existing payment records. Archive them first.');
        }

        $student->delete();

        return redirect()->route('students.index')
                         ->with('success', 'Student deleted successfully!');
    }

    /**
     * Search for students - Supports both 'query' and 'search' parameters
     * Used for AJAX search in fee creation and other forms
     */
    public function search(Request $request)
    {
        try {
            // Accept both 'query' and 'search' parameters
            $query = $request->input('query', $request->input('search', ''));
            
            // Trim the query
            $query = trim($query);
            
            // Return empty if query is too short
            if (strlen($query) < 1) {
                return response()->json([]);
            }
            
            // Search for students using multiple fields
            $students = Students::with('course')
                ->where(function($q) use ($query) {
                    $q->where('first_name', 'LIKE', "%{$query}%")
                      ->orWhere('last_name', 'LIKE', "%{$query}%")
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$query}%")
                      ->orWhere('admission_number', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('phone', 'LIKE', "%{$query}%")
                      ->orWhere('id', '=', is_numeric($query) ? $query : 0);
                })
                ->limit(20)
                ->get();
            
            // Format the response with all necessary fields
            $formattedStudents = $students->map(function($student) {
                return [
                    'id' => $student->id,
                    'first_name' => $student->first_name ?? '',
                    'last_name' => $student->last_name ?? '',
                    'full_name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'admission_number' => $student->admission_number ?? 'N/A',
                    'phone' => $student->phone ?? '',
                    'email' => $student->email ?? '',
                    'address' => $student->address ?? '',
                    'course_name' => $student->course->course_name ?? $student->course->name ?? 'N/A',
                    'course_id' => $student->course_id,
                    'status' => $student->status ?? 'active',
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
     * Get student details by ID (API)
     * Used for fetching student details after selection
     */
    public function details($id)
    {
        try {
            $student = Students::with('course')->find($id);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'first_name' => $student->first_name ?? '',
                    'last_name' => $student->last_name ?? '',
                    'full_name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'admission_number' => $student->admission_number ?? 'N/A',
                    'email' => $student->email ?? '',
                    'phone' => $student->phone ?? '',
                    'address' => $student->address ?? '',
                    'course_name' => $student->course->course_name ?? $student->course->name ?? 'N/A',
                    'course_id' => $student->course_id,
                    'status' => $student->status ?? 'active',
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Student details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student details'
            ], 500);
        }
    }

    /**
     * Get a single student by ID (API) - Legacy method
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
                'first_name' => $student->first_name ?? '',
                'last_name' => $student->last_name ?? '',
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'admission_number' => $student->admission_number ?? 'N/A',
                'phone' => $student->phone ?? '',
                'email' => $student->email ?? '',
                'course' => $student->course->course_name ?? 'N/A',
                'course_id' => $student->course_id,
                'status' => $student->status ?? 'active',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get student error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch student'], 500);
        }
    }

    /**
     * Student Dashboard
     */
    public function dashboard() 
    {
        $totalStudents = Students::count();
        $activeStudents = Students::where('status', 'active')->count();
        $recentStudents = Students::with('course')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('students.dashboard', compact('totalStudents', 'activeStudents', 'recentStudents'));
    }

    /**
     * Export students data
     */
    public function export() 
    {
        try {
            $students = Students::with('course')->get();
            
            // Create CSV file
            $filename = 'students_export_' . date('Y-m-d') . '.csv';
            $handle = fopen('php://temp', 'w');
            
            // Add headers
            fputcsv($handle, [
                'ID', 
                'Admission Number', 
                'First Name', 
                'Last Name', 
                'Email', 
                'Phone', 
                'Course', 
                'Status', 
                'Created At'
            ]);
            
            // Add data
            foreach ($students as $student) {
                fputcsv($handle, [
                    $student->id,
                    $student->admission_number ?? 'N/A',
                    $student->first_name ?? '',
                    $student->last_name ?? '',
                    $student->email ?? '',
                    $student->phone ?? '',
                    $student->course->course_name ?? $student->course->name ?? 'N/A',
                    $student->status ?? 'active',
                    $student->created_at ? $student->created_at->format('Y-m-d H:i') : 'N/A',
                ]);
            }
            
            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);
            
            return response($csvContent)
                ->withHeaders([
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename={$filename}",
                ]);
                
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export students');
        }
    }

    /**
     * Show trashed (soft deleted) students
     */
    public function trash() 
    {
        $students = Students::onlyTrashed()->with('course')->get();
        return view('students.trash', compact('students'));
    }

    /**
     * Restore a soft deleted student
     */
    public function restore($id) 
    {
        try {
            $student = Students::onlyTrashed()->findOrFail($id);
            $student->restore();
            return redirect()->route('students.index')
                ->with('success', 'Student restored successfully!');
        } catch (\Exception $e) {
            Log::error('Restore error: ' . $e->getMessage());
            return redirect()->route('students.trash')
                ->with('error', 'Failed to restore student');
        }
    }

    /**
     * Permanently delete a student
     */
    public function forceDelete($id) 
    {
        try {
            $student = Students::onlyTrashed()->findOrFail($id);
            
            // Delete profile image if exists
            if ($student->profile_image) {
                \Storage::disk('public')->delete($student->profile_image);
            }
            
            $student->forceDelete();
            return redirect()->route('students.trash')
                ->with('success', 'Student permanently deleted!');
        } catch (\Exception $e) {
            Log::error('Force delete error: ' . $e->getMessage());
            return redirect()->route('students.trash')
                ->with('error', 'Failed to delete student');
        }
    }

    /**
     * API: Get all students
     */
    public function apiIndex() 
    {
        try {
            $students = Students::with('course')->get();
            return response()->json([
                'success' => true,
                'data' => $students
            ]);
        } catch (\Exception $e) {
            Log::error('API Index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch students'
            ], 500);
        }
    }

    /**
     * API: Get a single student
     */
    public function apiShow($id) 
    {
        try {
            $student = Students::with('course')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $student
            ]);
        } catch (\Exception $e) {
            Log::error('API Show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }
    }

    /**
     * Filter students by various criteria
     */
    public function filter(Request $request)
    {
        try {
            $query = Students::with('course');
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%")
                      ->orWhere('admission_number', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }
            
            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $students = $query->orderBy('first_name')->paginate(10);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $students
                ]);
            }
            
            return view('students.index', compact('students'));
            
        } catch (\Exception $e) {
            Log::error('Filter error: ' . $e->getMessage());
            return redirect()->route('students.index')
                ->with('error', 'Failed to filter students');
        }
    }

    /**
     * Get student financial summary
     */
    public function financialSummary($id)
    {
        try {
            $student = Students::with(['payments', 'feeStructures'])->findOrFail($id);
            
            $totalFees = $student->feeStructures->sum('amount') ?? 0;
            $totalPaid = $student->payments->where('status', 'paid')->sum('amount') ?? 0;
            $balance = $totalFees - $totalPaid;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_fees' => $totalFees,
                    'total_paid' => $totalPaid,
                    'balance' => $balance,
                    'payment_status' => $balance <= 0 ? 'paid' : ($balance > 0 ? 'pending' : 'overdue'),
                    'payments_count' => $student->payments->count(),
                    'fee_structures_count' => $student->feeStructures->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Financial summary error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch financial summary'
            ], 500);
        }
    }
}