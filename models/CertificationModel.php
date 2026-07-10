<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class CertificationModel extends Model {
    protected string $table = 'certifications';
    protected string $primaryKey = 'CertID';

    public function getExpiringSoon(int $days = 90): array {
        return $this->query("SELECT * FROM certifications WHERE ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL ? DAY) ORDER BY ExpiryDate ASC", [$days]);
    }

    public function getActiveCount(): int {
        return (int) $this->queryScalar("SELECT COUNT(*) FROM certifications WHERE Status = 'Active'");
    }
}
