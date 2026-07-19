# FreshJuice Factory Management System

A comprehensive **web-based Factory Management System** for a Fresh Fruit Juice Production Factory. Built with **PHP 8.2+**, **MySQL (PDO)**, **Bootstrap 5**, **DataTables**, **SweetAlert2**, and **Chart.js**.

## Features Covered (All 14 SRS Modules)

| # | Module | Description |
|---|--------|-------------|
| 1 | **Supplier Management** | Supplier CRUD, fruit/ingredient delivery tracking, supplier evaluations |
| 2 | **Raw Material Inventory** | Fruits, sugar, additives — stock in/out, low-stock alerts |
| 3 | **Packaging Inventory** | Bottles, caps, labels, cartons, PVC wrappers |
| 4 | **Production Management** | Batch creation, auto-numbering, transactional stock deduction |
| 5 | **Quality Assurance/QC** | Incoming, In-process, Finished inspections — Pass/Fail/CAPA |
| 6 | **Finished Goods** | Auto-generated from approved batches, expiry tracking |
| 7 | **Sales & Invoicing** | Customer management, orders, stock deduction, invoices |
| 8 | **Staff & Shift Management** | Staff records, shifts, attendance tracking |
| 9 | **Maintenance Management** | Preventive/Corrective/Emergency, downtime, spare parts, cost |
| 10 | **Waste Management** | Waste per batch, disposal method, environmental impact |
| 11 | **Water Management** | Usage tracking (washing/mixing/cleaning), quality tests |
| 12 | **Power Management** | Electricity consumption, generator runtime & fuel |
| 13 | **Certification Management** | FDA, HACCP, ISO 22000, GSA — expiry reminders |
| 14 | **SOP Checklists** | Digital SOPs, checklist items, supervisor approval |

### Additional Modules

| Module | Description |
|--------|-------------|
| **Hazard Register** | Risk assessment with likelihood/consequence matrix and risk rating |
| **Accident Reports** | Incident tracking with root cause analysis and corrective actions |
| **Emergency Drills** | Drill scheduling, participants, outcomes, and issues |
| **Permits & Licenses** | Permit tracking with expiry reminders |
| **Training Records** | Staff training with certification expiry tracking |
| **PPE Tracking** | Personal protective equipment issuance and condition |
| **FAT Records** | Factory Acceptance Testing with pass/fail tracking |
| **Document Control** | Controlled document management with versioning |
| **CAPA / Initiatives** | Corrective and Preventive Actions with root cause analysis |
| **OEE / Efficiency** | Overall Equipment Effectiveness with live OEE calculation |
| **Machine Management** | Machine registry and status tracking |

## Security Features

- **CSRF Protection** — Tokens on all forms, validated at router level
- **Session Security** — `httponly`, `samesite=Strict`, strict mode, session regen on login
- **Rate Limiting** — Max 5 login attempts per user, 15-minute lockout window
- **Input Sanitization** — All output escaped via `htmlspecialchars()`
- **Prepared Statements** — PDO with `ERRMODE_EXCEPTION`, emulated prepares disabled
- **RBAC** — 8 roles with per-module access control
- **Audit Trail** — All CRUD operations logged with user, timestamp, and IP

## User Roles

| Role | Permissions |
|------|-------------|
| System Administrator | Full access, user management, audit trail |
| Factory Manager | All modules, reports, approvals |
| Production Supervisor | Production, batch management, SOP checklists |
| Inventory Officer | Raw materials, packaging, stock management |
| QA/QC Officer | Quality inspections, certifications, water quality |
| Sales Officer | Customers, orders, invoicing |
| Accountant | Invoices, payments, reports |
| Maintenance Engineer | Machines, maintenance records |

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Apache with mod_rewrite (XAMPP/WAMP/MAMP)

## Installation

### 1. Copy Project
```
# Copy freshjuice/ to your web server root
# e.g., C:\xampp\htdocs\freshjuice  OR  /var/www/html/freshjuice
```

