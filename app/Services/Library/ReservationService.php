<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\BookReservation;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAuditLog;
use Exception;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * Place a book reservation in the waiting list queue.
     */
    public function createReservation(int $bookId, ?int $studentId, ?int $employeeId): BookReservation
    {
        return DB::transaction(function () use ($bookId, $studentId, $employeeId) {
            $book = Book::findOrFail($bookId);

            // Check if user already has an active reservation
            $existing = BookReservation::where('book_id', $bookId)
                ->whereIn('status', ['waiting', 'ready'])
                ->when($studentId, fn($q) => $q->where('student_id', $studentId))
                ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
                ->first();

            if ($existing) {
                throw new Exception("You already have an active reservation (Position #{$existing->queue_position}) for this title.");
            }

            $maxQueue = BookReservation::where('book_id', $bookId)
                ->whereIn('status', ['waiting', 'ready'])
                ->max('queue_position') ?? 0;

            $reservation = BookReservation::create([
                'book_id'        => $bookId,
                'student_id'     => $studentId,
                'employee_id'    => $employeeId,
                'queue_position' => $maxQueue + 1,
                'status'         => 'waiting',
                'reserved_at'    => now(),
            ]);

            LibraryAuditLog::log('BOOK_RESERVATION_CREATED', 'BookReservation', $reservation->id, null, [
                'book_id'        => $bookId,
                'queue_position' => $reservation->queue_position,
                'borrower'       => $reservation->borrowerName(),
            ], "Reserved '{$book->title}' at queue position #{$reservation->queue_position}");

            return $reservation;
        });
    }

    /**
     * Cancel an existing reservation.
     */
    public function cancelReservation(int $id, string $reason): BookReservation
    {
        return DB::transaction(function () use ($id, $reason) {
            $reservation = BookReservation::lockForUpdate()->findOrFail($id);

            if (in_array($reservation->status, ['cancelled', 'fulfilled'])) {
                throw new Exception("Reservation already {$reservation->status}.");
            }

            $reservation->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            // Release holding copy if it was ready
            if ($reservation->copy_id) {
                $copy = BookCopy::find($reservation->copy_id);
                if ($copy && $copy->status === 'reserved') {
                    $copy->update(['status' => 'available']);
                    $copy->book->refreshCopiesCount();
                }
            }

            LibraryAuditLog::log('RESERVATION_CANCELLED', 'BookReservation', $reservation->id, null, [
                'reason' => $reason,
            ], "Cancelled reservation #{$reservation->id} for reason '{$reason}'");

            return $reservation;
        });
    }

    /**
     * Expire unclaimed holding reservations past the holding window (default 48h).
     */
    public function expireOldReservations(): int
    {
        $expired = BookReservation::where('status', 'ready')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $res) {
            $this->cancelReservation($res->id, 'Holding window expired (unclaimed within 48 hours)');
            $count++;
        }

        return $count;
    }
}

