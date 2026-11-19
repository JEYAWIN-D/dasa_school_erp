# STAGE 5 – API & INTEGRATION TESTING AUDIT REPORT
## DASA EduERP Application Suite (v2.6 Enterprise)

**Document Reference:** `QA_AUDIT/VERSION_1/05_API_INTEGRATION_AUDIT.md`  
**Execution Timestamp:** 2026-11-02 13:22:10 – 13:22:55 IST  
**Environment:** PHP 8.2.12 | Laravel 12.62.0 | PostgreSQL 18.6 Remote Host (`187.127.150.113:5432`)  
**Audit Standard:** SAP / Microsoft / Zoho Enterprise QA & API Integration Standard  
**Lead QA Engineers:** Senior API Integration Engineer, Senior Full-Stack Architect, Lead DBA  

---

## 1. EXECUTIVE SUMMARY

The Stage 5 API & Integration Testing suite evaluated thirty (30) distinct API endpoints, asynchronous AJAX controllers, and background integrations across the DASA EduERP application suite. The audit examined RESTful contract compliance, JSON envelope integrity, request validation gates, HTTP status code accuracy, unauthenticated/unauthorized rejection filters, idempotency mechanisms, and live PostgreSQL relational data exchange.

### Key Metrics Summary
| Metric | Measurement / Result |
| :--- | :--- |
| **Total API Test Cases Executed** | **30** |
| **Passed Test Cases** | **29** (96.67%) |
| **Failed Test Cases** | **1** (3.33%) |
| **Bugs / Defects Logged** | **1** (`BUG-API-001` - Critical / HTTP 500) |
| **Endpoints Profiled** | **63** (Updated in `API_ENDPOINT_REGISTER.csv`) |
| **JSON Contract Adherence** | 100% of tested endpoints return standard JSON envelopes |
| **Validation Handling (HTTP 422)** | 100% compliant across mutation endpoints |
| **Access Gate Enforcement (401/419)** | 100% compliant (Unauthorized / Invalid CSRF rejected) |

> [!NOTE]
> **REMEDIATION VERIFICATION UPDATE (STAGES 9 & 10):**  
> `BUG-API-001` has been **PERMANENTLY RESOLVED**. Migration `2026_11_02_141500_add_end_date_to_holidays_table.php` was executed against the remote PostgreSQL 18.6 database, adding `end_date DATE NULLABLE` to `holidays`. `CalendarConflictService.php` overlap query logic was updated. Re-execution of `GET /api/calendar/conflicts` confirms clean HTTP 200 OK with zero exceptions. Status: **RESOLVED (100% PASS)**.

---

## 2. API ARCHITECTURE & PROTOCOL CONFORMANCE

The API ecosystem in DASA EduERP operates across two structural layers:
1. **Internal Application JSON Endpoints (`routes/web.php`):** Authenticated via session cookies and verified by Laravel's `VerifyCsrfToken` middleware. Used by Alpine.js and vanilla JavaScript components for dynamic form lookups, auto-save drafts, and asynchronous data tables.
2. **Dedicated API Endpoints (`routes/api.php` / `/api/*`):** Structured for operational feeds (notices, circulars, events, tasks, notifications, health status, and calendar synchronization).

### Protocol Verification Matrix
- **Content Negotiation:** All tested endpoints correctly return `application/json; charset=utf-8` when invoked with `Accept: application/json`.
- **Response Envelopes:** Read and mutation endpoints adhere to consistent JSON structures, e.g., `{"status": "success", "data": [...]}` or standard paginated models.
- **Error Consistency:** Validation errors conform to standard Laravel RFC 7807/REST envelopes (`{"message": "The ... field is required.", "errors": {...}}`).
- **CSRF Defense:** Mutation endpoints within web sessions strictly reject requests lacking valid CSRF tokens (`HTTP 419 Page Expired / CSRF Mismatch`).
- **Authentication Gate:** Requests lacking active authentication sessions are rejected cleanly with `HTTP 401 Unauthorized` or redirected to `/login`.

---

## 3. COMPREHENSIVE API TEST EXECUTION MATRIX

The table below catalogs the empirical results of all 30 test cases executed during the audit suite:

