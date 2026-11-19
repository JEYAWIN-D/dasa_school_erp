# 04. Route, Stop & Student Passenger Allocation Audit

## 1. Route Corridor Summary
| Route ID | Route Name | Origin &rarr; Destination | Distance | Stops Count | Assigned Van |
|---|---|---|---|---|---|
| **1** | Perundurai Corridor | School Campus &rarr; Perundurai Old Bus Stand | 18.5 km | 13 Stops | `TN-33-AX-1001` (VAN I) |
| **2** | Bhavani Corridor | School Campus &rarr; Bhavani Kooduthurai | 16.2 km | 11 Stops | `TN-33-AX-1002` (VAN II) |
| **3** | Gobichettipalayam Corridor | School Campus &rarr; Gobi Bus Terminal | 24.0 km | 14 Stops | `TN-33-AX-1003` (VAN III) |
| **4** | Modakkurichi Corridor | School Campus &rarr; Modakkurichi Bus Stand | 14.8 km | 11 Stops | `TN-33-AX-1004` (VAN IV) |
| **5** | Chennimalai Corridor | School Campus &rarr; Chennimalai Temple Arch | 21.0 km | 12 Stops | `TN-33-AX-1005` (VAN V) |

## 2. Bus Stop Master Schema & Timing Integrity
- Every stop is mapped via foreign keys `route_id` and `vehicle_id`.
- Stops are sorted sequentially by integer column `stop_order`.
- Morning pickup timings start from the farthest terminal stop between `06:50 AM` and `07:15 AM`, terminating at the School Campus between `08:15 AM` and `08:25 AM`.
- Evening return drop timings commence from the School Campus at `03:45 PM`, arriving at terminal stops between `04:45 PM` and `05:15 PM`.
- Distance-based monthly fare tiers are mapped:
  - 0 &ndash; 5 km: ₹800 &ndash; ₹1,000 / month
  - 5 &ndash; 12 km: ₹1,200 &ndash; ₹1,500 / month
  - 12 &ndash; 25 km: ₹1,600 &ndash; ₹2,200 / month

## 3. Student Passenger Manifest Integrity
- Student allocations are maintained in `transport_allotments` linked to active student enrollments.
- Each allocation maps a boarding `stop_id`, vehicle `vehicle_id`, and `route_id`.
- The passenger manifest prints the student's Admission Number, Full Name, Class & Section, Boarding Stop, and Parent & Emergency Contact numbers for rapid child verification.

