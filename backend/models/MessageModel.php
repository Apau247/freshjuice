<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class MessageModel extends Model
{
    protected string $table = 'messages';
    protected string $primaryKey = 'MessageID';

    public function getInbox(string $userId, int $limit = 50): array {
        return $this->query(
            "SELECT m.*, u.Name AS SenderName, u.RoleID AS SenderRole
             FROM messages m
             JOIN users u ON m.SenderID = u.UserID
             WHERE m.ReceiverID = ? AND m.IsDeletedReceiver = 0
             ORDER BY m.created_at DESC
             LIMIT " . (int)$limit,
            [$userId]
        );
    }

    public function getSent(string $userId, int $limit = 50): array {
        return $this->query(
            "SELECT m.*, u.Name AS ReceiverName, u.RoleID AS ReceiverRole
             FROM messages m
             JOIN users u ON m.ReceiverID = u.UserID
             WHERE m.SenderID = ? AND m.IsDeletedSender = 0
             ORDER BY m.created_at DESC
             LIMIT " . (int)$limit,
            [$userId]
        );
    }

    public function find(string $id): ?array {
        return $this->queryOne(
            "SELECT m.*,
                    s.Name AS SenderName, s.RoleID AS SenderRole,
                    r.Name AS ReceiverName, r.RoleID AS ReceiverRole
             FROM messages m
             JOIN users s ON m.SenderID = s.UserID
             JOIN users r ON m.ReceiverID = r.UserID
             WHERE m.MessageID = ?",
            [$id]
        );
    }

    public function getThread(string $parentOrChildId): array {
        // Get the root message
        $msg = $this->find($parentOrChildId);
        if (!$msg) return [];
        $rootId = $msg['ParentMessageID'] ?? $parentOrChildId;
        if (!$rootId) $rootId = $parentOrChildId;

        return $this->query(
            "SELECT m.*,
                    s.Name AS SenderName, s.RoleID AS SenderRole,
                    r.Name AS ReceiverName, r.RoleID AS ReceiverRole
             FROM messages m
             JOIN users s ON m.SenderID = s.UserID
             JOIN users r ON m.ReceiverID = r.UserID
             WHERE m.MessageID = ? OR m.ParentMessageID = ?
             ORDER BY m.created_at ASC",
            [$rootId, $rootId]
        );
    }

    public function create(array $data): string {
        $id = generateId('MSG');
        $stmt = $this->db->prepare(
            "INSERT INTO messages (MessageID, SenderID, ReceiverID, Subject, Body, ParentMessageID)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $id,
            $data['SenderID'],
            $data['ReceiverID'],
            $data['Subject'],
            $data['Body'],
            $data['ParentMessageID'] ?? null,
        ]);
        return $id;
    }

    public function markRead(string $id): bool {
        $stmt = $this->db->prepare(
            "UPDATE messages SET IsRead = 1, ReadAt = NOW() WHERE MessageID = ? AND IsRead = 0"
        );
        return $stmt->execute([$id]);
    }

    public function updateMessage(string $id, array $data): bool {
        $data['IsEdited'] = 1;
        $data['EditedAt'] = date('Y-m-d H:i:s');
        return $this->update($id, $data);
    }

    public function softDelete(string $id, string $userId): bool {
        $msg = $this->find($id);
        if (!$msg) return false;

        if ($msg['SenderID'] === $userId) {
            $stmt = $this->db->prepare("UPDATE messages SET IsDeletedSender = 1 WHERE MessageID = ?");
        } elseif ($msg['ReceiverID'] === $userId) {
            $stmt = $this->db->prepare("UPDATE messages SET IsDeletedReceiver = 1 WHERE MessageID = ?");
        } else {
            return false;
        }
        return $stmt->execute([$id]);
    }

    public function getUnreadCount(string $userId): int {
        $r = $this->queryOne(
            "SELECT COUNT(*) AS cnt FROM messages WHERE ReceiverID = ? AND IsRead = 0 AND IsDeletedReceiver = 0",
            [$userId]
        );
        return $r ? (int)$r['cnt'] : 0;
    }

    public function getUnreadMessages(string $userId): array {
        return $this->query(
            "SELECT m.MessageID, m.Subject, m.created_at, u.Name AS SenderName
             FROM messages m
             JOIN users u ON m.SenderID = u.UserID
             WHERE m.ReceiverID = ? AND m.IsRead = 0 AND IsDeletedReceiver = 0
             ORDER BY m.created_at DESC
             LIMIT 10",
            [$userId]
        );
    }

    public function getConversationPartners(string $userId): array {
        return $this->query(
            "SELECT u.UserID, u.Name, u.RoleID,
                    (SELECT COUNT(*) FROM messages WHERE SenderID = u.UserID AND ReceiverID = ? AND IsRead = 0 AND IsDeletedReceiver = 0) AS UnreadCount,
                    (SELECT created_at FROM messages
                     WHERE (SenderID = ? AND ReceiverID = u.UserID) OR (SenderID = u.UserID AND ReceiverID = ?)
                     ORDER BY created_at DESC LIMIT 1) AS LastMessage
             FROM users u
             WHERE u.UserID != ? AND u.Status = 'Active'
             ORDER BY LastMessage DESC, u.Name ASC",
            [$userId, $userId, $userId, $userId]
        );
    }
}
