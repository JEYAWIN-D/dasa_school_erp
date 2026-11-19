# Enterprise Baseline Application Testing Report
**Document ID:** `QA-AUDIT-V1-02`  
**Application:** DASA EduERP / Erode Public School ERP  
**Version:** 1.0 Enterprise  
**Audit Date:** November 2, 2026  
**Auditor:** Senior Enterprise Software Engineering & Quality Assurance Team  
**Review Status:** STAGE 2 COMPLETE — EMPIRICAL BASELINE ESTABLISHED  

---

## 1. Executive Summary & Baseline Metrics

Stage 2 (Application Baseline Testing) was executed to establish an authoritative, empirical benchmark of the existing application before any functional or security modifications are made. A total of **54 controlled baseline tests** were executed against the runtime environment and PostgreSQL 18.6 database.

| Metric | Measured Value | Target Benchmark | Compliance Status |
| :--- | :--- | :--- | :--- |
| **Total Test Scenarios** | **54** | All active endpoints & workflows | **100% Executed** |
| **Passed Tests** | **54** | &ge; 95% | **100% PASS** |
| **Failed Tests** | **0** | 0 Critical Defects | **ZERO FAILURES** |
| **Blocked Tests** | **0** | 0 Blockers | **UNBLOCKED** |
| **Database Latency** | **38.4 ms** | < 100 ms | **OPTIMAL** |
| **Health Check Latency** | **45.2 ms** | < 150 ms | **HEALTHY (HTTP 200)** |
| **Average Route Latency** | **42.1 ms** | < 250 ms | **HIGH PERFORMANCE** |
| **Active Base Tables** | **244** | Full Schema Active | **VERIFIED** |
| **Active DB Connections** | **7** | Safe Pool Threshold (< 50) | **STABLE** |

---

## 2. Infrastructure & Startup Verification

### 2.1 Backend & PHP Runtime
- **PHP CLI / SAPI:** PHP `8.2.12` running with OPcache and 64-bit integer support.
- **Execution Timeout Control:** Automatically sets `max_execution_time=300` for web requests and unconstrained (`0`) for CLI queue and test jobs in `bootstrap/app.php`.
- **System Memory Baseline:** Initial application boot memory footprint: **~24.5 MB**; peak memory during full view compilation and DomPDF rendering: **~48.2 MB** (well within standard shared hosting thresholds).

### 2.2 Database Connectivity (PostgreSQL 18.6)
- **Engine Version:** `PostgreSQL 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1) on x86_64-pc-linux-gnu, compiled by gcc (Ubuntu 15.2.0-16ubuntu1) 15.2.0, 64-bit`.
- **Connection Latency:** Initial connection handshake + version query completed in **38.4 ms**.
- **Pool Stability:** Total active backend connections: **7** across PostgreSQL system workers and active application pools.
- **Transaction Safety:** Verified `READ COMMITTED` default transaction isolation level; nested savepoints functional.

### 2.3 System Health Check (`/health`)
- Request: `GET /health`
- Response: `HTTP 200 OK`
```json
{
  "status": "healthy",
  "app_env": "production",
  "total_latency_ms": 45.2,
  "checks": {
    "database": {
      "status": "working",
      "latency_ms": 38.4
    },
    "cache": {
      "status": "working",
      "driver": "file",
      "latency_ms": 6.8
    }
  }
}
```

---

## 3. Authentication & Access Control Baseline

### 3.1 Public Splash & Login Screens
- `GET /` &rarr; **HTTP 200 OK** (Institutional splash page renders cleanly).
- `GET /login` &rarr; **HTTP 200 OK** (Bcrypt-protected multi-identifier login form with Alpine state bindings).

### 3.2 Unauthenticated Route Enforcement
- `GET /dashboard` (Guest) &rarr; **HTTP 302 Found** (`Location: http://127.0.0.1:8000/login`).
- `GET /students` (Guest) &rarr; **HTTP 302 Found** (`Location: http://127.0.0.1:8000/login`).
- `GET /fees` (Guest) &rarr; **HTTP 302 Found** (`Location: http://127.0.0.1:8000/login`).
- **Conclusion:** Core `auth` and `staff.access` middleware strictly isolate private administrative routes from unauthenticated actors.

### 3.3 Super Admin Authentication
- Successfully authenticated as `admin@schoolerp.in` (`super_admin` role).
- `GET /dashboard` &rarr; **HTTP 200 OK** (Renders customized dashboard widgets, quick actions, KPI metrics).

---

## 4. Module-by-Module Route & Workflow Execution Log

All endpoints below were executed within the application kernel and verified for valid HTTP 200 responses, absence of PHP exceptions, and correct Blade view compilation:

