<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('books');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Code
        if ($request->filled('code')) {
            $query->where('code', $request->code);
        }

        // Filter by Name
        if ($request->filled('name')) {
            $query->where('name', $request->name);
        }

        // Sorting
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'code_asc':
                $query->orderBy('code', 'asc');
                break;
            case 'code_desc':
                $query->orderBy('code', 'desc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'books_count_desc':
                $query->orderByDesc('books_count');
                break;
            case 'books_count_asc':
                $query->orderBy('books_count');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            default: // name_asc
                $query->orderBy('name', 'asc');
                break;
        }

        $categories = $query->paginate(15)->onEachSide(1);
        
        // Get data for filters
        $dates = Category::select('code')->distinct()->orderBy('code')->pluck('code');
        $codes = $dates; // Alias for clarity if needed, or just use $codes directly. 
        // Actually let's just do it cleanly:
        $codes = Category::select('code')->distinct()->orderBy('code')->pluck('code');
        $names = Category::select('name')->distinct()->orderBy('name')->pluck('name');

        return view('pages.library.categories.index', compact('categories', 'codes', 'names'));
    }

    public function create()
    {
        return view('pages.library.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Category $category)
    {
        return view('pages.library.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang masih memiliki buku!');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}