### 2. Create Database
```bash
mysql -u root -p < sql/schema.sql
mysql -u root -p < sql/sample_data.sql
```

### 3. Configure (optional)
Copy `.env.example` to `.env` and edit your settings:
```ini
DB_HOST=localhost
DB_NAME=freshjuice
DB_USER=root
DB_PASS=
APP_URL=http://localhost/freshjuice
```
Or edit `config/database.php` directly.

### 4. Access
```
http://localhost/freshjuice/freshjuice
```

## Default Login

| User ID | Password | Role |
|---------|----------|------|
| USR-001 | password123 | System Administrator |
| USR-002 | password123 | Factory Manager |
| USR-003 | password123 | Production Supervisor |
| USR-004 | password123 | Inventory Officer |
| USR-005 | password123 | QA/QC Officer |
| USR-006 | password123 | Sales Officer |
| USR-007 | password123 | Accountant |
| USR-008 | password123 | Maintenance Engineer |

> **Note:** Default passwords are hashed with bcrypt. For production, update with `password_hash()`.

## Database Schema (26+ Tables)

`roles`, `users`, `staff`, `shifts`, `attendance`, `suppliers`, `supplier_deliveries`, `supplier_evaluations`, `raw_materials`, `packaging_materials`, `machines`, `machine_fat_records`, `production_batches`, `quality_inspections`, `finished_goods`, `customers`, `sales_orders`, `invoices`, `maintenance_records`, `waste_records`, `water_usage`, `water_quality_tests`, `power_usage`, `generator_log`, `certifications`, `sop_templates`, `sop_checklists`, `safety_inspections`, `hazard_register`, `accident_reports`, `emergency_drills`, `permits`, `training_records`, `ppe_records`, `documents`, `capa_initiatives`, `oee_records`, `audit_trail`

## Project Structure

```
freshjuice/
├── config/
│   ├── database.php          # DB config, session, CSRF, helpers
│   └── permissions.php       # RBAC permission matrix
├── models/                   # 20+ model files
├── controllers/              # 25+ controller files
├── auth/
│   └── AuthController.php    # Login/logout with CSRF & rate limiting
├── views/
│   ├── layouts/main.php      # Sidebar, navbar, flash messages
│   ├── auth/
│   ├── dashboard/
│   ├── suppliers/
│   ├── materials/
│   ├── production/
│   ├── quality/
│   ├── finished_goods/
│   ├── customers/
│   ├── sales/
│   ├── invoicing/
│   ├── staff/
│   ├── machines/
│   ├── maintenance/
│   ├── waste/
│   ├── water/
│   ├── power/
│   ├── certifications/
│   ├── sops/
│   ├── safety/
│   ├── improvement/
│   ├── efficiency/
│   ├── documents/
│   ├── permits/
│   ├── training/
│   ├── ppe/
│   └── users/
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── public/
│   └── index.php             # Router with CSRF enforcement
├── sql/
│   ├── schema.sql
│   └── sample_data.sql
├── .env.example              # Environment config template
├── .gitignore
├── .htaccess
└── README.md
```

## Key Business Logic

1. **Batch Creation** — Transactional deduction of raw materials and packaging stock (rollbacks on failure)
2. **Quality Pass** — Auto-creates finished goods with 6-month expiry (prevents duplicate creation)
3. **Sales Completion** — Transactional deduction of finished goods stock
4. **ID Generation** — Cryptographically random IDs: `FJ-20250710-ABC12`
5. **Audit Trail** — Tracks who did what, when, and from which IP
6. **Low-Stock Alerts** — Raw materials below minimum threshold
7. **Expiry Tracking** — Finished goods, certifications, and permits
8. **Risk Rating** — Automatic calculation from likelihood × consequence matrix
9. **OEE Calculation** — Real-time Availability × Performance × Quality

## License

MIT License — use freely for any project.
