# FreshJuice Factory Management System

A comprehensive **web-based Factory Management System** for a Fresh Fruit Juice Production Factory. Built with **PHP 8.2+**, **MySQL (PDO)**, **Bootstrap 5**, **DataTables**, **SweetAlert2**, and **Chart.js**.

## Features Covered (All 14 SRS Modules)

| # | Module | Description |
|---|--------|-------------|
| 1 | **Supplier Management** | Supplier CRUD, fruit/ingredient delivery tracking |
| 2 | **Raw Material Inventory** | Fruits, sugar, additives — stock in/out, low-stock alerts |
| 3 | **Packaging Inventory** | Bottles, caps, labels, cartons, PVC wrappers |
| 4 | **Production Management** | Batch creation, auto-numbering (FJ-20250710-001), stock deduction |
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

Plus: **Audit Trail**, **RBAC** (8 roles), **Dashboard with Charts**, **DataTables**, **SweetAlert2**.

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
- Apache with mod_rewrite
- Composer (optional)

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

### 3. Configure
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'freshjuice');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_URL', 'http://localhost/freshjuice');
```

### 4. Access
```
http://localhost/freshjuice
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

> **Note:** Sample passwords use bcrypt hash of `password`. For production, update with proper hashes using `password_hash()`.

## Database Schema (24 Tables)

`roles`, `users`, `staff`, `shifts`, `attendance`, `suppliers`, `supplier_deliveries`, `raw_materials`, `packaging_materials`, `machines`, `production_batches`, `quality_inspections`, `finished_goods`, `customers`, `sales_orders`, `invoices`, `maintenance_records`, `waste_records`, `water_usage`, `water_quality_tests`, `power_usage`, `generator_log`, `certifications`, `sop_templates`, `sop_checklists`, `audit_trail`

## Project Structure

```
freshjuice/
├── config/database.php
├── models/          (18 model files)
├── controllers/     (19 controller files)
├── views/
│   ├── layouts/main.php
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
│   └── users/
├── assets/css/style.css
├── assets/js/app.js
├── auth/AuthController.php
├── public/index.php
├── sql/schema.sql
├── sql/sample_data.sql
├── .htaccess
└── README.md
```

## Key Business Logic

1. **Batch Creation** → Auto-deducts raw material and packaging stock
2. **Quality Pass** → Auto-creates finished goods with 6-month expiry
3. **Sales Completion** → Auto-deducts finished goods stock
4. **ID Generation** → Smart codes: FJ-20250710-001, RM-20250710-ABC12
5. **Audit Trail** → Tracks who did what and when
6. **Low-Stock Alerts** → Raw materials below minimum threshold
7. **Expiry Tracking** → Finished goods and certifications

## License

MIT License — use freely for any project.
