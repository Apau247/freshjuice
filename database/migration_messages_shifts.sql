-- ================================================================
-- Migration: Worker Shift Assignments + Notification Expiry
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
ALTER TABLE notifications
    ADD COLUMN expires_at DATETIME DEFAULT NULL AFTER created_at,
    ADD INDEX idx_notif_expires (expires_at);