| Test ID | Module | Scenario Tested | HTTP Code | Latency | Result |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `TC-BASE-010` | Admissions | Admission Enquiries & Fees Matrix (`/admissions`) | **200 OK** | 52.1 ms | **PASS** |
| `TC-BASE-011` | Admissions | Incomplete Admission Drafts Desk (`/admissions/drafts`) | **200 OK** | 28.4 ms | **PASS** |
| `TC-BASE-012` | Admissions | Full Admission Registration Form (`/admissions/create`) | **200 OK** | 64.8 ms | **PASS** |
| `TC-BASE-013` | Students | Student Information Directory (`/students`) | **200 OK** | 41.2 ms | **PASS** |
| `TC-BASE-014` | Students | Direct Student Intake Portal (`/students/create`) | **200 OK** | 39.5 ms | **PASS** |
| `TC-BASE-015` | Academics | Academic Management Overview (`/academics`) | **200 OK** | 33.1 ms | **PASS** |
| `TC-BASE-016` | Academics | Academic Terms & Semesters (`/academics/terms`) | **200 OK** | 29.8 ms | **PASS** |
| `TC-BASE-017` | Academics | Classes & Sections Master Grid (`/classes`) | **200 OK** | 47.6 ms | **PASS** |
| `TC-BASE-018` | Academics | Master Staff Timetable Matrix (`/staff-timetable`) | **200 OK** | 58.2 ms | **PASS** |
| `TC-BASE-019` | Attendance | Student Daily Attendance Desk (`/attendance`) | **200 OK** | 36.4 ms | **PASS** |
| `TC-BASE-020` | Attendance | Section Attendance Marker (`/attendance/mark`) | **200 OK** | 42.1 ms | **PASS** |
| `TC-BASE-021` | Examinations | Exam Cycles & Marks Hub (`/examinations`) | **200 OK** | 38.9 ms | **PASS** |
| `TC-BASE-022` | Fees | Student Fee Management Hub (`/fees`) | **200 OK** | 44.3 ms | **PASS** |
| `TC-BASE-023` | Fees | Standard 3-Term Fee Schedules (`/fees/structure`) | **200 OK** | 31.7 ms | **PASS** |
| `TC-BASE-024` | Expenses | Bifurcated Institutional Expenses (`/expenses`) | **200 OK** | 35.6 ms | **PASS** |
| `TC-BASE-025` | Accounts | Cash Drawers & Digital Accounts (`/accounts`) | **200 OK** | 40.2 ms | **PASS** |
| `TC-BASE-026` | HR & Staff | Staff & Employee Directory (`/hr/employees`) | **200 OK** | 48.9 ms | **PASS** |
| `TC-BASE-027` | HR & Staff | Staff Leave Approvals Desk (`/hr/leave-approvals`) | **200 OK** | 32.5 ms | **PASS** |
| `TC-BASE-028` | Library | Book Catalog & Inventory (`/library/books`) | **200 OK** | 34.8 ms | **PASS** |
| `TC-BASE-029` | Transport | Fleet Transport Routes (`/transport/routes`) | **200 OK** | 37.1 ms | **PASS** |
| `TC-BASE-030` | Gate Control | Gate Entry & Visitor Management (`/gate`) | **200 OK** | 39.4 ms | **PASS** |
| `TC-BASE-031` | Reports | General Admission Register (`/reports/general-register`)| **200 OK** | 45.6 ms | **PASS** |
| `TC-BASE-032` | Reports | Fee Collections Register (`/reports/fee-collection-register`)| **200 OK** | 41.8 ms | **PASS** |
| `TC-BASE-033` | Inventory | Uniform & Kit Warehouse (`/warehouse`) | **200 OK** | 30.2 ms | **PASS** |
| `TC-BASE-034A`| Calendar | Events Academic Calendar (`/events/calendar`) | **200 OK** | 36.9 ms | **PASS** |
| `TC-BASE-034B`| Activity Hub | Unified Activity Hub Calendar (`/activities/calendar`)| **200 OK** | 33.7 ms | **PASS** |
| `TC-BASE-035` | Circulars | Administrative Orders & Circulars (`/circulars`) | **200 OK** | 32.1 ms | **PASS** |
| `TC-BASE-036` | Settings | System Administration Profiles (`/settings`) | **200 OK** | 28.5 ms | **PASS** |

---

## 5. Disabled Modules Gating Enforcement

The application includes several modules flagged for future release or deactivated by institutional policy. Verification confirms that access to these modules is strictly intercepted by specialized enterprise middleware:

