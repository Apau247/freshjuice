<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    protected $table = 'production_batches';
    protected $primaryKey = 'BatchID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'BatchID', 'BatchNumber', 'ProductionDate', 'Flavour', 'Quantity',
        'Unit', 'Status', 'UserID', 'RawMaterialID', 'PackagingMaterialID',
        'MachineID', 'Notes',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'MachineID', 'MachineID');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'RawMaterialID', 'MaterialID');
    }

    public function packagingMaterial()
    {
        return $this->belongsTo(PackagingMaterial::class, 'PackagingMaterialID', 'PackageID');
    }
}
