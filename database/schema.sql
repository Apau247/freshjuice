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