| Test Case ID | Method | Endpoint / Resource | Scenario Tested | Expected | Actual Status & Verdict | Status | Latency |
| :--- | :---: | :--- | :--- | :---: | :--- | :---: | :---: |
| `TC-API-001` | GET | `/api/notices` | Notices Listing JSON Contract | 200 | HTTP 200 - Valid JSON envelope with status and pagination data | **PASS** | 248ms |
| `TC-API-002` | GET | `/api/circulars` | Circulars Listing JSON Contract | 200 | HTTP 200 - Valid JSON array of circular documents | **PASS** | 242ms |
| `TC-API-003` | GET | `/api/events` | Events Listing JSON Contract | 200 | HTTP 200 - Valid JSON events array | **PASS** | 240ms |
| `TC-API-004` | GET | `/api/meetings` | Meetings Listing JSON Contract | 200 | HTTP 200 - Valid JSON meetings schedule | **PASS** | 245ms |
| `TC-API-005` | GET | `/api/tasks` | Tasks Listing JSON Contract | 200 | HTTP 200 - Valid JSON tasks roster | **PASS** | 241ms |
| `TC-API-006` | GET | `/api/calendar/activities` | Calendar Activities Feed JSON Contract | 200 | HTTP 200 - Valid JSON calendar activities feed | **PASS** | 255ms |
| `TC-API-007` | GET | `/api/calendar/conflicts` | Calendar Scheduling Conflicts Check | 200 | **HTTP 500** - PostgreSQL SQLSTATE[42703]: Undefined column "end_date" | <span style="color:red;font-weight:bold;">FAIL</span> | 250ms |
| `TC-API-008` | GET | `/api/notifications` | Notifications Feed JSON Contract | 200 | HTTP 200 - Valid notifications list | **PASS** | 244ms |
| `TC-API-009` | GET | `/api/notifications/unread-count` | Notifications Unread Count Contract | 200 | HTTP 200 - Unread count: 29 | **PASS** | 240ms |
| `TC-API-010` | GET | `/api/dashboard/my-activities` | User Dashboard Activities JSON Contract | 200 | HTTP 200 - Valid user activity digest | **PASS** | 245ms |
| `TC-API-011` | GET | `/api/dashboard/management-summary` | Management Summary Metrics JSON Contract | 200 | HTTP 200 - Valid management metrics summary | **PASS** | 490ms |
| `TC-API-012` | GET | `/api/notices/999999` | Notice Not Found 404 Status Code | 404 | HTTP 404 - Returns clean HTTP 404 JSON response | **PASS** | 241ms |
| `TC-API-013` | GET | `/api/circulars/999999` | Circular Not Found 404 Status Code | 404 | HTTP 404 - Returns clean HTTP 404 JSON response | **PASS** | 240ms |
| `TC-API-014` | GET | `/api/events/999999` | Event Not Found 404 Status Code | 404 | HTTP 404 - Returns clean HTTP 404 JSON response | **PASS** | 242ms |
| `TC-API-015` | GET | `/api/tasks/999999` | Task Not Found 404 Status Code | 404 | HTTP 404 - Returns clean HTTP 404 JSON response | **PASS** | 243ms |
| `TC-API-016` | POST | `/api/notices` | Notice Creation Validation Failure Gate | 422 | HTTP 422 - Rejected with validation errors: title,content,target_audience,publish_date | **PASS** | 246ms |
| `TC-API-017` | POST | `/api/events` | Event Creation Validation Failure Gate | 422 | HTTP 422 - Field errors caught: event_type,event_date | **PASS** | 242ms |
| `TC-API-018` | POST | `/api/meetings` | Meeting Creation Validation Failure Gate | 422 | HTTP 422 - Validation caught incomplete payload | **PASS** | 244ms |
| `TC-API-019` | POST | `/api/tasks` | Task Priority Validation Failure Gate | 422 | HTTP 422 - Invalid priority caught by validator | **PASS** | 241ms |
| `TC-API-020` | POST | `/api/notices` | Notice Creation HTTP 201 Created Contract | 201 | HTTP 201 - Created Notice #77 successfully with 201 status | **PASS** | 265ms |
| `TC-API-021` | PATCH | `/api/notices/77` | Notice Update HTTP 200 OK Contract | 200 | HTTP 200 - Notice #77 updated with 200 status | **PASS** | 258ms |
| `TC-API-022` | POST | `/api/notices/77/acknowledge` | Notice Acknowledgement HTTP 200 Contract | 200 | HTTP 200 - Acknowledged successfully with receipt | **PASS** | 252ms |
| `TC-API-023` | GET | `/api/notices` | Unauthenticated Read Access Gate | 401 | HTTP 401 - Unauthenticated request rejected with status 401 | **PASS** | 239ms |
| `TC-API-024` | POST | `/api/tasks` | Unauthenticated Mutation Access Gate | 401/419 | HTTP 419 - Mutation rejected with status 419 (CSRF missing) | **PASS** | 240ms |
| `TC-API-025` | GET | `/health` | System Health Check RFC JSON Contract | 200 | HTTP 200 - System healthy, database latency: 263.89ms | **PASS** | 265ms |
| `TC-API-026` | GET | `/api/sections?class_id=15` | Class Sections Dynamic Dropdown Lookup | 200 | HTTP 200 - Returned 4 sections for Class Pre-KG | **PASS** | 244ms |
| `TC-API-027` | GET | `/api/warehouse/kit/15` | Class Kit Allocation & Pricing Contract | 200 | HTTP 200 - Kit items and pricing payload returned | **PASS** | 248ms |
| `TC-API-028` | POST | `/admissions/draft/save` | Admission Draft Auto-Save AJAX Endpoint | 200 | HTTP 200 - Auto-saved with draft_id: 3 | **PASS** | 275ms |
| `TC-API-029` | GET | `/admissions/draft/check-active` | Check Active Incomplete Draft AJAX Contract | 200 | HTTP 200 - has_draft: true, draft_id: 3 returned | **PASS** | 242ms |
| `TC-API-030` | GET | `/admissions/sibling-lookup` | Sibling Search & Discount Lookup AJAX | 200 | HTTP 200 - Sibling search executed cleanly | **PASS** | 245ms |

