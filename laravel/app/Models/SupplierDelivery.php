<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierDelivery extends Model
{
    protected $table = 'supplier_deliveries';
    protected $primaryKey = 'DeliveryID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'DeliveryID', 'SupplierID', 'DeliveryDate', 'ItemName',
        'Quantity', 'Unit', 'QualityGrade', 'ReceivedBy', 'Notes', 'Status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }
}
