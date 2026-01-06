<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Book;
use App\Models\Transaction;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    /**
     * Show borrowing page
     */
    public function borrow()
    {
        return view('pages.library.circulation.borrow');
    }

    /**
     * Process borrowing
     */
    public function processBorrow(Request $request)
    {
        $validated = $request->validate([
            'member_number' => 'required|exists:members,member_number',
            'book_isbn' => 'required|exists:books,isbn',
            'due_days' => 'required|integer|min:1|max:30',
        ]);

        $member = Member::where('member_number', $validated['member_number'])->first();
        $book = Book::where('isbn', $validated['book_isbn'])->first();

        // Validation checks
        if (!$member->can_borrow) {
            return back()->with('error', 'Anggota tidak dapat melakukan peminjaman. Cek status keanggotaan.');
        }

        if (!$book->is_available) {
            return back()->with('error', 'Buku tidak tersedia untuk dipinjam.');
        }

        // Check if member already borrowed this book
        $existingBorrow = Transaction::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->active()
            ->exists();

        if ($existingBorrow) {
            return back()->with('error', 'Anggota sudah meminjam buku ini.');
        }

        // Create transaction
        $transaction = Transaction::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays((int) $validated['due_days']),
            'status' => 'borrowed',
        ]);

        // Decrement book availability
        $book->decrementAvailable();

        return redirect()->route('circulation.active')
            ->with('success', "Peminjaman berhasil! Buku '{$book->title}' dipinjam oleh {$member->name}.");
    }

    /**
     * Show return page
     */
    public function return()
    {
        return view('pages.library.circulation.return');
    }

    /**
     * Process return
     */
    public function processReturn(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'book_condition' => 'required|in:good,minor_damage,major_damage,lost',
            'pay_fine' => 'nullable|boolean',
        ]);

        $transaction = Transaction::where('id', $validated['transaction_id'])
            ->active()
            ->with(['member', 'book'])
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Tidak ditemukan transaksi peminjaman aktif untuk buku tersebut.');
        }

        $book = $transaction->book;

        // Process the return
        // Calculate Fines
        $daysOverdue = $transaction->days_overdue;
        $overdueFine = 0;
        $conditionFine = 0;
        $fineNotes = [];

        // 1. Overdue Fine (Rp 2.000 / day)
        if ($daysOverdue > 0) {
            $overdueFine = $daysOverdue * 2000;
            $fineNotes[] = "Telat {$daysOverdue} hari (Rp " . number_format($overdueFine, 0, ',', '.') . ")";
        }

        // 2. Condition Fine
        switch ($validated['book_condition']) {
            case 'minor_damage':
                $conditionFine = 20000;
                $fineNotes[] = "Rusak Ringan (Rp 20.000)";
                break;
            case 'major_damage':
                $conditionFine = 50000;
                $fineNotes[] = "Rusak Berat (Rp 50.000)";
                break;
            case 'lost':
                $conditionFine = 100000;
                $fineNotes[] = "Hilang (Rp 100.000)";
                $transaction->status = 'lost';
                $transaction->notes = 'Buku lapor hilang saat pengembalian';
                break;
            default: // good
                $conditionFine = 0;
                break;
        }

        $totalFine = $overdueFine + $conditionFine;

        // Process Return (if not lost, or handles lost status internally)
        if ($validated['book_condition'] !== 'lost') {
            $transaction->processReturn();
        } else {
            $transaction->returned_at = now();
            $transaction->save();
        }

        // Create Fine Record if there is any fine
        if ($totalFine > 0) {
            Fine::create([
                'transaction_id' => $transaction->id,
                'days_overdue' => $daysOverdue,
                'amount_per_day' => 2000,
                'total_amount' => $totalFine,
                'is_paid' => $request->boolean('pay_fine'),
                'paid_at' => $request->boolean('pay_fine') ? now() : null,
                'notes' => implode(', ', $fineNotes),
            ]);
        }

        $message = "Pengembalian berhasil! Buku '{$book->title}' telah dikembalikan oleh {$transaction->member->name}.";
        
        if ($totalFine > 0) {
            $message .= " Total Denda: Rp " . number_format($totalFine, 0, ',', '.') . " (" . implode(', ', $fineNotes) . ")";
        }

        return redirect()->route('circulation.active')
            ->with('success', $message);
    }

    /**
     * Show active borrowings list
     */
    public function active(Request $request)
    {
        $query = Transaction::with(['book', 'member'])
            ->active()
            ->orderByDesc('id');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->where('due_date', '<', now());
            } else {
                $query->where('due_date', '>=', now());
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%")
                       ->orWhere('isbn', 'like', "%{$search}%");
                })->orWhereHas('member', function ($mq) use ($search) {
                    $mq->where('name', 'like', "%{$search}%")
                       ->orWhere('member_number', 'like', "%{$search}%");
                });
            });
        }

        $transactions = $query->paginate(15)->onEachSide(1);

        return view('pages.library.circulation.active', compact('transactions'));
    }

    /**
     * Show borrowing history
     */
    public function history(Request $request)
    {
        $query = Transaction::with(['book', 'member', 'fine'])
            ->returned()
            ->orderByDesc('returned_at');

        // Date filter
        if ($request->filled('from_date')) {
            $query->whereDate('returned_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('returned_at', '<=', $request->to_date);
        }

        $transactions = $query->paginate(15)->onEachSide(1);

        return view('pages.library.circulation.history', compact('transactions'));
    }

    /**
     * Find transaction by book ISBN (AJAX)
     */
    public function findTransaction(string $isbn)
    {
        $book = Book::where('isbn', $isbn)->first();

        if (!$book) {
            return response()->json(['error' => 'Buku tidak ditemukan'], 404);
        }

        $transactions = Transaction::where('book_id', $book->id)
            ->active()
            ->with(['member', 'book'])
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json(['error' => 'Tidak ada peminjaman aktif untuk buku ini'], 404);
        }

        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'is_overdue' => $transaction->is_overdue,
                'days_overdue' => $transaction->days_overdue,
                'fine_amount' => $transaction->days_overdue * 2000, // Matching logic in processReturn
                'borrowed_at' => $transaction->borrowed_at,
                'due_date' => $transaction->due_date,
                'member' => $transaction->member,
                'book' => $transaction->book,
            ];
        });

        return response()->json([
            'count' => $formattedTransactions->count(),
            'transactions' => $formattedTransactions,
        ]);
    }
}
