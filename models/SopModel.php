<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class SopModel extends Model {

    public function getTemplates(): array {
        return $this->query(
            "SELECT s.*, u.Name AS CreatedByName
             FROM sop_templates s LEFT JOIN users u ON s.CreatedBy = u.UserID
             ORDER BY s.SOP_ID DESC"
        );
    }

    public function findTemplate(string $id): ?array {
        return $this->queryOne("SELECT * FROM sop_templates WHERE SOP_ID = ?", [$id]);
    }

    public function createTemplate(array $data): bool {
        $cols = implode(', ', array_keys($data));
        $phs = implode(', ', array_fill(0, count($data), '?'));
        return $this->db->prepare("INSERT INTO sop_templates ({$cols}) VALUES ({$phs})")->execute(array_values($data));
    }

    public function updateTemplate(string $id, array $data): bool {
        $sets = array_map(fn($c) => "{$c} = ?", array_keys($data));
        $params = array_values($data);
        $params[] = $id;
        return $this->db->prepare("UPDATE sop_templates SET " . implode(', ', $sets) . " WHERE SOP_ID = ?")->execute($params);
    }

    public function deleteTemplate(string $id): bool {
        return $this->db->prepare("DELETE FROM sop_templates WHERE SOP_ID = ?")->execute([$id]);
    }

    public function getChecklists(): array {
        return $this->query(
            "SELECT sc.*, s.Title AS SOPTitle, pb.BatchNumber, u.Name AS SupervisorName
             FROM sop_checklists sc
             LEFT JOIN sop_templates s ON sc.SOP_ID = s.SOP_ID
             LEFT JOIN production_batches pb ON sc.BatchID = pb.BatchID
             LEFT JOIN users u ON sc.SupervisorID = u.UserID
             ORDER BY sc.Date DESC"
        );
    }

    public function createChecklist(array $data): bool {
        $cols = implode(', ', array_keys($data));
        $phs = implode(', ', array_fill(0, count($data), '?'));
        return $this->db->prepare("INSERT INTO sop_checklists ({$cols}) VALUES ({$phs})")->execute(array_values($data));
    }

    public function updateChecklist(string $id, array $data): bool {
        $sets = array_map(fn($c) => "{$c} = ?", array_keys($data));
        $params = array_values($data);
        $params[] = $id;
        return $this->db->prepare("UPDATE sop_checklists SET " . implode(', ', $sets) . " WHERE ChecklistID = ?")->execute($params);
    }
}
