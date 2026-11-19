<?php

/**
 * Enterprise School Library Management System - Automated Integration Test Suite
 * 
 * Tests the complete lifecycle of books, physical copies, racks/shelves, circulation,
 * fines, procurement, periodicals, assets, wastage, stock verification, and reports.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryRack;
use App\Models\Library\LibraryShelf;
use App\Models\Library\LibrarySupplier;
use App\Models\Library\LibraryPurchaseOrder;
use App\Models\Library\LibraryPeriodical;
use App\Models\Library\LibraryAsset;
use App\Models\Library\LibraryDamageReport;
use App\Models\Library\LibraryStockVerificationSession;
use App\Services\Library\BookCopyService;
use App\Services\Library\RackAllocationService;
use App\Services\Library\CirculationService;
use App\Services\Library\FineManagementService;
use App\Services\Library\ProcurementService;
use App\Services\Library\PeriodicalService;
use App\Services\Library\AssetManagementService;
use App\Services\Library\WastageService;
use App\Services\Library\StockVerificationService;
use App\Services\Library\LibraryReportService;
use Illuminate\Support\Facades\DB;

class LibraryTestSuite
{
    private int $passed = 0;
    private int $failed = 0;

    public function run(): void
    {
        echo "====================================================================\n";
        echo "  ENTERPRISE SCHOOL LIBRARY MANAGEMENT SYSTEM - AUTOMATED TEST SUITE\n";
        echo "====================================================================\n\n";

        DB::beginTransaction();

        try {
            $this->testBookCategoryAndTitleCreation();
            $this->testPhysicalCopyGenerationAndUniqueness();
            $this->testRackHierarchyAndShelfCapacity();
            $this->testCirculationIssueAndStockSafety();
            $this->testCirculationReturnAndDamageRouting();
            $this->testFineManagementAndCollection();
            $this->testProcurementLifecycle();
            $this->testPeriodicalCheckIn();
            $this->testAssetManagement();
            $this->testWastageAndDisposalAudit();
            $this->testStockVerificationScanning();
            $this->testReportMetricsCalculation();

            DB::rollBack(); // Always roll back test data so DB remains completely clean
            echo "\n--------------------------------------------------------------------\n";
            echo "Test transactions safely rolled back to preserve database integrity.\n";
        } catch (\Throwable $e) {
            DB::rollBack();
            echo "\n[EXCEPTION OCCURRED]: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
            echo $e->getTraceAsString() . "\n";
            $this->failed++;
        }

        echo "\n====================================================================\n";
        echo "TEST SUMMARY: {$this->passed} PASSED | {$this->failed} FAILED\n";
        echo "====================================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function assert(string $testName, bool $condition, string $message = ''): void
    {
        if ($condition) {
            echo "  [PASS] {$testName}\n";
            $this->passed++;
        } else {
            echo "  [FAIL] {$testName}: {$message}\n";
            $this->failed++;
        }
    }

    private function testBookCategoryAndTitleCreation(): void
    {
        echo "--- 1. Testing Book Master & Category Creation ---\n";
        $category = BookCategory::firstOrCreate(
            ['code' => 'TEST-SCI'],
            ['name' => 'Test Science & Robotics', 'description' => 'Test STEM discipline']
        );
        $this->assert('Category Creation/Fetch', $category->exists && !empty($category->id));

        $book = Book::create([
            'title'           => 'Enterprise Software Architecture in Practice',
            'isbn'            => '978-0134494166',
            'author'          => 'Len Bass, Paul Clements',
            'publisher'       => 'Addison-Wesley',
            'edition'         => '3rd Edition',
            'publication_year'=> 2024,
            'category_id'     => $category->id,
            'total_copies'    => 3,
            'available_copies'=> 3,
            'purchase_price'  => 1499.00,
            'current_value'   => 1499.00,
            'status'          => 'active',
        ]);

        $this->assert('Book Master Record Created', $book->exists && $book->total_copies === 3);
        $this->assert('Book Accession Auto-generated', !empty($book->accession_number));
    }

    private function testPhysicalCopyGenerationAndUniqueness(): void
    {
        echo "--- 2. Testing Physical Copy Auto-generation & Barcodes ---\n";
        $book = Book::where('isbn', '978-0134494166')->firstOrFail();
        $copyService = app(BookCopyService::class);
        $copies = $copyService->createCopies($book, 3);

        $this->assert('Generated Exact 3 Physical Copies', count($copies) === 3);

        $accessionNumbers = [];
        $barcodes = [];
        foreach ($copies as $c) {
            $accessionNumbers[] = $c->accession_number;
            $barcodes[] = $c->barcode;
            $this->assert("Copy ID {$c->id} has valid accession {$c->accession_number}", !empty($c->accession_number));
            $this->assert("Copy ID {$c->id} has valid barcode {$c->barcode}", !empty($c->barcode));
            $this->assert("Copy status is available", $c->status === 'available');
        }

        $this->assert('Accession Numbers Are Unique', count(array_unique($accessionNumbers)) === 3);
        $this->assert('Barcodes Are Unique', count(array_unique($barcodes)) === 3);
    }

    private function testRackHierarchyAndShelfCapacity(): void
    {
        echo "--- 3. Testing Spatial Hierarchy & Rack/Shelf Allocation ---\n";
        $rackService = app(RackAllocationService::class);
        $rack = $rackService->createRack([
            'building'  => 'Main Academic Block',
            'floor'     => '2nd Floor',
            'room'      => 'Central Library Room A',
            'section'   => 'Computer Science & Engineering',
            'code'      => 'RACK-CS-' . strtoupper(substr(uniqid(), -4)),
            'name'      => 'Advanced Software Engineering Rack',
            'capacity'  => 100,
        ]);
        $this->assert('Rack Created with Spatial Attributes', $rack->exists);

        $shelf = $rackService->createShelf($rack, [
            'shelf_number' => 'S-01',
            'capacity'     => 30,
        ]);
        $this->assert('Shelf Created under Rack', $shelf->exists && $shelf->shelf_number === 'S-01');

        $book = Book::where('isbn', '978-0134494166')->firstOrFail();
        $copy = $book->copies()->first();
        $rackService->allocateCopyToShelf($copy, $shelf);

        $this->assert('Copy Physically Allocated to Shelf', $copy->fresh()->shelf_id === $shelf->id);
    }

    private function testCirculationIssueAndStockSafety(): void
    {
        echo "--- 4. Testing Circulation: Issue Transaction & Safety ---\n";
        $circulationService = app(CirculationService::class);
        $book = Book::where('isbn', '978-0134494166')->firstOrFail();
        $copy = $book->copies()->where('status', 'available')->firstOrFail();
        
        $student = Student::first();
        if (!$student) {
            $student = Student::create([
                'admission_no'     => 'STU-TEST-' . uniqid(),
                'first_name'       => 'Test',
                'last_name'        => 'Patron',
                'status'           => 'active',
            ]);
        }

        $initialAvailable = $book->available_copies;
        $issue = $circulationService->issueBook([
            'copy_id'          => $copy->id,
            'student_id'       => $student->id,
            'loan_days'        => 14,
            'borrower_remarks' => 'Automated integration test loan',
        ]);

        $this->assert('Issue Record Created', $issue->exists && !empty($issue->issue_receipt_no));
        $this->assert('Issue Status is Issued', $issue->status === 'issued');
        $this->assert('Copy Status Changed to Issued', $copy->fresh()->status === 'issued');
        $this->assert('Book Available Count Decremented by 1', $book->fresh()->available_copies === ($initialAvailable - 1));
    }

    private function testCirculationReturnAndDamageRouting(): void
    {
        echo "--- 5. Testing Circulation: Return Inspection & Damaged Routing ---\n";
        $circulationService = app(CirculationService::class);
        $book = Book::where('isbn', '978-0134494166')->firstOrFail();
        $copy = $book->copies()->where('status', 'issued')->firstOrFail();
        $issue = BookIssue::where('copy_id', $copy->id)->whereNull('return_date')->firstOrFail();

        // Simulate return with damage condition
        $returnedIssue = $circulationService->returnBook([
            'issue_id'         => $issue->id,
            'condition'        => 'torn_pages',
            'inspection_notes' => 'Spine damage during loan period',
            'damage_fine'      => 150.00,
        ]);

        $this->assert('Return Date Recorded', !empty($returnedIssue->return_date));
        $this->assert('Return Receipt Number Generated', !empty($returnedIssue->return_receipt_no));
        $this->assert('Damaged Book Routed to Damaged Status (Not Available)', $copy->fresh()->status === 'damaged');
        $this->assert('Damage Fine Recorded', (float) $returnedIssue->fine_amount === 150.00);
    }

    private function testFineManagementAndCollection(): void
    {
        echo "--- 6. Testing Fine Settlement, Collection & Waivers ---\n";
        $fineService = app(FineManagementService::class);
        $issue = BookIssue::where('fine_amount', '>', 0)->where('fine_paid', false)->firstOrFail();

        // Test partial payment
        $payment = $fineService->recordPayment($issue->id, [
            'amount_paid'           => 50.00,
            'payment_mode'          => 'cash',
            'transaction_reference' => 'CASH-REC-01',
            'notes'                 => 'Partial cash counter settlement',
        ]);
        $this->assert('Fine Payment Recorded', $payment->exists && !empty($payment->receipt_no));
        $this->assert('Issue Pending Fine Decreased', (float) $issue->fresh()->totalPendingFine() === 100.00);

        // Test fine waiver for remainder
        $adminId = 1;
        $waivedIssue = $fineService->waiveFine($issue->id, 100.00, 'Principal approved educational concession', $adminId);
        $this->assert('Fine Remainder Waived', (float) $waivedIssue->waiver_amount === 100.00);
        $this->assert('Issue Fine Settled', $waivedIssue->fine_paid && $waivedIssue->fine_waived);
    }

    private function testProcurementLifecycle(): void
    {
        echo "--- 7. Testing Procurement PO & GRN Inventory Generation ---\n";
        $procService = app(ProcurementService::class);
        $supplier = LibrarySupplier::firstOrCreate(
            ['name' => 'Oxford University Press India'],
            ['contact_person' => 'Procurement Desk', 'phone' => '044-28271122']
        );
        $this->assert('Supplier Registered', $supplier->exists);

        $po = $procService->createPurchaseOrder([
            'supplier_id'            => $supplier->id,
            'expected_delivery_date' => now()->addDays(14)->toDateString(),
            'notes'                  => 'Annual STEM library upgrade procurement',
        ], [
            [
                'book_title'       => 'Discrete Mathematics and Its Applications',
                'isbn'             => '978-0073383095',
                'author'           => 'Kenneth Rosen',
                'publisher'        => 'McGraw-Hill',
                'quantity_ordered' => 2,
                'unit_price'       => 850.00,
            ]
        ]);
        $this->assert('Purchase Order Generated', $po->exists && str_starts_with($po->po_number, 'PO-'));
        $this->assert('PO Status is Draft', $po->status === 'draft');

        // Approve PO
        $approvedPo = $procService->approvePurchaseOrder($po->id, 1);
        $this->assert('PO Approved', $approvedPo->status === 'approved');

        // Receive Goods Receipt Note (GRN)
        $category = BookCategory::first();
        $grn = $procService->createGoodsReceipt([
            'purchase_order_id' => $approvedPo->id,
            'supplier_id'       => $supplier->id,
            'invoice_number'    => 'INV-OUP-2026-991',
            'invoice_date'      => now()->toDateString(),
            'receipt_date'      => now()->toDateString(),
            'remarks'           => 'Stock verified and checked in good condition',
        ], [
            [
                'purchase_order_item_id' => $approvedPo->items->first()->id,
                'book_title'             => 'Discrete Mathematics and Its Applications',
                'isbn'                   => '978-0073383095',
                'author'                 => 'Kenneth Rosen',
                'publisher'              => 'McGraw-Hill',
                'quantity_received'      => 2,
                'unit_cost'              => 850.00,
                'category_id'            => $category->id,
            ]
        ]);

        $this->assert('GRN Created with Physical Stock Injection', $grn->exists && str_starts_with($grn->grn_number, 'GRN-'));

        // Verify book title and physical copies exist
        $receivedBook = Book::where('isbn', '978-0073383095')->first();
        $this->assert('Procured Book Cataloged', $receivedBook !== null && $receivedBook->total_copies >= 2);
        $this->assert('Copies Generated with GRN Reference', $receivedBook->copies()->count() >= 2);
    }

    private function testPeriodicalCheckIn(): void
    {
        echo "--- 8. Testing Periodicals & Newspaper Daily Check-in ---\n";
        $periodicalService = app(PeriodicalService::class);
        $paper = LibraryPeriodical::firstOrCreate(
            ['title' => 'The Hindu - National Daily'],
            [
                'periodical_type' => 'newspaper',
                'frequency'       => 'daily',
                'language'        => 'English',
                'publisher'       => 'Kasturi & Sons',
                'is_active'       => true,
            ]
        );
        $this->assert('Periodical Title Registered', $paper->exists && $paper->periodical_type === 'newspaper');

        $receipt = $periodicalService->recordDailyReceipt([
            'periodical_id'   => $paper->id,
            'issue_date'      => now()->toDateString(),
            'issue_no'        => 'Vol. 148 No. ' . now()->dayOfYear,
            'copies_received' => 2,
            'condition'       => 'good',
            'remarks'         => 'Delivered on time at 06:30 AM',
        ]);

        $this->assert('Daily Newspaper Issue Checked In', $receipt->exists && $receipt->copies_received === 2);
    }

    private function testAssetManagement(): void
    {
        echo "--- 9. Testing Library Hardware & Assets ---\n";
        $assetService = app(AssetManagementService::class);
        $asset = $assetService->createAsset([
            'name'           => 'Honeywell Voyager 1250g Handheld Barcode Scanner',
            'category'       => 'scanner',
            'brand_model'    => 'Honeywell 1250g USB',
            'serial_number'  => 'HW-1250G-' . uniqid(),
            'purchase_date'  => now()->subMonths(2)->toDateString(),
            'purchase_cost'  => 4500.00,
            'location_room'  => 'Circulation Counter #1',
            'status'         => 'working',
        ]);

        $this->assert('Asset Tagged with Unique Barcode', $asset->exists && str_starts_with($asset->asset_code, 'AST-'));

        $log = $assetService->recordMaintenance($asset->id, [
            'maintenance_date'  => now()->toDateString(),
            'service_provider'  => 'In-house IT Support',
            'cost'              => 0.00,
            'issue_description' => 'Firmware check and USB calibration',
            'action_taken'      => 'Cleaned lens and tested scan latency',
            'status'            => 'completed',
        ]);

        $this->assert('Asset Maintenance History Logged', $log->exists && $asset->fresh()->status === 'working');
    }

    private function testWastageAndDisposalAudit(): void
    {
        echo "--- 10. Testing Wastage Incident Logging & Disposal Audit ---\n";
        $wastageService = app(WastageService::class);
        $book = Book::where('isbn', '978-0134494166')->firstOrFail();
        $copy = $book->copies()->where('status', 'damaged')->first();

        if (!$copy) {
            $copy = $book->copies()->first();
        }

        $report = $wastageService->reportDamage([
            'book_copy_id'       => $copy->id,
            'incident_type'      => 'torn_pages',
            'damage_description' => 'Severe physical degradation, multiple chapters missing',
            'action_proposed'    => 'write_off',
            'repair_cost'        => 0.00,
            'fine_charged'       => 150.00,
        ]);

        $this->assert('Damage Incident Reported', $report->exists && $report->incident_type === 'torn_pages');

        $disposal = $wastageService->approveWriteOff($report->id, [
            'disposal_method'       => 'recycle_paper',
            'disposal_date'         => now()->toDateString(),
            'scrap_value_recovered' => 15.00,
            'justification'         => 'Irreparable physical damage certified by Librarian',
        ], 1);

        $this->assert('Copy Written Off / Disposed Without Deleting History', $disposal->exists && $copy->fresh()->status === 'disposed');
        $this->assert('Database Integrity: Copy Row Still Exists', BookCopy::find($copy->id) !== null);
    }

    private function testStockVerificationScanning(): void
    {
        echo "--- 11. Testing Stock Verification Barcode Audit ---\n";
        $stockService = app(StockVerificationService::class);
        $session = $stockService->startSession('Annual Comprehensive Library Audit 2026');

        $this->assert('Audit Session Initialized', $session->exists && $session->status === 'in_progress');

        $copy = BookCopy::whereNotIn('status', ['disposed', 'lost'])->firstOrFail();
        $scanResult = $stockService->scanBarcode($session->id, $copy->barcode);

        $this->assert('Barcode Scanned & Verified against Catalogue', isset($scanResult['item']) && $scanResult['item']->exists);
        $this->assert('Scan Status is Verified Present', $scanResult['status'] === 'verified_present');
    }

    private function testReportMetricsCalculation(): void
    {
        echo "--- 12. Testing Enterprise KPI & Report Services ---\n";
        $reportService = app(LibraryReportService::class);
        $kpis = $reportService->getDashboardKpis();

        $this->assert('KPI Total Books Calculated', isset($kpis['total_books']) && is_numeric($kpis['total_books']));
        $this->assert('KPI Total Copies Calculated', isset($kpis['total_copies']) && is_numeric($kpis['total_copies']));
        $this->assert('KPI Available Copies Calculated', isset($kpis['available_copies']) && is_numeric($kpis['available_copies']));
        $this->assert('KPI Overdue Count Calculated', isset($kpis['overdue_copies']) && is_numeric($kpis['overdue_copies']));
        $this->assert('KPI Fine Pending Calculated', isset($kpis['pending_fines']) && is_numeric($kpis['pending_fines']));
        $this->assert('KPI Fine Collected Calculated', isset($kpis['fine_collected']) && is_numeric($kpis['fine_collected']));
        $this->assert('KPI Assets Count Calculated', isset($kpis['library_assets_count']) && is_numeric($kpis['library_assets_count']));

        $trends = $reportService->getMonthlyBorrowingTrends();
        $this->assert('Monthly Borrowing Trends Computed', isset($trends['labels']) && count($trends['labels']) === 6);

        $borrowers = $reportService->getMostActiveBorrowers(5);
        $this->assert('Top Active Patrons Computed Without Column Errors', isset($borrowers['students']) && isset($borrowers['staff']));
    }
}

$suite = new LibraryTestSuite();
$suite->run();
