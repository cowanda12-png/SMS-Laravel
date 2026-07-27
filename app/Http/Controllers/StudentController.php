<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\Course;
use App\Models\Classes;
use App\Models\Grade;
use App\Models\Fee;
use App\Models\FeeStructure;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Students::with(['course', 'class', 'grade'])->orderBy('id', 'asc')->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::all();
        $classes = Classes::all();
        $grades = Grade::all();
        
        return view('students.create', compact('courses', 'classes', 'grades'));
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
            'registration_number' => 'nullable|string|max:50|unique:students,registration_number',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'class_id' => 'nullable|exists:classes,id',
            'grade_id' => 'nullable|exists:grades,id',
            'status' => 'nullable|in:active,inactive,pending,graduated,suspended,expelled',
            'enrollment_date' => 'nullable|date',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // Handle profile image upload if present
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('students', 'public');
            $validated['profile_image'] = $path;
        }

        // Auto-generate admission number if not provided
        if (empty($validated['admission_number'])) {
            $year = date('Y');
            $lastStudent = Students::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
            $nextNumber = $lastStudent ? intval(substr($lastStudent->admission_number, -4)) + 1 : 1;
            $validated['admission_number'] = 'ADM-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        // Set default enrollment date if not provided
        if (empty($validated['enrollment_date'])) {
            $validated['enrollment_date'] = now()->toDateString();
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
        // Load relationships with proper eager loading
        $student->load([
            'course', 
            'class', 
            'grade',
            'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'fees' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        // Get fee structures
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

        // Calculate financial data
        $student->total_fees = $feeStructures->sum('amount') ?? 0;
        
        // Get paid amount from payments
        $student->total_paid = $student->payments->where('status', 'paid')->sum('amount') ?? 0;
        
        // Get total pending from fees if payments table is not available
        if ($student->total_paid == 0 && $student->fees->isNotEmpty()) {
            $student->total_paid = $student->fees->where('status', 'paid')->sum('amount') ?? 0;
        }
        
        // Calculate balance
        $student->balance = $student->total_fees - $student->total_paid;
        $student->total_pending = $student->total_fees - $student->total_paid;
        
        // Determine payment status
        if ($student->balance <= 0 && $student->total_fees > 0) {
            $student->payment_status = 'paid';
        } elseif ($student->balance > 0 && $student->balance < $student->total_fees) {
            // Check if any payments are overdue
            $hasOverdue = $student->payments->where('status', 'overdue')->count() > 0;
            if (!$hasOverdue && $student->fees->isNotEmpty()) {
                $hasOverdue = $student->fees->where('status', 'overdue')->count() > 0;
            }
            $student->payment_status = $hasOverdue ? 'overdue' : 'partial';
        } elseif ($student->balance > 0 && $student->total_fees == 0) {
            $student->payment_status = 'pending';
        } else {
            $student->payment_status = 'pending';
        }

        // Counts for quick stats
        $student->courses_count = $student->course ? 1 : 0;
        $student->payments_count = $student->payments->count();
        if ($student->payments_count == 0 && $student->fees->isNotEmpty()) {
            $student->payments_count = $student->fees->count();
        }
        $student->overdue_count = $student->payments->where('status', 'overdue')->count();
        if ($student->overdue_count == 0 && $student->fees->isNotEmpty()) {
            $student->overdue_count = $student->fees->where('status', 'overdue')->count();
        }
        $student->exams_count = $student->exams_count ?? 0;

        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Students $student)
    {
        $courses = Course::all();
        $classes = Classes::all();
        $grades = Grade::all();
        
        // Load financial data for the fee summary
        $student->load(['payments', 'feeStructures']);
        
        // Calculate financial data
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
        
        $student->total_fees = $feeStructures->sum('amount') ?? 0;
        $student->total_paid = $student->payments->where('status', 'paid')->sum('amount') ?? 0;
        $student->total_pending = $student->total_fees - $student->total_paid;
        
        return view('students.edit', compact('student', 'courses', 'classes', 'grades'));
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
            'registration_number' => 'nullable|string|max:50|unique:students,registration_number,' . $student->id,
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'class_id' => 'nullable|exists:classes,id',
            'grade_id' => 'nullable|exists:grades,id',
            'status' => 'nullable|in:active,inactive,pending,graduated,suspended,expelled',
            'enrollment_date' => 'nullable|date',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // Handle profile image upload if present
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($student->profile_image) {
                Storage::disk('public')->delete($student->profile_image);
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

        // Check if student has related fees
        if ($student->fees()->count() > 0) {
            return redirect()->route('students.index')
                ->with('error', 'Cannot delete student with existing fee records. Delete the fees first.');
        }

        $student->delete();

        return redirect()->route('students.index')
                         ->with('success', 'Student deleted successfully!');
    }

    /**
     * Search for students - Supports both 'query' and 'search' parameters
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query', $request->input('search', ''));
            $query = trim($query);
            
            if (strlen($query) < 1) {
                return response()->json([]);
            }
            
            $students = Students::with(['course', 'class', 'grade'])
                ->where(function($q) use ($query) {
                    $q->where('first_name', 'LIKE', "%{$query}%")
                      ->orWhere('last_name', 'LIKE', "%{$query}%")
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$query}%")
                      ->orWhere('admission_number', 'LIKE', "%{$query}%")
                      ->orWhere('registration_number', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('phone', 'LIKE', "%{$query}%")
                      ->orWhere('id', '=', is_numeric($query) ? $query : 0);
                })
                ->limit(20)
                ->get();
            
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
                    'class_name' => $student->class->name ?? 'N/A',
                    'class_id' => $student->class_id,
                    'grade_name' => $student->grade->name ?? 'N/A',
                    'grade_id' => $student->grade_id,
                    'status' => $student->status ?? 'active',
                ];
            });
            
            return response()->json($formattedStudents);
            
        } catch (\Exception $e) {
            Log::error('Student search error: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Get student details by ID (API)
     */
    public function details($id)
    {
        try {
            $student = Students::with(['course', 'class', 'grade'])->find($id);
            
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
                    'class_name' => $student->class->name ?? 'N/A',
                    'class_id' => $student->class_id,
                    'grade_name' => $student->grade->name ?? 'N/A',
                    'grade_id' => $student->grade_id,
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
            
            $student = Students::with(['course', 'class', 'grade'])->find($id);
            
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
                'class' => $student->class->name ?? 'N/A',
                'class_id' => $student->class_id,
                'grade' => $student->grade->name ?? 'N/A',
                'grade_id' => $student->grade_id,
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
        $recentStudents = Students::with(['course', 'class', 'grade'])
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
            $students = Students::with(['course', 'class', 'grade'])->get();
            
            $filename = 'students_export_' . date('Y-m-d') . '.csv';
            $handle = fopen('php://temp', 'w');
            
            // Add headers
            fputcsv($handle, [
                'ID', 
                'Admission Number', 
                'Registration Number',
                'First Name', 
                'Last Name', 
                'Email', 
                'Phone', 
                'Alternate Phone',
                'Course', 
                'Class',
                'Grade',
                'Status', 
                'Enrollment Date',
                'Created At'
            ]);
            
            // Add data
            foreach ($students as $student) {
                fputcsv($handle, [
                    $student->id,
                    $student->admission_number ?? 'N/A',
                    $student->registration_number ?? 'N/A',
                    $student->first_name ?? '',
                    $student->last_name ?? '',
                    $student->email ?? '',
                    $student->phone ?? '',
                    $student->alternate_phone ?? '',
                    $student->course->course_name ?? $student->course->name ?? 'N/A',
                    $student->class->name ?? 'N/A',
                    $student->grade->name ?? 'N/A',
                    $student->status ?? 'active',
                    $student->enrollment_date ? date('Y-m-d', strtotime($student->enrollment_date)) : 'N/A',
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
        $students = Students::onlyTrashed()->with(['course', 'class', 'grade'])->get();
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
                Storage::disk('public')->delete($student->profile_image);
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
            $students = Students::with(['course', 'class', 'grade'])->get();
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
            $student = Students::with(['course', 'class', 'grade'])->findOrFail($id);
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
            $query = Students::with(['course', 'class', 'grade']);
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%")
                      ->orWhere('admission_number', 'LIKE', "%{$search}%")
                      ->orWhere('registration_number', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }
            
            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }
            
            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }
            
            if ($request->filled('grade_id')) {
                $query->where('grade_id', $request->grade_id);
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
            $student = Students::with(['payments', 'fees', 'feeStructures'])->findOrFail($id);
            
            // Get fee structures
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
            
            $totalFees = $feeStructures->sum('amount') ?? 0;
            $totalPaid = $student->payments->where('status', 'paid')->sum('amount') ?? 0;
            
            // If no payments, check fees table
            if ($totalPaid == 0 && $student->fees->isNotEmpty()) {
                $totalPaid = $student->fees->where('status', 'paid')->sum('amount') ?? 0;
            }
            
            $balance = $totalFees - $totalPaid;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_fees' => $totalFees,
                    'total_paid' => $totalPaid,
                    'balance' => $balance,
                    'payment_status' => $balance <= 0 ? 'paid' : ($balance > 0 ? 'pending' : 'overdue'),
                    'payments_count' => $student->payments->count(),
                    'fee_structures_count' => $feeStructures->count(),
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