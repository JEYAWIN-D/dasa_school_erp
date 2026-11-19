<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAuditLog;
use Illuminate\Support\Facades\DB;

class BookCopyService
{
    /**
     * Generate unique accession number for a copy.
     */
    public function generateAccessionNumber(Book $book, int $copyNumber): string
    {
        $base = $book->accession_number ? trim($book->accession_number) : ('ACC-' . $book->id);
        $suffix = str_pad($copyNumber, 2, '0', STR_PAD_LEFT);
        $candidate = "{$base}-{$suffix}";

        // Ensure absolute global uniqueness
        $counter = 1;
        while (BookCopy::where('accession_number', $candidate)->exists()) {
            $counter++;
            $candidate = "{$base}-{$suffix}-" . str_pad($counter, 2, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    /**
     * Generate a scannable barcode string.
     */
    public function generateBarcode(string $accessionNumber): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $accessionNumber);
        $barcode = 'BC-' . strtoupper($clean);

        $counter = 1;
        while (BookCopy::where('barcode', $barcode)->exists()) {
            $counter++;
            $barcode = 'BC-' . strtoupper($clean) . $counter;
        }

        return $barcode;
    }

    /**
     * Create physical copies for a book master.
     */
    public function createCopies(Book $book, int $count, array $defaults = []): array
    {
        return DB::transaction(function () use ($book, $count, $defaults) {
            $existingCount = $book->copies()->count();
            $created = [];

            for ($i = 1; $i <= $count; $i++) {
                $copyNum = $existingCount + $i;
                $acc = $defaults['accession_number'] ?? $this->generateAccessionNumber($book, $copyNum);
                $barcode = $defaults['barcode'] ?? $this->generateBarcode($acc);

                $copy = BookCopy::create([
                    'book_id'                => $book->id,
                    'accession_number'       => $acc,
                    'barcode'                => $barcode,
                    'rfid_tag'               => $defaults['rfid_tag'] ?? null,
                    'copy_number'            => $copyNum,
                    'shelf_id'               => $defaults['shelf_id'] ?? $book->shelf_id,
                    'condition'              => $defaults['condition'] ?? 'good',
                    'status'                 => $defaults['status'] ?? 'available',
                    'is_reference_only'      => $defaults['is_reference_only'] ?? false,
                    'purchase_order_item_id' => $defaults['purchase_order_item_id'] ?? null,
                    'cost_price'             => $defaults['cost_price'] ?? $book->purchase_price,
                    'remarks'                => $defaults['remarks'] ?? null,
                    'last_inspected_at'      => now(),
                ]);

                $created[] = $copy;
            }

            $this->syncBookTotals($book);

            LibraryAuditLog::log('BOOK_COPIES_CREATED', 'Book', $book->id, null, [
                'count' => $count,
                'total' => $book->fresh()->total_copies,
            ], "Created {$count} physical copies for Book ID {$book->id}");

            return $created;
        });
    }

    /**
     * Synchronize total and available copies counts on the title master.
     */
    public function syncBookTotals(Book $book): void
    {
        $total = $book->copies()->count();
        $avail = $book->copies()->where('status', 'available')->count();
        $lost = $book->copies()->where('status', 'lost')->count();

        $updateData = [
            'total_copies'     => $total,
            'available_copies' => $avail,
            'is_available'     => $avail > 0,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('books', 'lost_copies')) {
            $updateData['lost_copies'] = $lost;
        }

        $book->update($updateData);
    }
}
