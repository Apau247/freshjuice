<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'machines';
    protected $primaryKey = 'MachineID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'MachineID', 'Name', 'Type', 'Location', 'Status',
        'InstallDate', 'LastService',
    ];
}
