<?php

namespace App\Services\Library;

use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAsset;
use App\Models\Library\LibraryAuditLog;
use App\Models\Library\LibraryDamageReport;
use App\Models\Library\LibraryDisposalRecord;
use Exception;
use Illuminate\Support\Facades\DB;

class WastageService
{
    public function __construct(protected BookCopyService $copyService) {}

    /**
     * File a formal damage / loss incident report.
     */
    public function reportDamage(array $data): LibraryDamageReport
    {
        return DB::transaction(function () use ($data) {
            $reportNo = 'DAM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $report = LibraryDamageReport::create([
                'report_number'        => $reportNo,
                'book_copy_id'         => $data['book_copy_id'] ?? null,
                'asset_id'             => $data['asset_id'] ?? null,
                'incident_type'        => $data['incident_type'],
                'reported_by'          => auth()->id(),
                'borrower_student_id'  => $data['borrower_student_id'] ?? null,
                'borrower_employee_id' => $data['borrower_employee_id'] ?? null,
                'reported_date'        => $data['reported_date'] ?? now()->toDateString(),
                'damage_description'   => $data['damage_description'],
                'action_proposed'      => $data['action_proposed'] ?? 'repair',
                'repair_cost'          => $data['repair_cost'] ?? 0,
                'fine_charged'         => $data['fine_charged'] ?? 0,
                'status'               => 'pending_review',
            ]);

            // If it's a book copy, update condition & status
            if ($report->book_copy_id) {
                $copy = BookCopy::find($report->book_copy_id);
                if ($copy) {
                    $copy->update([
                        'condition' => $data['incident_type'],
                        'status'    => in_array($data['incident_type'], ['lost_by_borrower', 'lost_in_library']) ? 'lost' : 'damaged',
                    ]);
                    $this->copyService->syncBookTotals($copy->book);
                }
            }

            LibraryAuditLog::log('DAMAGE_REPORT_FILED', 'LibraryDamageReport', $report->id, null, $report->toArray(), "Filed damage report {$reportNo}");

            return $report;
        });
    }

    /**
     * Approve write-off and create irreversible disposal record (without deleting copy).
     */
    public function approveWriteOff(int $reportId, array $disposalData, int $approvedById): LibraryDisposalRecord
    {
        return DB::transaction(function () use ($reportId, $disposalData, $approvedById) {
            $report = LibraryDamageReport::lockForUpdate()->findOrFail($reportId);

            if ($report->status === 'disposed') {
                throw new Exception("This damage report has already been disposed and written off.");
            }

            $dispNo = 'DISP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $originalValue = 0.0;
            if ($report->book_copy_id) {
                $copy = BookCopy::with('book')->find($report->book_copy_id);
                $originalValue = (float) ($copy->cost_price ?? $copy->book?->purchase_price ?? 0);
                // Mark copy as disposed/written-off - NEVER permanently delete
                $copy->update([
                    'status'    => 'disposed',
                    'condition' => 'written_off',
                ]);
                $this->copyService->syncBookTotals($copy->book);
            } elseif ($report->asset_id) {
                $asset = LibraryAsset::find($report->asset_id);
                $originalValue = (float) ($asset->purchase_cost ?? 0);
                $asset->update(['status' => 'disposed']);
            }

            $disposal = LibraryDisposalRecord::create([
                'disposal_number'       => $dispNo,
                'damage_report_id'      => $report->id,
                'book_copy_id'          => $report->book_copy_id,
                'asset_id'              => $report->asset_id,
                'disposal_method'       => $disposalData['disposal_method'] ?? 'recycle_paper',
                'approved_by'           => $approvedById,
                'disposal_date'         => $disposalData['disposal_date'] ?? now()->toDateString(),
                'original_value'        => $originalValue,
                'scrap_value_recovered' => $disposalData['scrap_value_recovered'] ?? 0,
                'revenue_receipt_no'    => $disposalData['revenue_receipt_no'] ?? null,
                'justification'         => $disposalData['justification'],
                'witness_name'          => $disposalData['witness_name'] ?? null,
                'certificate_file_path' => $disposalData['certificate_file_path'] ?? null,
            ]);

            $report->update([
                'status'       => 'disposed',
                'reviewed_by'  => $approvedById,
                'review_notes' => $disposalData['justification'],
            ]);

            LibraryAuditLog::log('DISPOSAL_APPROVED', 'LibraryDisposalRecord', $disposal->id, null, [
                'disposal_number' => $dispNo,
                'original_value'  => $originalValue,
                'scrap_recovered' => $disposal->scrap_value_recovered,
            ], "Approved write-off and disposal #{$dispNo}");

            return $disposal;
        });
    }
}

