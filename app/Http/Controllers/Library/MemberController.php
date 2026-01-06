<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Visitor;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of members
     */
    public function index(Request $request)
    {
        $query = Member::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->orderBy('name')->paginate(15)->onEachSide(1);

        return view('pages.library.members.index', compact('members'));
    }

    /**
     * Show the form for creating a new member
     */
    public function create()
    {
        $classes = Member::select('class')->distinct()->whereNotNull('class')->orderBy('class')->pluck('class');
        return view('pages.library.members.create', compact('classes'));
    }

    /**
     * Store a newly created member
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'class' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'validity_years' => 'required|integer|min:1|max:5',
        ]);

        $validated['member_number'] = Member::generateMemberNumber();
        $validated['joined_at'] = now();
        $validated['expired_at'] = now()->addYears((int) $validated['validity_years']);
        $validated['status'] = 'active';

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store('members', 'public');
        }

        unset($validated['validity_years']);

        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil didaftarkan!');
    }

    /**
     * Display the specified member
     */
    public function show(Member $member)
    {
        $member->load([
            'activeTransactions.book',
            'transactions' => function ($q) {
                $q->with('book')->orderByDesc('borrowed_at')->limit(10);
            },
            'visits' => function ($q) {
                $q->orderByDesc('check_in')->limit(10);
            },
        ]);

        return view('pages.library.members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified member
     */
    public function edit(Member $member)
    {
        $classes = Member::select('class')->distinct()->whereNotNull('class')->orderBy('class')->pluck('class');
        return view('pages.library.members.edit', compact('member', 'classes'));
    }

    /**
     * Update the specified member
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'class' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,expired',
            'expired_at' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'max_borrow' => 'required|integer|min:1|max:10',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store('members', 'public');
        }

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Data anggota berhasil diperbarui!');
    }

    /**
     * Remove the specified member
     */
    public function destroy(Member $member)
    {
        // Check if member has active transactions
        if ($member->activeTransactions()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus anggota yang masih memiliki peminjaman aktif!');
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus!');
    }

    /**
     * Find member by member number (AJAX)
     */
    public function findByNumber(string $memberNumber)
    {
        $member = Member::where('member_number', $memberNumber)->first();

        if (!$member) {
            return response()->json(['error' => 'Anggota tidak ditemukan'], 404);
        }

        return response()->json([
            'member' => $member,
            'can_borrow' => $member->can_borrow,
            'remaining_quota' => $member->remaining_quota,
            'unpaid_fines' => $member->unpaid_fines,
            'is_expired' => $member->is_expired,
        ]);
    }

    /**
     * Show member card print page
     */
    public function card(Request $request)
    {
        if ($request->filled('member_id')) {
            $member = Member::findOrFail($request->member_id);
            return view('pages.library.members.card-print', compact('member'));
        }

        $members = Member::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('pages.library.members.card', compact('members'));
    }

    /**
     * Extend membership
     */
    public function extend(Request $request, Member $member)
    {
        $validated = $request->validate([
            'years' => 'required|integer|min:1|max:5',
        ]);

        $member->expired_at = $member->expired_at->addYears((int) $validated['years']);
        $member->status = 'active';
        $member->save();

        return back()->with('success', 'Keanggotaan berhasil diperpanjang!');
    }
}
