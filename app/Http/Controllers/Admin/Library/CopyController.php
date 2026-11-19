<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryShelf;
use App\Services\Library\BookCopyService;
use App\Services\Library\RackAllocationService;
use Illuminate\Http\Request;

class CopyController extends Controller
{
    public function __construct(
        protected BookCopyService $copyService,
        protected RackAllocationService $rackService
    ) {}

    public function index(int $bookId)
    {
        $book = Book::with(['copies.shelf.rack', 'category', 'supplier'])->findOrFail($bookId);
        $shelves = LibraryShelf::with('rack')->where('is_active', true)->get();

        return view('library.copies.index', compact('book', 'shelves'));
    }

    public function store(Request $request, int $bookId)
    {
        $book = Book::findOrFail($bookId);

        $request->validate([
            'count'             => 'required|integer|min:1|max:100',
            'shelf_id'          => 'nullable|exists:library_shelves,id',
            'condition'         => 'required|string|in:new,good,minor_damage',
            'is_reference_only' => 'nullable|boolean',
            'cost_price'        => 'nullable|numeric|min:0',
        ]);

        $this->copyService->createCopies($book, (int) $request->input('count'), [
            'shelf_id'          => $request->input('shelf_id'),
            'condition'         => $request->input('condition'),
            'is_reference_only' => $request->boolean('is_reference_only'),
            'cost_price'        => $request->input('cost_price') ?? $book->purchase_price,
        ]);

        return redirect()->route('library.copies.index', $bookId)->with('success', "Added {$request->input('count')} physical copies.");
    }

    public function updateCondition(Request $request, int $id)
    {
        $copy = BookCopy::findOrFail($id);

        $request->validate([
            'condition' => 'required|string',
            'remarks'   => 'nullable|string',
        ]);

        $copy->update([
            'condition' => $request->input('condition'),
            'remarks'   => $request->input('remarks'),
            'last_inspected_at' => now(),
        ]);

        return back()->with('success', "Copy {$copy->accession_number} updated.");
    }

    public function allocateShelf(Request $request, int $id)
    {
        $copy = BookCopy::findOrFail($id);
        $shelf = LibraryShelf::findOrFail($request->input('shelf_id'));

        $this->rackService->allocateCopyToShelf($copy, $shelf);

        return back()->with('success', "Copy allocated to shelf.");
    }
}

