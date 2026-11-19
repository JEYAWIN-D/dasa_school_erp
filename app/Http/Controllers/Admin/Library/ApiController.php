<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Employee;
use App\Models\Library\BookCopy;
use App\Models\Student;
use App\Services\Library\CirculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __construct(protected CirculationService $circulationService) {}

    /**
     * Scan Barcode or Accession Number.
     */
    public function scan(Request $request): JsonResponse
    {
        $code = trim($request->input('code', ''));
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Code parameter required.'], 400);
        }

        $copy = BookCopy::with(['book.category', 'shelf.rack', 'activeIssue.student', 'activeIssue.employee'])
            ->where('barcode', $code)
            ->orWhere('accession_number', $code)
            ->first();

        if ($copy) {
            return response()->json([
                'success' => true,
                'type'    => 'copy',
                'data'    => [
                    'id'               => $copy->id,
                    'book_id'          => $copy->book_id,
                    'title'            => $copy->book->title,
                    'author'           => $copy->book->author,
                    'accession_number' => $copy->accession_number,
                    'barcode'          => $copy->barcode,
                    'category'         => $copy->book->category?->name,
                    'shelf_location'   => $copy->shelf ? "Rack {$copy->shelf->rack->rack_number} / Shelf {$copy->shelf->shelf_number}" : 'Unallocated',
                    'condition'        => $copy->condition,
                    'status'           => $copy->status,
                    'is_reference_only'=> $copy->is_reference_only,
                    'active_issue'     => $copy->activeIssue ? [
                        'issue_id'      => $copy->activeIssue->id,
                        'borrower'      => $copy->activeIssue->borrowerName(),
                        'due_date'      => $copy->activeIssue->due_date?->toDateString(),
                        'days_overdue'  => $copy->activeIssue->days_overdue,
                    ] : null,
                ],
            ]);
        }

        // Try matching title by accession or ISBN
        $book = Book::with('category')
            ->where('accession_number', $code)
            ->orWhere('isbn', $code)
            ->first();

        if ($book) {
            return response()->json([
                'success' => true,
                'type'    => 'book',
                'data'    => [
                    'id'               => $book->id,
                    'title'            => $book->title,
                    'author'           => $book->author,
                    'accession_number' => $book->accession_number,
                    'available_copies' => $book->available_copies,
                    'total_copies'     => $book->total_copies,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => "Item not found for code '{$code}'."], 404);
    }

    /**
     * Search borrowers (Students and Employees).
     */
    public function searchBorrowers(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $students = Student::where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                      ->orWhere('last_name', 'like', "%{$q}%")
                      ->orWhere('admission_no', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($s) {
                $eligibility = $this->circulationService->checkBorrowerEligibility($s->id, null);
                return [
                    'id'         => $s->id,
                    'type'       => 'student',
                    'name'       => $s->full_name,
                    'identifier' => $s->admission_no ?? $s->admission_number ?? ('ADM-' . $s->id),
                    'eligible'   => $eligibility['eligible'],
                    'errors'     => $eligibility['errors'],
                    'warnings'   => $eligibility['warnings'],
                ];
            });

        $employees = Employee::where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                      ->orWhere('last_name', 'like', "%{$q}%")
                      ->orWhere('employee_code', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($e) {
                $eligibility = $this->circulationService->checkBorrowerEligibility(null, $e->id);
                return [
                    'id'         => $e->id,
                    'type'       => 'employee',
                    'name'       => $e->first_name . ' ' . $e->last_name,
                    'identifier' => $e->employee_code ?? ('EMP-' . $e->id),
                    'eligible'   => $eligibility['eligible'],
                    'errors'     => $eligibility['errors'],
                    'warnings'   => $eligibility['warnings'],
                ];
            });

        return response()->json([
            'results' => $students->concat($employees),
        ]);
    }
}

