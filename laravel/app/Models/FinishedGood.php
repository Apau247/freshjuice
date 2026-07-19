<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinishedGood extends Model
{
    protected $table = 'finished_goods';
    protected $primaryKey = 'FG_ID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'FG_ID', 'BatchID', 'Flavour', 'ExpiryDate',
        'QuantityAvailable', 'Unit', 'StorageLocation',
    ];
}
