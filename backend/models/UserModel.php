<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class UserModel extends Model {
    protected string $table = 'users';
    protected string $primaryKey = 'UserID';

    public function getRoles(): array {
        return $this->db->query("SELECT * FROM roles ORDER BY RoleName")->fetchAll();
    }

    public function getAllWithRoles(): array {
        return $this->query("SELECT u.*, r.RoleName FROM users u LEFT JOIN roles r ON u.RoleID = r.RoleID ORDER BY u.UserID");
    }
}
