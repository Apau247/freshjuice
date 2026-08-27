<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class NotificationModel extends Model
{
    protected string $table = 'notifications';
    protected string $primaryKey = 'NotificationID';

    public function getLowStockAlerts(): array
    {
        return $this->query(
            "SELECT * FROM raw_materials WHERE CurrentStock <= MinStock ORDER BY CurrentStock ASC"
        );
    }

    public function getLowPackagingAlerts(): array
    {
        return $this->query(
            "SELECT * FROM packaging_materials WHERE CurrentStock <= MinStock ORDER BY CurrentStock ASC"
        );
    }

    public function getExpiringCerts(): array
    {
        return $this->query(
            "SELECT * FROM certifications WHERE ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND ExpiryDate >= CURDATE() ORDER BY ExpiryDate ASC"
        );
    }

    public function getExpiringPermits(): array
    {
        return $this->query(
            "SELECT * FROM permits WHERE ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND ExpiryDate >= CURDATE() AND Status = 'Active' ORDER BY ExpiryDate ASC"
        );
    }

    /** Maintenance due within the next 7 days (or already overdue). */
    public function getDueMaintenance(): array
    {
        return $this->query(
            "SELECT r.MaintenanceID, r.MaintenanceDate, r.MaintenanceType, r.Status,
                    m.Name AS MachineName,
                    CASE WHEN r.MaintenanceDate < CURDATE() THEN 1 ELSE 0 END AS Overdue
             FROM maintenance_records r
             LEFT JOIN machines m ON r.MachineID = m.MachineID
             WHERE r.Status = 'Scheduled'
               AND r.MaintenanceDate <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
             ORDER BY r.MaintenanceDate ASC"
        );
    }

    /**
     * Alerts scoped to what the signed-in user is allowed to see. Notifications
     * is open to every role, so each section has to be filtered by its own
     * module rather than the page as a whole.
     */
    public function getAll(): array
    {
        return [
            'low_stock'        => can('materials')      ? $this->getLowStockAlerts()     : [],
            'low_packaging'    => can('materials')      ? $this->getLowPackagingAlerts() : [],
            'expiring_certs'   => can('certifications') ? $this->getExpiringCerts()      : [],
            'expiring_permits' => can('permits')        ? $this->getExpiringPermits()    : [],
            'due_maintenance'  => can('maintenance')    ? $this->getDueMaintenance()     : [],
            'my_payments'      => $this->getMyPaymentNotifications(),
        ];
    }

    /** Salary/payment notifications for the signed-in user. Expired ones hidden. */
    public function getMyPaymentNotifications(): array {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) return [];
        return $this->query(
            "SELECT NotificationID, Title, Message, IsRead, created_at
             FROM notifications
             WHERE UserID = ? AND (expires_at IS NULL OR expires_at > NOW())
             ORDER BY created_at DESC LIMIT 50",
            [$userId]
        );
    }

    /** Unread count for the navbar badge. Expired ones excluded. */
    public function getUnreadCount(): int {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) return 0;
        $r = $this->queryOne(
            "SELECT COUNT(*) AS cnt FROM notifications
             WHERE UserID = ? AND IsRead = 0 AND (expires_at IS NULL OR expires_at > NOW())",
            [$userId]
        );
        return $r ? (int)$r['cnt'] : 0;
    }

    /** Delete all expired notifications (run via cron or on page load). */
    public function cleanupExpired(): int {
        $stmt = $this->db->prepare(
            "DELETE FROM notifications WHERE expires_at IS NOT NULL AND expires_at <= NOW()"
        );
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** Mark one notification as read. */
    public function markRead(string $id): bool {
        return $this->update($id, ['IsRead' => 1]);
    }
}
