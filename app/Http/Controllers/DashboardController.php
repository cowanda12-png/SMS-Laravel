<?php

namespace App\Http\Controllers;

use App\Models\Students;  // Use Students (plural)
use App\Models\Course;
use App\Models\Fee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Add this for column checking

class DashboardController extends Controller
{
    public function index()
    {
        // ===== STUDENT STATISTICS =====
        $totalStudents = Students::count();
        
        // Student status counts - Check if status column exists
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
        
        // Active users
        $activeUsers = $activeStudents > 0 ? $activeStudents : $totalStudents;
        
        // ===== COURSE STATISTICS =====
        $totalCourses = Course::count();
        
        // Most Popular Course (with most students)
        $mostPopularCourse = Course::withCount('students')
                                  ->orderBy('students_count', 'desc')
                                  ->first();
        
        // Course enrollment distribution
        $courseDistribution = Course::withCount('students')
                                   ->orderBy('students_count', 'desc')
                                   ->limit(10)
                                   ->get();
        
        // ===== FEE STATISTICS =====
        // Total Fees Collected
        $totalFeesCollected = Fee::sum('amount') ?? 0;
        
        // Today's Collections - Check if payment_date column exists
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
        
        // Total Fee Transactions
        $totalTransactions = Fee::count();
        
        // Average Fee Amount
        $averageFee = Fee::avg('amount') ?? 0;
        
        // Payment Method Distribution
        $paymentDistribution = collect();
        if (Schema::hasColumn('fees', 'payment_method')) {
            $paymentDistribution = Fee::selectRaw('payment_method, count(*) as count, sum(amount) as total')
                                      ->whereNotNull('payment_method')
                                      ->groupBy('payment_method')
                                      ->get();
        }
        
        // Fee Type Distribution
        $feeTypeDistribution = collect();
        if (Schema::hasColumn('fees', 'fee_type')) {
            $feeTypeDistribution = Fee::selectRaw('fee_type, count(*) as count, sum(amount) as total')
                                      ->whereNotNull('fee_type')
                                      ->groupBy('fee_type')
                                      ->get();
        }
        
        // ===== FEE STATUS BREAKDOWN =====
        // Initialize with default values
        $paidFees = 0;
        $pendingFees = 0;
        $overdueFees = 0;
        $paidCount = 0;
        $pendingCount = 0;
        $overdueCount = 0;
        
        // Check if status column exists in fees table
        if (Schema::hasColumn('fees', 'status')) {
            $paidFees = Fee::where('status', 'paid')->sum('amount') ?? 0;
            $pendingFees = Fee::where('status', 'pending')->sum('amount') ?? 0;
            $overdueFees = Fee::where('status', 'overdue')->sum('amount') ?? 0;
            $paidCount = Fee::where('status', 'paid')->count() ?? 0;
            $pendingCount = Fee::where('status', 'pending')->count() ?? 0;
            $overdueCount = Fee::where('status', 'overdue')->count() ?? 0;
        } else {
            // If status column doesn't exist, treat all fees as paid
            $paidFees = Fee::sum('amount') ?? 0;
            $paidCount = Fee::count();
        }
        
        // Total Revenue
        $totalRevenue = Fee::sum('amount') ?? 0;
        
        // Calculate percentages for fee status
        $totalFeeAmount = $paidFees + $pendingFees + $overdueFees;
        $paidPercentage = $totalFeeAmount > 0 ? round(($paidFees / $totalFeeAmount) * 100, 1) : 0;
        $pendingPercentage = $totalFeeAmount > 0 ? round(($pendingFees / $totalFeeAmount) * 100, 1) : 0;
        $overduePercentage = $totalFeeAmount > 0 ? round(($overdueFees / $totalFeeAmount) * 100, 1) : 0;
        $collectionRate = $totalFeeAmount > 0 ? round(($paidFees / $totalFeeAmount) * 100, 1) : 0;
        
        // ===== RECENT RECORDS =====
        // Recent Students (latest 5)
        $recentStudents = Students::with('course')
                                  ->latest()
                                  ->take(5)
                                  ->get();
        
        // Recent Fee Payments
        $recentPayments = collect();
        if (Schema::hasColumn('fees', 'payment_date')) {
            $recentPayments = Fee::with('student')
                                 ->orderBy('payment_date', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->take(5)
                                 ->get();
        } else {
            $recentPayments = Fee::with('student')
                                 ->latest()
                                 ->take(5)
                                 ->get();
        }
        
        // ===== TOP PERFORMERS =====
        // Top Paying Students (by total fees)
        $topPayingStudents = Students::withSum('fees', 'amount')
                                    ->having('fees_sum_amount', '>', 0)
                                    ->orderBy('fees_sum_amount', 'desc')
                                    ->take(5)
                                    ->get();
        
        // Students with most transactions
        $mostActivePayers = Students::withCount('fees')
                                    ->having('fees_count', '>', 0)
                                    ->orderBy('fees_count', 'desc')
                                    ->take(5)
                                    ->get();
        
        // ===== WEEKLY/MONTHLY TRENDS =====
        $weeklyTrend = collect();
        $monthlyTrend = collect();
        
        if (Schema::hasColumn('fees', 'payment_date')) {
            // Last 7 days collections
            $weeklyTrend = Fee::selectRaw('DATE(payment_date) as date, sum(amount) as total')
                              ->whereNotNull('payment_date')
                              ->whereBetween('payment_date', [
                                  Carbon::now()->subDays(7),
                                  Carbon::now()
                              ])
                              ->groupBy('date')
                              ->orderBy('date')
                              ->get();
            
            // Last 12 months collections
            $monthlyTrend = Fee::selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, sum(amount) as total')
                               ->whereNotNull('payment_date')
                               ->whereBetween('payment_date', [
                                   Carbon::now()->subMonths(12),
                                   Carbon::now()
                               ])
                               ->groupBy('month')
                               ->orderBy('month')
                               ->get();
        }
        
        // ===== ENROLLMENT TRENDS =====
        $enrollmentTrends = Students::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                                    ->whereNotNull('created_at')
                                    ->whereBetween('created_at', [
                                        Carbon::now()->subMonths(7),
                                        Carbon::now()
                                    ])
                                    ->groupBy('month')
                                    ->orderBy('month')
                                    ->get();
        
        // ===== REPORTS =====
        $totalReports = $totalTransactions;
        
        // ===== CHART DATA =====
        $chartData = [
            'enrollment' => [
                'labels' => $enrollmentTrends->pluck('month'),
                'data' => $enrollmentTrends->pluck('count'),
            ],
            'weekly' => [
                'labels' => $weeklyTrend->pluck('date')->map(function($date) {
                    return Carbon::parse($date)->format('D, M d');
                }),
                'values' => $weeklyTrend->pluck('total'),
            ],
            'monthly' => [
                'labels' => $monthlyTrend->pluck('month'),
                'values' => $monthlyTrend->pluck('total'),
            ],
            'payment_method' => [
                'labels' => $paymentDistribution->pluck('payment_method'),
                'values' => $paymentDistribution->pluck('total'),
                'counts' => $paymentDistribution->pluck('count'),
            ],
            'fee_type' => [
                'labels' => $feeTypeDistribution->pluck('fee_type'),
                'values' => $feeTypeDistribution->pluck('total'),
                'counts' => $feeTypeDistribution->pluck('count'),
            ],
        ];
        
        return view('dashboard', compact(
            // Student stats
            'totalStudents',
            'activeStudents',
            'inactiveStudents',
            'pendingStudents',
            'graduatedStudents',
            'activeUsers',
            
            // Course stats
            'totalCourses',
            'mostPopularCourse',
            'courseDistribution',
            
            // Fee stats
            'totalFeesCollected',
            'todayCollections',
            'thisMonthCollections',
            'lastMonthCollections',
            'totalTransactions',
            'averageFee',
            'paymentDistribution',
            'feeTypeDistribution',
            
            // Fee status breakdown
            'paidFees',
            'pendingFees',
            'overdueFees',
            'totalRevenue',
            'paidCount',
            'pendingCount',
            'overdueCount',
            'paidPercentage',
            'pendingPercentage',
            'overduePercentage',
            'collectionRate',
            
            // Recent records
            'recentStudents',
            'recentPayments',
            
            // Top performers
            'topPayingStudents',
            'mostActivePayers',
            
            // Trends
            'weeklyTrend',
            'monthlyTrend',
            'enrollmentTrends',
            
            // Reports
            'totalReports',
            
            // Chart data
            'chartData'
        ));
    }
}