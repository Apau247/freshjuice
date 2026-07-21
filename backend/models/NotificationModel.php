<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class NotificationModel extends Model
{
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

    public function getAll(): array
    {
        return [
            'low_stock'       => $this->getLowStockAlerts(),
            'expiring_certs'  => $this->getExpiringCerts(),
            'expiring_permits' => $this->getExpiringPermits(),
        ];
    }
}
