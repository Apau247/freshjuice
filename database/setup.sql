-- ================================================================
-- FreshJuice Factory Management System
-- Complete MySQL Database Schema
-- PHP 8.2+ | MySQL 8.0+
-- ================================================================

CREATE DATABASE IF NOT EXISTS freshjuice
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE freshjuice;

-- ================================================================
-- 1. ROLES
-- ================================================================
CREATE TABLE roles (
    RoleID   VARCHAR(50)  PRIMARY KEY,
    RoleName VARCHAR(50)  NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ================================================================
-- 2. USERS (Authentication + RBAC)
-- ================================================================
CREATE TABLE users (
    UserID     VARCHAR(50)  PRIMARY KEY,
    RoleID     VARCHAR(50)  NOT NULL,
    Name       VARCHAR(100) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    Status     ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (RoleID) REFERENCES roles(RoleID) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ================================================================
-- 3. STAFF & SHIFTS
-- ================================================================
CREATE TABLE staff (
    StaffID       VARCHAR(50)  PRIMARY KEY,
    UserID        VARCHAR(50)  DEFAULT NULL,
    FirstName     VARCHAR(100) NOT NULL,
    LastName      VARCHAR(100) NOT NULL,
    Email         VARCHAR(150) DEFAULT NULL,
    Phone         VARCHAR(30)  DEFAULT NULL,
    Department    VARCHAR(50)  DEFAULT NULL,
    Position      VARCHAR(100) DEFAULT NULL,
    MonthlySalary DECIMAL(12,2) DEFAULT 0,      -- admin-set payment amount (payroll)
    DateHired     DATE         DEFAULT NULL,
    Status        ENUM('Active','On Leave','Terminated') DEFAULT 'Active',
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_staff_dept (Department)
) ENGINE=InnoDB;

CREATE TABLE shifts (
    ShiftID     VARCHAR(50)  PRIMARY KEY,
    ShiftName   VARCHAR(50)  NOT NULL,
    StartTime   TIME         NOT NULL,
    EndTime     TIME         NOT NULL,
    Description VARCHAR(200) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE attendance (
    AttendanceID  VARCHAR(50) PRIMARY KEY,
    StaffID       VARCHAR(50) NOT NULL,
    ShiftID       VARCHAR(50) DEFAULT NULL,
    Date          DATE        NOT NULL,
    ClockIn       TIME        DEFAULT NULL,
    ClockOut      TIME        DEFAULT NULL,
    Status        ENUM('Present','Absent','Late','Leave') DEFAULT 'Present',
    created_at    TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StaffID) REFERENCES staff(StaffID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ShiftID) REFERENCES shifts(ShiftID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_att_date (Date),
    INDEX idx_att_staff (StaffID)
) ENGINE=InnoDB;

-- ================================================================
--  3b. PAYROLL (employer payroll: monthly payslips, paid vs unpaid)
-- ================================================================
CREATE TABLE payroll (
    PayrollID      VARCHAR(50)    PRIMARY KEY,
    StaffID        VARCHAR(50)    NOT NULL,
    PeriodMonth    TINYINT        NOT NULL,              -- 1..12
    PeriodYear     SMALLINT       NOT NULL,
    BaseSalary     DECIMAL(12,2)  NOT NULL DEFAULT 0.00, -- copied from staff.MonthlySalary at generation
    Allowances     DECIMAL(12,2)  DEFAULT 0.00,          -- bonus / overtime / transport
    Deductions     DECIMAL(12,2)  DEFAULT 0.00,          -- SSNIT, advances, lateness...
    NetPay         DECIMAL(12,2)  NOT NULL DEFAULT 0.00,
    Status         ENUM('Unpaid','Paid') DEFAULT 'Unpaid',
    PaymentDate    DATE           DEFAULT NULL,
    PaymentMethod  VARCHAR(50)    DEFAULT NULL,
    Notes          TEXT           DEFAULT NULL,
    ProcessedBy    VARCHAR(50)    DEFAULT NULL,
    created_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StaffID) REFERENCES staff(StaffID) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY uq_payroll_period (StaffID, PeriodMonth, PeriodYear),
    INDEX idx_payroll_status (Status),
    INDEX idx_payroll_period (PeriodYear, PeriodMonth)
) ENGINE=InnoDB;

-- ================================================================
-- 4. SUPPLIERS
-- ================================================================
CREATE TABLE suppliers (
    SupplierID VARCHAR(50)  PRIMARY KEY,
    Name       VARCHAR(150) NOT NULL,
    Contact    VARCHAR(100) DEFAULT NULL,
    Email      VARCHAR(150) DEFAULT NULL,
    Phone      VARCHAR(30)  DEFAULT NULL,
    Address    TEXT         DEFAULT NULL,
    Type       VARCHAR(50)  DEFAULT 'Fruit Supplier',
    Status     ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ================================================================
-- 5. SUPPLIER DELIVERIES
-- ================================================================
CREATE TABLE supplier_deliveries (
    DeliveryID    VARCHAR(50)    PRIMARY KEY,
    SupplierID    VARCHAR(50)    NOT NULL,
    DeliveryDate  DATE           NOT NULL,
    ItemName      VARCHAR(150)   NOT NULL,
    Quantity      DECIMAL(10,2)  NOT NULL DEFAULT 0,
    Unit          VARCHAR(30)    DEFAULT 'kg',
    QualityGrade  VARCHAR(50)    DEFAULT 'Grade A',
    ReceivedBy    VARCHAR(50)    DEFAULT NULL,
    Notes         TEXT           DEFAULT NULL,
    Status        ENUM('Received','Pending','Rejected') DEFAULT 'Received',
    created_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SupplierID) REFERENCES suppliers(SupplierID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ReceivedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_del_date (DeliveryDate)
) ENGINE=InnoDB;

-- ================================================================
-- 6. RAW MATERIALS
-- ================================================================
CREATE TABLE raw_materials (
    MaterialID   VARCHAR(50)    PRIMARY KEY,
    Name         VARCHAR(150)   NOT NULL,
    Type         VARCHAR(50)    DEFAULT NULL,
    Unit         VARCHAR(30)    DEFAULT 'kg',
    CurrentStock DECIMAL(10,2)  DEFAULT 0,
    MinStock     DECIMAL(10,2)  DEFAULT 0,
    SupplierID   VARCHAR(50)    DEFAULT NULL,
    Status       ENUM('Active','Inactive') DEFAULT 'Active',
    created_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SupplierID) REFERENCES suppliers(SupplierID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_rm_type (Type)
) ENGINE=InnoDB;

-- ================================================================
-- 7. PACKAGING MATERIALS
-- ================================================================
CREATE TABLE packaging_materials (
    PackageID    VARCHAR(50)    PRIMARY KEY,
    Name         VARCHAR(150)   NOT NULL,
    Type         VARCHAR(50)    DEFAULT NULL,
    Unit         VARCHAR(30)    DEFAULT 'pcs',
    CurrentStock DECIMAL(10,2)  DEFAULT 0,
    MinStock     DECIMAL(10,2)  DEFAULT 0,
    Status       ENUM('Active','Inactive') DEFAULT 'Active',
    created_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pm_type (Type)
) ENGINE=InnoDB;

-- ================================================================
-- 8. MACHINES
-- ================================================================
CREATE TABLE machines (
    MachineID     VARCHAR(50)  PRIMARY KEY,
    Name          VARCHAR(150) NOT NULL,
    Type          VARCHAR(100) DEFAULT NULL,
    Location      VARCHAR(100) DEFAULT NULL,
    Status        ENUM('Operational','Maintenance','Down','Decommissioned') DEFAULT 'Operational',
    InstallDate   DATE         DEFAULT NULL,
    LastService   DATE         DEFAULT NULL,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ================================================================
-- 9. PRODUCTION BATCHES
-- ================================================================
CREATE TABLE production_batches (
    BatchID               VARCHAR(50)    PRIMARY KEY,
    BatchNumber           VARCHAR(50)    NOT NULL UNIQUE,
    ProductionDate        DATE           NOT NULL,
    Flavour               VARCHAR(100)   NOT NULL,
    Quantity              DECIMAL(10,2)  NOT NULL DEFAULT 0,
    Unit                  VARCHAR(30)    DEFAULT 'litres',
    Status                ENUM('Pending','In Progress','Completed','Rejected','Cancelled') DEFAULT 'Pending',
    UserID                VARCHAR(50)    DEFAULT NULL,
    RawMaterialID         VARCHAR(50)    DEFAULT NULL,
    PackagingMaterialID   VARCHAR(50)    DEFAULT NULL,
    MachineID             VARCHAR(50)    DEFAULT NULL,
    Notes                 TEXT           DEFAULT NULL,
    created_at            TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID)              REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (RawMaterialID)       REFERENCES raw_materials(MaterialID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (PackagingMaterialID) REFERENCES packaging_materials(PackageID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (MachineID)           REFERENCES machines(MachineID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_pb_date   (ProductionDate),
    INDEX idx_pb_status (Status),
    INDEX idx_pb_flavour(Flavour)
) ENGINE=InnoDB;

-- ================================================================
-- 10. QUALITY INSPECTIONS
-- ================================================================
CREATE TABLE quality_inspections (
    InspectionID     VARCHAR(50)  PRIMARY KEY,
    InspectionType   ENUM('Incoming','In-Process','Finished') NOT NULL,
    BatchID          VARCHAR(50)  DEFAULT NULL,
    InspectionDate   DATE         NOT NULL,
    Result           ENUM('Pass','Fail','Pending') DEFAULT 'Pending',
    DefectsFound     TEXT         DEFAULT NULL,
    TestResults      TEXT         DEFAULT NULL,
    CAPA             TEXT         DEFAULT NULL,
    InspectorID      VARCHAR(50)  DEFAULT NULL,
    Status           ENUM('Open','In Progress','Closed') DEFAULT 'Open',
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (BatchID)     REFERENCES production_batches(BatchID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (InspectorID) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_qi_type  (InspectionType),
    INDEX idx_qi_result(Result),
    INDEX idx_qi_batch (BatchID)
) ENGINE=InnoDB;

-- ================================================================
-- 11. FINISHED GOODS
-- ================================================================
CREATE TABLE finished_goods (
    FG_ID              VARCHAR(50)    PRIMARY KEY,
    BatchID            VARCHAR(50)    DEFAULT NULL,
    Flavour            VARCHAR(100)   NOT NULL,
    ExpiryDate         DATE           NOT NULL,
    QuantityAvailable  DECIMAL(10,2)  DEFAULT 0,
    Unit               VARCHAR(30)    DEFAULT 'bottles',
    StorageLocation    VARCHAR(100)   DEFAULT NULL,
    created_at         TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (BatchID) REFERENCES production_batches(BatchID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_fg_expiry (ExpiryDate),
    INDEX idx_fg_flavour(Flavour)
) ENGINE=InnoDB;

-- ================================================================
--  11b. PRODUCT PRICES (default selling price per product flavour)
-- ================================================================
CREATE TABLE product_prices (
    Flavour     VARCHAR(100)   PRIMARY KEY,
    UnitPrice   DECIMAL(12,2)  NOT NULL DEFAULT 0.00,
    UpdatedBy   VARCHAR(50)    DEFAULT NULL,
    updated_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (UpdatedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- ================================================================
-- 12. CUSTOMERS
-- ================================================================
CREATE TABLE customers (
    CustomerID VARCHAR(50)  PRIMARY KEY,
    Name       VARCHAR(150) NOT NULL,
    Contact    VARCHAR(100) DEFAULT NULL,
    Email      VARCHAR(150) DEFAULT NULL,
    Phone      VARCHAR(30)  DEFAULT NULL,
    Address    TEXT         DEFAULT NULL,
    Type       VARCHAR(50)  DEFAULT 'Retailer',
    Status     ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ================================================================
-- 13. SALES ORDERS
-- ================================================================
CREATE TABLE sales_orders (
    OrderID     VARCHAR(50)    PRIMARY KEY,
    OrderDate   DATE           NOT NULL,
    TotalAmount DECIMAL(15,2)  DEFAULT 0.00,
    Quantity    DECIMAL(10,2)  DEFAULT 0,
    Status      ENUM('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending',
    CustomerID  VARCHAR(50)    DEFAULT NULL,
    FG_ID       VARCHAR(50)    DEFAULT NULL,
    CreatedBy   VARCHAR(50)    DEFAULT NULL,
    Notes       TEXT           DEFAULT NULL,
    created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CustomerID) REFERENCES customers(CustomerID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (FG_ID)      REFERENCES finished_goods(FG_ID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (CreatedBy)  REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_so_date   (OrderDate),
    INDEX idx_so_status (Status)
) ENGINE=InnoDB;

-- ================================================================
-- 14. INVOICES
-- ================================================================
CREATE TABLE invoices (
    InvoiceID      VARCHAR(50)    PRIMARY KEY,
    InvoiceDate    DATE           NOT NULL,
    Amount         DECIMAL(15,2)  DEFAULT 0.00,
    Tax            DECIMAL(15,2)  DEFAULT 0.00,
    TotalDue       DECIMAL(15,2)  DEFAULT 0.00,
    PaymentStatus  ENUM('Unpaid','Partial','Paid','Overdue') DEFAULT 'Unpaid',
    DueDate        DATE           DEFAULT NULL,
    OrderID        VARCHAR(50)    DEFAULT NULL,
    created_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (OrderID) REFERENCES sales_orders(OrderID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_inv_status (PaymentStatus)
) ENGINE=InnoDB;

-- ================================================================
-- 15. MAINTENANCE RECORDS
-- ================================================================
CREATE TABLE maintenance_records (
    MaintenanceID   VARCHAR(50)    PRIMARY KEY,
    MaintenanceType ENUM('Preventive','Corrective','Emergency') DEFAULT 'Preventive',
    MaintenanceDate DATE           NOT NULL,
    Downtime        DECIMAL(10,2)  DEFAULT 0.00,
    Cost            DECIMAL(15,2)  DEFAULT 0.00,
    MachineID       VARCHAR(50)    DEFAULT NULL,
    TechnicianID    VARCHAR(50)    DEFAULT NULL,
    Description     TEXT           DEFAULT NULL,
    SpareParts      TEXT           DEFAULT NULL,
    Status          ENUM('Scheduled','In Progress','Completed','Cancelled') DEFAULT 'Scheduled',
    NextServiceDate DATE           DEFAULT NULL,
    created_at      TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MachineID)    REFERENCES machines(MachineID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (TechnicianID) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_maint_date (MaintenanceDate)
) ENGINE=InnoDB;

-- ================================================================
-- 16. WASTE RECORDS
-- ================================================================
CREATE TABLE waste_records (
    WasteID       VARCHAR(50)    PRIMARY KEY,
    Date          DATE           NOT NULL,
    WasteType     VARCHAR(50)    DEFAULT 'Production',
    Quantity      DECIMAL(10,2)  DEFAULT 0.00,
    Unit          VARCHAR(30)    DEFAULT 'kg',
    DisposalMethod VARCHAR(100)  DEFAULT 'Landfill',
    BatchID       VARCHAR(50)    DEFAULT NULL,
    EnvironmentalImpact TEXT     DEFAULT NULL,
    RecordedBy    VARCHAR(50)    DEFAULT NULL,
    created_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (BatchID)    REFERENCES production_batches(BatchID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (RecordedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_waste_date (Date)
) ENGINE=InnoDB;

-- ================================================================
-- 17. WATER USAGE
-- ================================================================
CREATE TABLE water_usage (
    WaterUsageID  VARCHAR(50)    PRIMARY KEY,
    Date          DATE           NOT NULL,
    UsageType     VARCHAR(50)    NOT NULL,
    Quantity      DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    Unit          VARCHAR(30)    DEFAULT 'litres',
    Purpose       VARCHAR(150)   DEFAULT NULL,
    RecordedBy    VARCHAR(50)    DEFAULT NULL,
    created_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (RecordedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_wu_date (Date)
) ENGINE=InnoDB;

-- ================================================================
-- 18. WATER QUALITY TESTS
-- ================================================================
CREATE TABLE water_quality_tests (
    WaterTestID     VARCHAR(50)    PRIMARY KEY,
    TestDate        DATE           NOT NULL,
    TestType        VARCHAR(100)   NOT NULL,
    pH_Level        DECIMAL(4,2)   DEFAULT NULL,
    Turbidity       DECIMAL(8,2)   DEFAULT NULL,
    TDS             DECIMAL(8,2)   DEFAULT NULL,
    Chlorine        DECIMAL(6,2)   DEFAULT NULL,
    BacteriaCount   DECIMAL(10,2)  DEFAULT NULL,
    Result          ENUM('Pass','Fail','Pending') DEFAULT 'Pending',
    Notes           TEXT           DEFAULT NULL,
    TestedBy        VARCHAR(50)    DEFAULT NULL,
    created_at      TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (TestedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_wqt_date (TestDate)
) ENGINE=InnoDB;

-- ================================================================
-- 19. POWER USAGE
-- ================================================================
CREATE TABLE power_usage (
    PowerUsageID   VARCHAR(50)    PRIMARY KEY,
    Date           DATE           NOT NULL,
    Source         ENUM('Grid','Generator','Solar') DEFAULT 'Grid',
    ConsumptionKWh DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    Cost           DECIMAL(15,2)  DEFAULT 0.00,
    Notes          TEXT           DEFAULT NULL,
    created_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pu_date (Date)
) ENGINE=InnoDB;

-- ================================================================
-- 20. GENERATOR LOG
-- ================================================================
CREATE TABLE generator_log (
    LogID         VARCHAR(50)    PRIMARY KEY,
    Date          DATE           NOT NULL,
    StartTime     TIME           DEFAULT NULL,
    EndTime       TIME           DEFAULT NULL,
    RuntimeHrs    DECIMAL(6,2)   DEFAULT 0.00,
    FuelUsed      DECIMAL(10,2)  DEFAULT 0.00,
    FuelUnit      VARCHAR(30)    DEFAULT 'litres',
    Reason        VARCHAR(200)   DEFAULT NULL,
    Notes         TEXT           DEFAULT NULL,
    created_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_gl_date (Date)
) ENGINE=InnoDB;

-- ================================================================
-- 21. CERTIFICATIONS
-- ================================================================
CREATE TABLE certifications (
    CertID          VARCHAR(50)  PRIMARY KEY,
    CertName        VARCHAR(150) NOT NULL,
    CertType        VARCHAR(100) NOT NULL,
    IssuingAuthority VARCHAR(150) DEFAULT NULL,
    IssueDate       DATE         NOT NULL,
    ExpiryDate      DATE         NOT NULL,
    DocumentPath    VARCHAR(500) DEFAULT NULL,
    Status          ENUM('Active','Expired','Pending Renewal') DEFAULT 'Active',
    Notes           TEXT         DEFAULT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cert_expiry (ExpiryDate),
    INDEX idx_cert_type  (CertType)
) ENGINE=InnoDB;

-- ================================================================
-- 22. SOP TEMPLATES
-- ================================================================
CREATE TABLE sop_templates (
    SOP_ID        VARCHAR(50)  PRIMARY KEY,
    Title         VARCHAR(200) NOT NULL,
    Department    VARCHAR(50)  DEFAULT NULL,
    Version       VARCHAR(20)  DEFAULT '1.0',
    Content       TEXT         DEFAULT NULL,
    EffectiveDate DATE         DEFAULT NULL,
    ReviewDate    DATE         DEFAULT NULL,
    Status        ENUM('Active','Under Review','Archived') DEFAULT 'Active',
    CreatedBy     VARCHAR(50)  DEFAULT NULL,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CreatedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- ================================================================
-- 23. SOP CHECKLISTS (Instances)
-- ================================================================
CREATE TABLE sop_checklists (
    ChecklistID     VARCHAR(50)  PRIMARY KEY,
    SOP_ID          VARCHAR(50)  NOT NULL,
    BatchID         VARCHAR(50)  DEFAULT NULL,
    Date            DATE         NOT NULL,
    ChecklistItems  JSON         DEFAULT NULL,
    CompletedItems  INT          DEFAULT 0,
    TotalItems      INT          DEFAULT 0,
    SupervisorID    VARCHAR(50)  DEFAULT NULL,
    ApprovalStatus  ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    ApprovedAt      DATETIME     DEFAULT NULL,
    Notes           TEXT         DEFAULT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SOP_ID)       REFERENCES sop_templates(SOP_ID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (BatchID)      REFERENCES production_batches(BatchID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (SupervisorID) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_sc_date (Date)
) ENGINE=InnoDB;

-- ================================================================
-- 24. AUDIT TRAIL
-- ================================================================
CREATE TABLE audit_trail (
    AuditID     BIGINT       AUTO_INCREMENT PRIMARY KEY,
    UserID      VARCHAR(50)  DEFAULT NULL,
    Action      VARCHAR(50)  NOT NULL,
    Module      VARCHAR(50)  NOT NULL,
    RecordID    VARCHAR(50)  DEFAULT NULL,
    Details     TEXT         DEFAULT NULL,
    IPAddress   VARCHAR(45)  DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_at_user   (UserID),
    INDEX idx_at_module (Module),
    INDEX idx_at_date   (created_at)
) ENGINE=InnoDB;

-- ================================================================
-- 25. SAFETY INSPECTIONS
-- ================================================================
CREATE TABLE safety_inspections (
    SafetyID          VARCHAR(50)    PRIMARY KEY,
    InspectionDate    DATE           NOT NULL,
    InspectionType    VARCHAR(100)   NOT NULL,
    Area              VARCHAR(150)   NOT NULL,
    Findings          TEXT           DEFAULT NULL,
    HazardLevel       ENUM('Low','Medium','High','Critical') DEFAULT 'Low',
    CorrectiveAction  TEXT           DEFAULT NULL,
    ResponsiblePerson VARCHAR(50)    DEFAULT NULL,
    TargetDate        DATE           DEFAULT NULL,
    Status            ENUM('Open','In Progress','Closed') DEFAULT 'Open',
    InspectorID       VARCHAR(50)    DEFAULT NULL,
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (InspectorID) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_si_date (InspectionDate),
    INDEX idx_si_level (HazardLevel)
) ENGINE=InnoDB;

-- ================================================================
-- 26. HAZARD REGISTER
-- ================================================================
CREATE TABLE hazard_register (
    HazardID          VARCHAR(50)    PRIMARY KEY,
    HazardDescription TEXT           NOT NULL,
    RiskCategory      VARCHAR(100)   DEFAULT NULL,
    Likelihood        ENUM('Rare','Unlikely','Possible','Likely','Almost Certain') DEFAULT 'Possible',
    Consequence       ENUM('Insignificant','Minor','Moderate','Major','Catastrophic') DEFAULT 'Moderate',
    RiskRating        INT            DEFAULT 0,
    ControlMeasures   TEXT           DEFAULT NULL,
    ResponsiblePerson VARCHAR(50)    DEFAULT NULL,
    ReviewDate        DATE           DEFAULT NULL,
    Status            ENUM('Active','Mitigated','Closed') DEFAULT 'Active',
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_hr_rating (RiskRating),
    INDEX idx_hr_status (Status)
) ENGINE=InnoDB;

-- ================================================================
-- 27. ACCIDENT / INCIDENT REPORTS
-- ================================================================
CREATE TABLE accident_reports (
    AccidentID        VARCHAR(50)    PRIMARY KEY,
    IncidentDate      DATETIME       NOT NULL,
    Location          VARCHAR(150)   NOT NULL,
    IncidentType      VARCHAR(100)   NOT NULL,
    Description       TEXT           NOT NULL,
    Injuries          TEXT           DEFAULT NULL,
    RootCause         TEXT           DEFAULT NULL,
    CorrectiveAction  TEXT           DEFAULT NULL,
    ReportedBy        VARCHAR(50)    DEFAULT NULL,
    Status            ENUM('Reported','Under Investigation','Closed') DEFAULT 'Reported',
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ReportedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_ar_date (IncidentDate)
) ENGINE=InnoDB;

-- ================================================================
-- 28. PERMITS & LICENSES
-- ================================================================
CREATE TABLE permits (
    PermitID          VARCHAR(50)    PRIMARY KEY,
    PermitName        VARCHAR(200)   NOT NULL,
    PermitType        VARCHAR(100)   NOT NULL,
    IssuingAuthority  VARCHAR(200)   DEFAULT NULL,
    PermitNumber      VARCHAR(100)   DEFAULT NULL,
    IssueDate         DATE           NOT NULL,
    ExpiryDate        DATE           NOT NULL,
    DocumentPath      VARCHAR(500)   DEFAULT NULL,
    Status            ENUM('Active','Expired','Suspended','Pending Renewal') DEFAULT 'Active',
    Notes             TEXT           DEFAULT NULL,
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_perm_expiry (ExpiryDate),
    INDEX idx_perm_type (PermitType)
) ENGINE=InnoDB;

-- ================================================================
-- 29. EMPLOYEE TRAINING RECORDS
-- ================================================================
CREATE TABLE training_records (
    TrainingID        VARCHAR(50)    PRIMARY KEY,
    StaffID           VARCHAR(50)    NOT NULL,
    TrainingType      VARCHAR(150)   NOT NULL,
    TrainingDate      DATE           NOT NULL,
    Duration          VARCHAR(50)    DEFAULT NULL,
    Trainer           VARCHAR(150)   DEFAULT NULL,
    CertificateNo     VARCHAR(100)   DEFAULT NULL,
    ExpiryDate        DATE           DEFAULT NULL,
    Status            ENUM('Scheduled','Completed','Failed','Cancelled') DEFAULT 'Scheduled',
    Notes             TEXT           DEFAULT NULL,
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StaffID) REFERENCES staff(StaffID) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_tr_date (TrainingDate),
    INDEX idx_tr_staff (StaffID)
) ENGINE=InnoDB;

-- ================================================================
-- 30. PPE TRACKING
-- ================================================================
CREATE TABLE ppe_records (
    PPE_ID            VARCHAR(50)    PRIMARY KEY,
    StaffID           VARCHAR(50)    NOT NULL,
    PPESource         VARCHAR(100)   NOT NULL,
    DateIssued        DATE           NOT NULL,
    ExpiryDate        DATE           DEFAULT NULL,
    `Condition`       ENUM('New','Good','Fair','Poor','Expired') DEFAULT 'New',
    ReplacementNeeded TINYINT(1)     DEFAULT 0,
    Notes             TEXT           DEFAULT NULL,
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StaffID) REFERENCES staff(StaffID) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_ppe_staff (StaffID)
) ENGINE=InnoDB;

-- ================================================================
-- 31. PRODUCTION EFFICIENCY / OEE
-- ================================================================
CREATE TABLE production_efficiency (
    EfficiencyID      VARCHAR(50)    PRIMARY KEY,
    Date              DATE           NOT NULL,
    Shift             VARCHAR(50)    DEFAULT NULL,
    MachineID         VARCHAR(50)    DEFAULT NULL,
    PlannedRunTime    DECIMAL(10,2)  DEFAULT 0.00,
    ActualRunTime     DECIMAL(10,2)  DEFAULT 0.00,
    DowntimeMinutes   DECIMAL(10,2)  DEFAULT 0.00,
    TotalProduced     INT            DEFAULT 0,
    GoodProduced      INT            DEFAULT 0,
    DefectCount       INT            DEFAULT 0,
    AvailabilityRate  DECIMAL(5,2)   DEFAULT 0.00,
    PerformanceRate   DECIMAL(5,2)   DEFAULT 0.00,
    QualityRate       DECIMAL(5,2)   DEFAULT 0.00,
    OEE               DECIMAL(5,2)   DEFAULT 0.00,
    Notes             TEXT           DEFAULT NULL,
    recordedBy        VARCHAR(50)    DEFAULT NULL,
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MachineID) REFERENCES machines(MachineID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (recordedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_pe_date (Date)
) ENGINE=InnoDB;

-- ================================================================
-- 32. CONTINUOUS IMPROVEMENT / CAPA
-- ================================================================
CREATE TABLE improvement_initiatives (
    InitiativeID      VARCHAR(50)    PRIMARY KEY,
    Title             VARCHAR(200)   NOT NULL,
    Category          VARCHAR(100)   DEFAULT NULL,
    Description       TEXT           DEFAULT NULL,
    RootCauseAnalysis TEXT           DEFAULT NULL,
    ActionPlan        TEXT           DEFAULT NULL,
    TargetDate        DATE           DEFAULT NULL,
    ResponsiblePerson VARCHAR(50)    DEFAULT NULL,
    Status            ENUM('Proposed','Approved','In Progress','Completed','Cancelled') DEFAULT 'Proposed',
    Effectiveness     TEXT           DEFAULT NULL,
    CreatedBy         VARCHAR(50)    DEFAULT NULL,
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (CreatedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_ii_status (Status),
    INDEX idx_ii_target (TargetDate)
) ENGINE=InnoDB;

-- ================================================================
-- 33. DOCUMENT CONTROL
-- ================================================================
CREATE TABLE documents (
    DocID             VARCHAR(50)    PRIMARY KEY,
    Title             VARCHAR(200)   NOT NULL,
    DocType           VARCHAR(100)   NOT NULL,
    Version           VARCHAR(20)    DEFAULT '1.0',
    FilePath          VARCHAR(500)   DEFAULT NULL,
    Description       TEXT           DEFAULT NULL,
    Department        VARCHAR(100)   DEFAULT NULL,
    EffectiveDate     DATE           DEFAULT NULL,
    ReviewDate        DATE           DEFAULT NULL,
    Status            ENUM('Draft','Under Review','Approved','Obsolete') DEFAULT 'Draft',
    ApprovedBy        VARCHAR(50)    DEFAULT NULL,
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ApprovedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_doc_type (DocType),
    INDEX idx_doc_status (Status)
) ENGINE=InnoDB;

-- ================================================================
-- 34. SUPPLIER EVALUATION
-- ================================================================
CREATE TABLE supplier_evaluations (
    EvaluationID      VARCHAR(50)    PRIMARY KEY,
    SupplierID        VARCHAR(50)    NOT NULL,
    EvaluationDate    DATE           NOT NULL,
    QualityScore      DECIMAL(3,1)   DEFAULT 0.0,
    DeliveryScore     DECIMAL(3,1)   DEFAULT 0.0,
    PriceScore        DECIMAL(3,1)   DEFAULT 0.0,
    OverallScore      DECIMAL(3,1)   DEFAULT 0.0,
    Strengths         TEXT           DEFAULT NULL,
    Weaknesses        TEXT           DEFAULT NULL,
    Recommendations   TEXT           DEFAULT NULL,
    EvaluatedBy       VARCHAR(50)    DEFAULT NULL,
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SupplierID) REFERENCES suppliers(SupplierID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (EvaluatedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_se_supplier (SupplierID)
) ENGINE=InnoDB;

-- ================================================================
-- 35. EMERGENCY DRILLS
-- ================================================================
CREATE TABLE emergency_drills (
    DrillID           VARCHAR(50)    PRIMARY KEY,
    DrillDate         DATE           NOT NULL,
    DrillType         VARCHAR(100)   NOT NULL,
    Location          VARCHAR(150)   DEFAULT NULL,
    ParticipantsCount INT            DEFAULT 0,
    DurationMinutes   INT            DEFAULT 0,
    Outcome           TEXT           DEFAULT NULL,
    IssuesFound       TEXT           DEFAULT NULL,
    CorrectiveAction  TEXT           DEFAULT NULL,
    ConductedBy       VARCHAR(50)    DEFAULT NULL,
    Status            ENUM('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ConductedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_ed_date (DrillDate)
) ENGINE=InnoDB;

-- ================================================================
-- 36. FAT RECORDS (Factory Acceptance Testing)
-- ================================================================
CREATE TABLE fat_records (
    FAT_ID            VARCHAR(50)    PRIMARY KEY,
    MachineID         VARCHAR(50)    DEFAULT NULL,
    TestDate          DATE           NOT NULL,
    TestType          VARCHAR(100)   NOT NULL,
    ExpectedResult    TEXT           DEFAULT NULL,
    ActualResult      TEXT           DEFAULT NULL,
    Result            ENUM('Pending','Pass','Fail','Conditional') DEFAULT 'Pending',
    DefectsFound      TEXT           DEFAULT NULL,
    TestedBy          VARCHAR(50)    DEFAULT NULL,
    Notes             TEXT           DEFAULT NULL,
    Status            ENUM('Pending','In Progress','Completed') DEFAULT 'Pending',
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MachineID) REFERENCES machines(MachineID) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (TestedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_fat_date (TestDate),
    INDEX idx_fat_result (Result)
) ENGINE=InnoDB;

-- ================================================================
-- MIGRATION: Add password reset fields
-- ================================================================
ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL;
ALTER TABLE users ADD COLUMN reset_expires DATETIME DEFAULT NULL;

-- ================================================================
-- MIGRATION: Notifications (per-user alerts)
-- ================================================================
CREATE TABLE IF NOT EXISTS notifications (
    NotificationID  VARCHAR(50)  PRIMARY KEY,
    UserID          VARCHAR(50)  NOT NULL,
    Title           VARCHAR(150) NOT NULL,
    Message         TEXT         DEFAULT NULL,
    IsRead          TINYINT(1)   DEFAULT 0,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_notif_user_read (UserID, IsRead),
    INDEX idx_notif_created  (created_at)
) ENGINE=InnoDB;

-- ================================================================
-- MIGRATION: Workers (factory laborers — separate from login users)
-- ================================================================
CREATE TABLE IF NOT EXISTS workers (
    WorkerID      VARCHAR(50)  PRIMARY KEY,
    FirstName     VARCHAR(100) NOT NULL,
    LastName      VARCHAR(100) NOT NULL,
    Phone         VARCHAR(30)  DEFAULT NULL,
    Position      VARCHAR(100) DEFAULT 'Laborer',
    MonthlyPay    DECIMAL(12,2) DEFAULT 0,
    DateHired     DATE         DEFAULT NULL,
    Status        ENUM('Active','On Leave','Terminated') DEFAULT 'Active',
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_workers_status (Status)
) ENGINE=InnoDB;


-- ================================================================
-- SAMPLE DATA
-- ================================================================

-- ================================================================
-- FreshJuice Factory Management System — Sample Data
-- ================================================================
USE freshjuice;

-- ROLES
INSERT INTO roles (RoleID, RoleName) VALUES
('ROLE-001','System Administrator'),
('ROLE-002','Factory Manager'),
('ROLE-003','Production Supervisor'),
('ROLE-004','Inventory Officer'),
('ROLE-005','QA/QC Officer'),
('ROLE-006','Sales Officer'),
('ROLE-007','Accountant'),
('ROLE-008','Maintenance Engineer');

-- USERS (password = 'password123')
INSERT INTO users (UserID, RoleID, Name, password) VALUES
('USR-001','ROLE-001','Kwame Admin',      '$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK'),
('USR-002','ROLE-002','Ama Manager',      '$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK'),
('USR-003','ROLE-003','Kofi Production',  '$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK'),
('USR-004','ROLE-004','Akosua Inventory', '$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK'),
('USR-005','ROLE-005','Yaw QA',           '$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK'),
('USR-006','ROLE-006','Esi Sales',        '$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK'),
('USR-007','ROLE-007','Kojo Accountant',  '$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK'),
('USR-008','ROLE-008','Nana Maintenance', '$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK');

-- STAFF
INSERT INTO staff (StaffID, UserID, FirstName, LastName, Email, Phone, Department, Position, DateHired) VALUES
('STF-001','USR-001','Kwame','Admin','kwame@freshjuice.com','0241234567','Administration','System Admin','2024-01-15'),
('STF-002','USR-002','Ama','Manager','ama@freshjuice.com','0242345678','Management','Factory Manager','2024-01-15'),
('STF-003','USR-003','Kofi','Production','kofi@freshjuice.com','0243456789','Production','Production Supervisor','2024-02-01'),
('STF-004','USR-004','Akosua','Inventory','akosua@freshjuice.com','0244567890','Inventory','Inventory Officer','2024-02-01'),
('STF-005','USR-005','Yaw','QA','yaw@freshjuice.com','0245678901','Quality Assurance','QA/QC Officer','2024-02-15'),
('STF-006','USR-006','Esi','Sales','esi@freshjuice.com','0246789012','Sales','Sales Officer','2024-03-01'),
('STF-007','USR-007','Kojo','Accountant','kojo@freshjuice.com','0247890123','Finance','Accountant','2024-03-01'),
('STF-008','USR-008','Nana','Maintenance','nana@freshjuice.com','0248901234','Maintenance','Maintenance Engineer','2024-03-15');

-- SHIFTS
INSERT INTO shifts (ShiftID, ShiftName, StartTime, EndTime, Description) VALUES
('SHF-001','Morning','06:00:00','14:00:00','Morning production shift'),
('SHF-002','Afternoon','14:00:00','22:00:00','Afternoon production shift'),
('SHF-003','Night','22:00:00','06:00:00','Night maintenance shift');

-- SUPPLIERS
INSERT INTO suppliers (SupplierID, Name, Contact, Email, Phone, Type) VALUES
('SUP-001','Tropical Fruits Ltd','John Doe','info@tropicalfruits.com','0301234567','Fruit Supplier'),
('SUP-002','Sweet Sugar Corp','Jane Smith','sales@sweetsugar.com','0302345678','Ingredient Supplier'),
('SUP-003','ClearPack Solutions','Mike Brown','orders@clearpack.com','0303456789','Packaging Supplier'),
('SUP-004','AquaPure Water','Sara Wilson','contact@aquapure.com','0304567890','Water Supplier'),
('SUP-005','GreenLeaf Organics','Tom Green','hello@greenleaf.com','0305678901','Organic Fruit Supplier');

-- SUPPLIER DELIVERIES
INSERT INTO supplier_deliveries (DeliveryID, SupplierID, DeliveryDate, ItemName, Quantity, Unit, QualityGrade, ReceivedBy, Status) VALUES
('DLV-001','SUP-001','2025-07-01','Fresh Mangoes',500,'kg','Grade A','USR-004','Received'),
('DLV-002','SUP-001','2025-07-03','Fresh Oranges',800,'kg','Grade A','USR-004','Received'),
('DLV-003','SUP-002','2025-07-05','Cane Sugar',200,'kg','Premium','USR-004','Received'),
('DLV-004','SUP-005','2025-07-07','Organic Pineapples',300,'kg','Organic','USR-004','Received'),
('DLV-005','SUP-001','2025-07-09','Fresh Strawberries',150,'kg','Grade B','USR-004','Received');

-- RAW MATERIALS
INSERT INTO raw_materials (MaterialID, Name, Type, Unit, CurrentStock, MinStock, SupplierID) VALUES
('RM-001','Fresh Mangoes','Fruit','kg',1200,200,'SUP-001'),
('RM-002','Fresh Oranges','Fruit','kg',1500,200,'SUP-001'),
('RM-003','Organic Pineapples','Fruit','kg',800,150,'SUP-005'),
('RM-004','Fresh Strawberries','Fruit','kg',400,100,'SUP-001'),
('RM-005','Cane Sugar','Sweetener','kg',600,100,'SUP-002'),
('RM-006','Citric Acid','Additive','kg',50,10,'SUP-002'),
('RM-007','Ascorbic Acid (Vitamin C)','Additive','kg',30,5,'SUP-002'),
('RM-008','Natural Flavour Enhancer','Additive','kg',20,5,'SUP-002');

-- PACKAGING MATERIALS
INSERT INTO packaging_materials (PackageID, Name, Type, Unit, CurrentStock, MinStock) VALUES
('PKG-001','500ml PET Bottle','Bottle','pcs',10000,2000),
('PKG-002','1L PET Bottle','Bottle','pcs',8000,2000),
('PKG-003','Bottle Cap (Standard)','Cap','pcs',20000,5000),
('PKG-004','Bottle Cap (Sports)','Cap','pcs',5000,2000),
('PKG-005','Product Label - Mango','Label','pcs',6000,2000),
('PKG-006','Product Label - Orange','Label','pcs',8000,2000),
('PKG-007','Product Label - Pineapple','Label','pcs',5000,2000),
('PKG-008','Product Label - Strawberry','Label','pcs',3000,1000),
('PKG-009','Shipping Carton','Carton','pcs',500,100),
('PKG-010','PVC Shrink Wrap','Wrapper','roll',200,50);

-- MACHINES
INSERT INTO machines (MachineID, Name, Type, Location, Status, InstallDate, LastService) VALUES
('MCH-001','Industrial Fruit Washer','Washer','Production Floor A','Operational','2023-06-15','2025-06-01'),
('MCH-002','Heavy Duty Juicer #1','Juicer','Production Floor A','Operational','2023-06-15','2025-06-15'),
('MCH-003','Heavy Duty Juicer #2','Juicer','Production Floor A','Operational','2023-08-01','2025-06-15'),
('MCH-004','Pasteurizer Unit','Pasteurizer','Production Floor B','Operational','2023-06-15','2025-05-20'),
('MCH-005','Bottling Line A','Bottling','Packaging Area','Operational','2023-07-01','2025-06-10'),
('MCH-006','Bottling Line B','Bottling','Packaging Area','Maintenance','2023-07-01','2025-05-15'),
('MCH-007','Capping Machine','Capping','Packaging Area','Operational','2023-07-01','2025-06-10'),
('MCH-008','Labeling Machine','Labeling','Packaging Area','Operational','2023-08-01','2025-06-12'),
('MCH-009','Shrink Wrapper','Wrapper','Packaging Area','Operational','2023-09-01','2025-06-12'),
('MCH-010','Cold Storage Unit #1','Storage','Warehouse','Operational','2023-06-15','2025-06-01');

-- PRODUCTION BATCHES
INSERT INTO production_batches (BatchID, BatchNumber, ProductionDate, Flavour, Quantity, Unit, Status, UserID, RawMaterialID, PackagingMaterialID, MachineID) VALUES
('BAT-001','FJ-20250710-001','2025-07-01','Mango',500,'litres','Completed','USR-003','RM-001','PKG-001','MCH-002'),
('BAT-002','FJ-20250710-002','2025-07-03','Orange',800,'litres','Completed','USR-003','RM-002','PKG-002','MCH-003'),
('BAT-003','FJ-20250710-003','2025-07-05','Pineapple',300,'litres','Completed','USR-003','RM-003','PKG-001','MCH-002'),
('BAT-004','FJ-20250710-004','2025-07-08','Strawberry',200,'litres','In Progress','USR-003','RM-004','PKG-001','MCH-002'),
('BAT-005','FJ-20250710-005','2025-07-10','Mango',600,'litres','Pending','USR-003','RM-001','PKG-002','MCH-003');

-- QUALITY INSPECTIONS
INSERT INTO quality_inspections (InspectionID, InspectionType, BatchID, InspectionDate, Result, DefectsFound, TestResults, CAPA, InspectorID, Status) VALUES
('QI-001','Incoming','BAT-001','2025-07-01','Pass',NULL,'pH: 3.8, Brix: 12.5','None required','USR-005','Closed'),
('QI-002','In-Process','BAT-001','2025-07-01','Pass',NULL,'Temperature: 4°C, Brix: 13.0','None required','USR-005','Closed'),
('QI-003','Finished','BAT-001','2025-07-02','Pass',NULL,'pH: 3.9, Brix: 12.8, Micro: PASS','None required','USR-005','Closed'),
('QI-004','Incoming','BAT-002','2025-07-03','Pass',NULL,'pH: 3.5, Brix: 11.8','None required','USR-005','Closed'),
('QI-005','Finished','BAT-002','2025-07-04','Pass',NULL,'pH: 3.6, Brix: 12.0, Micro: PASS','None required','USR-005','Closed'),
('QI-006','Incoming','BAT-003','2025-07-05','Pass',NULL,'pH: 3.2, Brix: 13.5','None required','USR-005','Closed'),
('QI-007','Finished','BAT-003','2025-07-06','Pass',NULL,'pH: 3.3, Brix: 13.2, Micro: PASS','None required','USR-005','Closed'),
('QI-008','Incoming','BAT-004','2025-07-08','Fail','Mold detected in 2 samples','Micro: FAIL','Supplier notified, batch quarantined','USR-005','Open');

-- FINISHED GOODS
INSERT INTO finished_goods (FG_ID, BatchID, Flavour, ExpiryDate, QuantityAvailable, Unit, StorageLocation) VALUES
('FG-001','BAT-001','Mango','2025-12-31',480,'bottles','Cold Storage A - Shelf 1'),
('FG-002','BAT-002','Orange','2025-12-31',750,'bottles','Cold Storage A - Shelf 2'),
('FG-003','BAT-003','Pineapple','2026-01-15',290,'bottles','Cold Storage A - Shelf 3');

-- CUSTOMERS
INSERT INTO customers (CustomerID, Name, Contact, Email, Phone, Type) VALUES
('CUS-001','FreshMart Supermarket','Abena Mensah','orders@freshmart.com','0201234567','Retailer'),
('CUS-002','HealthPlus Pharmacy','Yaw Boateng','buy@healthplus.com','0202345678','Distributor'),
('CUS-003','Accra Fresh Juice Bar','Esi Ampofo','info@accrafjb.com','0203456789','Restaurant'),
('CUS-004','Kumasi Market Traders','Kwesi Appiah','traders@kumasi.com','0204567890','Wholesaler'),
('CUS-005','Hotel & Catering Services','Adwoa Foli','procurement@hotel.com','0205678901','Hotel');

-- SALES ORDERS
INSERT INTO sales_orders (OrderID, OrderDate, TotalAmount, Quantity, Status, CustomerID, FG_ID, CreatedBy) VALUES
('ORD-001','2025-07-05',12500.00,200,'Completed','CUS-001','FG-001','USR-006'),
('ORD-002','2025-07-07',18000.00,300,'Completed','CUS-002','FG-002','USR-006'),
('ORD-003','2025-07-09',5000.00,100,'Pending','CUS-003','FG-001','USR-006'),
('ORD-004','2025-07-10',22500.00,500,'Processing','CUS-004','FG-002','USR-006');

-- INVOICES
INSERT INTO invoices (InvoiceID, InvoiceDate, Amount, Tax, TotalDue, PaymentStatus, DueDate, OrderID) VALUES
('INV-001','2025-07-05',12500.00,1875.00,14375.00,'Paid','2025-08-04','ORD-001'),
('INV-002','2025-07-07',18000.00,2700.00,20700.00,'Paid','2025-08-06','ORD-002'),
('INV-003','2025-07-09',5000.00,750.00,5750.00,'Unpaid','2025-08-08','ORD-003'),
('INV-004','2025-07-10',22500.00,3375.00,25875.00,'Partial','2025-08-09','ORD-004');

-- MAINTENANCE RECORDS
INSERT INTO maintenance_records (MaintenanceID, MaintenanceType, MaintenanceDate, Downtime, Cost, MachineID, TechnicianID, Description, SpareParts, Status, NextServiceDate) VALUES
('MNT-001','Preventive','2025-06-15',4.00,250.00,'MCH-002','USR-008','Routine oil change and belt inspection','Oil filter, Drive belt','Completed','2025-09-15'),
('MNT-002','Corrective','2025-06-20',8.00,850.00,'MCH-006','USR-008','Bottling line conveyor motor replacement','Motor unit, Bearings','Completed','2025-07-20'),
('MNT-003','Preventive','2025-07-01',2.00,120.00,'MCH-004','USR-008','Pasteurizer calibration','Calibration kit','Completed','2025-10-01'),
('MNT-004','Emergency','2025-07-08',6.00,1200.00,'MCH-006','USR-008','Hydraulic press failure repair','Hydraulic pump, Seals','In Progress','2025-07-15');

-- WASTE RECORDS
INSERT INTO waste_records (WasteID, Date, WasteType, Quantity, Unit, DisposalMethod, BatchID, EnvironmentalImpact, RecordedBy) VALUES
('WST-001','2025-07-01','Production',25.50,'kg','Composting','BAT-001','Minimal - organic waste composted','USR-003'),
('WST-002','2025-07-01','Packaging',5.00,'kg','Recycling','BAT-001','None - materials recycled','USR-003'),
('WST-003','2025-07-03','Production',30.00,'kg','Composting','BAT-002','Minimal - organic waste composted','USR-003'),
('WST-004','2025-07-05','Spoilage',45.00,'kg','Licensed Disposal','BAT-003','Moderate - disposed per EPA guidelines','USR-004'),
('WST-005','2025-07-08','Production',15.00,'kg','Composting','BAT-004','Minimal','USR-003');

-- WATER USAGE
INSERT INTO water_usage (WaterUsageID, Date, UsageType, Quantity, Unit, Purpose, RecordedBy) VALUES
('WU-001','2025-07-01','Washing',2000,'litres','Fruit washing and preparation','USR-003'),
('WU-002','2025-07-01','Mixing',1500,'litres','Juice mixing and dilution','USR-003'),
('WU-003','2025-07-01','Cleaning',3000,'litres','Equipment and floor cleaning','USR-003'),
('WU-004','2025-07-02','Washing',1800,'litres','Fruit washing','USR-003'),
('WU-005','2025-07-02','Mixing',2000,'litres','Juice production','USR-003'),
('WU-006','2025-07-03','Washing',2200,'litres','Fruit washing','USR-003'),
('WU-007','2025-07-03','Cleaning',4000,'litres','Deep clean day','USR-003'),
('WU-008','2025-07-08','Washing',1500,'litres','Fruit washing','USR-003');

-- WATER QUALITY TESTS
INSERT INTO water_quality_tests (WaterTestID, TestDate, TestType, pH_Level, Turbidity, TDS, Chlorine, BacteriaCount, Result, Notes, TestedBy) VALUES
('WQT-001','2025-07-01','Daily Check',7.20,0.50,120.00,0.50,0,'Pass','Normal readings','USR-005'),
('WQT-002','2025-07-03','Daily Check',7.15,0.45,118.00,0.48,0,'Pass','Normal readings','USR-005'),
('WQT-003','2025-07-05','Weekly Full',7.10,0.30,115.00,0.50,0,'Pass','Full panel test - all within limits','USR-005'),
('WQT-004','2025-07-08','Daily Check',7.25,0.55,125.00,0.52,0,'Pass','Slight increase in TDS, monitor','USR-005');

-- POWER USAGE
INSERT INTO power_usage (PowerUsageID, Date, Source, ConsumptionKWh, Cost, Notes) VALUES
('PU-001','2025-07-01','Grid',450.00,675.00,'Normal production day'),
('PU-002','2025-07-02','Grid',520.00,780.00,'Higher usage - extra bottling run'),
('PU-003','2025-07-03','Grid',380.00,570.00,'Reduced production'),
('PU-004','2025-07-04','Generator',300.00,1200.00,'Grid outage 2hrs - generator used'),
('PU-005','2025-07-05','Grid',480.00,720.00,'Normal operations'),
('PU-006','2025-07-08','Grid',420.00,630.00,'Partial production');

-- GENERATOR LOG
INSERT INTO generator_log (LogID, Date, StartTime, EndTime, RuntimeHrs, FuelUsed, FuelUnit, Reason, Notes) VALUES
('GEN-001','2025-07-04','10:00:00','12:00:00',2.00,15.00,'litres','Grid outage','Emergency backup power'),
('GEN-002','2025-07-06','08:00:00','08:30:00',0.50,4.00,'litres','Scheduled test run','Routine generator test');

-- CERTIFICATIONS
INSERT INTO certifications (CertID, CertName, CertType, IssuingAuthority, IssueDate, ExpiryDate, Status) VALUES
('CERT-001','FDA Food Safety Registration','FDA','Food & Drugs Authority Ghana','2024-01-01','2026-01-01','Active'),
('CERT-002','HACCP Certification','HACCP','Ghana Standards Authority','2024-03-15','2025-03-15','Expired'),
('CERT-003','ISO 22000 Food Safety','ISO 22000','SGS Ghana','2024-06-01','2027-06-01','Active'),
('CERT-004','GSA Product Certification','GSA','Ghana Standards Authority','2024-02-01','2026-02-01','Active'),
('CERT-005','Environmental Permit','EPA','Environmental Protection Agency','2024-01-15','2025-07-15','Pending Renewal');

-- SOP TEMPLATES
INSERT INTO sop_templates (SOP_ID, Title, Department, Version, Content, EffectiveDate, ReviewDate, Status, CreatedBy) VALUES
('SOP-001','Fruit Receiving & Inspection SOP','Quality Assurance','2.0','1. Verify delivery note\n2. Visual inspection of fruit quality\n3. Temperature check (must be <8°C for berries)\n4. Weight verification\n5. Sample collection for lab testing\n6. Accept or reject with documentation','2024-06-01','2025-06-01','Active','USR-005'),
('SOP-002','Juice Production Line SOP','Production','3.0','1. Sanitize all equipment\n2. Calibrate juicer settings\n3. First batch quality check\n4. Monitor Brix levels throughout\n5. Record temperatures every 30 mins\n6. Batch completion documentation','2024-06-01','2025-06-01','Active','USR-003'),
('SOP-003','Bottling & Packaging SOP','Production','2.0','1. Clean bottles with UV\n2. Fill to specified volume\n3. Cap and seal verification\n4. Label application check\n5. Code date printing\n6. Shrink wrap application\n7. Carton packing\n8. Quality spot check','2024-07-01','2025-07-01','Active','USR-003'),
('SOP-004','Cleaning & Sanitation SOP','Quality Assurance','1.5','1. Pre-rinse all surfaces\n2. Apply food-grade sanitizer\n3. Contact time: 15 minutes\n4. Final rinse with potable water\n5. Visual and ATP verification\n6. Log completion','2024-06-01','2025-06-01','Active','USR-005'),
('SOP-005','Emergency Recall SOP','Management','1.0','1. Stop production immediately\n2. Quarantine all affected products\n3. Notify management team\n4. Contact regulatory authorities\n5. Initiate customer notifications\n6. Document all actions\n7. Root cause analysis','2024-01-01','2025-01-01','Active','USR-002');

-- SOP CHECKLISTS
INSERT INTO sop_checklists (ChecklistID, SOP_ID, BatchID, Date, ChecklistItems, CompletedItems, TotalItems, SupervisorID, ApprovalStatus, Notes) VALUES
('SC-001','SOP-002','BAT-001','2025-07-01','{"sanitized":"yes","calibrated":"yes","first_check":"pass","temp_monitored":"yes","docs_complete":"yes"}',5,5,'USR-003','Approved','All steps completed successfully'),
('SC-002','SOP-003','BAT-001','2025-07-02','{"bottle_clean":"yes","filled":"yes","capped":"yes","labeled":"yes","coded":"yes","shrinkwrap":"yes","carton":"yes","spot_check":"pass"}',8,8,'USR-003','Approved','Perfect execution'),
('SC-003','SOP-002','BAT-002','2025-07-03','{"sanitized":"yes","calibrated":"yes","first_check":"pass","temp_monitored":"yes","docs_complete":"yes"}',5,5,'USR-003','Approved','Completed per SOP'),
('SC-004','SOP-001','BAT-004','2025-07-08','{"delivery_note":"yes","visual_inspection":"fail","temp_check":"pass","weight":"pass","sampling":"yes","decision":"reject"}',5,6,'USR-003','Rejected','Visual inspection failed - mold on strawberries');

-- AUDIT TRAIL (sample)
INSERT INTO audit_trail (UserID, Action, Module, RecordID, Details, IPAddress) VALUES
('USR-004','CREATE','Raw Materials','RM-001','Added Fresh Mangoes stock: 1200 kg','192.168.1.10'),
('USR-003','CREATE','Production Batch','BAT-001','Created batch FJ-20250710-001 for 500L Mango','192.168.1.20'),
('USR-005','UPDATE','Quality Inspection','QI-001','Incoming inspection passed for BAT-001','192.168.1.30'),
('USR-006','CREATE','Sales Order','ORD-001','Order placed for 200 bottles Mango','192.168.1.40'),
('USR-008','UPDATE','Maintenance','MNT-004','Emergency repair on MCH-006 in progress','192.168.1.50');

-- ============================================
-- COMPLIANCE MODULES SAMPLE DATA
-- ============================================

-- SAFETY INSPECTIONS
INSERT INTO safety_inspections (SafetyID, InspectionDate, InspectionType, Area, Findings, HazardLevel, CorrectiveAction, ResponsiblePerson, TargetDate, Status, InspectorID) VALUES
('SAF-001','2025-07-01','Machine Guarding','Production Floor A','All guards in place, 2 loose bolts on Juicer #1','Medium','Tighten bolts and log','USR-008','2025-07-05','Closed','USR-005'),
('SAF-002','2025-07-03','PPE Compliance','Packaging Area','3 workers without safety goggles on labeling line','High','Immediate PPE enforcement, retraining scheduled','USR-002','2025-07-10','In Progress','USR-005'),
('SAF-003','2025-07-05','Emergency Systems','Warehouse','Fire extinguisher in Cold Storage missing inspection tag','Low','Replace extinguisher and tag','USR-008','2025-07-12','Open','USR-005'),
('SAF-004','2025-07-08','Chemical Handling','Mixing Area','Improper storage of cleaning chemicals','Critical','Reorganize chemical storage with proper segregation','USR-002','2025-07-09','Open','USR-005');

-- HAZARD REGISTER
INSERT INTO hazard_register (HazardID, HazardDescription, RiskCategory, Likelihood, Consequence, RiskRating, ControlMeasures, ResponsiblePerson, ReviewDate, Status) VALUES
('HAZ-001','Juicer blade exposure during cleaning','Mechanical','Possible','Major',12,'Lock-out tag-out procedure, blade guard interlock','USR-008','2025-10-01','Active'),
('HAZ-002','Chemical splash during mixing','Chemical','Likely','Moderate',12,'Chemical handling SOP, PPE including face shield','USR-005','2025-09-01','Active'),
('HAZ-003','Slip hazard on wet production floor','Safety','Almost Certain','Minor',10,'Anti-slip mats, immediate spill cleanup protocol','USR-003','2025-08-01','Active'),
('HAZ-004','Electrical panel exposed in packaging area','Electrical','Unlikely','Catastrophic',10,'Locked panel, only authorized electricians','USR-008','2025-11-01','Active'),
('HAZ-005','Manual lifting of heavy fruit crates','Ergonomic','Possible','Moderate',9,'Provide lifting aids, team lifting training','USR-003','2025-09-01','Mitigated');

-- ACCIDENT REPORTS
INSERT INTO accident_reports (AccidentID, IncidentDate, Location, IncidentType, Description, Injuries, RootCause, CorrectiveAction, ReportedBy, Status) VALUES
('ACC-001','2025-06-28 10:30:00','Production Floor A','Near Miss','Worker almost caught sleeve in juicer conveyor','None','Missing emergency stop button cover','Install new e-stop covers and retrain','USR-003','Closed'),
('ACC-002','2025-07-05 14:15:00','Packaging Area','First Aid','Minor cut from loose metal edge on carton stack','Small laceration on left hand','Damaged carton stacker edge','Repair carton stacker, inspect all equipment','USR-003','Under Investigation'),
('ACC-003','2025-07-09 09:00:00','Mixing Area','Property Damage','Chemical spill during cleaning chemical transfer','None - evacuated area','Improper funnel usage','Chemical transfer training, install proper dispensing equipment','USR-005','Reported');

-- PERMITS & LICENSES
INSERT INTO permits (PermitID, PermitName, PermitType, IssuingAuthority, PermitNumber, IssueDate, ExpiryDate, Status) VALUES
('PRM-001','Food Manufacturing License','Health Permit','Ghana FDA','FDA-FML-2024-001','2024-01-15','2026-01-15','Active'),
('PRM-002','Environmental Operating Permit','Environmental','EPA Ghana','EPA-EOP-2024-042','2024-03-01','2025-09-01','Active'),
('PRM-003','Building Safety Certificate','Safety','Ghana Fire Service','GFS-BSC-2024-008','2024-02-01','2025-08-01','Active'),
('PRM-004','Water Extraction License','Water','Water Resources Commission','WRC-WEL-2024-015','2024-01-01','2025-12-31','Active'),
('PRM-005','Waste Disposal License','Waste','EPA Ghana','EPA-WDL-2024-007','2024-06-01','2025-06-01','Expired');

-- TRAINING RECORDS
INSERT INTO training_records (TrainingID, StaffID, TrainingType, TrainingDate, Duration, Trainer, CertificateNo, ExpiryDate, Status) VALUES
('TRN-001','STF-003','Food Safety & Hygiene','2025-06-15','2 days','Dr. Mensah','FSH-2025-003','2026-06-15','Completed'),
('TRN-002','STF-004','Inventory Management','2025-06-20','1 day','Mr. Osei','IM-2025-012','2025-12-20','Completed'),
('TRN-003','STF-005','HACCP Refresher','2025-06-25','1 day','Dr. Mensah','HACCP-2025-008','2026-06-25','Completed'),
('TRN-004','STF-003','Machine Operation - Juicer','2025-07-05','3 days','MCH-002 Manual','MOJ-2025-001','2026-01-05','Completed'),
('TRN-005','STF-006','Customer Service','2025-07-15','1 day','External Trainer',NULL,NULL,'Scheduled'),
('TRN-006','STF-003','Emergency Response','2025-07-20','0.5 day','Fire Service',NULL,NULL,'Scheduled');

-- PPE RECORDS
INSERT INTO ppe_records (PPE_ID, StaffID, PPESource, DateIssued, ExpiryDate, `Condition`, ReplacementNeeded, Notes) VALUES
('PPE-001','STF-003','Safety Goggles','2025-01-15','2025-07-15','Fair',1,'Needs replacement soon'),
('PPE-002','STF-003','Steel Toe Boots','2025-01-15','2025-10-15','Good',0,'In good condition'),
('PPE-003','STF-005','Lab Coat','2025-02-01','2025-08-01','Poor',1,'Torn at sleeve, replace'),
('PPE-004','STF-004','Safety Gloves (Cut Resistant)','2025-03-01','2025-09-01','Good',0,'Still effective'),
('PPE-005','STF-003','Hearing Protection (Ear Muffs)','2025-01-15','2025-07-15','Expired',1,'Expired, replace immediately');

-- PRODUCTION EFFICIENCY / OEE
INSERT INTO production_efficiency (EfficiencyID, Date, Shift, MachineID, PlannedRunTime, ActualRunTime, DowntimeMinutes, TotalProduced, GoodProduced, DefectCount, AvailabilityRate, PerformanceRate, QualityRate, OEE, recordedBy) VALUES
('EFF-001','2025-07-01','Morning','MCH-002',480,450,30,12000,11760,240,93.75,97.78,98.00,89.70,'USR-003'),
('EFF-002','2025-07-01','Morning','MCH-004',480,460,20,15000,14850,150,95.83,100.00,99.00,94.87,'USR-003'),
('EFF-003','2025-07-02','Morning','MCH-002',480,440,40,11000,10780,220,91.67,95.65,98.00,85.97,'USR-003'),
('EFF-004','2025-07-03','Morning','MCH-003',480,470,10,16000,15840,160,97.92,100.00,99.00,96.94,'USR-003'),
('EFF-005','2025-07-08','Morning','MCH-002',480,400,80,10000,9500,500,83.33,95.24,95.00,75.40,'USR-003'),
('EFF-006','2025-07-08','Afternoon','MCH-004',480,420,60,13500,13095,405,87.50,98.57,97.00,83.63,'USR-003');

-- IMPROVEMENT INITIATIVES (CAPA)
INSERT INTO improvement_initiatives (InitiativeID, Title, Category, Description, RootCauseAnalysis, ActionPlan, TargetDate, ResponsiblePerson, Status, CreatedBy) VALUES
('CAPA-001','Reduce bottle cap defect rate','Quality','Current cap defect rate is 2.3%, target <0.5%','Worn capping machine head','Replace capping head, calibrate torque settings, 100% inspection for 1 week','2025-07-30','USR-008','Approved','USR-005'),
('CAPA-002','Improve fruit washing efficiency','Process','Fruit washer using 20% more water than designed','Clogged spray nozzles, improper pressure settings','Clean all nozzles, calibrate pressure regulators, install flow meters','2025-07-25','USR-008','In Progress','USR-003'),
('CAPA-003','Reduce employee turnover in packaging','HR','30% turnover rate in packaging department','Lack of growth opportunities, inadequate training','Implement career path program, increase training budget, monthly reviews','2025-09-01','USR-002','Proposed','USR-002'),
('CAPA-004','Supplier delivery accuracy improvement','Supply Chain','SUP-003 delivery accuracy at 92% vs 98% target','Inconsistent communication and no quality checklist','Implement supplier scorecard, share quality requirements document, weekly check-in calls','2025-08-15','USR-004','Proposed','USR-004'),
('CAPA-005','Standardize batch documentation','Documentation','Batch records inconsistent across shifts','No standard template, different supervisors use different formats','Create standardized batch record template, train all supervisors, audit compliance','2025-07-20','USR-003','Completed','USR-002');

-- DOCUMENTS
INSERT INTO documents (DocID, Title, DocType, Version, Description, Department, EffectiveDate, ReviewDate, Status, ApprovedBy) VALUES
('DOC-001','Factory Quality Manual','Procedure','3.0','Comprehensive quality management procedures','Quality Assurance','2025-01-01','2025-07-01','Approved','USR-002'),
('DOC-002','Production Floor Layout v2','Drawing','2.0','Updated floor plan with new equipment locations','Production','2025-03-15','2025-09-15','Approved','USR-002'),
('DOC-003','Emergency Evacuation Plan','Plan','1.0','Fire and chemical spill evacuation routes','Safety','2025-04-01','2025-10-01','Under Review','USR-005'),
('DOC-004','Supplier Quality Requirements','Specification','2.5','Quality standards for all supplied materials','Quality Assurance','2025-02-01','2025-08-01','Approved','USR-005'),
('DOC-005','Energy Management Policy','Policy','1.0','Company policy on energy efficiency and monitoring','Management','2025-05-01','2025-11-01','Draft',NULL);

-- SUPPLIER EVALUATIONS
INSERT INTO supplier_evaluations (EvaluationID, SupplierID, EvaluationDate, QualityScore, DeliveryScore, PriceScore, OverallScore, Strengths, Weaknesses, Recommendations, EvaluatedBy) VALUES
('SEV-001','SUP-001','2025-06-30',4.5,4.0,3.5,4.0,'Consistently high quality fruit, good communication','Premium pricing, occasional delayed shipments','Negotiate volume discount, improve delivery scheduling','USR-004'),
('SEV-002','SUP-002','2025-06-30',5.0,5.0,4.0,4.7,'Excellent quality sugar, always on time','Higher than market average price','Consider long-term contract for better pricing','USR-004'),
('SEV-003','SUP-003','2025-06-30',3.5,3.0,4.0,3.5,'Competitive pricing, wide product range','Inconsistent delivery, packaging quality varies','Establish clear quality checklist, penalize late deliveries','USR-004'),
('SEV-004','SUP-005','2025-06-30',5.0,4.5,3.0,4.2,'Premium organic quality, good traceability','Expensive, limited seasonal availability','Plan seasonal contracts, explore partial substitutions','USR-004');

-- EMERGENCY DRILLS
INSERT INTO emergency_drills (DrillID, DrillDate, DrillType, Location, ParticipantsCount, DurationMinutes, Outcome, IssuesFound, CorrectiveAction, ConductedBy, Status) VALUES
('DRL-001','2025-06-20','Fire Drill','Entire Factory',35,15,'Satisfactory - 15 min evacuation','1 person did not hear alarm due to noisy area','Install strobe light in high-noise zones','USR-002','Completed'),
('DRL-002','2025-07-05','Chemical Spill','Mixing Area',10,20,'Adequate - contained quickly, some confusion on protocol','Response team unclear on neutralization procedure','Schedule chemical spill response training','USR-005','Completed'),
('DRL-003','2025-07-18','First Aid Emergency','Packaging Area',15,30,'Scheduled','','','USR-002','Scheduled'),
('DRL-004','2025-08-01','Fire Drill','Entire Factory',40,0,'Scheduled','','','USR-002','Scheduled');

-- FAT RECORDS
INSERT INTO fat_records (FAT_ID, MachineID, TestDate, TestType, ExpectedResult, ActualResult, Result, DefectsFound, TestedBy, Notes, Status) VALUES
('FAT-001','MCH-002','2023-05-15','Performance','Throughput 500L/hr','510L/hr achieved','Pass',NULL,'USR-008',NULL,'Completed'),
('FAT-002','MCH-004','2023-05-20','Performance','Temperature accuracy +/-0.5C','Accuracy +/-0.3C','Pass',NULL,'USR-008',NULL,'Completed'),
('FAT-003','MCH-006','2023-06-01','Installation','Bottling line installation per specs','All spec items verified','Pass',NULL,'USR-008',NULL,'Completed'),
('FAT-004','MCH-005','2025-07-10','Safety','Emergency stop within 2 seconds','E-stop activated in 1.5 seconds','Pass',NULL,'USR-008',NULL,'Completed'),
('FAT-005','MCH-010','2025-07-12','Performance','Cooling to 4C within 30 min','Reached 4C in 25 min','Pass',NULL,'USR-008',NULL,'Completed');


-- ================================================================
-- MIGRATION: Worker-Shift Assignments + Notification Expiry
-- ================================================================

-- 1. Worker-Shift Assignments pivot table
CREATE TABLE IF NOT EXISTS worker_shift_assignments (
    AssignmentID VARCHAR(50) PRIMARY KEY,
    WorkerID     VARCHAR(50) NOT NULL,
    ShiftID      VARCHAR(50) NOT NULL,
    ShiftDate    DATE        NOT NULL,
    Status       ENUM('Scheduled','Completed','Absent','Swapped') DEFAULT 'Scheduled',
    created_at   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (WorkerID) REFERENCES workers(WorkerID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ShiftID)  REFERENCES shifts(ShiftID)  ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY uniq_worker_date (WorkerID, ShiftDate),
    INDEX idx_shift_date (ShiftDate),
    INDEX idx_shift_id (ShiftID)
) ENGINE=InnoDB;

-- 2. Add expires_at to notifications for auto-expiry
SET @nx = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='notifications' AND COLUMN_NAME='expires_at'); SET @d2 = IF(@nx=0,'ALTER TABLE notifications ADD COLUMN expires_at DATETIME DEFAULT NULL AFTER created_at, ADD INDEX idx_notif_expires (expires_at)','SELECT 1'); PREPARE p2 FROM @d2; EXECUTE p2; DEALLOCATE PREPARE p2;


-- ================================================================
-- MIGRATION: WorkerID on payroll
-- ================================================================

-- ================================================================
--  WORKERS TABLE (laborers — separate from system users & staff)
--  USR-001..008 are login accounts; workers are the factory laborers
--  the accountant adds by form or Excel import.
-- ================================================================

CREATE TABLE IF NOT EXISTS workers (
    WorkerID      VARCHAR(50)  PRIMARY KEY,
    FirstName     VARCHAR(100) NOT NULL,
    LastName      VARCHAR(100) NOT NULL,
    Phone         VARCHAR(30)  DEFAULT NULL,
    Position      VARCHAR(100) DEFAULT 'Laborer',
    MonthlyPay    DECIMAL(12,2) DEFAULT 0,
    DateHired     DATE         DEFAULT NULL,
    Status        ENUM('Active','On Leave','Terminated') DEFAULT 'Active',
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_workers_status (Status)
) ENGINE=InnoDB;

-- Payroll can now reference either a staff member OR a worker
SET @pw = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payroll' AND COLUMN_NAME='WorkerID'); SET @d1 = IF(@pw=0,'ALTER TABLE payroll ADD COLUMN WorkerID VARCHAR(50) DEFAULT NULL AFTER StaffID, ADD INDEX idx_payroll_worker (WorkerID)','SELECT 1'); PREPARE p1 FROM @d1; EXECUTE p1; DEALLOCATE PREPARE p1;
