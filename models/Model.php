<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey;

    public function __construct()
    {
        $this->db = getDb();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC")->fetchAll();
    }

    public function find(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public function create(array $data): string
    {
        $cols = implode(', ', array_keys($data));
        $phs = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$cols}) VALUES ({$phs})");
        $stmt->execute(array_values($data));
        return $data[$this->primaryKey] ?? '';
    }

    public function update(string $id, array $data): bool
    {
        $sets = array_map(fn($c) => "{$c} = ?", array_keys($data));
        $params = array_values($data);
        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = ?");
        return $stmt->execute($params);
    }

    public function delete(string $id): bool
    {
        return $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?")->execute([$id]);
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public function queryScalar(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function sum(string $column, string $where = '1=1', array $params = []): float
    {
        return (float) $this->queryScalar("SELECT COALESCE(SUM({$column}), 0) FROM {$this->table} WHERE {$where}", $params);
    }
}
