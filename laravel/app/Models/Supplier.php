<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'SupplierID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'SupplierID', 'Name', 'Contact', 'Email', 'Phone',
        'Address', 'Type', 'Status',
    ];

    public function deliveries()
    {
        return $this->hasMany(SupplierDelivery::class, 'SupplierID', 'SupplierID');
    }
}
