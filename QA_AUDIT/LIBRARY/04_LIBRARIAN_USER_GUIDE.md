# Enterprise School Library Management System — Librarian User Guide

**Target Audience:** Head Librarian, Assistant Librarians, Circulation Staff, School Administrators  
**Application:** Erode Public School ERP (`EPS_SCHOOL_ERP`)  
**URL:** `https://eps.dasatech.in/admin/library`

---

## 1. Getting Started & Dashboard Overview

The **Enterprise Library Management System** centralizes all library workflows within the unified School ERP. All operations are accessible from the top navigation bar inside the Library portal.

### 1.1 Accessing the Library Module
1. Log in to the ERP at `https://eps.dasatech.in`.
2. On the main navigation sidebar, click **Library**.
3. The **Library Dashboard** loads, presenting real-time institutional metrics.

### 1.2 The 21 Real-Time Dashboard KPIs
The dashboard provides instant visibility into 21 critical metrics:
- **Total Titles:** Unique cataloged book titles.
- **Total Physical Copies:** Every barcode-tagged physical book in the library.
- **Available Copies:** Physical copies currently on shelves ready for loan.
- **Issued Copies:** Copies currently borrowed by students or staff.
- **Reserved Copies:** Books held for patrons awaiting pickup.
- **Overdue Books:** Loans past their due date requiring follow-up.
- **Damaged & Lost Books:** Quarantined items flagged during circulation.
- **Written-Off Copies:** Permanently retired or recycled copies.
- **Total Registered Students & Staff:** Patrons synchronized directly with the ERP.
- **Active Borrowers:** Patrons currently holding one or more library books.
- **Pending Returns:** All outstanding loans.
- **Today's Issued & Returned:** Daily circulation counter volume.
- **Pending & Collected Fines:** Real-time monetary balances.
- **Monthly Book Purchases:** Volume of books procured in the active calendar month.
- **Periodical & Newspaper Subscriptions:** Active serial counts.
- **Library Hardware Assets:** Scanners, kiosks, computers, and furniture tracked.

---

## 2. Book Master Catalog & Physical Copy Inventory

The system separates **Book Title Masters** from **Physical Book Copies**.

### 2.1 Adding a New Book Title
1. Click **Catalogue & Copies** in the top navigation bar.
2. Click the **+ Add Book** button.
3. Fill in the required bibliographic details:
   - **Title & Subtitle:** Exact book title.
   - **ISBN / ISSN:** International standard book number.
   - **Author & Co-Authors:** Primary and secondary contributors.
   - **Category:** Select from the 16 standard library categories (e.g., Mathematics, Fiction, STEM).
   - **Publication Year, Edition, Language, and Pages.**
   - **Price & Current Value:** Purchase price in INR (₹).
   - **Initial Copies to Generate:** Enter the quantity of physical books received (e.g., `5`).
4. Click **Save Book**.
5. The system automatically creates the Title record AND auto-generates 5 physical copy records with sequential accession numbers (`ACC-...-01` to `-05`) and unique barcodes.

### 2.2 Managing Physical Copies
1. On the Catalogue list, click the **Copies** pill next to any book title.
2. The copy inventory table displays:
   - **Accession Number** (e.g., `ACC-1789535105-01`).
   - **Barcode** (e.g., `BC-ACC178953510501`).
   - **Rack & Shelf Location** (e.g., `Rack CS-01 / Shelf S-02`).
   - **Condition** (Good, Minor Wear, Damaged, Quarantined).
   - **Circulation Status** (Available, Issued, Reserved, Lost, Disposed).
3. Use the **Print Barcode Label** button to generate thermal/sheet stickers for book spines.

---

## 3. Circulation Desk: Book Issue & Return

### 3.1 Issuing a Book to a Student or Staff Member
1. Click **Circulation Desk** in the navigation bar.
2. On the **Issue Book** tab:
   - **Step 1: Select Borrower:** Search by Student Name, Admission Number, or Staff Employee ID. The system validates real-time eligibility:
     - Enforces quota limits (configured in Settings, e.g., 2 books for students, 5 for staff).
     - Checks for overdue books and unpaid fines exceeding threshold limits.
     - *Manager Override:* If authorized, librarians can check "Manager Override" with mandatory reason.
   - **Step 2: Scan Book Copy:** Scan the physical book barcode or type the accession number.
   - **Step 3: Loan Period:** The system automatically calculates the Due Date based on school policy (e.g., 14 days for students, 30 days for staff).
3. Click **Confirm Issue**.
4. A printable **Book Issue Receipt** is generated with transaction timestamp and return instructions.

### 3.2 Returning a Book & Condition Inspection
1. On the Circulation Desk, switch to the **Return Book** tab.
2. Scan the barcode on the returned book copy. The active loan record is immediately identified.
3. **Condition Inspection:**
   - Select condition: **Good Condition**, **Minor Damage**, **Torn Pages**, **Water Damage**, or **Lost**.
   - If marked *Good Condition*, the copy is immediately returned to shelf availability.
   - If marked *Torn Pages* or *Water Damage*, the copy is automatically quarantined as `Damaged`, and damage fine fields appear.
   - If marked *Lost*, the lost penalty multiplier is calculated.
