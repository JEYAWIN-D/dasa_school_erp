# School ERP Print System Discovery & Touchpoint Register

**Audit Date:** 05 November 2026  
**Auditor:** Quality Engineering & Systems Architecture  
**Scope:** Complete Codebase Scan across Frontend, Backend Controllers, DomPDF Templates, and Browser `@media print` Views.

---

## 1. Executive Summary

A comprehensive, deep scan of the EPS School ERP codebase was conducted to identify every screen, report, letter, certificate, schedule, ledger, and receipt capable of being printed or downloaded as a PDF. Prior to this initiative, printing was fragmented across distinct engines with inconsistent branding, hardcoded dummy metadata, missing school credentials, and layout distortions caused by unsupported CSS Flexbox/Grid in DomPDF.

This discovery register catalogues **58 unique printable templates and views** across 10 functional subsystems.

---

## 2. Discovered Print & PDF Touchpoint Inventory

### A. Academic & Curriculum Management
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 1 | Master Class Timetable | `pdf/class-timetable.blade.php` | DomPDF | A4 | Landscape | `ClassTimetableController@downloadPdf` |
| 2 | General Timetable Grid | `pdf/timetable.blade.php` | DomPDF | A4 | Landscape | `TimetableController@downloadPdf` |
| 3 | Faculty Teaching Timetable | `staff-timetable/pdf.blade.php` | Dual (Browser/PDF) | A4 | Landscape | `StaffTimetableController@print` |
| 4 | Annual Academic Calendar | `pdf/academic-calendar.blade.php` | DomPDF | A4 | Landscape | `AcademicCalendarController@pdf` |
| 5 | Pedagogical Lesson Plan | `pdf/lesson-plan.blade.php` | DomPDF | A4 | Portrait | `LessonPlanController@pdf` |
| 6 | Curriculum Syllabus Matrix | `academics/syllabus-print.blade.php` | Browser | A4 | Portrait | `AcademicController@syllabusPrint` |

### B. Admissions & Student Onboarding
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 7 | Official CBSE Admission Form (LKG–X) | `admissions/print-application-form.blade.php` | Browser | A4 (Multi-page) | Portrait | `AdmissionController@printApplication` |
| 8 | Grade XI Application Form | `admissions/print-application-form.blade.php` | Browser | A4 (Multi-page) | Portrait | `AdmissionController@printApplication` |
| 9 | Admission Enquiry Form | `admissions/print-application-form.blade.php` | Browser | A4 | Portrait | `AdmissionController@printApplication` |
| 10 | Formal Admission Confirmation Letter | `pdf/admission-confirmation.blade.php` | DomPDF | A4 | Portrait | `AdmissionController@confirmationLetter` |
| 11 | Student Application Master File | `pdf/application-form.blade.php` | DomPDF | A4 | Portrait | `AdmissionController@applicationPdf` |
| 12 | Official Fee Structure Schedule | `admissions/print-fee-structure.blade.php` | Browser | A4 | Portrait | `AdmissionController@printFeeStructure` |
| 13 | Admission Analytics & Funnel Report | `pdf/admission-analytics.blade.php` | DomPDF | A4 | Portrait | `AdmissionController@analyticsPdf` |

### C. Student Demographics & Records
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 14 | General Admission Register | `reports/general-register.blade.php` | Browser | A4 | Landscape | `ReportController@generalRegister` |
| 15 | Monthly Attendance Register (Web) | `reports/attendance-register.blade.php` | Browser | A4 | Landscape | `ReportController@attendanceRegister` |
| 16 | Monthly Attendance Register (PDF) | `pdf/attendance-register.blade.php` | DomPDF | A4 | Landscape | `AttendanceController@exportPdf` |
| 17 | Student Cumulative Profile Dossier | `students/print-profile.blade.php` | Browser | A4 | Portrait | `StudentController@printProfile` |
| 18 | Roll Number & Attendance Roster | `students/print-roll-numbers.blade.php` | Browser | A4 | Portrait | `StudentController@printRollNumbers` |
| 19 | Official Transfer Certificate (TC) | `pdf/tc.blade.php` | DomPDF | A4 | Portrait | `StudentController@generateTc` |
| 20 | Transfer Certificate (TC) Register | `reports/tc-register.blade.php` | Browser | A4 | Landscape | `ReportController@tcRegister` |

