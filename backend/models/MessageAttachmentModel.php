<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class MessageAttachmentModel extends Model
{
    protected string $table = 'message_attachments';
    protected string $primaryKey = 'AttachmentID';

    public function getByMessage(string $messageId): array {
        return $this->query(
            "SELECT * FROM message_attachments WHERE MessageID = ? ORDER BY created_at ASC",
            [$messageId]
        );
    }

    public function find(string $id): ?array {
        return $this->queryOne("SELECT * FROM message_attachments WHERE AttachmentID = ?", [$id]);
    }

    public function create(array $data): string {
        $id = generateId('ATT');
        $stmt = $this->db->prepare(
            "INSERT INTO message_attachments (AttachmentID, MessageID, FileName, FilePath, FileType, FileSize, MimeType, Duration)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $id,
            $data['MessageID'],
            $data['FileName'],
            $data['FilePath'],
            $data['FileType'] ?? 'file',
            $data['FileSize'] ?? 0,
            $data['MimeType'] ?? null,
            $data['Duration'] ?? null,
        ]);
        return $id;
    }

    public function deleteByMessage(string $messageId): bool {
        $attachments = $this->getByMessage($messageId);
        $uploadDir = __DIR__ . '/../../uploads/messages';
        foreach ($attachments as $att) {
            $fullPath = $uploadDir . '/' . basename($att['FilePath']);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        $stmt = $this->db->prepare("DELETE FROM message_attachments WHERE MessageID = ?");
        return $stmt->execute([$messageId]);
    }

    public function getAttachmentsForThread(array $messageIds): array {
        if (empty($messageIds)) return [];
        $placeholders = implode(',', array_fill(0, count($messageIds), '?'));
        return $this->query(
            "SELECT * FROM message_attachments WHERE MessageID IN ({$placeholders}) ORDER BY created_at ASC",
            $messageIds
        );
    }
}
