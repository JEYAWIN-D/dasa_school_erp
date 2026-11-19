<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\BookReservation;
use App\Models\Employee;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAsset;
use App\Models\Library\LibraryAuditLog;
use App\Models\Library\LibraryDisposalRecord;
use App\Models\Library\LibraryFinePayment;
use App\Models\Library\LibraryGoodsReceipt;
use App\Models\Library\LibraryPeriodical;
use App\Models\Library\LibrarySubscription;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LibraryReportService
{
    /**
     * Compute all 21 Dashboard KPIs in real time.
     */
    public function getDashboardKPIs(): array
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();

        $totalTitles = Book::where('status', 'active')->count();
        $totalCopies = BookCopy::count();
        $availableCopies = BookCopy::where('status', 'available')->count();
        $issuedCopies = BookCopy::where('status', 'issued')->count();
        $reservedCopies = BookReservation::where('status', 'ready')->count();
        $overdueIssues = BookIssue::where('status', 'issued')->where('due_date', '<', $today)->count();
        $damagedCopies = BookCopy::where('status', 'damaged')->count();
        $lostCopies = BookCopy::where('status', 'lost')->count();
        $disposedCopies = BookCopy::where('status', 'disposed')->count();

        $totalStudents = Student::where('status', 'active')->count();
        $totalStaff = Employee::where('status', 'active')->count();

        $activeBorrowerStudents = BookIssue::where('status', 'issued')->whereNotNull('student_id')->distinct('student_id')->count('student_id');
        $activeBorrowerStaff = BookIssue::where('status', 'issued')->whereNotNull('employee_id')->distinct('employee_id')->count('employee_id');
        $activeBorrowers = $activeBorrowerStudents + $activeBorrowerStaff;

        $pendingReturns = BookIssue::where('status', 'issued')->count();
        $todayIssued = BookIssue::whereDate('issue_date', $today)->count();
        $todayReturned = BookIssue::whereDate('return_date', $today)->count();

        // Financials
        $collectedFines = (float) LibraryFinePayment::sum('amount_paid');
        $unpaidIssues = BookIssue::where('fine_paid', false)
            ->where(function ($q) {
                $q->where('fine_amount', '>', 0)
                  ->orWhere('damage_fine', '>', 0)
                  ->orWhere('lost_fine', '>', 0);
            })->get();

        $pendingFines = 0.0;
        foreach ($unpaidIssues as $issue) {
            $pendingFines += $issue->totalPendingFine();
        }

        $monthlyBookPurchases = (int) LibraryGoodsReceipt::whereDate('receipt_date', '>=', $startOfMonth)->sum('total_items_received');
        $magazineCount = LibraryPeriodical::where('periodical_type', 'magazine')->where('is_active', true)->count();
        $newspaperCount = LibraryPeriodical::where('periodical_type', 'newspaper')->where('is_active', true)->count();
        $totalAssets = LibraryAsset::where('status', '!=', 'disposed')->count();

        return [
            'total_books'               => $totalTitles,
            'total_copies'              => $totalCopies,
            'available_copies'          => $availableCopies,
            'issued_copies'             => $issuedCopies,
            'reserved_copies'           => $reservedCopies,
            'overdue_copies'            => $overdueIssues,
            'damaged_copies'            => $damagedCopies,
            'lost_copies'               => $lostCopies,
            'written_off_copies'        => $disposedCopies,
            'total_students'            => $totalStudents,
            'total_staff'               => $totalStaff,
            'active_borrowers'          => $activeBorrowers,
            'pending_returns'           => $pendingReturns,
            'today_issued'              => $todayIssued,
            'today_returned'            => $todayReturned,
            'pending_fines'             => round($pendingFines, 2),
            'fine_collected'            => round($collectedFines, 2),
            'monthly_book_purchases'    => $monthlyBookPurchases,
            'magazine_subscriptions'    => $magazineCount,
            'newspaper_subscriptions'   => $newspaperCount,
            'library_assets_count'      => $totalAssets,
        ];
    }

    /**
     * Borrowing trends over the last 6 months.
     */
    public function getMonthlyBorrowingTrends(): array
    {
        $months = [];
        $issuedData = [];
        $returnedData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $label = $monthStart->format('M Y');

            $months[] = $label;
            $issuedData[] = BookIssue::whereBetween('issue_date', [$monthStart->toDateString(), $monthEnd->toDateString()])->count();
            $returnedData[] = BookIssue::whereBetween('return_date', [$monthStart->toDateString(), $monthEnd->toDateString()])->count();
        }

        return [
            'labels'   => $months,
            'issued'   => $issuedData,
            'returned' => $returnedData,
        ];
    }

    /**
     * Most borrowed books.
     */
    public function getMostBorrowedBooks(int $limit = 10)
    {
        return Book::select('books.id', 'books.title', 'books.author', 'books.accession_number')
            ->withCount('issues')
            ->orderByDesc('issues_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Most active student and staff borrowers.
     */
    public function getMostActiveBorrowers(int $limit = 10): array
    {
        $students = Student::select('students.id', 'students.first_name', 'students.last_name', 'students.admission_no')
            ->withCount('bookIssues')
            ->orderByDesc('book_issues_count')
            ->limit($limit)
            ->get();

        $staff = Employee::select('employees.id', 'employees.first_name', 'employees.last_name', 'employees.employee_code')
            ->withCount('bookIssues')
            ->orderByDesc('book_issues_count')
            ->limit($limit)
            ->get();

        return [
            'students' => $students,
            'staff'    => $staff,
        ];
    }

    /**
     * Category-wise book distribution.
     */
    public function getCategoryDistribution()
    {
        return DB::table('book_categories')
            ->leftJoin('books', 'book_categories.id', '=', 'books.category_id')
            ->leftJoin('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->select(
                'book_categories.name',
                DB::raw('COUNT(DISTINCT books.id) as title_count'),
                DB::raw('COUNT(book_copies.id) as copy_count'),
                DB::raw("COUNT(CASE WHEN book_copies.status = 'available' THEN 1 END) as available_count")
            )
            ->groupBy('book_categories.id', 'book_categories.name')
            ->orderByDesc('copy_count')
            ->get();
    }

    /**
     * Recent library activity logs.
     */
    public function getRecentActivities(int $limit = 15)
    {
        return LibraryAuditLog::with('user')
            ->latest('id')
            ->limit($limit)
            ->get();
    }
}

