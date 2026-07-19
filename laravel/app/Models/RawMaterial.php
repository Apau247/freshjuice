<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $table = 'raw_materials';
    protected $primaryKey = 'MaterialID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'MaterialID', 'Name', 'Type', 'Unit', 'CurrentStock',
        'MinStock', 'SupplierID', 'Status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }
}
