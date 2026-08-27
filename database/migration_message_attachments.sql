-- ================================================================
-- Migration: Message Attachments
-- ================================================================

CREATE TABLE IF NOT EXISTS message_attachments (
    AttachmentID VARCHAR(50)  PRIMARY KEY,
    MessageID    VARCHAR(50)  NOT NULL,
    FileName     VARCHAR(255) NOT NULL,
    FilePath     VARCHAR(500) NOT NULL,
    FileType     VARCHAR(50)  NOT NULL DEFAULT 'file',
    FileSize     INT UNSIGNED DEFAULT 0,
    MimeType     VARCHAR(100) DEFAULT NULL,
    Duration     INT UNSIGNED DEFAULT NULL,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MessageID) REFERENCES messages(MessageID) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_attach_msg (MessageID)
) ENGINE=InnoDB;
