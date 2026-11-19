# Enterprise School Library Management System — Deployment & Backup Runbook

**System:** Erode Public School ERP (`EPS_SCHOOL_ERP` / `eps.dasatech.in`)  
**Target Environment:** Production Live Server & Remote PostgreSQL Database  
**Author:** Senior Software Engineering & DevSecOps Team  
**Standard:** SAP Enterprise Operations & Microsoft Production Readiness  
**Date:** 2026-11-03

---

## 1. System Architecture & Topology

```
┌────────────────────────────────────────────────────────┐
│                   Application Tier                     │
│         PHP 8.2.12 + Laravel 12.62.0 (Host)            │
│         Nginx / Apache HTTP Reverse Proxy              │
└──────────────────────────┬─────────────────────────────┘
                           │ TLS / TCP 5432
┌──────────────────────────▼─────────────────────────────┐
│                   Database Tier                        │
│          PostgreSQL 18.6 Engine                        │
│          Host: 187.127.150.113:5432                    │
│          Database: school_erp                          │
└────────────────────────────────────────────────────────┘
```

---

## 2. Production Deployment Steps

### 2.1 Code Synchronization
On the production server or staging host:
```bash
cd /path/to/school_erp

# Ensure branch is clean and author identity is strictly JEYAWIN-D
git config user.name "JEYAWIN-D"
git config user.email "jeyawind@gmail.com"

# Fetch latest verified code
git pull origin main
```

### 2.2 Database Migrations
Execute pending enterprise schema migrations with explicit force flag:
```bash
php artisan migrate --force
```
*Note:* The migration `2026_11_03_230000_create_enterprise_library_management_system.php` was designed with idempotent column and table existence checks (`if (!Schema::hasTable(...))`), guaranteeing safe re-runs.

### 2.3 Synchronize RBAC Permissions
Ensure all 14 library permission nodes are granted to Super Admin, Admin, and Librarian roles:
```bash
php scripts/sync_library_permissions.php
```

### 2.4 Cache Optimization
Clear stale route, view, and config caches to register the 35+ new library routes and views:
```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear

# Optimize for production
php artisan route:cache
php artisan view:cache
php artisan config:cache
```

---

## 3. PostgreSQL Database Backup Runbook

### 3.1 Automated Nightly Backup Cron
Schedule a daily full database dump at 02:00 AM IST to local storage and remote backup directory:

```bash
# /etc/cron.d/eps_library_backup
0 2 * * * postgres pg_dump -h 187.127.150.113 -U erp_user -d school_erp -Fc -f /backups/postgres/school_erp_$(date +\%Y\%m\%d_\%H\%M\%S).dump
```

### 3.2 Selective Library Tables Backup
To take a lightweight snapshot of library tables specifically:
```bash
pg_dump -h 187.127.150.113 -U erp_user -d school_erp \
  -t books \
  -t book_copies \
  -t book_categories \
  -t book_issues \
  -t book_reservations \
  -t library_settings \
  -t library_racks \
  -t library_shelves \
  -t library_suppliers \
  -t library_purchase_orders \
  -t library_purchase_order_items \
  -t library_goods_receipts \
  -t library_goods_receipt_items \
  -t library_periodicals \
  -t library_subscriptions \
  -t library_periodical_receipts \
  -t library_assets \
  -t library_asset_maintenance \
  -t library_damage_reports \
  -t library_disposal_records \
  -t library_stock_verification_sessions \
  -t library_stock_verification_items \
  -t library_fine_payments \
  -t library_audit_logs \
  -Fc -f /backups/postgres/library_module_$(date +\%Y\%m\%d).dump
```

---

## 4. Disaster Recovery & Restoration Procedures

### 4.1 Full Restoration
In the event of database catastrophe:
```bash
# Terminate existing connections
psql -h 187.127.150.113 -U erp_user -d postgres -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = 'school_erp' AND pid <> pg_backend_pid();"

# Restore database from compressed dump
pg_restore -h 187.127.150.113 -U erp_user -d school_erp --clean --if-exists --no-owner --no-privileges /backups/postgres/school_erp_YYYYMMDD_HHMMSS.dump
```

### 4.2 Post-Restoration Verification
Run the automated test suite to verify table structures, constraints, and service bindings:
```bash
php scripts/test_library_workflow.php
```
Ensure all 55 tests pass and transactions roll back cleanly.

---

## 5. Health Checks & Production Monitoring

| Component | Health Check Endpoint / Command | Expected Metric |
| :--- | :--- | :--- |
| **Library Web App** | `GET https://eps.dasatech.in/admin/library` | HTTP 200 OK |
| **REST Scanner API** | `GET https://eps.dasatech.in/admin/library/api/scan?query=ACC-` | JSON `{status: "success", data: [...]}` |
| **Database Latency** | `SELECT 1;` via pgsql | `< 150ms` |
| **Audit Log Count** | `SELECT count(*) FROM library_audit_logs;` | Monotonically increasing |
| **Orphaned Issues** | `SELECT count(*) FROM book_issues WHERE copy_id IS NULL;` | `0` (Zero orphaned rows) |

---

## 6. Rollback Runbook

If immediate rollback is mandated:
1. Revert to prior stable release commit:
   ```bash
   git checkout <PREVIOUS_COMMIT_SHA>
   ```
2. Clear caches:
   ```bash
   php artisan route:clear && php artisan config:clear
   ```
*Note:* The database schema changes are strictly additive and backward compatible with legacy code paths, meaning database rollback is not necessary for application revert.

