<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Rack;
use App\Models\Member;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of books
     */
    public function index(Request $request)
    {
        $query = Book::with(['category', 'rack']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by rack
        if ($request->filled('rack')) {
            $query->where('rack_id', $request->rack);
        }

        // Filter by availability
        if ($request->filled('available')) {
            if ($request->available === '1') {
                $query->where('available', '>', 0);
            } else {
                $query->where('available', 0);
            }
        }

        $books = $query->orderBy('title')->paginate(15)->onEachSide(1);
        $categories = Category::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();

        return view('pages.library.books.index', compact('books', 'categories', 'racks'));
    }

    /**
     * Show the form for creating a new book
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();

        return view('pages.library.books.create', compact('categories', 'racks'));
    }

    /**
     * Store a newly created book
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20|unique:books',
            'category_id' => 'required|exists:categories,id',
            'rack_id' => 'nullable|exists:racks,id',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'stock' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['available'] = $validated['stock'];

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Display the specified book
     */
    public function show(Book $book)
    {
        $book->load(['category', 'rack', 'transactions' => function ($q) {
            $q->with('member')->orderByDesc('borrowed_at')->limit(10);
        }]);

        return view('pages.library.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book
     */
    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();

        return view('pages.library.books.edit', compact('book', 'categories', 'racks'));
    }

    /**
     * Update the specified book
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20|unique:books,isbn,' . $book->id,
            'category_id' => 'required|exists:categories,id',
            'rack_id' => 'nullable|exists:racks,id',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'stock' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Adjust available count based on stock change
        $stockDiff = $validated['stock'] - $book->stock;
        $validated['available'] = max(0, $book->available + $stockDiff);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diperbarui!');
    }

    /**
     * Remove the specified book
     */
    public function destroy(Book $book)
    {
        // Check if book has active transactions
        if ($book->transactions()->active()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus buku yang sedang dipinjam!');
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil dihapus!');
    }

    /**
     * Search books via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $books = Book::where('title', 'like', "%{$query}%")
            ->orWhere('isbn', 'like', "%{$query}%")
            ->orWhere('author', 'like', "%{$query}%")
            ->with('category')
            ->limit(10)
            ->get();

        return response()->json($books);
    }

    /**
     * Find book by ISBN
     */
    public function findByIsbn(string $isbn)
    {
        $book = Book::where('isbn', $isbn)->with(['category', 'rack'])->first();

        if (!$book) {
            return response()->json(['error' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json($book);
    }

    /**
     * Global search for books and members
     */
    public function globalSearch(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'books' => [],
                'members' => []
            ]);
        }

        $books = Book::where('title', 'like', "%{$query}%")
            ->orWhere('isbn', 'like', "%{$query}%")
            ->orWhere('author', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'subtitle' => $book->author . ' (' . $book->isbn . ')',
                    'url' => route('books.show', $book),
                    'type' => 'Buku',
                    'icon' => '📖'
                ];
            });

        $members = Member::where('name', 'like', "%{$query}%")
            ->orWhere('member_number', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($member) {
                return [
                    'id' => $member->id,
                    'title' => $member->name,
                    'subtitle' => $member->member_number . ($member->class ? ' - ' . $member->class : ''),
                    'url' => route('members.show', $member),
                    'type' => 'Anggota',
                    'icon' => '👤'
                ];
            });

        return response()->json([
            'books' => $books,
            'members' => $members
        ]);
    }
}
