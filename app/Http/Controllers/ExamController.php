<?php
// app/Http/Controllers/ExamController.php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Course;
use App\Models\Classes;
use App\Models\ExamResult;
use App\Models\Students;
use App\Models\PerformanceTracking;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['course', 'class']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $exams = $query->orderBy('exam_date', 'desc')->paginate(15);
        $courses = Course::all();
        $classes = Classes::all();
        $statuses = ['draft', 'published', 'completed', 'graded'];
        $examTypes = ['quiz', 'assignment', 'midterm', 'final', 'practical', 'project'];
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $academicYears = Exam::distinct('academic_year')->pluck('academic_year')->filter()->values();

        return view('exams.index', compact('exams', 'courses', 'classes', 'statuses', 'examTypes', 'terms', 'academicYears'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        $courses = Course::all();
        $classes = Classes::all();
        $examTypes = ['quiz', 'assignment', 'midterm', 'final', 'practical', 'project'];
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $academicYears = [
            (date('Y') - 1) . '/' . date('Y'),
            date('Y') . '/' . (date('Y') + 1),
            (date('Y') + 1) . '/' . (date('Y') + 2)
        ];
        
        return view('exams.create', compact('courses', 'classes', 'examTypes', 'terms', 'academicYears'));
    }

    /**
     * Store a newly created exam.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:exams,code',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:classes,id',
            'type' => 'required|in:quiz,assignment,midterm,final,practical,project',
            'exam_date' => 'required|date',
            'submission_date' => 'nullable|date|after_or_equal:exam_date',
            'max_score' => 'required|numeric|min:1',
            'passing_score' => 'required|numeric|min:0|max:100',
            'weight' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:draft,published,completed,graded',
            'instructions' => 'nullable|string',
            'term' => 'required|string|in:Term 1,Term 2,Term 3',
            'academic_year' => 'required|string',
        ]);

        Exam::create($validated);

        return redirect()->route('exams.index')
            ->with('success', 'Exam created successfully!');
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        $exam->load(['course', 'class', 'results.student']);
        
        // Statistics
        $totalStudents = $exam->results()->count();
        $submitted = $exam->results()->where('status', 'submitted')->count();
        $graded = $exam->results()->where('status', 'graded')->count();
        $absent = $exam->results()->where('status', 'absent')->count();
        
        $averageScore = $exam->getAverageScore();
        $passRate = $exam->getPassRate();
        
        // Grade distribution
        $gradeDistribution = $this->getGradeDistribution($exam->id);
        
        // Top performers
        $topPerformers = $exam->results()
            ->with('student')
            ->where('status', 'graded')
            ->orderBy('percentage', 'desc')
            ->limit(10)
            ->get();

        return view('exams.show', compact(
            'exam', 
            'totalStudents', 
            'submitted', 
            'graded', 
            'absent',
            'averageScore',
            'passRate',
            'gradeDistribution',
            'topPerformers'
        ));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        $courses = Course::all();
        $classes = Classes::all();
        $examTypes = ['quiz', 'assignment', 'midterm', 'final', 'practical', 'project'];
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $academicYears = [
            (date('Y') - 1) . '/' . date('Y'),
            date('Y') . '/' . (date('Y') + 1),
            (date('Y') + 1) . '/' . (date('Y') + 2)
        ];
        
        return view('exams.edit', compact('exam', 'courses', 'classes', 'examTypes', 'terms', 'academicYears'));
    }

    /**
     * Update the specified exam.
     */
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:exams,code,' . $exam->id,
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:classes,id',
            'type' => 'required|in:quiz,assignment,midterm,final,practical,project',
            'exam_date' => 'required|date',
            'submission_date' => 'nullable|date|after_or_equal:exam_date',
            'max_score' => 'required|numeric|min:1',
            'passing_score' => 'required|numeric|min:0|max:100',
            'weight' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:draft,published,completed,graded',
            'instructions' => 'nullable|string',
            'term' => 'required|string|in:Term 1,Term 2,Term 3',
            'academic_year' => 'required|string',
        ]);

        $exam->update($validated);

        return redirect()->route('exams.index')
            ->with('success', 'Exam updated successfully!');
    }

    /**
     * Remove the specified exam.
     */
    public function destroy(Exam $exam)
    {
        try {
            $exam->delete();
            return redirect()->route('exams.index')
                ->with('success', 'Exam deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('exams.index')
                ->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * Record marks for an exam.
     */
    public function recordMarks(Request $request, Exam $exam)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'results' => 'required|array',
                'results.*.student_id' => 'required|exists:students,id',
                'results.*.score' => 'required|numeric|min:0|max:' . $exam->max_score,
                'results.*.status' => 'required|in:submitted,graded,absent',
                'results.*.feedback' => 'nullable|string',
            ]);

            DB::beginTransaction();
            try {
                foreach ($validated['results'] as $data) {
                    $result = ExamResult::where('exam_id', $exam->id)
                        ->where('student_id', $data['student_id'])
                        ->first();

                    if (!$result) {
                        $result = new ExamResult();
                        $result->exam_id = $exam->id;
                        $result->student_id = $data['student_id'];
                        $result->course_id = $exam->course_id;
                        $result->class_id = $exam->class_id;
                    }

                    $result->score = $data['score'];
                    $result->percentage = ($data['score'] / $exam->max_score) * 100;
                    $result->grade = GradeScale::getGrade($result->percentage);
                    $result->remarks = GradeScale::getRemark($result->percentage);
                    $result->status = $data['status'];
                    $result->feedback = $data['feedback'] ?? null;
                    $result->graded_at = now();
                    $result->save();

                    // Update performance tracking
                    $this->updatePerformanceTracking($result->student_id, $exam->course_id, $exam->class_id);
                }

                // Update exam status
                $exam->status = 'graded';
                $exam->save();

                DB::commit();

                return redirect()->route('exams.show', $exam->id)
                    ->with('success', 'Marks recorded successfully!');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Failed to record marks: ' . $e->getMessage())
                    ->withInput();
            }
        }

        // GET request - show form
        $students = Students::where('class_id', $exam->class_id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $existingResults = ExamResult::where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');

        return view('exams.record-marks', compact('exam', 'students', 'existingResults'));
    }

    /**
     * Generate report card for a student.
     */
    public function generateReportCard($studentId, $term, $academicYear)
    {
        try {
            $student = Students::with(['course', 'class'])->findOrFail($studentId);
            
            $results = ExamResult::where('student_id', $studentId)
                ->whereHas('exam', function($q) use ($term, $academicYear) {
                    $q->where('term', $term)
                      ->where('academic_year', $academicYear);
                })
                ->with('exam')
                ->get();

            $performance = PerformanceTracking::where('student_id', $studentId)
                ->where('term', $term)
                ->where('academic_year', $academicYear)
                ->first();

            $gradeScale = GradeScale::default()->get();

            return view('exams.report-card', compact(
                'student', 
                'results', 
                'performance', 
                'term', 
                'academicYear',
                'gradeScale'
            ));
        } catch (\Exception $e) {
            Log::error('Generate Report Card Error: ' . $e->getMessage());
            return redirect()->route('exams.performance-analysis')
                ->with('error', 'Failed to generate report card: ' . $e->getMessage());
        }
    }

    /**
     * Performance analysis dashboard.
     */
    public function performanceAnalysis(Request $request)
    {
        try {
            $studentId = $request->input('student_id');
            $courseId = $request->input('course_id');
            $term = $request->input('term');
            $academicYear = $request->input('academic_year');

            $query = PerformanceTracking::with(['student', 'course', 'class']);

            if ($studentId) {
                $query->where('student_id', $studentId);
            }

            if ($courseId) {
                $query->where('course_id', $courseId);
            }

            if ($term) {
                $query->where('term', $term);
            }

            if ($academicYear) {
                $query->where('academic_year', $academicYear);
            }

            $performanceRecords = $query->orderBy('average_score', 'desc')
                ->paginate(20);

            // Statistics with fallback values
            $stats = [
                'total_students' => PerformanceTracking::distinct('student_id')->count(),
                'average_performance' => PerformanceTracking::avg('average_score') ?? 0,
                'highest_performance' => PerformanceTracking::max('average_score') ?? 0,
                'lowest_performance' => PerformanceTracking::min('average_score') ?? 0,
            ];

            $students = Students::orderBy('first_name')->orderBy('last_name')->get();
            $courses = Course::all();
            $terms = ['Term 1', 'Term 2', 'Term 3'];
            $academicYears = ['2024/2025', '2025/2026', '2026/2027'];

            return view('exams.performance-analysis', compact(
                'performanceRecords',
                'stats',
                'students',
                'courses',
                'terms',
                'academicYears',
                'studentId',
                'courseId',
                'term',
                'academicYear'
            ));
        } catch (\Exception $e) {
            Log::error('Performance Analysis Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Return with empty data if error occurs.
            // IMPORTANT: this branch must pass the SAME set of variables as the
            // success branch above (studentId, courseId, term, academicYear included),
            // otherwise the Blade view throws an "Undefined variable" error while
            // rendering the filter form's selected values — and since that second
            // exception happens outside this try/catch, it surfaces as an
            // uncaught 500 that masks the real error logged above.
            $performanceRecords = collect();
            $stats = [
                'total_students' => 0,
                'average_performance' => 0,
                'highest_performance' => 0,
                'lowest_performance' => 0,
            ];
            $students = Students::orderBy('first_name')->orderBy('last_name')->get();
            $courses = Course::all();
            $terms = ['Term 1', 'Term 2', 'Term 3'];
            $academicYears = ['2024/2025', '2025/2026', '2026/2027'];

            // Preserve whatever filter values were submitted, defaulting to null
            // so the Blade view's old()/selected-value checks don't blow up.
            $studentId = $studentId ?? null;
            $courseId = $courseId ?? null;
            $term = $term ?? null;
            $academicYear = $academicYear ?? null;

            return view('exams.performance-analysis', compact(
                'performanceRecords',
                'stats',
                'students',
                'courses',
                'terms',
                'academicYears',
                'studentId',
                'courseId',
                'term',
                'academicYear'
            ))->with('error', 'Unable to load performance data: ' . $e->getMessage());
        }
    }

    /**
     * Get grade distribution for an exam.
     */
    private function getGradeDistribution($examId)
    {
        $results = ExamResult::where('exam_id', $examId)
            ->where('status', 'graded')
            ->get();

        $distribution = [];
        $gradeScales = GradeScale::orderBy('order')->get();

        foreach ($gradeScales as $scale) {
            $count = $results->filter(function($result) use ($scale) {
                return $result->percentage >= $scale->min_score && 
                       $result->percentage <= $scale->max_score;
            })->count();

            $distribution[$scale->grade] = $count;
        }

        return $distribution;
    }

    /**
     * Update performance tracking for a student.
     */
    private function updatePerformanceTracking($studentId, $courseId, $classId)
    {
        try {
            $term = 'Term 1';
            $academicYear = date('Y') . '/' . (date('Y') + 1);

            $tracking = PerformanceTracking::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('term', $term)
                ->where('academic_year', $academicYear)
                ->first();

            if (!$tracking) {
                $tracking = new PerformanceTracking();
                $tracking->student_id = $studentId;
                $tracking->course_id = $courseId;
                $tracking->class_id = $classId;
                $tracking->term = $term;
                $tracking->academic_year = $academicYear;
            }

            $tracking->updatePerformance();
        } catch (\Exception $e) {
            Log::error('Update Performance Tracking Error: ' . $e->getMessage());
        }
    }

    /**
     * Export exam results.
     */
    public function exportResults(Exam $exam)
    {
        try {
            $results = ExamResult::where('exam_id', $exam->id)
                ->with('student')
                ->where('status', 'graded')
                ->get();

            $filename = 'exam_results_' . $exam->code . '_' . date('Y-m-d') . '.csv';
            $handle = fopen('php://temp', 'w');

            fputcsv($handle, [
                'Student Name',
                'Admission Number',
                'Score',
                'Percentage',
                'Grade',
                'Remarks',
                'Status'
            ]);

            foreach ($results as $result) {
                fputcsv($handle, [
                    ($result->student->first_name ?? '') . ' ' . ($result->student->last_name ?? ''),
                    $result->student->admission_number ?? 'N/A',
                    $result->score,
                    number_format($result->percentage, 2) . '%',
                    $result->grade,
                    $result->remarks,
                    $result->status
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
            Log::error('Export Results Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export results');
        }
    }
}