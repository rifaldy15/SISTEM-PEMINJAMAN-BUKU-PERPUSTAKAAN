<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Visitor;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Circulation report
     */
    public function circulation(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Transaction summary
        $transactions = Transaction::whereBetween('borrowed_at', [$startDate, $endDate])
            ->selectRaw('DATE(borrowed_at) as date, COUNT(*) as borrows')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $returns = Transaction::whereBetween('returned_at', [$startDate, $endDate])
            ->selectRaw('DATE(returned_at) as date, COUNT(*) as returns')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Summary stats
        $summary = [
            'total_borrows' => Transaction::whereBetween('borrowed_at', [$startDate, $endDate])->count(),
            'total_returns' => Transaction::whereBetween('returned_at', [$startDate, $endDate])->count(),
            'overdue_returns' => Transaction::whereBetween('returned_at', [$startDate, $endDate])
                ->whereHas('fine')
                ->count(),
            'total_fines' => Fine::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
        ];

        // Top borrowers
        $topBorrowers = Transaction::whereBetween('borrowed_at', [$startDate, $endDate])
            ->selectRaw('member_id, COUNT(*) as borrow_count')
            ->groupBy('member_id')
            ->orderByDesc('borrow_count')
            ->limit(10)
            ->with('member:id,name,member_number,class')
            ->get();

        // Top books
        $topBooks = Transaction::whereBetween('borrowed_at', [$startDate, $endDate])
            ->selectRaw('book_id, COUNT(*) as borrow_count')
            ->groupBy('book_id')
            ->orderByDesc('borrow_count')
            ->limit(10)
            ->with('book:id,title,author')
            ->get();

        return view('pages.library.reports.circulation', compact(
            'startDate', 'endDate', 'transactions', 'returns',
            'summary', 'topBorrowers', 'topBooks'
        ));
    }

    /**
     * Visitor report
     */
    public function visitors(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Daily visitors
        $dailyVisitors = Visitor::whereBetween('check_in', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->selectRaw('DATE(check_in) as date, COUNT(*) as visitor_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Summary
        $summary = [
            'total_visits' => $dailyVisitors->sum('visitor_count'),
            'average_daily' => round($dailyVisitors->avg('visitor_count') ?? 0),
            'peak_day' => $dailyVisitors->sortByDesc('visitor_count')->first(),
        ];

        // Hourly distribution (for the date range)
        $hourlyDistribution = Visitor::whereBetween('check_in', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->selectRaw('HOUR(check_in) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Top visitors
        $topVisitors = Visitor::whereBetween('check_in', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->selectRaw('member_id, COUNT(*) as visit_count')
            ->groupBy('member_id')
            ->orderByDesc('visit_count')
            ->limit(10)
            ->with('member:id,name,member_number,class')
            ->get();

        return view('pages.library.reports.visitors', compact(
            'startDate', 'endDate', 'dailyVisitors',
            'summary', 'hourlyDistribution', 'topVisitors'
        ));
    }
}
