<?php

namespace App\Services\Library;

use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAuditLog;
use App\Models\Library\LibraryRack;
use App\Models\Library\LibraryShelf;
use Illuminate\Support\Facades\DB;

class RackAllocationService
{
    /**
     * Create rack with unique code.
     */
    public function createRack(array $data): LibraryRack
    {
        return DB::transaction(function () use ($data) {
            $rack = LibraryRack::create([
                'rack_number'  => $data['rack_number'] ?? ($data['code'] ?? ('RCK-' . strtoupper(substr(uniqid(), -4)))),
                'name'         => $data['name'],
                'building'     => $data['building'] ?? 'Main Campus',
                'floor'        => $data['floor'] ?? 'Ground Floor',
                'room'         => $data['room'] ?? 'Central Library',
                'section'      => $data['section'] ?? null,
                'category_id'  => $data['category_id'] ?? null,
                'capacity'     => $data['capacity'] ?? 100,
                'is_active'    => $data['is_active'] ?? true,
                'remarks'      => $data['remarks'] ?? null,
            ]);

            LibraryAuditLog::log('RACK_CREATED', 'LibraryRack', $rack->id, null, $rack->toArray(), "Created Rack {$rack->rack_number}");

            return $rack;
        });
    }

    /**
     * Create shelf inside a rack.
     */
    public function createShelf(LibraryRack $rack, array $data): LibraryShelf
    {
        return DB::transaction(function () use ($rack, $data) {
            $shelf = $rack->shelves()->create([
                'shelf_number' => $data['shelf_number'],
                'name'         => $data['name'] ?? null,
                'subject'      => $data['subject'] ?? null,
                'capacity'     => $data['capacity'] ?? 25,
                'is_active'    => $data['is_active'] ?? true,
                'remarks'      => $data['remarks'] ?? null,
            ]);

            LibraryAuditLog::log('SHELF_CREATED', 'LibraryShelf', $shelf->id, null, $shelf->toArray(), "Created Shelf {$shelf->shelf_number} in Rack {$rack->rack_number}");

            return $shelf;
        });
    }

    /**
     * Allocate or transfer a copy to a shelf.
     */
    public function allocateCopyToShelf(BookCopy $copy, LibraryShelf $shelf): void
    {
        DB::transaction(function () use ($copy, $shelf) {
            $before = ['shelf_id' => $copy->shelf_id];
            $copy->update(['shelf_id' => $shelf->id]);

            // Also keep book master location synchronized if it was empty
            if (!$copy->book->rack_id) {
                $copy->book->update([
                    'rack_id'  => $shelf->rack_id,
                    'shelf_id' => $shelf->id,
                    'location' => "{$shelf->rack->rack_number} / {$shelf->shelf_number}",
                ]);
            }

            LibraryAuditLog::log('COPY_ALLOCATED_TO_SHELF', 'BookCopy', $copy->id, $before, [
                'shelf_id' => $shelf->id,
                'rack_id'  => $shelf->rack_id,
            ], "Moved copy {$copy->accession_number} to Rack {$shelf->rack->rack_number} Shelf {$shelf->shelf_number}");
        });
    }

    /**
     * Bulk allocate copies to a shelf.
     */
    public function bulkAllocate(array $copyIds, int $shelfId): int
    {
        $shelf = LibraryShelf::with('rack')->findOrFail($shelfId);
        $copies = BookCopy::whereIn('id', $copyIds)->get();

        foreach ($copies as $copy) {
            $this->allocateCopyToShelf($copy, $shelf);
        }

        return $copies->count();
    }
}
