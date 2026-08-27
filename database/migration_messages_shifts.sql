-- ================================================================
-- Migration: Worker Shift Assignments + Internal Messaging + Notification Expiry
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

-- 2. Internal Messages
CREATE TABLE IF NOT EXISTS messages (
    MessageID       VARCHAR(50)  PRIMARY KEY,
    SenderID        VARCHAR(50)  NOT NULL,
    ReceiverID      VARCHAR(50)  NOT NULL,
    Subject         VARCHAR(200) NOT NULL,
    Body            TEXT         NOT NULL,
    IsRead          TINYINT(1)   DEFAULT 0,
    ReadAt          DATETIME     DEFAULT NULL,
    IsDeletedSender TINYINT(1)   DEFAULT 0,
    IsDeletedReceiver TINYINT(1) DEFAULT 0,
    IsEdited        TINYINT(1)   DEFAULT 0,
    EditedAt        DATETIME     DEFAULT NULL,
    ParentMessageID VARCHAR(50)  DEFAULT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SenderID)   REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ReceiverID) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ParentMessageID) REFERENCES messages(MessageID) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_msg_receiver (ReceiverID, IsRead, IsDeletedReceiver),
    INDEX idx_msg_sender (SenderID, IsDeletedSender),
    INDEX idx_msg_created (created_at)
) ENGINE=InnoDB;

-- 3. Add expires_at to notifications for auto-expiry
ALTER TABLE notifications
    ADD COLUMN expires_at DATETIME DEFAULT NULL AFTER created_at,
    ADD INDEX idx_notif_expires (expires_at);
