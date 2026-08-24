-- ================================================================
--  PAYROLL MIGRATION
--  Adds the employer payroll module:
--    * `payroll` table          -- one row per staff per pay period,
--                                  tracking paid vs not-paid status
--    * `staff.MonthlySalary`    -- admin-configured payment amount
--
--  Idempotent: safe to run more than once.
--  Run:  mysql -u root freshjuice < database/migration_payroll.sql
-- ================================================================

-- ── 1. Salary setting on the staff record (admin sets payment amount) ──
SET @col_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'staff'
      AND COLUMN_NAME  = 'MonthlySalary'
);
SET @ddl = IF(@col_exists = 0,
    'ALTER TABLE staff ADD COLUMN MonthlySalary DECIMAL(12,2) DEFAULT 0 AFTER Position',
    'SELECT ''staff.MonthlySalary already exists'' AS info');
PREPARE stmt FROM @ddl;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ── 2. Payroll records ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS payroll (
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
    PaymentMethod  VARCHAR(50)    DEFAULT NULL,          -- Cash / MoMo / Bank Transfer / Cheque
    Notes          TEXT           DEFAULT NULL,
    ProcessedBy    VARCHAR(50)    DEFAULT NULL,          -- user who marked it paid/reverted
    created_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StaffID) REFERENCES staff(StaffID)
        ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY uq_payroll_period (StaffID, PeriodMonth, PeriodYear),
    INDEX idx_payroll_status (Status),
    INDEX idx_payroll_period (PeriodYear, PeriodMonth)
) ENGINE=InnoDB;
