<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class MachineModel extends Model {
    protected string $table = 'machines';
    protected string $primaryKey = 'MachineID';

    public function getOperational(): array {
        return $this->query("SELECT * FROM machines WHERE Status = 'Operational' ORDER BY Name");
    }

    public function getStatusCounts(): array {
        $rows = $this->query("SELECT Status, COUNT(*) AS cnt FROM machines GROUP BY Status");
        $out = ['Operational' => 0, 'Maintenance' => 0, 'Down' => 0];
        foreach ($rows as $r) $out[$r['Status']] = (int)$r['cnt'];
        return $out;
    }
}