### D. Examinations & Grading
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 21 | Examination Hall Ticket (Admin) | `pdf/hall-ticket.blade.php` | DomPDF | A4 | Portrait | `ExamController@hallTicketPdf` |
| 22 | Student Progress Report Card | `pdf/report-card.blade.php` | DomPDF | A4 | Portrait | `ExamController@reportCardPdf` |
| 23 | Bulk Class Report Cards | `pdf/bulk-report-cards.blade.php` | DomPDF | A4 (Multi-page) | Portrait | `ExamController@bulkReportCards` |
| 24 | Examination Tabulation Register | `pdf/tabulation.blade.php` | DomPDF | A4 | Landscape | `ExamController@tabulationPdf` |
| 25 | Class Result Analysis & Summary | `pdf/class-result-summary.blade.php` | DomPDF | A4 | Portrait | `ExamController@resultSummaryPdf` |
| 26 | Comparative Academic Cross-Analysis | `pdf/comparative-analysis.blade.php` | DomPDF | A4 | Landscape | `ExamController@comparativePdf` |
| 27 | Remedial & Supplementary Register | `pdf/failed-students.blade.php` | DomPDF | A4 | Portrait | `ExamController@failedStudentsPdf` |
| 28 | Assembled Examination Question Paper | `pdf/assembled-question-paper.blade.php`| DomPDF | A4 | Portrait | `QuestionPaperController@assembledPdf` |

### E. Finance, Accounts & Fee Collection
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 29 | Official Student Fee Receipt (Admin) | `fees/receipt.blade.php` | Browser | A4 | Portrait | `FeeController@receipt` |
| 30 | PDF Fee Receipt & Tax Invoice | `pdf/receipt.blade.php` | DomPDF | A4 | Portrait | `FeeController@receiptPdf` |
| 31 | Student Account Fee Statement | `fees/statement.blade.php` | Browser | A4 | Portrait | `FeeController@statement` |
| 32 | Fee Collection Daily Register | `reports/fee-collection-register.blade.php` | Browser | A4 | Landscape | `ReportController@feeCollection` |
| 33 | Late Fee & Fine Collection Register | `fees/fine-report.blade.php` | Browser | A4 | Landscape | `FeeController@fineReport` |
| 34 | Fee Outstanding Demand Notice | `pdf/demand-notice.blade.php` | DomPDF | A4 | Portrait | `FeeController@demandNoticePdf` |
| 35 | Daily Cash Daybook (Web Print) | `accounts/daybook.blade.php` | Browser | A4 | Landscape | `AccountController@daybook` |
| 36 | Daily Cash Daybook (PDF) | `pdf/daybook.blade.php` | DomPDF | A4 | Landscape | `AccountController@daybookPdf` |

### F. HR, Staff & Payroll Management
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 37 | Staff Monthly Payslip | `pdf/payslip.blade.php` | DomPDF | A4 | Portrait | `PayrollController@payslipPdf` |
| 38 | Bulk Monthly Staff Payslips | `pdf/bulk-payslips.blade.php` | DomPDF | A4 (Multi-page) | Portrait | `PayrollController@bulkPayslips` |
| 39 | Monthly Salary Register | `pdf/salary-register.blade.php` | DomPDF | A4 | Landscape | `PayrollController@salaryRegisterPdf` |
| 40 | Statutory Compliance Challan (EPF/ESI)| `pdf/statutory-challan.blade.php` | DomPDF | A4 | Landscape | `PayrollController@statutoryPdf` |
| 41 | Leave Encashment Statement | `pdf/leave-encashment-report.blade.php`| DomPDF | A4 | Portrait | `LeaveController@encashmentPdf` |
| 42 | Annual Form 16 (TDS Certificate) | `pdf/form-16.blade.php` | DomPDF | A4 | Portrait | `PayrollController@form16Pdf` |
| 43 | Formal Staff Appointment Order | `pdf/appointment-letter.blade.php` | DomPDF | A4 | Portrait | `StaffController@appointmentPdf` |
| 44 | Service Experience Certificate | `pdf/experience-certificate.blade.php`| DomPDF | A4 | Portrait | `StaffController@experiencePdf` |
| 45 | Official Relieving Order | `pdf/relieving-letter.blade.php` | DomPDF | A4 | Portrait | `StaffController@relievingPdf` |
| 46 | No Objection Certificate (NOC) | `pdf/noc-letter.blade.php` | DomPDF | A4 | Portrait | `StaffController@nocPdf` |
| 47 | Staff Salary Increment Order | `pdf/increment-letter.blade.php` | DomPDF | A4 | Portrait | `StaffController@incrementPdf` |
| 48 | Increment History Audit Ledger | `pdf/increment-history.blade.php` | DomPDF | A4 | Landscape | `StaffController@incrementHistoryPdf` |
| 49 | Attendance Shortage Formal Notice | `pdf/shortage-letter.blade.php` | DomPDF | A4 | Portrait | `StaffController@shortagePdf` |
| 50 | Formal Disciplinary Warning Letter | `pdf/warning-letter.blade.php` | DomPDF | A4 | Portrait | `StaffController@warningPdf` |

