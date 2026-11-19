<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\BookReservation;
use App\Models\Employee;
use App\Models\LibrarySetting;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAuditLog;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CirculationService
{
    public function __construct(
        protected BookCopyService $copyService,
        protected FineManagementService $fineService
    ) {}

    /**
     * Verify borrower eligibility. Returns array with [eligible => bool, errors => string[], warnings => string[]].
     */
    public function checkBorrowerEligibility(?int $studentId, ?int $employeeId): array
    {
        $result = ['eligible' => true, 'errors' => [], 'warnings' => [], 'borrower' => null, 'type' => null];

        if ($studentId) {
            $student = Student::with(['currentEnrollment.class', 'currentEnrollment.section'])->find($studentId);
            if (!$student) {
                return ['eligible' => false, 'errors' => ['Student not found.'], 'warnings' => []];
            }
            $result['borrower'] = $student;
            $result['type'] = 'student';

            if ($student->library_suspended) {
                $result['eligible'] = false;
                $result['errors'][] = "Student library privileges are suspended. Reason: " . ($student->library_suspension_reason ?: 'Administrative hold');
            }
            if ($student->status && strtolower($student->status) !== 'active') {
                $result['eligible'] = false;
                $result['errors'][] = "Student is not in active status ({$student->status}).";
            }
        } elseif ($employeeId) {
            $employee = Employee::find($employeeId);
            if (!$employee) {
                return ['eligible' => false, 'errors' => ['Employee not found.'], 'warnings' => []];
            }
            $result['borrower'] = $employee;
            $result['type'] = 'employee';

            if ($employee->library_suspended) {
                $result['eligible'] = false;
                $result['errors'][] = "Staff library privileges are suspended. Reason: " . ($employee->library_suspension_reason ?: 'Administrative hold');
            }
            if ($employee->status && strtolower($employee->status) !== 'active') {
                $result['eligible'] = false;
                $result['errors'][] = "Staff member is not active ({$employee->status}).";
            }
        } else {
            return ['eligible' => false, 'errors' => ['No borrower selected.'], 'warnings' => []];
        }

        $type = $result['type'];
        $settings = LibrarySetting::forType($type);

        // Check active borrowings count
        $activeCount = BookIssue::where('status', 'issued')
            ->when($studentId, fn($q) => $q->where('student_id', $studentId))
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->count();

        if ($activeCount >= $settings->max_books) {
            $result['eligible'] = false;
            $result['errors'][] = "Borrowing quota reached: already has {$activeCount} of {$settings->max_books} books allowed.";
        }

        // Check overdue books
        $overdueCount = BookIssue::where('status', 'issued')
            ->where('due_date', '<', now()->toDateString())
            ->when($studentId, fn($q) => $q->where('student_id', $studentId))
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->count();

        if ($overdueCount > 0) {
            $result['eligible'] = false;
            $result['errors'][] = "Borrower has {$overdueCount} overdue book(s). Return them before issuing new items.";
        }

        // Check pending unpaid fines
        $pendingIssues = BookIssue::where('fine_paid', false)
            ->where(function ($q) {
                $q->where('fine_amount', '>', 0)
                  ->orWhere('damage_fine', '>', 0)
                  ->orWhere('lost_fine', '>', 0);
            })
            ->when($studentId, fn($q) => $q->where('student_id', $studentId))
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->get();

        $totalPendingFine = 0.0;
        foreach ($pendingIssues as $pi) {
            $totalPendingFine += $pi->totalPendingFine();
        }

        if ($totalPendingFine > 0) {
            $result['warnings'][] = "Borrower has ₹" . number_format($totalPendingFine, 2) . " in outstanding library fines.";
            if ($totalPendingFine >= $settings->max_fine_limit) {
                $result['eligible'] = false;
                $result['errors'][] = "Outstanding fines (₹" . number_format($totalPendingFine, 2) . ") exceed permissible limit (₹" . number_format($settings->max_fine_limit, 2) . ").";
            }
        }

        return $result;
    }

    /**
     * Issue book copy with concurrency protection and optional manager override.
     */
    public function issueBook(array $params): BookIssue
    {
        return DB::transaction(function () use ($params) {
            $studentId  = $params['student_id'] ?? null;
            $employeeId = $params['employee_id'] ?? null;
            $isOverride = !empty($params['is_override']);
            $overrideReason = $params['override_reason'] ?? null;

            // 1. Eligibility Check
            $eligibility = $this->checkBorrowerEligibility($studentId, $employeeId);
            if (!$eligibility['eligible'] && !$isOverride) {
                throw new Exception(implode(' ', $eligibility['errors']));
            }

            // 2. Resolve Physical Copy with Row Lock
            $copyQuery = BookCopy::lockForUpdate();
            if (!empty($params['copy_id'])) {
                $copy = $copyQuery->findOrFail($params['copy_id']);
            } elseif (!empty($params['barcode'])) {
                $barcode = trim($params['barcode']);
                $copy = $copyQuery->where('barcode', $barcode)
                    ->orWhere('accession_number', $barcode)
                    ->firstOrFail();
            } elseif (!empty($params['book_id'])) {
                $copy = $copyQuery->where('book_id', $params['book_id'])
                    ->where('status', 'available')
                    ->where('is_reference_only', false)
                    ->first();
                if (!$copy) {
                    throw new Exception("No physical copy available for this title right now.");
                }
            } else {
                throw new Exception("Specify a book copy barcode, accession number, or book ID.");
            }

            if ($copy->status !== 'available' && !$isOverride) {
                throw new Exception("Copy {$copy->accession_number} is currently {$copy->status}.");
            }

            if ($copy->is_reference_only && !$isOverride) {
                throw new Exception("Copy {$copy->accession_number} is marked Reference Only and cannot be issued.");
            }

            // 3. Due Date Calculation
            $type = $studentId ? 'student' : 'employee';
            $settings = LibrarySetting::forType($type);
            $loanDays = (int) ($params['loan_days'] ?? $settings->loan_days);
            $issueDate = !empty($params['issue_date']) ? Carbon::parse($params['issue_date']) : now();
            $dueDate = $issueDate->copy()->addDays($loanDays);

            // 4. Issue Receipt Number
            $receiptNo = 'ISS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // 5. Create Issue Record
            $issue = BookIssue::create([
                'book_id'          => $copy->book_id,
                'copy_id'          => $copy->id,
                'student_id'       => $studentId,
                'employee_id'      => $employeeId,
                'issued_by'        => auth()->id(),
                'issue_date'       => $issueDate->toDateString(),
                'due_date'         => $dueDate->toDateString(),
                'status'           => 'issued',
                'fine_amount'      => 0,
                'fine_paid'        => false,
                'renew_count'      => 0,
                'issue_receipt_no' => $receiptNo,
                'notes'            => $params['notes'] ?? null,
                'override_by'      => $isOverride ? auth()->id() : null,
                'override_reason'  => $isOverride ? $overrideReason : null,
            ]);

            // 6. Update Copy Status
            $copy->update([
                'status' => 'issued',
                'last_inspected_at' => now(),
            ]);

            // 7. Refresh Book Totals
            $this->copyService->syncBookTotals($copy->book);

            // 8. Fulfill reservation if borrower held one
            BookReservation::where('book_id', $copy->book_id)
                ->where('status', 'ready')
                ->when($studentId, fn($q) => $q->where('student_id', $studentId))
                ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
                ->update(['status' => 'fulfilled']);

            // 9. Audit Log
            LibraryAuditLog::log('BOOK_ISSUED', 'BookIssue', $issue->id, null, [
                'receipt_no'       => $receiptNo,
                'copy_accession'   => $copy->accession_number,
                'borrower'         => $issue->borrowerName(),
                'due_date'         => $dueDate->toDateString(),
                'override'         => $isOverride,
            ], "Issued book copy {$copy->accession_number} to {$issue->borrowerName()} under receipt {$receiptNo}");

            return $issue;
        });
    }

    /**
     * Process return of an issued book with condition check and fine calculation.
     */
    public function returnBook(array $params): BookIssue
    {
        return DB::transaction(function () use ($params) {
            $issueQuery = BookIssue::with(['book', 'copy'])->lockForUpdate();

            if (!empty($params['issue_id'])) {
                $issue = $issueQuery->findOrFail($params['issue_id']);
            } elseif (!empty($params['barcode'])) {
                $barcode = trim($params['barcode']);
                $copy = BookCopy::where('barcode', $barcode)->orWhere('accession_number', $barcode)->firstOrFail();
                $issue = $issueQuery->where('copy_id', $copy->id)->where('status', 'issued')->firstOrFail();
            } else {
                throw new Exception("Please provide an issue ID or scannable barcode.");
            }

            if ($issue->status === 'returned') {
                throw new Exception("This book issue has already been returned.");
            }

            $returnDate = !empty($params['return_date']) ? Carbon::parse($params['return_date']) : now();
            $condition = $params['condition'] ?? 'good';
            $inspectionNotes = $params['inspection_notes'] ?? null;
            $returnReceiptNo = 'RET-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // Calculate Overdue Fine
            $overdueFine = $this->fineService->calculateOverdueFine($issue, $returnDate);

            // Damage / Lost fines
            $damageFine = (float) ($params['damage_fine'] ?? 0);
            $lostFine = 0.0;

            $copyStatus = 'available';
            if ($condition === 'lost') {
                $copyStatus = 'lost';
                $lostFine = $this->fineService->calculateLostBookFine($issue->copy ?? $issue->book);
            } elseif (in_array($condition, ['major_damage', 'torn_pages', 'water_damage'])) {
                $copyStatus = 'damaged';
                if ($damageFine <= 0) {
                    $damageFine = (float) ($params['damage_fine_estimate'] ?? 50.0);
                }
            }

            $totalFine = round($overdueFine + $damageFine + $lostFine, 2);

            $issue->update([
                'return_date'       => $returnDate->toDateString(),
                'status'            => $condition === 'lost' ? 'lost' : 'returned',
                'return_condition'  => $condition,
                'fine_amount'       => $totalFine,
                'damage_fine'       => $damageFine,
                'lost_fine'         => $lostFine,
                'fine_paid'         => $totalFine <= 0,
                'returned_to'       => auth()->id(),
                'return_receipt_no' => $returnReceiptNo,
                'inspection_notes'  => $inspectionNotes,
            ]);

            // Update physical copy
            if ($issue->copy) {
                $issue->copy->update([
                    'condition'         => $condition,
                    'status'            => $copyStatus,
                    'last_inspected_at' => now(),
                ]);
            }

            // Sync Book Master
            $this->copyService->syncBookTotals($issue->book);

            // Check if there is a pending reservation waiting for this title
            if ($copyStatus === 'available') {
                $nextReservation = BookReservation::where('book_id', $issue->book_id)
                    ->where('status', 'waiting')
                    ->orderBy('queue_position')
                    ->orderBy('created_at')
                    ->first();

                if ($nextReservation) {
                    $nextReservation->update([
                        'status'               => 'ready',
                        'copy_id'              => $issue->copy_id,
                        'expires_at'           => now()->addHours(48),
                        'notification_sent_at' => now(),
                    ]);
                    if ($issue->copy) {
                        $issue->copy->update(['status' => 'reserved']);
                        $this->copyService->syncBookTotals($issue->book);
                    }
                }
            }

            // Audit Log
            LibraryAuditLog::log('BOOK_RETURNED', 'BookIssue', $issue->id, null, [
                'return_receipt_no' => $returnReceiptNo,
                'condition'         => $condition,
                'overdue_fine'      => $overdueFine,
                'damage_fine'       => $damageFine,
                'lost_fine'         => $lostFine,
            ], "Returned book copy for issue #{$issue->id} condition '{$condition}' receipt {$returnReceiptNo}");

            return $issue;
        });
    }

    /**
     * Renew an issued book.
     */
    public function renewBook(int $issueId): BookIssue
    {
        return DB::transaction(function () use ($issueId) {
            $issue = BookIssue::with(['book', 'copy'])->lockForUpdate()->findOrFail($issueId);

            if ($issue->status !== 'issued') {
                throw new Exception("Cannot renew an issue with status '{$issue->status}'.");
            }

            $type = $issue->student_id ? 'student' : 'employee';
            $settings = LibrarySetting::forType($type);

            if ($issue->renew_count >= $settings->max_renewals) {
                throw new Exception("Maximum renewals ({$settings->max_renewals}) reached for this issue.");
            }

            // Check if another borrower has reserved this book
            $hasReservation = BookReservation::where('book_id', $issue->book_id)
                ->where('status', 'waiting')
                ->exists();

            if ($hasReservation) {
                throw new Exception("Cannot renew: another patron has placed a reservation on this title.");
            }

            $newDueDate = $issue->due_date->copy()->addDays($settings->loan_days);
            $issue->update([
                'due_date'    => $newDueDate->toDateString(),
                'renew_count' => $issue->renew_count + 1,
            ]);

            LibraryAuditLog::log('BOOK_RENEWED', 'BookIssue', $issue->id, null, [
                'new_due_date' => $newDueDate->toDateString(),
                'renew_count'  => $issue->renew_count,
            ], "Renewed book issue #{$issue->id} until {$newDueDate->toDateString()}");

            return $issue;
        });
    }
}
