# Centralized School Identity & Branding Audit

**Audit Date:** 05 November 2026  
**Subsystem:** Institutional Identity, Print Branding & Watermarking Architecture

---

## 1. Architectural Model & Data Pipeline

To ensure absolute institutional consistency across every printed document, all school identity attributes are centralized within a singleton database model: `\App\Models\SchoolSetting`.

```
┌────────────────────────────────────────────────────────┐
│                   SchoolSetting Model                  │
├────────────────────────────────────────────────────────┤
│  • school_name             • affiliation_no            │
│  • tagline                 • board (CBSE / State)      │
│  • address, city, district • school_code               │
│  • state, pincode          • udise_no                  │
│  • phone, email, website   • recognition_no            │
│  • logo_path, crest_path   • print_watermark_enabled   │
│  • primary_color           • print_watermark_text      │
└──────────────────────────┬─────────────────────────────┘
                           │
             ┌─────────────┴─────────────┐
             ▼                           ▼
┌────────────────────────┐  ┌────────────────────────┐
│  DomPDF Partials       │  │  Browser Components    │
│  (Base64 Embedded)     │  │  (Responsive CSS)      │
├────────────────────────┤  ├────────────────────────┤
│ • pdf.partials.header  │  │ • x-print.header       │
│ • pdf.partials.footer  │  │ • x-print.footer       │
│ • pdf.partials.water-  │  │ • x-print.watermark    │
│   mark                 │  │ • x-print.signatures   │
│ • pdf.partials.signa-  │  │ • x-print.controls     │
│   tures                │  │                        │
└────────────────────────┘  └────────────────────────┘
```

---

## 2. Database Schema Extension

A dedicated migration `2026_11_05_120000_add_print_identity_fields_to_school_settings.php` was executed to support enterprise school accreditation requirements:

| Column Name | Type | Modifiers | Functional Purpose |
|-------------|------|-----------|--------------------|
| `udise_no` | VARCHAR(50) | Nullable | Unified District Information System for Education code. |
| `recognition_no` | VARCHAR(100) | Nullable | Government State Directorate Recognition/RTE certificate number. |
| `district` | VARCHAR(100) | Nullable | Administrative district for postal and jurisdiction clarity. |
| `print_watermark_enabled` | BOOLEAN | Default `true` | Global administrative toggle for background watermarking. |
| `print_watermark_text` | VARCHAR(255) | Nullable | Custom watermark string (falls back to `school_name`). |

---

## 3. Eloquent Model Accessors & Helpers

In `app/Models/SchoolSetting.php`, four optimized accessors provide normalized, presentation-ready strings without empty separators:

1. **`getFullAddressAttribute()`**:
   ```php
   // Concatenates address, city, district, state, and pincode:
   "No. 420, Vakkil Thottam, Manickampalayam, Erode, Tamil Nadu, PIN: 638004"
   ```
2. **`getAffiliationLineAttribute()`**:
   ```php
   // Formats board, affiliation number, school code, UDISE, and recognition:
   "Affiliated to CBSE | Affiliation No: 1931585 | School Code: 12345 | UDISE: 33100100101"
   ```
3. **`getContactLineAttribute()`**:
   ```php
   // Combines phone, official email, and website:
   "Tel: +91 93677 54654 | Email: info@erodepublicschool.edu.in | Web: www.erodepublicschool.edu.in"
   ```
4. **`getWatermarkTextAttribute()`**:
   ```php
   // Returns custom watermark text or defaults to school name:
   return $this->print_watermark_text ?: ($this->school_name ?: 'ERODE PUBLIC SCHOOL');
   ```

---

## 4. Centralized Letterhead Design Standard

All printable documents adhere to a strict dual-divider official school letterhead:

1. **Header Layout (HTML Table Format for 100% Engine Stability):**
   - **Left Column (15%):** Primary institutional crest/logo rendered at 55px height.
   - **Center Column (70%):**
     - Primary line: Official School Name in 16pt bold uppercase with institution's brand color.
     - Secondary line: Official affiliation line (Board, Affiliation No, School Code, UDISE).
     - Tertiary line: Full registered postal address with PIN.
     - Fourth line: Contact particulars (Phone, Email, Web).
   - **Right Column (15%):** Optional secondary crest, ISO accreditation badge, or QR verification seal.
   - **Divider:** Dual-line rule (primary accent color 2.5px solid top border, 1px solid secondary border).

2. **Subdued Security Watermark Standard:**
   - **Fixed Positioning:** `top: 34%; left: 5%; width: 90%;`
   - **Rotation:** `-30deg` angle.
   - **Opacity:** strictly calibrated at `0.04` to `0.05` (`#0f172a` slate).
   - **Z-Index:** `-1000` with `pointer-events: none;` to guarantee text, student photos, and handwritten signatures remain 100% legible.

3. **Dynamic Footer Standard:**
   - Computer-generated document statement with ISO generation timestamp (`d M Y, h:i A`).
   - Software verification reference and system URL.
   - Native DomPDF dynamic two-pass page numbering: `Page $PAGE_NUM of $PAGE_COUNT`.

4. **Multi-Mode Signature Verification Section:**
   - **`standard` Mode:** Class Teacher / Prepared By / Principal.
   - **`financial` Mode:** Cashier / Accountant / Principal.
   - **`academic` Mode:** Subject Teacher / Exam Coordinator / Principal.
   - **`certificate` Mode:** Verified By / Administrative Officer / Principal & Institutional Seal.

