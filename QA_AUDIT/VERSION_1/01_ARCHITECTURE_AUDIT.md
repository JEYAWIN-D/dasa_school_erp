# ENTERPRISE QUALITY ASSURANCE AUDIT
## 01. ARCHITECTURE AUDIT & APPLICATION TOPOLOGY MAP
**Audit Version:** V1.0  
**Application:** Erode Public School ERP (DasaTech EduERP v2.6 Enterprise)  
**Execution Timestamp:** 2026-11-09  
**Audit Standard:** Enterprise Software Engineering (OWASP, ISO/IEC 25010)

---

### 1. Executive Summary of Application Architecture

The system is an enterprise-scale, multi-tier Educational Resource Planning (ERP) platform architected specifically for CBSE Senior Secondary institutional governance. It provides unified multi-stakeholder management encompassing Executive Leadership, Academics, Admissions, Human Resources & Payroll, Examinations & Digital Gradebook, Fee Billing, Transport Fleet Logistics, Library, Hostel, and Warehouse Supply Operations.

```
+-----------------------------------------------------------------------------------+
|                            CLIENT / PRESENTATION TIER                             |
|  - Modern Blade Server-Rendered Views with Alpine.js Reactive Island Architecture |
|  - Tailwind CSS Engine (Vite 7.0 Bundling)                                       |
|  - Responsive Viewport Adapters (Mobile 375px -> 4K Desktop 2560px)               |
+-----------------------------------------------------------------------------------+
                                        |  HTTP/HTTPS Requests (CSRF Protected)
                                        v
+-----------------------------------------------------------------------------------+
|                        APPLICATION / BUSINESS LOGIC TIER                          |
|  - PHP 8.2.35 runtime on Laravel 12.62.0 Enterprise Framework                     |
|  - 55 Specialized HTTP Controllers across Admin, Public, and Auth Domains        |
|  - 1,243 Registered Routing Endpoints (Web & REST API)                            |
|  - Spatie Laravel-Permission v6.25 (23 Distinct Hierarchical Institutional Roles) |
|  - Secure Session & Token Management, Hash-Verified Passwords (Argon2id/Bcrypt)   |
|  - Middleware Pipeline (Auth, Scoping, Security Headers, Rate Limiting)          |
+-----------------------------------------------------------------------------------+
                                        |  Eloquent ORM / Query Builder
                                        v
+-----------------------------------------------------------------------------------+
|                            DATA / PERSISTENCE TIER                                |
|  - PostgreSQL Enterprise Relational Database Engine                               |
|  - 263 Physical Tables across Relational Schemas                                  |
|  - 201 Domain Eloquent Models with Custom Type Casts                              |
|  - Database-Enforced Foreign Keys, Unique Indexes, and Soft-Deletes               |
|  - Local / Storage-Linked File System with Resilient Token Protection             |
+-----------------------------------------------------------------------------------+
```

---

### 2. Discovered Technology Stack

| Layer | Technology | Version | Purpose |
| :--- | :--- | :--- | :--- |
| **Backend Language** | PHP (Visual C++ 2019 x64) | 8.2.35 | Server-side execution runtime |
| **Backend Framework** | Laravel Framework | 12.62.0 | Core MVC architecture, routing, ORM, queues |
| **Database Engine** | PostgreSQL (`school_erp`) | Enterprise | Relational transactional persistence |
| **Frontend Templates** | Blade Templating Engine | Built-in | Server-side rendered component layouts |
| **Client Reactivity** | Alpine.js + Plugins | 3.15.12 | Lightweight reactive state, modals, interactive UI |
| **Styling Engine** | Tailwind CSS | 3.4.19 / 4.0 | Utility-first responsive design system |
| **Asset Bundler** | Vite + Laravel Vite Plugin | 7.0.7 / 2.0.0 | Hot-module replacement & CSS/JS compiling |
| **Role-Based Access** | Spatie Laravel-Permission | 6.25 | 23-Role permissions and access control |
| **PDF Generation** | DomPDF (`barryvdh/laravel-dompdf`) | 3.1 | Report cards, TC, hall tickets, duty sheets |
| **Spreadsheets** | Laravel Excel (`maatwebsite/excel`)| 3.1 | Excel import/export for rosters and reports |
| **QR & Barcodes** | Simple-QRCode (`simplesoftwareio`) | 4.2 | Student ID verification & library barcode scanning |

---

### 3. Application Domain & Module Inventory

Total Registered Endpoints: **1,243 Routes** across **55 Controllers**:

1. **Authentication & Session Security** (`/login`, `/logout`, `/password`):
   - Secure manual email/ID + password verification with password hashing.
   - Throttled brute-force defense via Laravel RateLimiter.
2. **Student Information System (SIS)** (`/students` - 88 routes):
   - Full student lifecycle: Enrolment, Class/Section allocation, Roll numbers, House allotting, ID card printing, Transfer Certificates (TC).
3. **Admissions & Enquiries Desk** (`/admissions`, `/apply` - 69 routes):
   - Online and walk-in application workflows, multi-step drafts, document verification, photo capture, and admission fee integration.
4. **Academics & Timetables** (`/academics`, `/classes`, `/staff-timetable` - 112 routes):
   - Class teacher allocation (strict 1-to-1 section allocation), subject catalog, master periods, faculty timetables, and conflicts prevention.
