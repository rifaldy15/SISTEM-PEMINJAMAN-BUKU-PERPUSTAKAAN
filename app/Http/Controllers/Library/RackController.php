<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Rack;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function index(Request $request)
    {
        $query = Rack::withCount('books')->withSum('books', 'stock');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by Location
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Sorting
        $sort = $request->get('sort', 'code_asc');
        switch ($sort) {
            case 'code_desc':
                $query->orderBy('code', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'capacity_desc':
                $query->orderBy('capacity', 'desc');
                break;
            case 'capacity_asc':
                $query->orderBy('capacity', 'asc');
                break;
            default: // code_asc
                $query->orderBy('code', 'asc');
                break;
        }

        $racks = $query->paginate(15)->onEachSide(1);
        
        // Get unique locations for filter dropdown
        $locations = Rack::select('location')->distinct()->whereNotNull('location')->orderBy('location')->pluck('location');

        return view('pages.library.racks.index', compact('racks', 'locations'));
    }

    public function show(Rack $rack)
    {
        $rack->load(['books.category'])->loadSum('books', 'stock');
        return view('pages.library.racks.show', compact('rack'));
    }

    public function create()
    {
        $locations = Rack::select('location')->distinct()->whereNotNull('location')->orderBy('location')->pluck('location');
        $names = Rack::select('name')->distinct()->whereNotNull('name')->orderBy('name')->pluck('name');
        return view('pages.library.racks.create', compact('locations', 'names'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:racks',
            'name' => 'required|string|max:100',
            'location' => 'nullable|string',
            'capacity' => 'required|integer|min:1|max:500',
        ]);

        Rack::create($validated);

        return redirect()->route('racks.index')
            ->with('success', 'Lokasi rak berhasil ditambahkan!');
    }

    public function edit(Rack $rack)
    {
        $locations = Rack::select('location')->distinct()->whereNotNull('location')->orderBy('location')->pluck('location');
        $names = Rack::select('name')->distinct()->whereNotNull('name')->orderBy('name')->pluck('name');
        return view('pages.library.racks.edit', compact('rack', 'locations', 'names'));
    }

    public function update(Request $request, Rack $rack)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:racks,code,' . $rack->id,
            'name' => 'required|string|max:100',
            'location' => 'nullable|string',
            'capacity' => 'required|integer|min:1|max:500',
        ]);

        $rack->update($validated);

        return redirect()->route('racks.index')
            ->with('success', 'Lokasi rak berhasil diperbarui!');
    }

    public function destroy(Rack $rack)
    {
        if ($rack->books()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus rak yang masih memiliki buku!');
        }

        $rack->delete();

        return redirect()->route('racks.index')
            ->with('success', 'Lokasi rak berhasil dihapus!');
    }
}
