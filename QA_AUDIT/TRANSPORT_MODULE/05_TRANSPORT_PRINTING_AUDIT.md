# 05. Transport Printing System Architecture & Document Audit

## 1. Unified Print Template Architecture
Every printable document in the Transport Module adheres to the institutional design guidelines:
1. **Interactive Print Controls:** `<x-print.controls>` provides paper size selection (A4 / Legal / Letter), orientation toggling (Portrait / Landscape), direct print triggering, and navigation back to the operational module.
2. **Centralized Institutional Header:** `<x-print.header>` dynamically pulls the school's primary logo, secondary emblem/crest, primary institutional maroon brand color (`#8C2826`), school name, CBSE/State Board affiliation line, full postal address, phone, and official web address.
3. **Subdued Watermark:** `<x-print.watermark>` embeds an authentic, low-opacity diagonal watermark behind document content to protect against unauthorized document duplication.
4. **Official Signatures Block:** All transport documents include multi-party authorization signature lines (Driver, Attendant, Transport In-Charge, Principal / Authorized Institutional Seal).

## 2. Zero-Margin Browser Header Suppression Standard
To ensure browser headers (e.g., `127.0.0.1:8000/...`, `https://eps.dasatech.in/...`, and date timestamps) never bleed into printed certificates, registers, or dossiers:
```css
@page {
  size: A4 portrait; /* or A4 landscape */
  margin: 0 !important;
}

body {
  margin: 0 auto !important;
  padding: 10mm 12mm 10mm 18mm !important; /* Generous 18mm left margin for binder punching */
  box-sizing: border-box !important;
  background: #ffffff !important;
}

@media print {
  .no-print {
    display: none !important;
  }
}
```

## 3. Register of Transport Print & PDF Endpoints
| Document Type | Web Print URL | PDF Stream URL | Target Orientation | Key Content Sections |
|---|---|---|---|---|
| **Van Dossier** | `/transport/vehicles/{id}/print` | `/transport/vehicles/{id}/pdf` | A4 Portrait | Specs, Fitness/Insurance, Driver, Attendant, Sequential Stops, Student Manifest, Signatures |
| **Fleet Master Register** | `/transport/vehicles/print/all` | `/transport/vehicles/pdf/all` | A4 Landscape | Complete fleet table, capacity, compliance dates, status |
| **Route Schedule** | `/transport/routes/{id}/print` | `/transport/routes/{id}/pdf` | A4 Portrait | Corridor, assigned van, sequential stop timetable, student counts |
| **Route Directory** | `/transport/routes/print/all` | N/A | A4 Landscape | All route corridors, distance, assigned vans & crews |
| **Bus Stop Directory** | `/transport/stops/print` | `/transport/stops/pdf` | A4 Portrait | 61 stops with pickup/drop times, fares, van labels |
| **Driver Directory** | `/transport/drivers/print` | `/transport/drivers/pdf` | A4 Portrait | Drivers, contact mobile, license number, license expiry, van |
| **Attendant Directory**| `/transport/attendants/print` | `/transport/attendants/pdf` | A4 Portrait | Nannies, mobile, Aadhaar verification, van links |
| **Daily Duty Sheet** | `/transport/duty-sheet/print` | `/transport/duty-sheet/pdf` | A4 Landscape | Pre-trip safety checklist, odometer/fuel log, arrival time table |
| **Compliance Matrix** | `/transport/compliance/print` | `/transport/compliance/pdf` | A4 Landscape | Fitness, Insurance, Permit, PUC, Road Tax expiry matrix |
| **Emergency Safety** | `/transport/emergency/print` | N/A | A4 Portrait | Police, hospital, trauma center, internal crisis desk |