### G. Student & Parent Self-Service Portals
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 51 | Portal Fee Account Statement | `portal/pdf/fee-receipt.blade.php` | DomPDF | A4 | Portrait | `PortalController@studentFeePdf` |
| 52 | Portal Payment Acknowledgment | `portal/pdf/payment-receipt.blade.php` | DomPDF | A4 | Portrait | `PortalController@studentPaymentReceipt`|
| 53 | Portal Student Progress Report Card | `portal/pdf/report-card.blade.php` | DomPDF | A4 | Portrait | `PortalController@studentReportPdf` |
| 54 | Portal Examination Hall Ticket | `portal/pdf/hall-ticket.blade.php` | DomPDF | A4 | Portrait | `PortalController@studentHallTicket` |

### H. Transport & Hostel Services
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 55 | Student Transport Passenger Manifest| `transport/pdf-students.blade.php` | DomPDF | A4 | Landscape | `TransportController@studentsPdf` |
| 56 | Fleet Transport Operations Roster | `pdf/transport-roster.blade.php` | DomPDF | A4 | Landscape | `TransportController@rosterPdf` |
| 57 | Vehicle Fuel Consumption Log | `pdf/transport-fuel-summary.blade.php`| DomPDF | A4 | Portrait | `TransportController@fuelPdf` |
| 58 | Fleet Maintenance Cost Audit | `pdf/transport-maintenance-cost.blade.php`| DomPDF | A4 | Portrait | `TransportController@maintenancePdf`|
| 59 | Hostel Resident Allotment Order | `pdf/room-allotment-letter.blade.php`| DomPDF | A4 | Portrait | `HostelController@allotmentLetterPdf`|
| 60 | Hostel Gate Pass (Outpass Slip) | `pdf/hostel-gate-pass.blade.php` | DomPDF | A5 | Portrait | `HostelController@gatePassPdf` |
| 61 | Hostel Gate Pass Movement Register | `pdf/hostel-gate-pass-register.blade.php`| DomPDF| A4 | Landscape | `HostelController@gatePassRegisterPdf`|
| 62 | Hostel Occupancy Audit Register | `pdf/hostel-occupancy-report.blade.php`| DomPDF | A4 | Landscape | `HostelController@occupancyReportPdf`|

### I. Inventory & Library
| # | Document / Screen | Blade Template Path | Engine | Default Page Size | Orientation | Controller / Route |
|---|-------------------|---------------------|--------|-------------------|-------------|--------------------|
| 63 | Official Purchase Order (PO) | `inventory/po-print.blade.php` | Browser | A4 | Portrait | `InventoryController@poPrint` |
| 64 | Official Purchase Requisition (PR) | `inventory/requisition-print.blade.php`| Browser| A4 | Portrait | `InventoryController@requisitionPrint`|
| 65 | Stock Valuation & Balance Register | `inventory/stock-report.blade.php` | Browser | A4 | Landscape | `InventoryController@stockReport` |
| 66 | Library Circulation Issue/Return Slip| `pdf/library-receipt.blade.php` | DomPDF | Thermal / A5 | Portrait | `LibraryController@receiptPdf` |
| 67 | Single Book Spine QR Barcode Label | `pdf/book-qr-label.blade.php` | DomPDF | 60mm × 35mm | Landscape | `LibraryController@bookQrLabel` |
| 68 | Bulk Sheet Book Spine QR Labels | `pdf/book-qr-labels-bulk.blade.php` | DomPDF | A4 (Sheet 3xN) | Portrait | `LibraryController@bulkBookQrLabels` |

---

## 3. Technology Stack & Rendering Mechanisms

1. **DomPDF Backend Pipeline:**
   - Package: `barryvdh/laravel-dompdf` (DomPDF 2.x).
   - Execution context: Headless HTML-to-PDF rendering on server.
   - Core constraint: Zero CSS Grid or Flexbox support. Strict reliance on HTML tables with explicit pixel/percentage widths and collapsed borders.
   - Image resolution: Base64 data URIs (`data:image/png;base64,...`) to bypass local filesystem / sandboxed network boundary checks.

2. **Browser Print Pipeline (`window.print()`):**
   - Execution context: Client browser rendering engine.
   - Styling: Tailwind CSS utility classes augmented with `@media print` directives in `layouts/app.blade.php`.
   - Print chrome: Interactive, fixed top floating control bar (`<x-print.controls>`) allowing paper size and orientation switching, hidden automatically during physical spooling.

