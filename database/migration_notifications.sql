-- ================================================================
--  NOTIFICATIONS TABLE
--  Stores per-user notifications (e.g. salary paid alerts).
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
