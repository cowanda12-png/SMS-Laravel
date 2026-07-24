<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\Course;
use App\Models\Fee;
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
            'averageFee'
        ));
    }
}