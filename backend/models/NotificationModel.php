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

    /**
     * Alerts scoped to what the signed-in user is allowed to see. Notifications
     * is open to every role, so each section has to be filtered by its own
     * module rather than the page as a whole.
     */
    public function getAll(): array
    {
        return [
            'low_stock'        => can('materials')      ? $this->getLowStockAlerts()  : [],
            'expiring_certs'   => can('certifications') ? $this->getExpiringCerts()   : [],
            'expiring_permits' => can('permits')        ? $this->getExpiringPermits() : [],
        ];
    }
}
