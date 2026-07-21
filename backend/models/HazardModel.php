<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class HazardModel extends Model
{
    protected string $table = 'hazard_register';
    protected string $primaryKey = 'HazardID';

    public function getAllActive(): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE Status = 'Active'
            ORDER BY RiskRating DESC
        ";
        return $this->query($sql);
    }

    public function getHighRisk(): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE RiskRating >= 12 AND Status = 'Active'
            ORDER BY RiskRating DESC
        ";
        return $this->query($sql);
    }
}