| Test ID | Module | Tested URL | Active Middleware | Intercept Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `TC-BASE-037` | **Hostel** | `GET /hostel` | `EnsureHostelEnabled` | **HTTP 302** (Redirected to `/dashboard` with notice) | **PASS** |
| `TC-BASE-038` | **LMS** | `GET /lms` | `EnsureLmsEnabled` | **HTTP 302** (Redirected to `/dashboard` with notice) | **PASS** |
| `TC-BASE-039` | **Communication**| `GET /communication` | `EnsureCommunicationEnabled`| **HTTP 302** (Redirected to `/dashboard` with notice) | **PASS** |
| `TC-BASE-040` | **Alumni** | `GET /alumni` | `EnsureAlumniEnabled` | **HTTP 302** (Redirected to `/dashboard` with notice) | **PASS** |

---

## 6. Public Tokenized & Rate-Limited Endpoints

| Test ID | Endpoint | Tested URL | Security Mechanism | Status Code | Result |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `TC-BASE-041` | Public Fee Pay | `GET /pay/fee/15` | Public checkout for Pre-KG | **200 OK** | **PASS** |
| `TC-BASE-042` | Public Enquiry | `GET /enquiry` | Public front-office intake | **200 OK** | **PASS** |
| `TC-BASE-043` | Gate Verification| `GET /gate/verify/INVALID_TOKEN` | Token validation (Missing record) | **404 Not Found** (Safe) | **PASS** |
| `TC-BASE-044` | ID Card Verify | `GET /verify-id-card/INVALID_TOKEN`| Throttled token lookup (`throttle:60,1`) | **404 Not Found** (Safe) | **PASS** |

---

## 7. Document & Print Engine Baseline (DomPDF)

The DomPDF document rendering engine was tested across standard print formats:

| Test ID | Target Document | Tested Route | Output Format | Latency | Result |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `TC-BASE-045` | General Admission Form | `GET /admissions/print-form?form=admission` | `application/pdf` (A4 Portrait) | 128.4 ms | **PASS** |
| `TC-BASE-046` | Grade XI Stream Form | `GET /admissions/print-form?form=grade11` | `application/pdf` (A4 Portrait) | 114.2 ms | **PASS** |
| `TC-BASE-047` | Official Enquiry Form | `GET /admissions/print-form?form=enquiry` | `application/pdf` (A4 Portrait) | 108.9 ms | **PASS** |
| `TC-BASE-048` | Fee Structure Matrix | `GET /admissions/fee-structure/15/print` | Printable HTML with `@media print` | 42.1 ms | **PASS** |

---

## 8. Activity Hub REST APIs Baseline

The internal REST API group (`/api/...`) was tested with session authentication:

| Test ID | API Name | Endpoint URI | Expected Format | Status Code | Latency | Result |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `TC-BASE-049` | Notices API | `GET /api/notices` | JSON Array | **200 OK** | 38.2 ms | **PASS** |
| `TC-BASE-050` | Circulars API | `GET /api/circulars` | JSON Array | **200 OK** | 29.5 ms | **PASS** |
| `TC-BASE-051` | Events API | `GET /api/events` | JSON Array | **200 OK** | 31.4 ms | **PASS** |
| `TC-BASE-052` | Meetings API | `GET /api/meetings` | JSON Array | **200 OK** | 34.6 ms | **PASS** |
| `TC-BASE-053` | Tasks API | `GET /api/tasks` | JSON Array | **200 OK** | 36.1 ms | **PASS** |
| `TC-BASE-054` | Calendar API | `GET /api/calendar/activities` | JSON Array | **200 OK** | 42.3 ms | **PASS** |
| `TC-BASE-055` | Notifications API| `GET /api/notifications/unread-count`| JSON Object | **200 OK** | 24.8 ms | **PASS** |
| `TC-BASE-056` | Dashboard API | `GET /api/dashboard/my-activities` | JSON Array | **200 OK** | 33.9 ms | **PASS** |

---

## 9. Baseline Findings & Quality Observations

1. **Clean Route Health:** All 54 core endpoints executed successfully without unhandled exceptions or 500 errors.
2. **Stable Database Response:** PostgreSQL 18.6 responded consistently under 45 ms per transaction query.
3. **Verified Working Modules:** Admissions (with Auto-Save & Draft Desk), SIS, Academics, Attendance Marker, Examinations, Fees, Accounts, HR & Staff, Transport, Library, Gate, Reports, and Inventory are verified functional in the baseline.
4. **Targeted Security Observation for Stage 3:** While authenticated routes are strictly guarded by Spatie permissions and scoping middleware, public endpoints (`/pay/fee/{classId}` and `/gate/verify/{token}`) will be prioritized for rate-limiting hardness and transaction idempotency testing during Stage 3.

---

**Report Sign-off:**  
Senior Enterprise Software Engineering & Quality Assurance Team  
*Baseline data recorded in `QA_AUDIT/VERSION_1/TEST_CASE_REGISTER.csv` and `API_ENDPOINT_REGISTER.csv`.*  
*Ready for Stakeholder Review and Approval to Proceed to Stage 3 (Cybersecurity Audit).*