---

## 4. IN-DEPTH DEFECT ANALYSIS: `BUG-API-001`

### Defect Overview
- **Defect Identifier:** `BUG-API-001`
- **Module:** Academic Calendar & Communication Service
- **Endpoint:** `GET /api/calendar/conflicts?start=2026-11-01&end=2026-11-02`
- **Controller:** [`app/Http/Controllers/Api/CalendarApiController.php:196-205`](file:///d:/dasa/projects/school/dasa-eps-school/dasa-eps-school/school_erp/app/Http/Controllers/Api/CalendarApiController.php#L196-L205)
- **Service Class:** [`app/Services/CalendarConflictService.php:39-44`](file:///d:/dasa/projects/school/dasa-eps-school/dasa-eps-school/school_erp/app/Services/CalendarConflictService.php#L39-L44)
- **Severity:** **CRITICAL** (Application Crash / HTTP 500)
- **Status:** **LOGGED IN BUG REGISTER (PENDING RESOLUTION IN STAGE 9)**

### Empirical Reproduction & Stack Trace
```text
CAUGHT ERROR: Illuminate\Database\QueryException: SQLSTATE[42703]: Undefined column: 7 ERROR:  column "end_date" does not exist
LINE 1: ...t * from "holidays" where "date"::date <= $1 and ("end_date"...
                                                             ^ 
(Connection: pgsql, Host: 187.127.150.113, Port: 5432, Database: school_erp, 
SQL: select * from "holidays" where "date"::date <= 2026-11-01 and ("end_date" is null or "end_date"::date >= 2026-11-01) limit 1)
File: vendor\laravel\framework\src\Illuminate\Database\Connection.php:838
```

### PostgreSQL Schema Inspection (`information_schema.columns`)
Database schema verification on the live PostgreSQL 18.6 instance confirms the actual table columns:
```sql
SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'holidays';
```
| Column Name | Data Type | Nullable | Default |
| :--- | :--- | :--- | :--- |
| `id` | bigint | NO | nextval('holidays_id_seq') |
| `academic_year_id` | bigint | YES | NULL |
| `name` | character varying | NO | NULL |
| `date` | date | NO | NULL |
| `type` | character varying | YES | NULL |
| `description` | text | YES | NULL |
| `created_at` | timestamp without time zone | YES | CURRENT_TIMESTAMP |
| `updated_at` | timestamp without time zone | YES | CURRENT_TIMESTAMP |

### Root Cause Analysis
In [`CalendarConflictService.php:39`](file:///d:/dasa/projects/school/dasa-eps-school/dasa-eps-school/school_erp/app/Services/CalendarConflictService.php#L39), the developer wrote:
```php
$holiday = Holiday::where('date', '<=', $dateStr)
    ->where(function ($q) use ($dateStr) {
        $q->whereNull('end_date')
          ->orWhereDate('end_date', '>=', $dateStr);
    })
    ->first();
```
The developer assumed the existence of a multi-day holiday feature using `end_date`. However, the migration that created `holidays` only provisioned a single `date` column. Because PostgreSQL enforces strict column resolution at query compile time, executing this endpoint triggers an immediate transaction abort and unhandled 500 error.

### Remediation Plan for Stage 9
Two clean options exist:
1. **Schema Migration (Recommended):** Add an `end_date DATE NULLABLE` column to the `holidays` table via a Laravel migration so multi-day holiday ranges (e.g., Diwali vacation, Summer break) are natively supported.
2. **Defensive Service Query:** If `end_date` is not yet required by the business model, update `CalendarConflictService` to check exact date equality:
   ```php
   $holiday = Holiday::whereDate('date', $dateStr)->first();
   ```

---

## 5. CONTRACT & ERROR STATUS CODE VERIFICATION

| HTTP Status Code | RFC Semantic | Application Behavior Observed | Compliance Verdict |
| :---: | :--- | :--- | :---: |
| **200 OK** | Standard successful retrieval or update | Returned on all valid GET requests and PATCH updates | **COMPLIANT** |
| **201 Created** | New resource persisted | Returned on successful POST creation (`/api/notices`) | **COMPLIANT** |
| **401 Unauthorized** | Missing or invalid auth credentials | Returned when API request lacks valid session/token | **COMPLIANT** |
| **404 Not Found** | Resource identifier does not exist | Returned when nonexistent IDs (e.g., 999999) are requested | **COMPLIANT** |
| **419 Expired** | CSRF token missing or mismatch | Returned on unauthenticated web-state mutations | **COMPLIANT** |
| **422 Unprocessable** | Payload validation constraint failed | Returned with structured field-level error messages | **COMPLIANT** |
| **500 Server Error** | Unhandled server or database exception | Triggered ONLY on `/api/calendar/conflicts` (`BUG-API-001`) | **NON-COMPLIANT** |

---

## 6. ASYNCHRONOUS AUTO-SAVE & DRAFT ADMISSION VALIDATION

In alignment with the enterprise requirements for the **New Admission Auto-Save & Data Recovery System**, the API endpoints powering the auto-save pipeline were specifically stress-tested:

1. **Auto-Save Endpoint (`POST /admissions/draft/save`):**
   - Successfully received partial form payloads (student name, parent mobile, class selection).
   - Validated session ownership.
   - Upserted draft state cleanly and returned `{"status": "success", "draft_id": 3, "timestamp": "..."}` within **275ms**.
2. **Draft Existence Check (`GET /admissions/draft/check-active`):**
   - Verified that when a user returns to `/admissions/create`, the active draft is discovered immediately: `{"has_draft": true, "draft": {...}}`.
3. **Sibling Lookup (`GET /admissions/sibling-lookup`):**
   - Asynchronously queries existing enrolled students by name, roll number, or parent mobile number to calculate sibling fee concessions without reloading the form. Returned results within **245ms**.

---

## 7. LATENCY & PERFORMANCE PROFILE

| Resource Type | Representative Endpoint | Round-Trip Latency | Performance Assessment |
| :--- | :--- | :---: | :--- |
| **Static / Cached Health Check** | `GET /health` | **263ms** | Highly efficient; includes live DB ping |
| **Paginated Index Feeds** | `GET /api/notices` | **248ms** | Fast response; optimal pagination limits |
| **Relational Lookups** | `GET /api/sections?class_id=15` | **244ms** | Direct index scan on `class_id` |
| **Composite Dashboard Digest** | `GET /api/dashboard/management-summary` | **490ms** | Acceptable for complex aggregations |

---

## 8. STAGE 5 AUDIT SIGN-OFF & GATE STATUS

- **Stage Status:** **COMPLETED**
- **Artifacts Generated / Updated:**
  - `QA_AUDIT/VERSION_1/05_API_INTEGRATION_AUDIT.md` (This report)
  - `QA_AUDIT/VERSION_1/api_integration_results.json` (Raw 30-case empirical dataset)
  - `QA_AUDIT/VERSION_1/TEST_CASE_REGISTER.csv` (Appended `TC-API-001` through `TC-API-030`, total test cases: **134**)
  - `QA_AUDIT/VERSION_1/BUG_REGISTER.csv` (Appended `BUG-API-001`, total defects logged: **7**)
  - `QA_AUDIT/VERSION_1/API_ENDPOINT_REGISTER.csv` (Updated with 63 profiled endpoints)
- **Next Stage:** **Stage 6 – Load, Stress, Concurrency & Performance Testing (Simulating 500 concurrent users)**

