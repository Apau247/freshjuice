<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class CustomerModel extends Model {
    protected string $table = 'customers';
    protected string $primaryKey = 'CustomerID';
}
