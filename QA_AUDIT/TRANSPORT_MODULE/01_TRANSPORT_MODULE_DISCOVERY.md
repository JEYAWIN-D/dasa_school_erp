# 01. Transport Module Discovery & Architecture Audit

## 1. Executive Summary
The Transport Module in the School ERP provides fleet tracking, bus stop sequencing, driver/attendant crew management, passenger manifest generation, student transport fee realization, and regulatory compliance oversight. Prior to this enhancement, vehicle records and stops existed in the database, but administrative staff were unable to print comprehensive van dossiers, sequential stops with timings/fares, crew credentials, or fleet registers.

## 2. Technical Stack & Environment
- **Framework:** Laravel 12.62.0 / PHP 8.2.12
- **Database Engine:** PostgreSQL 16 (Remote Host: `187.127.150.113:5432`, Database: `school_erp`)
- **Printing Architecture:** Dual-engine (Interactive CSS `@media print` with centralized `<x-print.controls>`, `<x-print.header>`, `<x-print.watermark>` + DomPDF server-side binary rendering)
- **Role & Permission Guard:** Spatie Laravel-Permission (`manage transport`, `view transport`, `admin`, `super-admin`)

## 3. Core Database Entities & Schema Mapping
| Entity | Model Class | DB Table | Primary Attributes & Relationships |
|---|---|---|---|
| **Vehicles** | `App\Models\Vehicle` | `vehicles` | `id`, `vehicle_number`, `make`, `model`, `capacity`, `driver_name`, `driver_mobile`, `driver_license`, `fitness_expiry`, `insurance_expiry`, `route_id` |
| **Routes** | `App\Models\TransportRoute` | `transport_routes` | `id`, `route_name`, `route_code`, `origin`, `destination`, `distance_km`, `vehicle_id` |
| **Bus Stops** | `App\Models\TransportStop` | `transport_stops` | `id`, `route_id`, `vehicle_id`, `stop_name`, `landmark`, `stop_order`, `pickup_time`, `drop_time`, `distance_km`, `fare` |
| **Attendants** | `App\Models\TransportAttendant` | `transport_attendants` | `id`, `name`, `mobile`, `aadhaar_no`, `emergency_contact`, `vehicle_id`, `route_id` |
| **Allotments** | `App\Models\TransportAllotment` | `transport_allotments` | `id`, `student_id`, `vehicle_id`, `route_id`, `stop_id`, `drop_stop_id`, `is_active` |

## 4. Active Fleet Inventory Audited
- **Total Active Fleet:** 5 Vans (`TN-33-AX-1001` through `TN-33-AX-1005`)
- **Assigned Corridors:**
  1. `TN-33-AX-1001` (VAN I) &rarr; Perundurai Corridor (13 Stops)
  2. `TN-33-AX-1002` (VAN II) &rarr; Bhavani Corridor (11 Stops)
  3. `TN-33-AX-1003` (VAN III) &rarr; Gobichettipalayam Corridor (14 Stops)
  4. `TN-33-AX-1004` (VAN IV) &rarr; Modakkurichi Corridor (11 Stops)
  5. `TN-33-AX-1005` (VAN V) &rarr; Chennimalai Corridor (12 Stops)
- **Total Master Stops:** 61 Sequential Stops
- **Total Designated Drivers:** 5 Professional Drivers (DL verified)
- **Total Attendants / Nannies:** 5 Female Child Escorts (Aadhaar verified)

