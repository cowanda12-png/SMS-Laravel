<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Reports Dashboard
     */
    public function dashboard()
    {
        $totalStudents = Students::count();
        $totalRevenue = Fee::sum('amount') ?? 0;
        $outstandingBalance = Fee::where('status', 'pending')->orWhere('status', 'overdue')->sum('amount') ?? 0;
        $todayCollections = Fee::whereDate('payment_date', Carbon::today())->sum('amount') ?? 0;
        
        $recentTransactions = Fee::with('student')
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();
        
        $monthlyData = Fee::selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total')
            ->whereNotNull('payment_date')
            ->whereYear('payment_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // REMOVED: payment_method column doesn't exist
        // $paymentMethodsData = Fee::selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
        //     ->whereNotNull('payment_method')
        //     ->groupBy('payment_method')
        //     ->get();
        
        // Use status breakdown instead
        $statusBreakdown = [
            'paid' => Fee::where('status', 'paid')->sum('amount') ?? 0,
            'pending' => Fee::where('status', 'pending')->sum('amount') ?? 0,
            'overdue' => Fee::where('status', 'overdue')->sum('amount') ?? 0,
        ];
        
        $statusCounts = [
            'paid' => Fee::where('status', 'paid')->count(),
            'pending' => Fee::where('status', 'pending')->count(),
            'overdue' => Fee::where('status', 'overdue')->count(),
        ];
        
        $collectionRate = $totalRevenue > 0 
            ? round(($totalRevenue - $outstandingBalance) / $totalRevenue * 100, 1) 
            : 0;
        
        return view('reports.dashboard', compact(
            'totalStudents',
            'totalRevenue',
            'outstandingBalance',
            'todayCollections',
            'recentTransactions',
            'monthlyData',
            'statusBreakdown',
            'statusCounts',
            'collectionRate'
        ));
    }

    /**
     * REPORT 1: STUDENT STATEMENT REPORT
     */
    public function studentStatement(Request $request)
    {
        $students = Students::with(['course', 'fees'])->get();
        $selectedStudent = null;
        $studentFees = collect();
        
        if ($request->has('student_id') && $request->student_id) {
            $selectedStudent = Students::with(['course', 'fees'])->find($request->student_id);
            if ($selectedStudent) {
                $studentFees = $selectedStudent->fees()->orderBy('payment_date', 'desc')->get();
            }
        }
        
        return view('reports.student-statement', compact('students', 'selectedStudent', 'studentFees'));
    }

    /**
     * REPORT 2: FEE COLLECTION REPORT
     */
    public function feeCollection(Request $request)
    {
        $query = Fee::with('student.course');
        
        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
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
        
        $fees = $query->orderBy('payment_date', 'desc')->get();
        
        $summary = [
            'total_amount' => $fees->sum('amount'),
            'total_transactions' => $fees->count(),
            'average_payment' => $fees->count() > 0 ? $fees->avg('amount') : 0,
            'total_paid' => $fees->where('status', 'paid')->sum('amount'),
            'total_pending' => $fees->where('status', 'pending')->sum('amount'),
            'total_overdue' => $fees->where('status', 'overdue')->sum('amount'),
        ];
        
        $statuses = ['paid', 'pending', 'overdue'];
        $terms = Fee::distinct()->whereNotNull('term')->pluck('term');
        $academicYears = Fee::distinct()->whereNotNull('academic_year')->pluck('academic_year');
        
        return view('reports.fee-collection', compact('fees', 'summary', 'statuses', 'terms', 'academicYears'));
    }

    /**
     * REPORT 3: OUTSTANDING BALANCES REPORT
     */
    public function outstandingBalances(Request $request)
    {
        $students = Students::with(['course', 'fees'])->get();
        
        // Calculate outstanding balance for each student
        $outstandingStudents = $students->filter(function($student) {
            $totalFees = $student->fees->sum('amount');
            $totalPaid = $student->fees->where('status', 'paid')->sum('amount');
            $balance = $totalFees - $totalPaid;
            return $balance > 0;
        })->map(function($student) {
            $totalFees = $student->fees->sum('amount');
            $totalPaid = $student->fees->where('status', 'paid')->sum('amount');
            $student->balance = $totalFees - $totalPaid;
            $student->total_fees = $totalFees;
            $student->total_paid = $totalPaid;
            return $student;
        })->values();
        
        // Apply course filter
        if ($request->filled('course_id')) {
            $outstandingStudents = $outstandingStudents->filter(function($student) use ($request) {
                return $student->course_id == $request->course_id;
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $outstandingStudents = $outstandingStudents->filter(function($student) use ($request) {
                if ($request->status === 'critical') {
                    return $student->balance > 50000;
                } elseif ($request->status === 'high') {
                    return $student->balance > 20000 && $student->balance <= 50000;
                } elseif ($request->status === 'medium') {
                    return $student->balance > 5000 && $student->balance <= 20000;
                } elseif ($request->status === 'low') {
                    return $student->balance <= 5000;
                }
                return true;
            });
        }
        
        $courses = Course::all();
        $totalOutstanding = $outstandingStudents->sum('balance');
        
        return view('reports.outstanding-balances', compact('outstandingStudents', 'courses', 'totalOutstanding'));
    }

    /**
     * REPORT 4: COURSE REVENUE REPORT
     */
    public function courseRevenue(Request $request)
    {
        $courses = Course::with('students.fees')->get();
        
        $courseData = $courses->map(function($course) {
            $students = $course->students;
            $totalRevenue = 0;
            $paidRevenue = 0;
            $pendingRevenue = 0;
            
            foreach ($students as $student) {
                $totalRevenue += $student->fees->sum('amount');
                $paidRevenue += $student->fees->where('status', 'paid')->sum('amount');
                $pendingRevenue += $student->fees->where('status', 'pending')->sum('amount');
            }
            
            return [
                'id' => $course->id,
                'name' => $course->course_name ?? $course->name,
                'students_count' => $students->count(),
                'revenue' => $totalRevenue,
                'paid_revenue' => $paidRevenue,
                'pending_revenue' => $pendingRevenue,
                'collection_rate' => $totalRevenue > 0 ? round(($paidRevenue / $totalRevenue) * 100, 1) : 0,
            ];
        });
        
        $grandTotal = $courseData->sum('revenue');
        $grandPaid = $courseData->sum('paid_revenue');
        $grandPending = $courseData->sum('pending_revenue');
        
        return view('reports.course-revenue', compact('courseData', 'grandTotal', 'grandPaid', 'grandPending'));
    }

    /**
     * REPORT 5: DAILY COLLECTION REPORT
     */
    public function dailyCollection(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $fees = Fee::with('student.course')
            ->whereDate('payment_date', $date)
            ->orderBy('payment_date', 'desc')
            ->get();
        
        $summary = [
            'total_collections' => $fees->sum('amount'),
            'total_transactions' => $fees->count(),
            'total_paid' => $fees->where('status', 'paid')->sum('amount'),
            'total_pending' => $fees->where('status', 'pending')->sum('amount'),
            'average_payment' => $fees->count() > 0 ? $fees->avg('amount') : 0,
        ];
        
        return view('reports.daily-collection', compact('fees', 'summary', 'date'));
    }

    /**
     * REPORT 6: MONTHLY COLLECTION REPORT
     */
    public function monthlyCollection(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $fees = Fee::with('student.course')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date', 'desc')
            ->get();
        
        $summary = [
            'total_collected' => $fees->sum('amount'),
            'total_transactions' => $fees->count(),
            'highest_payment' => $fees->max('amount') ?? 0,
            'average_payment' => $fees->count() > 0 ? $fees->avg('amount') : 0,
            'total_paid' => $fees->where('status', 'paid')->sum('amount'),
            'total_pending' => $fees->where('status', 'pending')->sum('amount'),
        ];
        
        // Daily breakdown for the month
        $dailyBreakdown = $fees->groupBy(function($fee) {
            return Carbon::parse($fee->payment_date)->format('Y-m-d');
        })->map(function($group) {
            return $group->sum('amount');
        });
        
        $months = range(1, 12);
        $years = range(Carbon::now()->year - 5, Carbon::now()->year);
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        return view('reports.monthly-collection', compact(
            'fees', 
            'summary', 
            'month', 
            'year', 
            'months', 
            'years',
            'monthNames',
            'dailyBreakdown'
        ));
    }

    /**
     * REPORT 7: M-PESA TRANSACTIONS REPORT
     */
    public function mpesaTransactions(Request $request)
    {
        // Since payment_method doesn't exist, we can't filter by M-Pesa
        // This report is not available with current schema
        return redirect()->route('reports.dashboard')
            ->with('error', 'M-Pesa transactions report is not available. Please add payment_method column to fees table.');
    }

    /**
     * REPORT 8: PAYMENT METHOD ANALYSIS
     */
    public function paymentMethodAnalysis(Request $request)
    {
        // Since payment_method doesn't exist, this report is not available
        return redirect()->route('reports.dashboard')
            ->with('error', 'Payment method analysis report is not available. Please add payment_method column to fees table.');
    }

    /**
     * REPORT 9: FEE SUMMARY (Additional)
     */
    public function feeSummary()
    {
        $totalFees = Fee::sum('amount') ?? 0;
        $totalPaid = Fee::where('status', 'paid')->sum('amount') ?? 0;
        $totalPending = Fee::where('status', 'pending')->sum('amount') ?? 0;
        $totalOverdue = Fee::where('status', 'overdue')->sum('amount') ?? 0;
        
        $statusCounts = [
            'paid' => Fee::where('status', 'paid')->count(),
            'pending' => Fee::where('status', 'pending')->count(),
            'overdue' => Fee::where('status', 'overdue')->count(),
        ];
        
        $recentFees = Fee::with('student')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Term and year breakdown
        $termBreakdown = Fee::select('term', 'academic_year')
            ->selectRaw('SUM(amount) as total')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('term')
            ->groupBy('term', 'academic_year')
            ->orderBy('academic_year', 'desc')
            ->get();
        
        return view('reports.fee-summary', compact(
            'totalFees',
            'totalPaid',
            'totalPending',
            'totalOverdue',
            'statusCounts',
            'recentFees',
            'termBreakdown'
        ));
    }

    /**
     * Export reports to CSV
     */
    public function export(Request $request, $type)
    {
        $data = [];
        $filename = "report_{$type}_" . date('Y-m-d') . ".csv";
        
        switch ($type) {
            case 'fee-collection':
                $fees = Fee::with('student')->orderBy('payment_date', 'desc')->get();
                $headers = ['Student', 'Amount', 'Status', 'Term', 'Academic Year', 'Payment Date', 'Due Date'];
                $data = $fees->map(function($fee) {
                    return [
                        $fee->student->full_name ?? 'N/A',
                        $fee->amount,
                        $fee->status,
                        $fee->term ?? 'N/A',
                        $fee->academic_year ?? 'N/A',
                        $fee->payment_date,
                        $fee->due_date ?? 'N/A',
                    ];
                })->toArray();
                break;
                
            case 'outstanding':
                $students = Students::with('fees')->get();
                $headers = ['Student', 'Course', 'Total Fees', 'Paid', 'Balance'];
                $data = $students->filter(function($student) {
                    $total = $student->fees->sum('amount');
                    $paid = $student->fees->where('status', 'paid')->sum('amount');
                    return ($total - $paid) > 0;
                })->map(function($student) {
                    $total = $student->fees->sum('amount');
                    $paid = $student->fees->where('status', 'paid')->sum('amount');
                    return [
                        $student->full_name ?? 'N/A',
                        $student->course->course_name ?? 'N/A',
                        $total,
                        $paid,
                        $total - $paid,
                    ];
                })->toArray();
                break;
                
            case 'student-statement':
                if (!$request->has('student_id')) {
                    return back()->with('error', 'Please select a student');
                }
                $student = Students::with('fees')->find($request->student_id);
                if (!$student) {
                    return back()->with('error', 'Student not found');
                }
                $headers = ['Date', 'Amount', 'Status', 'Term', 'Academic Year'];
                $data = $student->fees->map(function($fee) use ($student) {
                    return [
                        $fee->payment_date,
                        $fee->amount,
                        $fee->status,
                        $fee->term ?? 'N/A',
                        $fee->academic_year ?? 'N/A',
                    ];
                })->toArray();
                $filename = "student_statement_{$student->full_name}_{$filename}";
                break;
                
            default:
                return back()->with('error', 'Invalid report type');
        }
        
        // Generate CSV
        $callback = function() use ($headers, $data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}