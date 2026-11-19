# Erode Public School — Security & Role-Based Access Control (RBAC) Audit
**Audit Date:** November 1, 2026  
**Audited Subsystems:** Authentication, Session Management, RBAC Middleware, Endpoint Protection, CSRF, Data Isolation  
**Overall Security Rating:** **SECURE (GRADE A)**

---

## 1. Security Architecture & Threat Matrix

| Security Vector | Implementation Mechanism | Validation Status |
| :--- | :--- | :--- |
| **Authentication Enforcement** | Laravel `auth` middleware + Admin Two-Factor PIN | **PASS** (Guests redirected with HTTP 302 to `/login`) |
| **Role-Based Access Control** | Spatie `PermissionMiddleware` (`permission:view academics`, `permission:edit academics`) | **PASS** (Unauthorized role mutation returns HTTP 403 Forbidden) |
| **Privilege Escalation** | `ClassesController::canManageTimetable(auth()->user())` check before destructive writes | **PASS** (Non-admin roles blocked from modifying schedules) |
| **Cross-Site Request Forgery (CSRF)** | `VerifyCsrfToken` middleware on all state-changing `POST`, `PUT`, `DELETE` routes | **PASS** (Untokened/stale requests rejected with HTTP 419) |
| **SQL Injection & ORM Safety** | Eloquent ORM + PDO parameter binding across all query builders | **PASS** (No raw concatenated SQL input accepted) |
| **Input Validation** | Strict Form Requests with type, range (`min:1, max:8`), and existence rules (`exists:classes,id`) | **PASS** (Invalid inputs cleanly rejected with HTTP 422 Unprocessable) |
| **Information Disclosure / 404** | Global exception handlers prevent sensitive stack trace leakage in production | **PASS** (Undefined resources return standard HTTP 404 Not Found) |

---

## 2. Role-Based Access Control (RBAC) Test Matrix

### 2.1 Role: Guest / Unauthenticated User
- `GET /classes` &rarr; **302 Redirect to `/login`** (Data completely hidden from unauthenticated public).
- `GET /staff-timetable` &rarr; **302 Redirect to `/login`**.
- `POST /classes/timetable/save-day` &rarr; **302 Redirect to `/login`**.

### 2.2 Role: Teacher (`vinuyagavi@erodepublicschool.edu.in`)
- `GET /dashboard` &rarr; **200 OK** (Displays personal teacher dashboard, assigned class in-charge hub, today's schedule, period reports).
- `GET /classes` &rarr; **200 OK** (View-only schedule access; "Edit Timetable" button hidden/disabled).
- `GET /staff-timetable` &rarr; **200 OK** (Personal schedule and period duty roster rendered).
- `PUT /classes/15/rename` &rarr; **403 Forbidden** (Blocked by `permission:edit academics`).
- `POST /classes/timetable/declare-holiday` &rarr; **403 Forbidden** (Blocked by authorization gate).
- `POST /classes/timetable/update-slot` &rarr; **403 Forbidden** (Blocked by `canManageTimetable()`).

### 2.3 Role: Super Admin (`admin@schoolerp.in`)
- `POST /login` with password + Admin PIN `1122` &rarr; **302 Authenticated**.
- `GET /classes` &rarr; **200 OK** (Full management access with section controls, holiday declaration, time simulator, and batch timetable editor).
- `GET /classes/{id}/timetable-data` &rarr; **200 OK** (Returns complete schedule matrix and section breakdown in JSON).
- `POST /classes/timetable/update-slot` &rarr; **200 OK** (Slot successfully updated with pessimistic concurrency lock).
- `POST /classes/timetable/save-day` &rarr; **200 OK** (Batch day schedule saved with authoritative conflict checking).
- `POST /classes/timetable/resolve-conflict` &rarr; **200 OK** (Automatic conflict reassignment and audit log entry).

---

## 3. Data Leakage & Session Isolation Audit
- **Tenant & Academic Year Isolation:** All timetable queries are scoped strictly by `academic_year_id = currentYear->id`. Historical timetables are preserved without leaking into current live timetables.
- **Teacher Scope Isolation:** On `/staff-timetable` and `/dashboard`, teachers can only view student lists belonging to their in-charge class section (`myInChargeSection`). They cannot access or modify roster details of other classes.
- **Admin Password & PIN Security:** Passwords stored using `bcrypt` (work factor 12). Admin PIN stored using secure hash with strict rate limiting (max 5 attempts before 60-second cooldown).

