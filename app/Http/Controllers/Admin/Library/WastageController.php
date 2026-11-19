<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAsset;
use App\Models\Library\LibraryDamageReport;
use App\Models\Library\LibraryDisposalRecord;
use App\Models\Student;
use App\Services\Library\WastageService;
use Illuminate\Http\Request;

class WastageController extends Controller
{
    public function __construct(protected WastageService $wastageService) {}

    public function index(Request $request)
    {
        $damageReports = LibraryDamageReport::with(['bookCopy.book', 'asset', 'reporter', 'borrowerStudent', 'borrowerEmployee', 'disposalRecord'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest('id')
            ->paginate(15);

        $disposals = LibraryDisposalRecord::with(['bookCopy.book', 'asset', 'approver'])
            ->latest('id')
            ->paginate(15);

        $damagedCopies = BookCopy::with('book')
            ->whereIn('status', ['damaged', 'lost'])
            ->get();

        $assets = LibraryAsset::where('status', '!=', 'disposed')->get();

        return view('library.wastage.index', compact('damageReports', 'disposals', 'damagedCopies', 'assets'));
    }

    public function storeDamageReport(Request $request)
    {
        $data = $request->validate([
            'book_copy_id'         => 'nullable|exists:book_copies,id',
            'asset_id'             => 'nullable|exists:library_assets,id',
            'incident_type'        => 'required|string',
            'borrower_student_id'  => 'nullable|exists:students,id',
            'borrower_employee_id' => 'nullable|exists:employees,id',
            'reported_date'        => 'required|date',
            'damage_description'   => 'required|string',
            'action_proposed'      => 'required|in:repair,rebind,fine_and_replace,write_off,dispose',
            'repair_cost'          => 'nullable|numeric|min:0',
            'fine_charged'         => 'nullable|numeric|min:0',
        ]);

        $report = $this->wastageService->reportDamage($data);

        return redirect()->route('library.wastage.index')->with('success', "Damage report {$report->report_number} filed.");
    }

    public function approveWriteOff(Request $request, int $reportId)
    {
        $data = $request->validate([
            'disposal_method'       => 'required|in:recycle_paper,scrap_sale,donation,destruction,other',
            'disposal_date'         => 'required|date',
            'scrap_value_recovered' => 'nullable|numeric|min:0',
            'revenue_receipt_no'    => 'nullable|string|max:50',
            'justification'         => 'required|string|min:5',
            'witness_name'          => 'nullable|string|max:100',
        ]);

        $disposal = $this->wastageService->approveWriteOff($reportId, $data, auth()->id());

        return redirect()->route('library.wastage.index')->with('success', "Disposal record {$disposal->disposal_number} approved and written off.");
    }
}

