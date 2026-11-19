<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibraryAsset;
use App\Models\Library\LibrarySupplier;
use App\Services\Library\AssetManagementService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct(protected AssetManagementService $assetService) {}

    public function index(Request $request)
    {
        $assets = LibraryAsset::with(['supplier', 'maintenanceRecords.conductor'])
            ->when($request->category, fn($q, $v) => $q->where('category', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderBy('name')
            ->paginate(15);

        $suppliers = LibrarySupplier::where('is_active', true)->orderBy('name')->get();
        $expiringWarranties = $this->assetService->getExpiringWarranties(60);

        return view('library.assets.index', compact('assets', 'suppliers', 'expiringWarranties'));
    }

    public function storeAsset(Request $request)
    {
        $data = $request->validate([
            'asset_code'     => 'nullable|string|max:50|unique:library_assets,asset_code',
            'name'           => 'required|string|max:150',
            'category'       => 'required|string|in:furniture,computer,scanner,printer,projector,appliance,other',
            'brand_model'    => 'nullable|string|max:100',
            'serial_number'  => 'nullable|string|max:100',
            'purchase_date'  => 'nullable|date',
            'purchase_cost'  => 'nullable|numeric|min:0',
            'current_value'  => 'nullable|numeric|min:0',
            'location_room'  => 'nullable|string|max:100',
            'warranty_until' => 'nullable|date',
            'supplier_id'    => 'nullable|exists:library_suppliers,id',
            'remarks'        => 'nullable|string',
        ]);

        $asset = $this->assetService->createAsset($data);

        return redirect()->route('library.assets.index')->with('success', "Asset {$asset->name} registered.");
    }

    public function storeMaintenance(Request $request, int $assetId)
    {
        $data = $request->validate([
            'maintenance_date'    => 'required|date',
            'service_provider'    => 'nullable|string|max:150',
            'cost'                => 'required|numeric|min:0',
            'issue_description'   => 'required|string',
            'action_taken'        => 'nullable|string',
            'next_scheduled_date' => 'nullable|date',
            'asset_status'        => 'nullable|in:working,under_maintenance,damaged,scrapped',
        ]);

        $this->assetService->recordMaintenance($assetId, $data);

        return redirect()->route('library.assets.index')->with('success', "Maintenance record logged.");
    }
}

