<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== STUDENT STATISTICS =====
        $totalStudents = Students::count();
        
        // Student status counts
        $activeStudents = 0;
        $inactiveStudents = 0;
        $pendingStudents = 0;
        $graduatedStudents = 0;
        
        if (Schema::hasColumn('students', 'status')) {
            $activeStudents = Students::where('status', 'active')->count() ?? 0;
            $inactiveStudents = Students::where('status', 'inactive')->count() ?? 0;
            $pendingStudents = Students::where('status', 'pending')->count() ?? 0;
            $graduatedStudents = Students::where('status', 'graduated')->count() ?? 0;
        }
        
        // Student growth
        $lastMonthStudents = Students::whereMonth('created_at', Carbon::now()->subMonth()->month)
                                     ->whereYear('created_at', Carbon::now()->subMonth()->year)
                                     ->count() ?? 0;
        $studentGrowth = $lastMonthStudents > 0 ? round((($totalStudents - $lastMonthStudents) / $lastMonthStudents) * 100, 1) : 0;
        
        // ===== COURSE STATISTICS =====
        $totalCourses = Course::count();
        
        $lastMonthCourses = Course::whereMonth('created_at', Carbon::now()->subMonth()->month)
                                  ->whereYear('created_at', Carbon::now()->subMonth()->year)
                                  ->count() ?? 0;
        $courseGrowth = $lastMonthCourses > 0 ? round((($totalCourses - $lastMonthCourses) / $lastMonthCourses) * 100, 1) : 0;
        
        $mostPopularCourse = Course::withCount('students')
                                  ->orderBy('students_count', 'desc')
                                  ->first();
        
        // ===== FEE STATISTICS =====
        $totalCollected = Fee::sum('amount') ?? 0;
        $totalRevenue = $totalCollected;
        
        // Fee status breakdown
        $paidFees = 0;
        $pendingFees = 0;
        $overdueFees = 0;
        $paidCount = 0;
        $pendingCount = 0;
        $overdueCount = 0;
        
        if (Schema::hasColumn('fees', 'status')) {
            $paidFees = Fee::where('status', 'paid')->sum('amount') ?? 0;
            $pendingFees = Fee::where('status', 'pending')->sum('amount') ?? 0;
            $overdueFees = Fee::where('status', 'overdue')->sum('amount') ?? 0;
            $paidCount = Fee::where('status', 'paid')->count() ?? 0;
            $pendingCount = Fee::where('status', 'pending')->count() ?? 0;
            $overdueCount = Fee::where('status', 'overdue')->count() ?? 0;
        }
        
        $paidFeesTotal = $paidFees;
        $pendingFeesTotal = $pendingFees;
        $overdueFeesTotal = $overdueFees;
        
        // Revenue growth
        $lastMonthRevenue = Fee::whereMonth('payment_date', Carbon::now()->subMonth()->month)
                               ->whereYear('payment_date', Carbon::now()->subMonth()->year)
                               ->sum('amount') ?? 0;
        $revenueGrowth = $lastMonthRevenue > 0 ? round((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;
        
        // Pending growth
        $lastMonthPending = Fee::whereMonth('payment_date', Carbon::now()->subMonth()->month)
                               ->whereYear('payment_date', Carbon::now()->subMonth()->year)
                               ->where('status', 'pending')
                               ->sum('amount') ?? 0;
        $pendingGrowth = $lastMonthPending > 0 ? round((($pendingFees - $lastMonthPending) / $lastMonthPending) * 100, 1) : 0;
        
        // Calculate percentages
        $totalFeeAmount = $paidFees + $pendingFees + $overdueFees;
        $paidPercentage = $totalFeeAmount > 0 ? round(($paidFees / $totalFeeAmount) * 100, 1) : 0;
        $pendingPercentage = $totalFeeAmount > 0 ? round(($pendingFees / $totalFeeAmount) * 100, 1) : 0;
        $overduePercentage = $totalFeeAmount > 0 ? round(($overdueFees / $totalFeeAmount) * 100, 1) : 0;
        $collectionRate = $totalFeeAmount > 0 ? round(($paidFees / $totalFeeAmount) * 100, 1) : 0;
        
        // ===== STUDENTS WITH FEES =====
        $studentsWithFees = Students::with(['fees' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->has('fees')->limit(8)->get();
        
        // ===== RECENT RECORDS =====
        $recentStudents = Students::with('course')
                                  ->latest()
                                  ->take(5)
                                  ->get();
        
        $recentPayments = Fee::with('student')
                             ->latest()
                             ->take(5)
                             ->get();
        
        // ===== TODAY'S COLLECTIONS =====
        $todayCollections = 0;
        $thisMonthCollections = 0;
        $lastMonthCollections = 0;
        
        if (Schema::hasColumn('fees', 'payment_date')) {
            $todayCollections = Fee::whereDate('payment_date', Carbon::today())
                                   ->sum('amount') ?? 0;
            
            $thisMonthCollections = Fee::whereMonth('payment_date', Carbon::now()->month)
                                       ->whereYear('payment_date', Carbon::now()->year)
                                       ->sum('amount') ?? 0;
            
            $lastMonthCollections = Fee::whereMonth('payment_date', Carbon::now()->subMonth()->month)
                                       ->whereYear('payment_date', Carbon::now()->subMonth()->year)
                                       ->sum('amount') ?? 0;
        }
        
        // ===== ADDITIONAL STATS =====
        $totalTransactions = Fee::count();
        $averageFee = Fee::avg('amount') ?? 0;

        // ===== EXAM STATISTICS =====
        $upcomingExams = 0;
        $completedExams = 0;
        $ongoingExams = 0;
        $nextExamDate = 'N/A';
        $recentExams = collect([]);

        // Check if Exam model exists and table has data
        if (class_exists('App\Models\Exam')) {
            try {
                // Check if exams table exists
                if (Schema::hasTable('exams')) {
                    // Upcoming exams
                    if (Schema::hasColumn('exams', 'exam_date') && Schema::hasColumn('exams', 'status')) {
                        $upcomingExams = Exam::where('exam_date', '>', Carbon::now())
                                            ->where('status', 'upcoming')
                                            ->count() ?? 0;
                        
                        $completedExams = Exam::where('exam_date', '<', Carbon::now())
                                             ->where('status', 'completed')
                                             ->count() ?? 0;
                        
                        $ongoingExams = Exam::where('status', 'ongoing')->count() ?? 0;
                        
                        // Get next exam date
                        $nextExam = Exam::where('exam_date', '>', Carbon::now())
                                       ->orderBy('exam_date', 'asc')
                                       ->first();
                        $nextExamDate = $nextExam ? Carbon::parse($nextExam->exam_date)->format('M d, Y') : 'N/A';
                        
                        // Get recent exams for display
                        $recentExams = Exam::orderBy('exam_date', 'desc')
                                          ->limit(3)
                                          ->get();
                    }
                }
            } catch (\Exception $e) {
                // If exam table doesn't exist or other errors, use default values
                $upcomingExams = 0;
                $completedExams = 0;
                $ongoingExams = 0;
                $nextExamDate = 'N/A';
                $recentExams = collect([]);
            }
        }

        // ===== REPORT STATISTICS =====
        $reportStats = [
            'student_reports' => Students::count(),
            'fee_reports' => Fee::count(),
            'exam_reports' => Exam::count() ?? 0,
            'performance_reports' => 0,
            'attendance_reports' => 0
        ];

        // ===== CHART DATA (Prepared for JavaScript) =====
        // Daily enrollment data (last 7 days)
        $dailyEnrollment = [];
        $dailyLabels = [];
        $dailyTotal = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyLabels[] = $date->format('D');
            $dailyEnrollment[] = Students::whereDate('created_at', $date)->count() ?? 0;
        }

        // Calculate running total for daily
        $runningTotal = 0;
        foreach ($dailyEnrollment as $count) {
            $runningTotal += $count;
            $dailyTotal[] = $runningTotal;
        }

        // Yearly enrollment data (last 7 years)
        $yearlyEnrollment = [];
        $yearlyLabels = [];
        $yearlyTotal = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $yearlyLabels[] = $year;
            $yearlyEnrollment[] = Students::whereYear('created_at', $year)->count() ?? 0;
        }

        // Calculate running total for yearly
        $runningTotal = 0;
        foreach ($yearlyEnrollment as $count) {
            $runningTotal += $count;
            $yearlyTotal[] = $runningTotal;
        }

        // Daily fee collection data (last 7 days)
        $dailyCollected = [];
        $dailyPending = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            if (Schema::hasColumn('fees', 'status')) {
                $dailyCollected[] = Fee::whereDate('created_at', $date)
                                      ->where('status', 'paid')
                                      ->sum('amount') ?? 0;
                $dailyPending[] = Fee::whereDate('created_at', $date)
                                    ->where('status', 'pending')
                                    ->sum('amount') ?? 0;
            } else {
                $dailyCollected[] = Fee::whereDate('created_at', $date)->sum('amount') ?? 0;
                $dailyPending[] = 0;
            }
        }

        // Yearly fee collection data (last 7 years)
        $yearlyCollected = [];
        $yearlyPending = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            
            if (Schema::hasColumn('fees', 'status')) {
                $yearlyCollected[] = Fee::whereYear('created_at', $year)
                                       ->where('status', 'paid')
                                       ->sum('amount') ?? 0;
                $yearlyPending[] = Fee::whereYear('created_at', $year)
                                     ->where('status', 'pending')
                                     ->sum('amount') ?? 0;
            } else {
                $yearlyCollected[] = Fee::whereYear('created_at', $year)->sum('amount') ?? 0;
                $yearlyPending[] = 0;
            }
        }
        
        // ===== RETURN VIEW WITH ALL VARIABLES =====
        return view('dashboard', compact(
            // Student stats
            'totalStudents',
            'activeStudents',
            'inactiveStudents',
            'pendingStudents',
            'graduatedStudents',
            'studentGrowth',
            
            // Course stats
            'totalCourses',
            'courseGrowth',
            'mostPopularCourse',
            
            // Fee stats
            'totalRevenue',
            'totalCollected',
            'paidFees',
            'pendingFees',
            'overdueFees',
            'paidCount',
            'pendingCount',
            'overdueCount',
            'paidFeesTotal',
            'pendingFeesTotal',
            'overdueFeesTotal',
            'revenueGrowth',
            'pendingGrowth',
            'paidPercentage',
            'pendingPercentage',
            'overduePercentage',
            'collectionRate',
            
            // Collections
            'todayCollections',
            'thisMonthCollections',
            'lastMonthCollections',
            
            // Students with fees
            'studentsWithFees',
            
            // Recent records
            'recentStudents',
            'recentPayments',
            
            // Additional stats
            'totalTransactions',
            'averageFee',
            
            // Exam stats
            'upcomingExams',
            'completedExams',
            'ongoingExams',
            'nextExamDate',
            'recentExams',
            
            // Report stats
            'reportStats',
            
            // Chart data - MATCH THE VARIABLE NAMES USED IN THE VIEW
            'dailyLabels',
            'dailyEnrollment',
            'dailyTotal',
            'dailyCollected',
            'dailyPending',
            'yearlyLabels',
            'yearlyEnrollment',
            'yearlyTotal',
            'yearlyCollected',
            'yearlyPending'
        ));
    }
}