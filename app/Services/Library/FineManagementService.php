<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Holiday;
use App\Models\LibrarySetting;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAuditLog;
use App\Models\Library\LibraryFinePayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class FineManagementService
{
    /**
     * Calculate overdue fine based on member type, grace period, holidays, and caps.
     */
    public function calculateOverdueFine(BookIssue $issue, ?Carbon $returnDate = null): float
    {
        $returnDate = $returnDate ?? now();
        $dueDate = $issue->due_date;

        if (!$dueDate || $returnDate->lte($dueDate)) {
            return 0.0;
        }

        $type = $issue->student_id ? 'student' : 'employee';
        $settings = LibrarySetting::forType($type);

        $totalDays = $dueDate->diffInDays($returnDate);
        $graceDays = (int) ($settings->grace_period_days ?? 0);

        if ($totalDays <= $graceDays) {
            return 0.0;
        }

        $billableDays = $totalDays - $graceDays;

        // Holiday exclusion if configured
        if (!$settings->allow_holiday_fine) {
            $holidaysCount = Holiday::whereBetween('holiday_date', [$dueDate->toDateString(), $returnDate->toDateString()])->count();
            $billableDays = max(0, $billableDays - $holidaysCount);
        }

        $finePerDay = (float) $settings->fine_per_day;
        $calculated = $billableDays * $finePerDay;

        $maxFine = (float) ($settings->max_fine_limit ?? 500.0);
        if ($maxFine > 0) {
            $calculated = min($calculated, $maxFine);
        }

        return round($calculated, 2);
    }

    /**
     * Calculate lost book penalty based on policy.
     */
    public function calculateLostBookFine($bookOrCopy): float
    {
        $book = $bookOrCopy instanceof BookCopy ? $bookOrCopy->book : $bookOrCopy;
        $price = (float) ($book->current_value ?? $book->purchase_price ?? 200.0);

        $settings = LibrarySetting::forType('student');
        $multiplier = (float) ($settings->lost_book_multiplier ?? 1.5);

        return round(max($price * $multiplier, 100.0), 2);
    }

    /**
     * Record fine payment (supports partial and full payments).
     */
    public function recordPayment(int $issueId, array $data): LibraryFinePayment
    {
        return DB::transaction(function () use ($issueId, $data) {
            $issue = BookIssue::lockForUpdate()->findOrFail($issueId);
            $amount = (float) ($data['amount_paid'] ?? 0);

            if ($amount <= 0) {
                throw new Exception("Payment amount must be greater than zero.");
            }

            $pending = $issue->totalPendingFine();
            if ($amount > $pending) {
                throw new Exception("Payment amount (₹{$amount}) cannot exceed pending fine (₹{$pending}).");
            }

            $receiptNo = 'REC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $payment = LibraryFinePayment::create([
                'book_issue_id'         => $issue->id,
                'receipt_no'            => $receiptNo,
                'amount_paid'           => $amount,
                'waiver_amount'         => 0,
                'payment_mode'          => $data['payment_mode'] ?? 'cash',
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'received_by'           => auth()->id(),
                'notes'                 => $data['notes'] ?? null,
            ]);

            // If remaining pending is 0, mark fine_paid as true
            $newPending = $issue->fresh()->totalPendingFine();
            if ($newPending <= 0.01) {
                $issue->update([
                    'fine_paid'            => true,
                    'fine_collected_at'    => now(),
                    'fine_collection_mode' => $data['payment_mode'] ?? 'cash',
                ]);
            }

            LibraryAuditLog::log('FINE_PAYMENT_COLLECTED', 'LibraryFinePayment', $payment->id, null, [
                'receipt_no'   => $receiptNo,
                'amount_paid'  => $amount,
                'mode'         => $data['payment_mode'] ?? 'cash',
                'remaining'    => $newPending,
            ], "Collected fine ₹{$amount} for issue #{$issue->id} via receipt {$receiptNo}");

            return $payment;
        });
    }

    /**
     * Process authorized fine waiver with mandatory remarks.
     */
    public function waiveFine(int $issueId, float $waiverAmount, string $reason, int $approvedById): BookIssue
    {
        return DB::transaction(function () use ($issueId, $waiverAmount, $reason, $approvedById) {
            $issue = BookIssue::lockForUpdate()->findOrFail($issueId);

            if (empty(trim($reason))) {
                throw new Exception("A valid reason is required for waiving library fines.");
            }

            $pending = $issue->totalPendingFine();
            if ($waiverAmount <= 0 || $waiverAmount > $pending) {
                throw new Exception("Waiver amount must be between ₹1 and the pending amount (₹{$pending}).");
            }

            $existingWaiver = (float) ($issue->waiver_amount ?? 0);
            $newWaiverTotal = round($existingWaiver + $waiverAmount, 2);

            $issue->update([
                'fine_waived'   => true,
                'waiver_amount' => $newWaiverTotal,
                'waiver_reason' => $reason,
                'waived_by'     => $approvedById,
                'waived_at'     => now(),
            ]);

            if ($issue->fresh()->totalPendingFine() <= 0.01) {
                $issue->update(['fine_paid' => true]);
            }

            LibraryAuditLog::log('FINE_WAIVED', 'BookIssue', $issue->id, ['pending' => $pending], [
                'waiver_amount' => $waiverAmount,
                'reason'        => $reason,
                'approved_by'   => $approvedById,
            ], "Waived fine ₹{$waiverAmount} on issue #{$issue->id} for reason: '{$reason}'");

            return $issue;
        });
    }
}

