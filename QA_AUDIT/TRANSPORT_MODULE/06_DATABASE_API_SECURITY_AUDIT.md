# 06. Database, API & Security Authorization Audit

## 1. Security & Authorization Middleware Guard
All transport routes are registered within the `admin` route group and protected by the following middleware stack:
1. `web`: Session state, CSRF protection, and encrypted cookie verification.
2. `auth`: Strict authentication enforcing valid staff login.
3. `permission:manage transport|view transport`: Spatie Laravel-Permission middleware ensuring that unprivileged users or unauthorized staff cannot access transport registers, manifests, or driver sensitive information (e.g., Aadhaar numbers, driving licenses).

## 2. Route Collision Prevention
In `routes/web.php`, static endpoints were placed prior to dynamic parameterized routes:
- `/transport/vehicles/print/all` is defined BEFORE `/transport/vehicles/{id}`
- `/transport/vehicles/pdf/all` is defined BEFORE `/transport/vehicles/{id}`
- `/transport/routes/print/all` is defined BEFORE `/transport/routes/{id}`
This eliminates wildcard route hijacking and guarantees 100% predictable URL resolution.

## 3. SQL Injection & Database Query Hardening
- All Eloquent queries utilize parameterized bindings.
- Defensive fallback handling is applied for student admission numbers (`$student->admission_no ?? $student->admission_number ?? '—'`) preventing PostgreSQL `Undefined column` query exceptions.
- Attendant and driver relationships use Eloquent eager loading (`with(['route', 'attendants', 'documents'])`) to eliminate N+1 performance bottlenecks.

