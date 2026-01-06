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

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Today's statistics
        $stats = [
            'visitors_today' => Visitor::today()->count(),
            'visitors_change' => $this->calculateChange(
                Visitor::whereDate('check_in', today())->count(),
                Visitor::whereDate('check_in', today()->subDay())->count()
            ),
            'books_borrowed' => Transaction::active()->count(),
            'books_borrowed_change' => $this->calculateChange(
                Transaction::active()->count(),
                Transaction::whereDate('created_at', today()->subDay())->active()->count()
            ),
            'returned_today' => Transaction::whereDate('returned_at', today())->count(),
            'overdue' => Transaction::overdue()->count(),
        ];

        // Visitor chart data (last 7 days)
        $visitorChart = $this->getVisitorChartData();

        // Top borrowed books this month
        $topBooks = $this->getTopBorrowedBooks();

        // Overdue books list
        $overdueBooks = Transaction::with(['book', 'member'])
            ->overdue()
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(function ($trans) {
                return [
                    'title' => $trans->book->title,
                    'member' => $trans->member->name,
                    'days_overdue' => $trans->days_overdue,
                ];
            });

        // Recent activity
        $recentActivity = $this->getRecentActivity();

        // Book Condition Chart Data
        $totalStock = Book::sum('stock');
        $totalAvailable = Book::sum('available');
        $totalBorrowed = $totalStock - $totalAvailable;
        
        $bookCondition = [
            'available' => $totalAvailable,
            'borrowed' => $totalBorrowed,
            'lost' => 0, // Placeholder for future feature
            'tersedia_pct' => $totalStock > 0 ? round(($totalAvailable / $totalStock) * 100) : 0,
            'dipinjam_pct' => $totalStock > 0 ? round(($totalBorrowed / $totalStock) * 100) : 0,
            'rusak_pct' => 0,
        ];

        return view('pages.library.dashboard', compact(
            'stats',
            'visitorChart',
            'topBooks',
            'overdueBooks',
            'recentActivity',
            'bookCondition'
        ));
    }

    /**
     * Calculate percentage change
     */
    private function calculateChange(int $current, int $previous): array
    {
        if ($previous === 0) {
            return ['value' => $current > 0 ? 100 : 0, 'direction' => 'up'];
        }
        
        $change = (($current - $previous) / $previous) * 100;
        return [
            'value' => round(abs($change)),
            'direction' => $change >= 0 ? 'up' : 'down',
        ];
    }

    /**
     * Get visitor chart data for last 7 days
     */
    private function getVisitorChartData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $labels[] = $date->format('D');
            $data[] = Visitor::whereDate('check_in', $date)->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get top borrowed books this month
     */
    private function getTopBorrowedBooks(): array
    {
        return Transaction::select('book_id')
            ->selectRaw('COUNT(*) as borrow_count')
            ->whereMonth('borrowed_at', now()->month)
            ->whereYear('borrowed_at', now()->year)
            ->groupBy('book_id')
            ->orderByDesc('borrow_count')
            ->limit(5)
            ->with('book:id,title')
            ->get()
            ->map(function ($item) {
                return [
                    'title' => $item->book->title ?? 'Unknown',
                    'count' => $item->borrow_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get recent activity (borrows and returns)
     */
    private function getRecentActivity(): array
    {
        $activities = [];

        // Recent borrows
        $borrows = Transaction::with(['book', 'member'])
            ->whereDate('borrowed_at', '>=', today()->subDays(1))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        foreach ($borrows as $borrow) {
            $activities[] = [
                'type' => 'borrow',
                'icon' => '📥',
                'description' => "{$borrow->member->name} meminjam {$borrow->book->title}",
                'time' => $borrow->created_at->diffForHumans(),
                'timestamp' => $borrow->created_at,
            ];
        }

        // Recent returns
        $returns = Transaction::with(['book', 'member'])
            ->whereDate('returned_at', '>=', today()->subDays(1))
            ->whereNotNull('returned_at')
            ->orderByDesc('returned_at')
            ->limit(5)
            ->get();

        foreach ($returns as $return) {
            $activities[] = [
                'type' => 'return',
                'icon' => '✅',
                'description' => "{$return->member->name} mengembalikan {$return->book->title}",
                'time' => $return->updated_at->diffForHumans(),
                'timestamp' => $return->updated_at,
            ];
        }

        // Sort by timestamp and take top 10
        usort($activities, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        return array_slice($activities, 0, 10);
    }
}
