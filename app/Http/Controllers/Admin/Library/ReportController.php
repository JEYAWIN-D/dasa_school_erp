<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryFinePayment;
use App\Services\Library\LibraryReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected LibraryReportService $reportService) {}

    public function index()
    {
        $kpis = $this->reportService->getDashboardKPIs();
        $trends = $this->reportService->getMonthlyBorrowingTrends();
        $topBooks = $this->reportService->getMostBorrowedBooks(10);
        $activeBorrowers = $this->reportService->getMostActiveBorrowers(10);
        $categories = $this->reportService->getCategoryDistribution();
        $recentLogs = $this->reportService->getRecentActivities(15);

        return view('library.reports.index', compact('kpis', 'trends', 'topBooks', 'activeBorrowers', 'categories', 'recentLogs'));
    }

    public function export(Request $request, string $type)
    {
        $filename = "library_{$type}_" . date('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'catalog') {
                fputcsv($handle, ['Accession Number', 'Title', 'Author', 'Category', 'Total Copies', 'Available Copies', 'Status']);
                Book::with('category')->chunk(100, function ($books) use ($handle) {
                    foreach ($books as $b) {
                        fputcsv($handle, [$b->accession_number, $b->title, $b->author, $b->category?->name, $b->total_copies, $b->available_copies, $b->status]);
                    }
                });
            } elseif ($type === 'circulation') {
                fputcsv($handle, ['Issue ID', 'Receipt', 'Book Title', 'Accession', 'Borrower', 'Type', 'Issue Date', 'Due Date', 'Return Date', 'Status']);
                BookIssue::with(['book', 'copy', 'student', 'employee'])->chunk(100, function ($issues) use ($handle) {
                    foreach ($issues as $i) {
                        fputcsv($handle, [
                            $i->id,
                            $i->issue_receipt_no,
                            $i->book?->title,
                            $i->copy?->accession_number ?? $i->book?->accession_number,
                            $i->borrowerName(),
                            $i->student_id ? 'Student' : 'Staff',
                            $i->issue_date?->toDateString(),
                            $i->due_date?->toDateString(),
                            $i->return_date?->toDateString(),
                            $i->status,
                        ]);
                    }
                });
            } elseif ($type === 'fines') {
                fputcsv($handle, ['Payment Receipt', 'Issue ID', 'Borrower', 'Amount Paid', 'Waiver', 'Mode', 'Date']);
                LibraryFinePayment::with('issue.student', 'issue.employee')->chunk(100, function ($payments) use ($handle) {
                    foreach ($payments as $p) {
                        fputcsv($handle, [
                            $p->receipt_no,
                            $p->book_issue_id,
                            $p->issue?->borrowerName(),
                            $p->amount_paid,
                            $p->waiver_amount,
                            $p->payment_mode,
                            $p->created_at?->toDateString(),
                        ]);
                    }
                });
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

