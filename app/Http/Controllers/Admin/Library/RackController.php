<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryRack;
use App\Models\Library\LibraryShelf;
use App\Services\Library\RackAllocationService;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function __construct(protected RackAllocationService $rackService) {}

    public function index(Request $request)
    {
        $racks = LibraryRack::with(['category', 'shelves.copies'])
            ->when($request->search, fn($q, $v) => $q->where('rack_number', 'like', "%$v%")->orWhere('name', 'like', "%$v%"))
            ->orderBy('rack_number')
            ->paginate(15);

        $categories = BookCategory::orderBy('name')->get();

        return view('library.racks.index', compact('racks', 'categories'));
    }

    public function storeRack(Request $request)
    {
        $data = $request->validate([
            'rack_number' => 'required|string|max:50|unique:library_racks,rack_number',
            'name'        => 'required|string|max:150',
            'building'    => 'nullable|string|max:100',
            'floor'       => 'nullable|string|max:50',
            'room'        => 'nullable|string|max:100',
            'section'     => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:book_categories,id',
            'capacity'    => 'required|integer|min:1',
            'remarks'     => 'nullable|string',
        ]);

        $this->rackService->createRack($data);

        return redirect()->route('library.racks.index')->with('success', "Rack {$data['rack_number']} created successfully.");
    }

    public function storeShelf(Request $request, int $rackId)
    {
        $rack = LibraryRack::findOrFail($rackId);

        $data = $request->validate([
            'shelf_number' => 'required|string|max:50',
            'name'         => 'nullable|string|max:150',
            'subject'      => 'nullable|string|max:100',
            'capacity'     => 'required|integer|min:1',
            'remarks'      => 'nullable|string',
        ]);

        $this->rackService->createShelf($rack, $data);

        return redirect()->route('library.racks.index')->with('success', "Shelf {$data['shelf_number']} added to Rack {$rack->rack_number}.");
    }

    public function shelfCopies(int $shelfId)
    {
        $shelf = LibraryShelf::with(['rack', 'copies.book'])->findOrFail($shelfId);
        return view('library.racks.shelf-copies', compact('shelf'));
    }
}

