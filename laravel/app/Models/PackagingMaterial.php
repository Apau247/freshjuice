<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterial extends Model
{
    protected $table = 'packaging_materials';
    protected $primaryKey = 'PackageID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'PackageID', 'Name', 'Type', 'Unit', 'CurrentStock',
        'MinStock', 'Status',
    ];
}