5. **Human Resources & Payroll** (`/hr` - 136 routes):
   - Faculty and non-teaching employee management, biometric logs, leave desks, salary structures, payslips, and compliance tracking.
6. **Transport & Fleet Logistics** (`/transport` - 99 routes):
   - Vehicle fleet, GPS tracking, drivers, nannies, 12 active routes, sequential stops, student pickups, duty sheets, and compliance printing.
7. **Examinations & Digital Gradebook** (`/examinations`, `/online-exams` - 96 routes):
   - CBSE standard grading, exam schedules, marks entry, hall ticket generation, report cards, and ranking algorithms.
8. **Fee Management & Billing** (`/fees`, `/accounts`, `/pay` - 74 routes):
   - Fee structures, concessions, student ledgers, receipt printing, payment transactions, and reconciliation.
9. **Library Resource Center** (`/library` - 86 routes):
   - 301 book catalog, barcode scanning, issue/return tracking, overdue fine calculation, and student reservation queues.
10. **Hostel & Facilities** (`/hostel` - 79 routes):
    - Dormitories, room/bed allocations, warden management, and hostel roll calls.
11. **Warehouse, Inventory & Kit** (`/warehouse`, `/inventory` - 40 routes):
    - Uniforms, textbook bundles, admission kits, suppliers, purchase orders, and stock ledgers.
12. **Gate, Visitor & Campus Security** (`/gate`, `/visitor-card` - 30 routes):
    - Visitor passes, digital visitor tracking, student gate out-passes, and vehicle entry logs.
13. **Communications & Circulars** (`/communication`, `/circulars`, `/events` - 33 routes):
    - Notice boards, parent circulars, SMS/Email broadcasts, and academic event calendars.
14. **Role-Based Access Control (RBAC)** (`/rbac` - 24 routes):
    - Permission matrix, role targets, account provisioning, and audit log tracking.

---

### 4. Database Schema & Persistence Topology

- **Database Engine**: PostgreSQL (`school_erp`)
- **Total Physical Tables**: 263 tables
- **Total Registered Eloquent Models**: 201 models in `app/Models/`
- **Key Record Volumes**:
  - `students`: 632 records (631 preloaded roster + 1 new admission)
  - `employees`: 44 records (Teaching & Non-Teaching faculty)
  - `users`: 60 records (Administrators, Teachers, Staff)
  - `classes`: 16 standards (Pre-KG through Grade XII)
  - `sections`: 60 active sections
  - `books`: 301 cataloged library volumes
  - `transport_routes`: 12 operational bus routes
  - `academic_years`: 5 academic sessions (active: 2026-2027)

---

### 5. Architectural Risks & Observations

1. **Large Route Surface Area**: With 1,243 routes, strict authorization middleware verification is essential to guarantee zero unauthorized route accessibility or privilege escalation.
2. **Multi-Role Scoping**: 23 distinct user roles exist in Spatie RBAC; role boundary isolation (e.g. Teacher seeing only assigned classes, Parent seeing only their ward) must be strictly tested.
3. **Complex Form Validation**: Multi-step forms in Admissions, Fee Payment, and Timetable Generation require verification against race conditions, duplicate submissions, and network interruptions.
4. **Print & PDF Workloads**: DomPDF generation for bulk report cards, ID cards, and duty sheets requires memory and execution time profiling to prevent PHP worker timeout under load.
5. **Database Transaction Integrity**: Multi-table insertions across `students`, `student_enrollments`, `fee_payments`, and `timetables` require DB transaction guarantees with reliable rollback.

---

### 6. Testing Strategy & Execution Matrix

| Stage | Audit Domain | Scope | Tools / Harness | Risk Level |
| :---: | :--- | :--- | :--- | :---: |
| **Stage 1** | Architecture Discovery | Codebase inspection, technology stack, directory mapping | Static Analysis, Artisan, AST probe | LOW (Read-only) |
| **Stage 2** | Baseline Testing | App startup, health check, login, core modules baseline | HTTP Probes, Artisan Serve, cURL | LOW (Safe read) |
| **Stage 3** | Security & OWASP Audit | SQLi, XSS, CSRF, IDOR, brute-force, rate-limiting, RBAC | OWASP Test Suite, Penetration harness | HIGH (Controlled) |
| **Stage 4** | Functional & DB Audit | All CRUD forms, data validation, race conditions, DB integrity | Transactional test harness, DB triggers | MEDIUM (Safe test) |
| **Stage 5** | API Endpoint Audit | 1,243 routes, status codes, payload validation, errors | Automated API runner, Status validator | MEDIUM |
| **Stage 6** | Performance & Load Audit| 10 -> 25 -> 50 -> 100 -> 250 -> 500 concurrent sessions | Progressive async load generator | HIGH (Monitored) |
| **Stage 7** | UI & Responsive Audit | Mobile (375px), Tablet (768px), Laptop, Desktop, Print | Viewport analyzer, DOM syntax linter | LOW |
| **Stage 8** | Advanced Reliability | Failover, memory leaks, audit trails, backups, timezones | Chaos harness, memory leak monitor | HIGH (Isolated) |
| **Stage 9** | Autonomous Bug Fixing | Root-cause analysis, controlled fixes, regression test | Code editor, test verifier, git commit | HIGH (Verified) |
| **Stage 10**| Final Certification | Master regression, final metrics, production certification | End-to-end verification harness | MEDIUM |

*Stage 1 Architecture Audit completed and certified.*