4. If the book is returned past the due date, the system automatically computes overdue fines according to school grace periods and holiday exclusions.
5. Click **Process Return**.
6. A printable **Return Receipt** is generated. If a fine is assessed, the patron can pay immediately or have the balance recorded on their account.

---

## 4. Fine Management & Authorized Waivers

### 4.1 Collecting Fine Payments at the Desk
1. Click **Settings & Fines** -> **Fine Management**.
2. Search for the student or staff borrower.
3. Click **Collect Fine**:
   - Choose **Payment Mode**: Cash Counter, UPI / QR, Card, or Fee Module.
   - Partial payments are supported (e.g., pay ₹50 towards a ₹100 fine).
   - Click **Save Payment**. An immutable payment receipt (`LFP-...`) is issued.

### 4.2 Authorized Fine Waivers
1. If approved by the Principal or authorized Administrator:
   - Enter the waiver amount (up to the remaining balance).
   - Enter the **Mandatory Waiver Reason** (e.g., "Medical emergency approved by Principal").
2. Click **Apply Fine Waiver**.
3. The transaction is permanently logged in the audit trail with the authorizer's user ID and timestamp.

---

## 5. Physical Spatial Location: Racks & Shelves

The system manages physical organization using the hierarchy:  
**Campus → Building → Floor → Room → Section → Rack → Shelf**.

### 5.1 Setting Up Racks & Shelves
1. Click **Racks & Shelves** on the navigation bar.
2. Click **+ Add Rack**:
   - Specify Building (e.g., Main Academic Block), Floor, Room, and Section.
   - Assign Rack Number (e.g., `RACK-MAT-01`) and capacity.
3. Inside each Rack, click **+ Add Shelf** (e.g., `Shelf S-01`, capacity 30 books).

### 5.2 Allocating Books to Shelves
1. When viewing any book copy, select the destination Rack and Shelf.
2. Alternatively, use **Bulk Shelf Allocation** to assign an entire box of books to a shelf in one click.
3. Every book's exact shelf location is displayed during catalog searches and patron inquiries.

---

## 6. Book Procurement & Purchasing

Follow the SAP-standard 3-way procurement workflow:

```
Purchase Order (PO) ──> Approval ──> Goods Receipt Note (GRN) ──> Inventory Injection
```

1. **Suppliers:** Register vendors with GST numbers, contact persons, and banking details.
2. **Purchase Orders:**
   - Create PO with ISBN, title, quantities, unit prices, and expected delivery date.
   - Submit for administrative approval.
3. **Goods Receipt Note (GRN):**
   - When shipment arrives at the library, create a GRN against the approved PO.
   - Enter supplier invoice number and verified quantities.
   - Click **Save & Inject into Inventory**. The system automatically creates catalog titles and barcodes for all received copies!

---

## 7. Periodicals & Newspaper Daily Check-In

1. Click **Periodicals & Papers**.
2. **Registry:** Add active magazines and daily newspapers with ISSN and publisher information.
3. **Daily Check-In:**
   - Every morning, click **Daily Check-In** for each newspaper (e.g., *The Hindu*, *Times of India*).
   - Enter issue number and date.
   - Tracks missing issues, late deliveries, and subscription renewal alerts (flags expiring subscriptions within 30 days).

---

## 8. Library Hardware & Asset Management

1. Click **Assets**.
2. Track all non-book physical assets:
   - Handheld barcode scanners, circulation desktop PCs, barcode label printers, study carrels, and bookshelves.
3. Log routine maintenance, service costs, warranty dates, and hardware depreciation.

---

## 9. Wastage, Damage & Disposal Audits

In accordance with enterprise institutional auditing standards:
1. **Damage Incident Reporting:** Log any torn pages, water exposure, or lost books with incident details and patron attribution.
2. **Write-Off Approval:** With Principal authorization, approve write-off and scrap disposal.
3. **Data Integrity Guarantee:** The physical copy row is marked `Disposed` / `Written-off` and **never permanently deleted**, preserving historical transaction accountability for lifetime audits.

---

## 10. Annual Stock Verification (Barcode Audit)

1. Click **Stock Audit**.
2. Click **+ Start New Verification Session** (e.g., "Academic Year 2026-27 Annual Audit").
3. Connect your handheld USB barcode scanner.
4. Walk along the shelves scanning each book spine. The system instantly sounds:
   - **Green chime:** Item verified and matched.
   - **Yellow chime:** Item misplaced on wrong shelf.
   - **Red alert:** Damaged copy or barcode mismatch.
5. At the end of the audit, generate the **Stock Reconciliation Report** highlighting missing vs. matched books.

