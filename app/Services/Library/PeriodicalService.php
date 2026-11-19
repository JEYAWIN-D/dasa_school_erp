<?php

namespace App\Services\Library;

use App\Models\Library\LibraryAuditLog;
use App\Models\Library\LibraryPeriodical;
use App\Models\Library\LibraryPeriodicalReceipt;
use App\Models\Library\LibrarySubscription;
use Exception;
use Illuminate\Support\Facades\DB;

class PeriodicalService
{
    public function createPeriodical(array $data): LibraryPeriodical
    {
        return DB::transaction(function () use ($data) {
            $periodical = LibraryPeriodical::create([
                'title'           => $data['title'],
                'periodical_type' => $data['periodical_type'] ?? 'magazine',
                'frequency'       => $data['frequency'] ?? 'monthly',
                'issn'            => $data['issn'] ?? null,
                'publisher'       => $data['publisher'] ?? null,
                'language'        => $data['language'] ?? 'English',
                'rack_id'         => $data['rack_id'] ?? null,
                'shelf_id'        => $data['shelf_id'] ?? null,
                'is_active'       => $data['is_active'] ?? true,
                'remarks'         => $data['remarks'] ?? null,
            ]);

            LibraryAuditLog::log('PERIODICAL_CREATED', 'LibraryPeriodical', $periodical->id, null, $periodical->toArray(), "Created periodical '{$periodical->title}'");

            return $periodical;
        });
    }

    public function createSubscription(array $data): LibrarySubscription
    {
        return DB::transaction(function () use ($data) {
            $sub = LibrarySubscription::create([
                'periodical_id'       => $data['periodical_id'],
                'supplier_id'         => $data['supplier_id'] ?? null,
                'subscription_number' => $data['subscription_number'] ?? ('SUB-' . strtoupper(substr(uniqid(), -6))),
                'start_date'          => $data['start_date'],
                'end_date'            => $data['end_date'],
                'cost'                => $data['cost'] ?? 0,
                'copies_per_issue'    => $data['copies_per_issue'] ?? 1,
                'auto_renew'          => $data['auto_renew'] ?? false,
                'status'              => 'active',
                'notes'               => $data['notes'] ?? null,
            ]);

            LibraryAuditLog::log('SUBSCRIPTION_CREATED', 'LibrarySubscription', $sub->id, null, $sub->toArray(), "Subscribed to periodical #{$sub->periodical_id} until {$sub->end_date}");

            return $sub;
        });
    }

    public function recordDailyReceipt(array $data): LibraryPeriodicalReceipt
    {
        return DB::transaction(function () use ($data) {
            $receipt = LibraryPeriodicalReceipt::create([
                'periodical_id'   => $data['periodical_id'],
                'subscription_id' => $data['subscription_id'] ?? null,
                'issue_date'      => $data['issue_date'],
                'volume_no'       => $data['volume_no'] ?? null,
                'issue_no'        => $data['issue_no'] ?? null,
                'received_date'   => $data['received_date'] ?? now()->toDateString(),
                'copies_received' => $data['copies_received'] ?? 1,
                'received_by'     => auth()->id(),
                'condition'       => $data['condition'] ?? 'good',
                'remarks'         => $data['remarks'] ?? null,
            ]);

            LibraryAuditLog::log('PERIODICAL_RECEIPT_LOGGED', 'LibraryPeriodicalReceipt', $receipt->id, null, $receipt->toArray(), "Received issue of periodical #{$receipt->periodical_id} for {$receipt->issue_date}");

            return $receipt;
        });
    }

    public function getExpiringSubscriptions(int $days = 30)
    {
        return LibrarySubscription::with(['periodical', 'supplier'])
            ->where('status', 'active')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->orderBy('end_date')
            ->get();
    }
}

