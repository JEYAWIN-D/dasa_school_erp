<?php

namespace App\Services\Library;

use App\Models\Library\LibraryAsset;
use App\Models\Library\LibraryAssetMaintenance;
use App\Models\Library\LibraryAuditLog;
use Exception;
use Illuminate\Support\Facades\DB;

class AssetManagementService
{
    public function createAsset(array $data): LibraryAsset
    {
        return DB::transaction(function () use ($data) {
            $code = $data['asset_code'] ?? ('AST-' . strtoupper(substr(uniqid(), -6)));

            $asset = LibraryAsset::create([
                'asset_code'     => $code,
                'name'           => $data['name'],
                'category'       => $data['category'],
                'brand_model'    => $data['brand_model'] ?? null,
                'serial_number'  => $data['serial_number'] ?? null,
                'purchase_date'  => $data['purchase_date'] ?? null,
                'purchase_cost'  => $data['purchase_cost'] ?? 0,
                'current_value'  => $data['current_value'] ?? ($data['purchase_cost'] ?? 0),
                'location_room'  => $data['location_room'] ?? 'Central Library',
                'status'         => 'working',
                'warranty_until' => $data['warranty_until'] ?? null,
                'supplier_id'    => $data['supplier_id'] ?? null,
                'remarks'        => $data['remarks'] ?? null,
            ]);

            LibraryAuditLog::log('ASSET_REGISTERED', 'LibraryAsset', $asset->id, null, $asset->toArray(), "Registered library asset '{$asset->name}' [{$asset->asset_code}]");

            return $asset;
        });
    }

    public function recordMaintenance(int $assetId, array $data): LibraryAssetMaintenance
    {
        return DB::transaction(function () use ($assetId, $data) {
            $asset = LibraryAsset::findOrFail($assetId);

            $maint = $asset->maintenanceRecords()->create([
                'maintenance_date'    => $data['maintenance_date'] ?? now()->toDateString(),
                'service_provider'    => $data['service_provider'] ?? null,
                'cost'                => $data['cost'] ?? 0,
                'issue_description'   => $data['issue_description'],
                'action_taken'        => $data['action_taken'] ?? null,
                'next_scheduled_date' => $data['next_scheduled_date'] ?? null,
                'status'              => $data['status'] ?? 'completed',
                'conducted_by'        => auth()->id(),
            ]);

            if (!empty($data['asset_status'])) {
                $asset->update(['status' => $data['asset_status']]);
            }

            LibraryAuditLog::log('ASSET_MAINTENANCE_LOGGED', 'LibraryAssetMaintenance', $maint->id, null, $maint->toArray(), "Conducted maintenance on asset '{$asset->name}' costing ₹{$maint->cost}");

            return $maint;
        });
    }

    public function getExpiringWarranties(int $days = 60)
    {
        return LibraryAsset::whereNotNull('warranty_until')
            ->whereBetween('warranty_until', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->orderBy('warranty_until')
            ->get();
    }
}

