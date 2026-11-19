<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Library\LibraryShelf;
use App\Models\Library\LibraryStockVerificationSession;
use App\Services\Library\StockVerificationService;
use Illuminate\Http\Request;

class StockVerificationController extends Controller
{
    public function __construct(protected StockVerificationService $stockService) {}

    public function index()
    {
        $sessions = LibraryStockVerificationSession::with(['academicYear', 'initiator', 'completer'])
            ->latest('id')
            ->paginate(15);

        $activeYears = AcademicYear::orderByDesc('start_date')->get();

        return view('library.stock-verification.index', compact('sessions', 'activeYears'));
    }

    public function startSession(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:150',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $session = $this->stockService->startSession($request->input('title'), $request->input('academic_year_id'));

        return redirect()->route('library.stock-verification.scan', $session->id)->with('success', "Stock audit session {$session->session_code} initiated.");
    }

    public function scan(int $sessionId)
    {
        $session = LibraryStockVerificationSession::with(['items.copy.book', 'items.scannedShelf', 'items.scanner'])
            ->findOrFail($sessionId);

        $shelves = LibraryShelf::with('rack')->where('is_active', true)->get();

        return view('library.stock-verification.scan', compact('session', 'shelves'));
    }

    public function processScan(Request $request, int $sessionId)
    {
        $request->validate([
            'barcode'  => 'required|string',
            'shelf_id' => 'nullable|exists:library_shelves,id',
        ]);

        $result = $this->stockService->scanBarcode($sessionId, $request->input('barcode'), $request->input('shelf_id'));

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message'] ?? 'Scanned successfully.');
    }

    public function complete(Request $request, int $sessionId)
    {
        $session = $this->stockService->completeSession($sessionId, $request->input('summary_notes'));

        return redirect()->route('library.stock-verification.index')
            ->with('success', "Session {$session->session_code} completed. Reconciled {$session->missing_count} missing copies.");
    }
}

