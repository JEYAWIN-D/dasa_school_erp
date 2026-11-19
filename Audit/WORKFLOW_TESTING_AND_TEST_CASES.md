# Erode Public School — Workflow Testing Results & Test Cases
**Execution Date:** November 1, 2026  
**Test Suite:** End-to-End Timetable Lifecycle, Conflict Detection, API Validation & Error Handling  
**Summary:** **11 Executed | 11 Passed | 0 Failed (100% Pass Rate)**

---

## 1. Test Case Execution Summary Table

| Test ID | Module / Workflow | Test Description | Input Data | Expected Status | Actual Status | Result |
| :---: | :--- | :--- | :--- | :---: | :---: | :---: |
| **TC-01** | Security / Auth | Unauthenticated access to academic classes | `GET /classes` (No cookie) | `302 / 401` | `302 Found` | **PASS** |
| **TC-02** | Security / Auth | Administrator authentication with 2FA PIN | `POST /login` (admin@schoolerp.in + PIN 1122) | `302 Found` | `302 Found` | **PASS** |
| **TC-03** | Academics / UI | Admin loads Classes & Timetables management | `GET /classes` (Admin session) | `200 OK` | `200 OK` | **PASS** |
| **TC-04** | Timetable / API | Fetch section schedule JSON matrix | `GET /classes/15/timetable-data` | `200 OK` | `200 OK` | **PASS** |
| **TC-05** | HR & Finance | Staff Timetable & Teaching Work View | `GET /staff-timetable` (Admin session) | `200 OK` | `200 OK` | **PASS** |
| **TC-06** | Security / Auth | Teacher authentication | `POST /login` (vinuyagavi + Teacher@123) | `302 Found` | `302 Found` | **PASS** |
| **TC-07** | Dashboard / UI | Teacher loads custom In-Charge Hub Dashboard | `GET /dashboard` (Teacher session) | `200 OK` | `200 OK` | **PASS** |
| **TC-08** | Academics / UI | Teacher loads read-only class view | `GET /classes` (Teacher session) | `200 OK` | `200 OK` | **PASS** |
| **TC-09** | Staff Timetable | Teacher personal schedule & period reports | `GET /staff-timetable` (Teacher session) | `200 OK` | `200 OK` | **PASS** |
| **TC-10** | Security / RBAC | Teacher unauthorized mutation blocked | `POST /classes/timetable/declare-holiday` | `403 Forbidden` | `403 Forbidden`| **PASS** |
| **TC-11** | Error Handling | System handles undefined / invalid URL | `GET /classes/non-existent-endpoint` | `404 Not Found` | `404 Not Found`| **PASS** |

---

## 2. Detailed Workflow Test Scenarios

### Workflow 1: Single Slot Modification with Conflict Engine (`/classes/timetable/update-slot`)
1. **Action:** Admin opens Edit Timetable modal, selects Day Monday, Period 3 for Class I Section A, chooses Subject "Mathematics" and Teacher "SNEHA".
2. **Pessimistic Concurrency Lock:** Server executes `lockForUpdate()` on row `(class_id=16, section_id=23, day=1, period=3)`.
3. **Availability Check:** Engine queries `timetables` where `teacher_id = Sneha->id AND day = 1 AND period = 3`.
4. **Result:** If Sneha is free, slot is committed and updated across client live UI. If Sneha is teaching another section, server throws HTTP 422 with structured conflict payload (`conflict: true, message: "Faculty SNEHA is already assigned to Class I Sec B at this time"`).

### Workflow 2: Bulk Day Schedule Batch Save (`/classes/timetable/save-day`)
1. **Action:** Admin loads entire Monday schedule for Grade 8 Section A (8 periods), adjusts time slots, modifies room, assigns substitute teacher, and clicks "Save Monday Timetable".
2. **Batch Validation:** `validateBatch()` inspects all 8 periods sequentially, checking:
   - No period exceeds maximum institutional hours.
   - Break & Lunch intervals are properly maintained.
   - Teachers are not scheduled for consecutive over-quota exhaustion without intervals.
3. **Database Transaction:** All 8 periods are atomically committed within `DB::transaction()`. If any period fails, entire transaction rolls back cleanly.

### Workflow 3: Saturday Afternoon Extracurricular Roster
1. **Action:** Every Saturday afternoon (Periods 6, 7, 8):
   - Period 6: Keyboard & Instrumental Music
   - Period 7: Chess & Strategic Games
   - Period 8: Special Common Class / School Assembly & Club Activities
2. **Result:** All standards participate simultaneously without teacher collisions, as dedicated activity instructors and floating co-teachers oversee each club.

