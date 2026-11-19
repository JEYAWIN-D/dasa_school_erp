<?php

namespace App\Services\Library;

use App\Models\AcademicYear;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAuditLog;
use App\Models\Library\LibraryStockVerificationItem;
use App\Models\Library\LibraryStockVerificationSession;
use Exception;
use Illuminate\Support\Facades\DB;

class StockVerificationService
{
    /**
     * Start a new physical stock verification session.
     */
    public function startSession(string $title, ?int $academicYearId = null): LibraryStockVerificationSession
    {
        return DB::transaction(function () use ($title, $academicYearId) {
            $code = 'STK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // Count total active expected physical copies (not disposed)
            $expectedCount = BookCopy::whereNotIn('status', ['disposed', 'lost'])->count();

            $session = LibraryStockVerificationSession::create([
                'session_code'          => $code,
                'title'                 => $title,
                'academic_year_id'      => $academicYearId ?? (AcademicYear::current()?->id ?? AcademicYear::first()?->id),
                'start_date'            => now()->toDateString(),
                'status'                => 'in_progress',
                'total_expected_copies' => $expectedCount,
                'total_scanned_copies'  => 0,
                'matched_count'         => 0,
                'missing_count'         => 0,
                'damaged_count'         => 0,
                'misplaced_count'       => 0,
                'initiated_by'          => auth()->id(),
            ]);

            LibraryAuditLog::log('STOCK_VERIFICATION_STARTED', 'LibraryStockVerificationSession', $session->id, null, [
                'session_code'    => $code,
                'expected_copies' => $expectedCount,
            ], "Initiated stock verification session '{$title}' ({$code})");

            return $session;
        });
    }

    /**
     * Scan an item during stock verification.
     */
    public function scanBarcode(int $sessionId, string $barcode, ?int $scannedShelfId = null): array
    {
        return DB::transaction(function () use ($sessionId, $barcode, $scannedShelfId) {
            $session = LibraryStockVerificationSession::lockForUpdate()->findOrFail($sessionId);

            if ($session->status !== 'in_progress') {
                throw new Exception("Stock session is {$session->status}. No further scans allowed.");
            }

            $cleanBarcode = trim($barcode);
            $copy = BookCopy::with('book')
                ->where('barcode', $cleanBarcode)
                ->orWhere('accession_number', $cleanBarcode)
                ->first();

            if (!$copy) {
                return [
                    'success' => false,
                    'message' => "Unregistered item or invalid barcode: {$barcode}",
                    'session' => $session,
                ];
            }

            // Check if already scanned in this session
            $existing = LibraryStockVerificationItem::where('session_id', $session->id)
                ->where('book_copy_id', $copy->id)
                ->first();

            if ($existing) {
                return [
                    'success' => true,
                    'already_scanned' => true,
                    'message' => "Copy {$copy->accession_number} was already verified in this session.",
                    'copy' => $copy,
                    'session' => $session,
                ];
            }

            $expectedShelfId = $copy->shelf_id;
            $status = 'verified_present';

            if ($scannedShelfId && $expectedShelfId && $scannedShelfId !== $expectedShelfId) {
                $status = 'misplaced';
                $session->increment('misplaced_count');
            } elseif ($copy->condition && in_array($copy->condition, ['major_damage', 'torn_pages', 'water_damage'])) {
                $status = 'damaged';
                $session->increment('damaged_count');
            } else {
                $session->increment('matched_count');
            }

            $session->increment('total_scanned_copies');

            $item = LibraryStockVerificationItem::create([
                'session_id'          => $session->id,
                'book_copy_id'        => $copy->id,
                'scanned_shelf_id'    => $scannedShelfId,
                'expected_shelf_id'   => $expectedShelfId,
                'verification_status' => $status,
                'scanned_at'          => now(),
                'scanned_by'          => auth()->id(),
                'condition_at_scan'   => $copy->condition,
            ]);

            return [
                'success' => true,
                'already_scanned' => false,
                'status' => $status,
                'copy' => $copy,
                'item' => $item,
                'session' => $session->fresh(),
            ];
        });
    }

    /**
     * Complete and reconcile stock verification session.
     */
    public function completeSession(int $sessionId, ?string $summaryNotes = null): LibraryStockVerificationSession
    {
        return DB::transaction(function () use ($sessionId, $summaryNotes) {
            $session = LibraryStockVerificationSession::lockForUpdate()->findOrFail($sessionId);

            if ($session->status !== 'in_progress') {
                throw new Exception("Session is not in progress.");
            }

            // Identify missing copies: active copies that were not scanned
            $scannedCopyIds = LibraryStockVerificationItem::where('session_id', $session->id)->pluck('book_copy_id');

            $missingCopies = BookCopy::whereNotIn('status', ['disposed', 'lost', 'issued'])
                ->whereNotIn('id', $scannedCopyIds)
                ->get();

            foreach ($missingCopies as $missingCopy) {
                LibraryStockVerificationItem::create([
                    'session_id'          => $session->id,
                    'book_copy_id'        => $missingCopy->id,
                    'scanned_shelf_id'    => null,
                    'expected_shelf_id'   => $missingCopy->shelf_id,
                    'verification_status' => 'missing',
                    'scanned_at'          => now(),
                    'scanned_by'          => auth()->id(),
                    'notes'               => 'Unscanned during physical audit session',
                ]);
            }

            $missingCount = $missingCopies->count();

            $session->update([
                'status'         => 'completed',
                'end_date'       => now()->toDateString(),
                'missing_count'  => $missingCount,
                'completed_by'   => auth()->id(),
                'summary_notes'  => $summaryNotes,
            ]);

            LibraryAuditLog::log('STOCK_VERIFICATION_COMPLETED', 'LibraryStockVerificationSession', $session->id, null, [
                'total_scanned' => $session->total_scanned_copies,
                'matched'       => $session->matched_count,
                'missing'       => $missingCount,
                'misplaced'     => $session->misplaced_count,
            ], "Completed stock verification session {$session->session_code}");

            return $session;
        });
    }
}
