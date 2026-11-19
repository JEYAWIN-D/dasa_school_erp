<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibraryPeriodical;
use App\Models\Library\LibraryPeriodicalReceipt;
use App\Models\Library\LibraryRack;
use App\Models\Library\LibraryShelf;
use App\Models\Library\LibrarySubscription;
use App\Models\Library\LibrarySupplier;
use App\Services\Library\PeriodicalService;
use Illuminate\Http\Request;

class PeriodicalController extends Controller
{
    public function __construct(protected PeriodicalService $periodicalService) {}

    public function index(Request $request)
    {
        $periodicals = LibraryPeriodical::with(['rack', 'shelf', 'activeSubscription.supplier', 'receipts'])
            ->when($request->type, fn($q, $v) => $q->where('periodical_type', $v))
            ->orderBy('title')
            ->paginate(15);

        $todayReceipts = LibraryPeriodicalReceipt::with('periodical')
            ->whereDate('receipt_date', now()->toDateString())
            ->latest('id')
            ->get();

        $expiringSubscriptions = $this->periodicalService->getExpiringSubscriptions(30);

        $suppliers = LibrarySupplier::where('is_active', true)->orderBy('name')->get();
        $racks = LibraryRack::with('shelves')->where('is_active', true)->get();

        return view('library.periodicals.index', compact('periodicals', 'todayReceipts', 'expiringSubscriptions', 'suppliers', 'racks'));
    }

    public function storePeriodical(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:200',
            'periodical_type' => 'required|in:newspaper,magazine,journal,newsletter',
            'frequency'       => 'required|in:daily,weekly,fortnightly,monthly,quarterly,annual',
            'issn'            => 'nullable|string|max:30',
            'publisher'       => 'nullable|string|max:150',
            'language'        => 'nullable|string|max:50',
            'rack_id'         => 'nullable|exists:library_racks,id',
            'shelf_id'        => 'nullable|exists:library_shelves,id',
            'remarks'         => 'nullable|string',
        ]);

        $this->periodicalService->createPeriodical($data);

        return redirect()->route('library.periodicals.index')->with('success', "Periodical '{$data['title']}' registered.");
    }

    public function storeSubscription(Request $request)
    {
        $data = $request->validate([
            'periodical_id'       => 'required|exists:library_periodicals,id',
            'supplier_id'         => 'nullable|exists:library_suppliers,id',
            'subscription_number' => 'nullable|string|max:50',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after:start_date',
            'cost'                => 'required|numeric|min:0',
            'copies_per_issue'    => 'required|integer|min:1',
            'auto_renew'          => 'nullable|boolean',
            'notes'               => 'nullable|string',
        ]);

        $this->periodicalService->createSubscription($data);

        return redirect()->route('library.periodicals.index')->with('success', "Subscription recorded successfully.");
    }

    public function storeDailyReceipt(Request $request)
    {
        $data = $request->validate([
            'periodical_id'   => 'required|exists:library_periodicals,id',
            'subscription_id' => 'nullable|exists:library_subscriptions,id',
            'issue_date'      => 'required|date',
            'volume_no'       => 'nullable|string|max:30',
            'issue_no'        => 'nullable|string|max:30',
            'received_date'   => 'required|date',
            'copies_received' => 'required|integer|min:1',
            'condition'       => 'nullable|string|max:30',
            'remarks'         => 'nullable|string',
        ]);

        $this->periodicalService->recordDailyReceipt($data);

        return redirect()->route('library.periodicals.index')->with('success', "Receipt logged.");
    }
}

