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
ALTER TABLE payroll
    MODIFY StaffID VARCHAR(50) DEFAULT NULL,
    ADD COLUMN WorkerID VARCHAR(50) DEFAULT NULL AFTER StaffID,
    ADD INDEX idx_payroll_worker (WorkerID);
