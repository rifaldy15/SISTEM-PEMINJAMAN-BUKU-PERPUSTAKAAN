<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Display visitor list / attendance log
     */
    public function index(Request $request)
    {
        $query = Visitor::with('member')->orderByDesc('check_in');

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('check_in', $request->date);
        } else {
            $query->whereDate('check_in', today());
        }

        $visitors = $query->paginate(20)->onEachSide(1);

        // Statistics
        $stats = [
            'today' => Visitor::today()->count(),
            'still_in' => Visitor::today()->stillIn()->count(),
            'average' => round(Visitor::whereDate('check_in', '>=', now()->subDays(30))
                ->selectRaw('DATE(check_in) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get()
                ->avg('count') ?? 0),
        ];

        return view('pages.library.visitors.index', compact('visitors', 'stats'));
    }

    /**
     * Show kiosk mode page (fullscreen)
     */
    public function kiosk()
    {
        $todayCount = Visitor::today()->count();

        return view('pages.library.visitors.kiosk', compact('todayCount'));
    }

    /**
     * Process kiosk check-in (AJAX)
     */
    public function processCheckIn(Request $request)
    {
        $validated = $request->validate([
            'member_number' => 'required|string',
        ]);

        $member = Member::where('member_number', $validated['member_number'])->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota tidak ditemukan',
                'type' => 'not_found',
            ], 404);
        }

        if ($member->is_expired) {
            return response()->json([
                'success' => false,
                'message' => 'Keanggotaan sudah kedaluwarsa',
                'type' => 'expired',
                'member' => $member,
            ], 400);
        }

        // Check if already checked in today and not checked out
        $existingVisit = Visitor::where('member_id', $member->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        if ($existingVisit) {
            // Check out
            $existingVisit->checkOut();

            return response()->json([
                'success' => true,
                'type' => 'checkout',
                'message' => 'Sampai jumpa lagi!',
                'member' => $member,
                'check_out' => $existingVisit->check_out->format('H:i'),
                'duration' => $existingVisit->duration,
            ]);
        }

        // Check in
        $visit = Visitor::checkIn($member);

        return response()->json([
            'success' => true,
            'type' => 'checkin',
            'message' => 'Selamat datang!',
            'member' => $member,
            'check_in' => $visit->check_in->format('H:i'),
            'today_count' => Visitor::today()->count(),
        ]);
    }

    /**
     * Get today's visitor count (AJAX for kiosk refresh)
     */
    public function todayCount()
    {
        return response()->json([
            'count' => Visitor::today()->count(),
            'still_in' => Visitor::today()->stillIn()->count(),
        ]);
    }

    /**
     * Manual check-in by admin
     */
    public function manualCheckIn(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'purpose' => 'nullable|string|max:100',
        ]);

        $member = Member::find($validated['member_id']);

        Visitor::checkIn($member, $validated['purpose'] ?? null);

        return back()->with('success', "{$member->name} berhasil check-in.");
    }

    /**
     * Manual check-out by admin
     */
    public function manualCheckOut(Visitor $visitor)
    {
        $visitor->checkOut();

        return back()->with('success', 'Check-out berhasil.');
    }
}
