<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function index(Request $request)
    {
        $query = Fine::with(['transaction.member', 'transaction.book'])
            ->orderByDesc('created_at');

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('is_paid', $request->status === 'paid');
        }

        $fines = $query->paginate(15)->onEachSide(1);

        // Statistics
        $stats = [
            'total_unpaid' => Fine::unpaid()->sum('total_amount'),
            'total_collected' => Fine::paid()->sum('total_amount'),
            'unpaid_count' => Fine::unpaid()->count(),
        ];

        return view('pages.library.fines.index', compact('fines', 'stats'));
    }

    public function markAsPaid(Fine $fine)
    {
        $fine->markAsPaid();

        return back()->with('success', 'Denda berhasil dibayar!');
    }

    public function destroy(Fine $fine)
    {
        if ($fine->is_paid) {
            return back()->with('error', 'Tidak dapat menghapus denda yang sudah dibayar!');
        }

        $fine->delete();

        return back()->with('success', 'Denda berhasil dihapus!');
    }
}
